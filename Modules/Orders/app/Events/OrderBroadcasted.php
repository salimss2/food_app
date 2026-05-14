<?php

namespace Modules\Orders\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Orders\Models\Order;

/**
 * OrderBroadcasted
 *
 * Fires when a new COD order is placed. Broadcasts simultaneously to:
 *   - private-restaurant.{restaurant_id}  → Restaurant Owner app (Flutter)
 *   - private-drivers.available           → Drivers waiting for orders
 *   - private-admin.orders                → Admin Dashboard (real-time table)
 *
 * Also dispatches an FCM push notification to the restaurant owner's device token.
 */
class OrderBroadcasted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    /**
     * Create a new event instance.
     *
     * @param  Order  $order
     */
    public function __construct(Order $order)
    {
        // Eager load everything needed for the payload and FCM notification.
        $this->order = $order->loadMissing('items.meal', 'user', 'restaurant.owner');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('restaurant.' . $this->order->restaurant_id),
            new PrivateChannel('drivers.available'),
            new PrivateChannel('admin.orders'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'OrderBroadcasted';
    }

    /**
     * Full data payload sent via WebSocket.
     */
    public function broadcastWith(): array
    {
        $items = $this->order->items->map(fn($item) => [
            'id' => $item->id,
            'meal_id' => $item->meal_id,
            'meal_name' => $item->meal->name ?? 'Unknown',
            'quantity' => $item->quantity,
            'subtotal' => (float) $item->subtotal,
        ]);

        return [
            'id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => (float) $this->order->total,
            'status' => $this->order->status,
            'payment_method' => $this->order->payment_method,
            'payment_status' => $this->order->payment_status,
            'restaurant_id' => $this->order->restaurant_id,
            'restaurant_name' => $this->order->restaurant->name ?? 'Unknown',
            'restaurant_location' => $this->order->restaurant->location ?? null,
            'customer' => [
                'id' => $this->order->user?->id,
                'name' => $this->order->user?->name ?? 'Unknown',
                'phone' => $this->order->user?->phone ?? null,
            ],
            'items' => $items,
            'items_count' => $items->count(),
            'created_at' => $this->order->created_at?->toISOString(),
        ];
    }

    /**
     * Handle the event after broadcasting: send FCM notification to the restaurant owner.
     *
     * This method is automatically called by Laravel after the broadcast.
     * We send an FCM push to the restaurant owner's device token (stored in fcm_token).
     */
    public function broadcastAfter(): void
    {
        $this->sendFcmToRestaurantOwner();
    }

    /**
     * Send an FCM push notification to the restaurant owner's device.
     *
     * Uses the FCM HTTP v1 API. The owner's token is stored in users.fcm_token.
     * Falls back gracefully if no token is set.
     */
    private function sendFcmToRestaurantOwner(): void
    {
        try {
            $owner = $this->order->restaurant?->owner;

            if (!$owner || !$owner->fcm_token) {
                Log::info("OrderBroadcasted: No FCM token for restaurant owner", [
                    'restaurant_id' => $this->order->restaurant_id,
                ]);
                return;
            }

            $serverKey = config('services.firebase.server_key');

            if (!$serverKey) {
                Log::warning("OrderBroadcasted: Firebase server key not configured.");
                return;
            }

            $payload = [
                'to' => $owner->fcm_token,
                'notification' => [
                    'title' => '🛎️ New Order Received!',
                    'body' => "Order #{$this->order->order_number} — " . number_format($this->order->total, 2) . " SAR",
                    'sound' => 'default',
                ],
                'data' => [
                    'type' => 'new_order',
                    'order_id' => (string) $this->order->id,
                    'order_number' => $this->order->order_number,
                    'total' => (string) $this->order->total,
                    'status' => $this->order->status,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
                'priority' => 'high',
            ];

            $client = new \GuzzleHttp\Client();
            $response = $client->post('https://fcm.googleapis.com/fcm/send', [
                'headers' => [
                    'Authorization' => 'key=' . $serverKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            Log::info("FCM notification sent to restaurant owner", [
                'owner_id' => $owner->id,
                'order_number' => $this->order->order_number,
                'fcm_status_code' => $response->getStatusCode(),
            ]);
        } catch (\Throwable $e) {
            // Never let FCM failure break the order flow.
            Log::error("OrderBroadcasted FCM failed: " . $e->getMessage(), [
                'order_id' => $this->order->id,
            ]);
        }
    }
}
