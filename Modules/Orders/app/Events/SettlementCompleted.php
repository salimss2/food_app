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

class SettlementCompleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $driverId;

    /**
     * Create a new event instance.
     */
    public function __construct($driverId)
    {
        $this->driverId = $driverId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info('Broadcasting SettlementCompleted event on driver private channel:', ['driverId' => $this->driverId]);

        return [
            new PrivateChannel('driver.' . $this->driverId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'SettlementCompleted';
    }

    /**
     * Data to be sent with the broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'action' => 'cleared',
        ];
    }
}
