<?php

namespace Modules\Delivery\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $orderId;
    public float $latitude;
    public float $longitude;

    /**
     * Create a new event instance.
     */
    public function __construct(int $orderId, float $latitude, float $longitude)
    {
        $this->orderId = $orderId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Broadcast on order tracking channel.
     * Flutter and web clients listen on: order.{order_id}.tracking
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('order.' . $this->orderId . '.tracking'),
        ];
    }

    /**
     * Custom event name so frontend clients can identify the broadcast type.
     */
    public function broadcastAs(): string
    {
        return 'DriverLocationUpdated';
    }

    /**
     * Real-time GPS coordinate payload.
     */
    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->orderId,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'timestamp' => now()->toISOString(),
        ];
    }
}
