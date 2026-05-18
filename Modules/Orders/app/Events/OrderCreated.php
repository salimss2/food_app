<?php

namespace Modules\Orders\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Orders\Models\Order;

class OrderCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        // Eager load items and user for broadcasting, if needed
        $this->order = $order->loadMissing('items.meal', 'user');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info('Broadcasting to channel:', ['channel' => 'private-restaurant.' . $this->order->restaurant_id]);

        return [
            new PrivateChannel('restaurant.' . $this->order->restaurant_id),
            new PrivateChannel('drivers'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'OrderCreated';
    }

    /**
     * Data to be sent with the broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->order->id,
            'total' => $this->order->total,
            'status' => $this->order->status,
            'payment_method' => $this->order->payment_method,
            'restaurant_id' => $this->order->restaurant_id,
            'user' => [
                'id' => $this->order->user->id,
                'name' => $this->order->user->name,
            ],
            'items_count' => $this->order->items->count(),
        ];
    }
}
