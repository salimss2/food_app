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
    public array $metaData;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order, array $metaData = [])
    {
        $this->order = $order->loadMissing('restaurant');
        $this->metaData = $metaData;
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
        return array_merge([
            'order_id' => $this->order->id,
            'group_id' => $this->order->group_id,
            'is_multi_vendor' => $this->order->isMultiVendorOrder(),
            'pickup_location' => $this->order->restaurant->name ?? 'Unknown',
            'total' => (float) ($this->order->total_amount ?? $this->order->total_price ?? $this->order->total ?? 0),
            'status' => $this->order->status,
            'created_at' => $this->order->created_at?->toISOString(),
        ], $this->metaData);
    }
}
