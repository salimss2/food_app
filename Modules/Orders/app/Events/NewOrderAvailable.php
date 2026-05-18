<?php

namespace Modules\Orders\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Orders\Models\Order;

class NewOrderAvailable implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order->loadMissing('restaurant');
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        Log::info('Broadcasting NewOrderAvailable to channel: drivers.available');
        return [
            new PrivateChannel('drivers.available'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'NewOrderAvailable';
    }

    /**
     * Data to be sent with the broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'pickup_location' => $this->order->restaurant->name ?? 'Unknown',
            'total' => (float) $this->order->total,
            'status' => $this->order->status,
            'created_at' => $this->order->created_at?->toISOString(),
        ];
    }
}
