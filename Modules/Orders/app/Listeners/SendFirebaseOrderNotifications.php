<?php

namespace Modules\Orders\Listeners;

use Modules\Orders\Events\OrderBroadcasted;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class SendFirebaseOrderNotifications
{
    /**
     * Handle the event.
     */
    public function handle(OrderBroadcasted $event): void
    {
        $order = $event->order;

        try {
            $messaging = Firebase::messaging();

            // 1. Send to available drivers specifically via FCM tokens
            $restaurantName = $order->restaurant->name ?? 'مجهول';

            $drivers = \App\Models\User::role('driver')
                ->whereHas('availability', function ($q) {
                    $q->where('is_online', true)->where('availability', 'idle');
                })
                ->whereNotNull('fcm_token')
                ->get();

            $tokens = $drivers->pluck('fcm_token')->toArray();

            if (!empty($tokens)) {
                $notification = Notification::create(
                    'طلب جديد متاح!',
                    "هناك طلب جديد من مطعم {$restaurantName}، سارع بقبوله."
                );

                $driverMessage = CloudMessage::new()
                    ->withNotification($notification)
                    ->withData([
                        'order_id' => (string) $order->id,
                        'restaurant_id' => (string) $order->restaurant_id,
                        'type' => 'available_delivery',
                    ]);

                $messaging->sendMulticast($driverMessage, $tokens);
            }

            // 2. Send to Restaurant Owner
            // Ensure we load the restaurant and its owner
            $restaurant = $order->restaurant()->with('owner')->first();

            if ($restaurant && $restaurant->owner && $restaurant->owner->fcm_token) {
                $restaurantMessage = CloudMessage::withTarget('token', $restaurant->owner->fcm_token)
                    ->withNotification(Notification::create('طلب جديد! 👨‍🍳', "لديك طلب جديد برقم #{$order->id}، يرجى البدء بالتجهيز."))
                    ->withData([
                        'order_id' => (string) $order->id,
                        'type' => 'new_order',
                    ]);

                $messaging->send($restaurantMessage);
            }

        } catch (\Exception $e) {
            // Log the error but don't stop the order creation process
            Log::error('Failed to send Firebase notifications for Order #' . $order->id . ': ' . $e->getMessage());
        }
    }
}
