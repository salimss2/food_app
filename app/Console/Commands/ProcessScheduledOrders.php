<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessScheduledOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:process-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled orders into active orders when their time arrives';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();
        $this->info("Checking for scheduled orders at: {$now}");

        // Query the scheduled_orders table for orders arriving within 30 minutes
        $scheduledOrders = \Modules\Scheduling\Models\ScheduledOrder::where('scheduled_at', '<=', now()->addMinutes(30))
            ->where('status', 'scheduled')
            ->get();

        $count = $scheduledOrders->count();
        $this->info("Found {$count} scheduled order(s) to process.");

        foreach ($scheduledOrders as $scheduledOrder) {
            $order = \Illuminate\Support\Facades\DB::transaction(function () use ($scheduledOrder) {
                // Generate a unique ORD- prefixed order number
                do {
                    $orderNumber = 'ORD-' . strtoupper(\Illuminate\Support\Str::random(10));
                } while (\Modules\Orders\Models\Order::where('order_number', $orderNumber)->exists());

                $grandTotal = round((float) $scheduledOrder->total_amount + (float) $scheduledOrder->delivery_fee, 2);

                // 1. Convert to a real active order
                $newOrder = \Modules\Orders\Models\Order::create([
                    'order_number' => $orderNumber,
                    'group_id' => \Illuminate\Support\Str::uuid()->toString(),
                    'user_id' => $scheduledOrder->user_id,
                    'restaurant_id' => $scheduledOrder->restaurant_id,
                    'driver_id' => null,
                    'payment_method' => 'cod',
                    'total' => $grandTotal,
                    'total_price' => $grandTotal,
                    'status' => 'pending_driver_acceptance',
                    'payment_status' => 'pending_collection',
                    'scheduled_at' => $scheduledOrder->scheduled_at,
                    'latitude' => $scheduledOrder->latitude,
                    'longitude' => $scheduledOrder->longitude,
                    'delivery_distance' => $scheduledOrder->delivery_distance,
                    'delivery_fee' => $scheduledOrder->delivery_fee,
                    'driver_commission' => $scheduledOrder->driver_commission,
                    'platform_commission' => $scheduledOrder->platform_commission,
                ]);

                // 2. Recreate OrderItems from the snapshot
                $items = $scheduledOrder->items_content ?? [];
                foreach ($items as $item) {
                    $qty = (int) ($item['quantity'] ?? 1);
                    $sub = (float) ($item['subtotal'] ?? 0);
                    $unitPrice = isset($item['price']) ? (float) $item['price'] : ($qty > 0 ? $sub / $qty : $sub);

                    \Modules\Orders\Models\OrderItem::create([
                        'order_id'       => $newOrder->id,
                        'meal_id'        => $item['meal_id'] ?? null,
                        'variant_id'     => $item['variant_id'] ?? null,
                        'variant_name'   => $item['variant_name'] ?? null,
                        'offer_id'       => $item['offer_id'] ?? null,
                        'name'           => $item['name'] ?? null,
                        'type'           => $item['type'] ?? 'regular_meal',
                        'quantity'       => $qty,
                        'price'          => $unitPrice,
                        'subtotal'       => $sub,
                        'customizations' => $item['customizations'] ?? null,
                        'combo_meals'    => $item['combo_meals'] ?? null,
                    ]);
                }

                // 3. Update the scheduled order status to 'completed'
                $scheduledOrder->update(['status' => 'completed']);

                return $newOrder;
            });

            // 4. Notify Listeners and fire FCM Push Notifications (outside transaction to avoid delays/locking)
            $order->load('items.meal', 'user', 'restaurant.owner');

            // 1. OrderBroadcasted
            try {
                \Illuminate\Support\Facades\Log::info("Scheduled Order Command: Dispatching OrderBroadcasted for Order #{$order->id}");
                event(new \Modules\Orders\Events\OrderBroadcasted($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Scheduled Order Command: OrderBroadcasted failed for order {$order->id}: " . $e->getMessage());
            }

            // 2. NewOrderEvent
            try {
                $ownerId = $order->restaurant?->owner_id;
                if ($ownerId) {
                    \Illuminate\Support\Facades\Log::info("Scheduled Order Command: Dispatching NewOrderEvent to owner private channel for Order #{$order->id}");
                    event(new \Modules\Orders\Events\NewOrderEvent($order, $ownerId));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Scheduled Order Command: NewOrderEvent failed for order {$order->id}: " . $e->getMessage());
            }

            // 3. OrderCreated
            try {
                \Illuminate\Support\Facades\Log::info("Scheduled Order Command: Dispatching OrderCreated for Order #{$order->id}");
                event(new \Modules\Orders\Events\OrderCreated($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Scheduled Order Command: OrderCreated failed for order {$order->id}: " . $e->getMessage());
            }

            // 4. Send FCM Push Notification to Restaurant Owner
            try {
                $owner = $order->restaurant?->owner;
                $token = $owner?->fcm_token;
                if ($token) {
                    \Illuminate\Support\Facades\Log::info("Scheduled Order Command: Sending FCM to Owner #{$owner->id} for Order #{$order->id}");
                    app(\App\Services\FcmService::class)->sendNotification(
                        $token,
                        "طلب مجدول جديد! 🍔",
                        "حان موعد الطلب المجدول بقيمة {$order->total} ريال.",
                        ['type' => 'new_order', 'order_id' => (string) $order->id]
                    );
                } else {
                    \Illuminate\Support\Facades\Log::warning("Scheduled Order Command: Missing FCM token or owner for Order #{$order->id}");
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Scheduled Order Command: FCM to Owner failed for order {$order->id}: " . $e->getMessage());
            }

            // 5. NewOrderAvailable
            try {
                \Illuminate\Support\Facades\Log::info("Scheduled Order Command: Dispatching NewOrderAvailable for Order #{$order->id}");
                event(new \Modules\Orders\Events\NewOrderAvailable($order));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Scheduled Order Command: NewOrderAvailable failed for order {$order->id}: " . $e->getMessage());
            }

            // 6. Send FCM Push Notification to Available Drivers
            try {
                $availableDrivers = \App\Models\User::role('driver')
                    ->whereHas('availability', function ($q) {
                        $q->where('is_online', true)->where('availability', 'idle');
                    })
                    ->whereNotNull('fcm_token')
                    ->get();

                $driverTokens = $availableDrivers->pluck('fcm_token')->toArray();

                if (!empty($driverTokens)) {
                    \Illuminate\Support\Facades\Log::info("Scheduled Order Command: Sending FCM to " . count($driverTokens) . " available drivers for Order #{$order->id}");
                    app(\App\Services\FcmService::class)->sendToMultipleDevices(
                        $driverTokens,
                        "طلب جديد متاح! 🚚",
                        "لديك طلب جديد سارع في قبوله.",
                        ['type' => 'available_order', 'order_id' => (string) $order->id]
                    );
                } else {
                    \Illuminate\Support\Facades\Log::info("Scheduled Order Command: No available drivers found for Order #{$order->id}");
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Scheduled Order Command: Driver FCM failed for order {$order->id}: " . $e->getMessage());
            }

            $this->info("Processed ScheduledOrder #{$scheduledOrder->id} into Order #{$order->id}");
        }

        $this->info('Scheduled orders processed successfully.');
    }
}
