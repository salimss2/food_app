<?php

namespace Modules\Orders\Observers;

use Modules\Orders\Models\Order;
use App\Models\User;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $order->logs()->create([
            'status' => $order->status ?? 'pending',
            'description' => 'Customer created order'
        ]);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->isDirty('status')) {
            $description = 'Order status updated';
            switch ($order->status) {
                case 'accepted':
                    $description = 'Restaurant accepted the order';
                    break;
                case 'preparing':
                    $description = 'Order is being prepared';
                    break;
                case 'picked_up':
                    $description = 'Order picked up by driver';
                    break;
                case 'delivered':
                    $description = 'Order delivered successfully';
                    break;
                case 'canceled':
                    $description = 'Order has been canceled';
                    break;
            }

            $order->logs()->create([
                'status' => $order->status,
                'description' => $description
            ]);
        }

        if ($order->isDirty('driver_id') && $order->driver_id) {
            $driver = User::find($order->driver_id);
            if ($driver) {
                $order->logs()->create([
                    'status' => $order->status, // Use current status
                    'description' => 'Driver ' . $driver->name . ' assigned to order'
                ]);
            }
        }
    }
}
