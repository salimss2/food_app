<?php

namespace Modules\Orders\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Orders\Models\Order;

/**
 * NewOrderEvent — Broadcasts a new order to the restaurant owner's private channel.
 *
 * Channel: private-restaurant.{owner_id}
 * Implements ShouldBroadcastNow to bypass the queue and fire immediately.
 */
class NewOrderEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;

    /**
     * The restaurant owner's user ID (used for the private channel name).
     */
    public int $ownerId;

    /**
     * Create a new event instance.
     *
     * @param  Order  $order   The newly created order (eager-loaded with items/user).
     * @param  int    $ownerId The restaurant owner's user ID.
     */
    public function __construct(Order $order, int $ownerId)
    {
        $this->order   = $order->loadMissing('items.meal', 'user', 'restaurant');
        $this->ownerId = $ownerId;
    }

    /**
     * Broadcast on a private channel scoped to the restaurant owner.
     * Flutter app listens on: private-restaurant.{owner_id}
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('restaurant.' . $this->ownerId),
        ];
    }

    /**
     * Custom event name so Flutter can distinguish it from other broadcasts.
     */
    public function broadcastAs(): string
    {
        return 'NewOrderEvent';
    }

    /**
     * Full order payload sent to the Flutter app.
     * Includes items, customer info, and totals for immediate UI update.
     */
    public function broadcastWith(): array
    {
        $items = $this->order->items->map(fn($item) => [
            'id'        => $item->id,
            'meal_id'   => $item->meal_id,
            'meal_name' => $item->meal->name ?? 'Unknown',
            'quantity'  => $item->quantity,
            'subtotal'  => (float) $item->subtotal,
        ]);

        return [
            'id'             => $this->order->id,
            'order_number'   => $this->order->order_number,
            'status'         => $this->order->status,
            'total'          => (float) $this->order->total,
            'payment_method' => $this->order->payment_method,
            'payment_status' => $this->order->payment_status,
            'created_at'     => $this->order->created_at?->toISOString(),
            'customer'       => [
                'id'    => $this->order->user?->id,
                'name'  => $this->order->user?->name ?? 'Unknown',
                'phone' => $this->order->user?->phone ?? null,
            ],
            'items'          => $items,
            'items_count'    => $items->count(),
        ];
    }
}
