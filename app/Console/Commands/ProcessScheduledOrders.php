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

        $scheduledOrders = \Modules\Scheduling\Models\ScheduledOrder::where('scheduled_at', '<=', $now)
            ->whereIn('status', ['scheduled', 'pending'])
            ->get();

        $count = $scheduledOrders->count();
        $this->info("Found {$count} scheduled order(s) to process.");

        foreach ($scheduledOrders as $scheduledOrder) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($scheduledOrder) {
                // 1. Convert to a real order
                $order = \Modules\Orders\Models\Order::create([
                    'user_id'        => $scheduledOrder->user_id,
                    'restaurant_id'  => $scheduledOrder->restaurant_id,
                    'total'          => $scheduledOrder->total_amount,
                    'status'         => 'pending_driver_acceptance', 
                    'payment_method' => 'cod', 
                    'payment_status' => 'pending_collection',
                ]);

                // 2. Recreate OrderItems from the snapshot
                $items = $scheduledOrder->items_content ?? [];
                foreach ($items as $item) {
                    \Modules\Orders\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'meal_id'  => $item['meal_id'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }

                // 3. Update the scheduled order status
                $scheduledOrder->update(['status' => 'completed']);

                // 4. Notify Listeners (Kanban boards, Restaurant Dashboards, etc.)
                $fullOrder = $order->load('items.meal', 'user', 'restaurant');
                event(new \Modules\Orders\Events\OrderBroadcasted($fullOrder));
                event(new \Modules\Orders\Events\OrderCreated($fullOrder));

                $this->info("Processed ScheduledOrder #{$scheduledOrder->id} into Order #{$order->id}");
            });
        }

        $this->info('Scheduled orders processed successfully.');
    }
}
