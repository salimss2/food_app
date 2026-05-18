<?php

namespace Modules\Orders\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Orders\Models\Order;

class OrderAssignedToDriver implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;
    public $driver_id;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, $driver_id)
    {
        $this->order = $order;
        $this->driver_id = $driver_id;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        Log::info("Broadcasting OrderAssignedToDriver to user channel: App.Models.User.{$this->driver_id}");
        return [
            new PrivateChannel('App.Models.User.' . $this->driver_id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'OrderAssignedToDriver';
    }

    /**
     * Data to be sent with the broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'message' => 'لقد تم تعيينك لهذا الطلب من قبل الإدارة.',
        ];
    }
}
