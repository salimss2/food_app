<?php

namespace Modules\Orders\Observers;

use Modules\Orders\Models\Order;
use App\Models\User;

use Kreait\Laravel\Firebase\Facades\Firebase;

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

        $this->syncToFirebase($order);
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

        $this->syncToFirebase($order);
    }

    /**
     * Sync order data to Firebase Realtime Database.
     */
    private function syncToFirebase(Order $order): void
    {
        try {
            // Load required relations if they are not loaded to prevent N+1 and errors
            $order->loadMissing(['user', 'restaurant']);

            $data = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'payment_status' => $order->payment_status,
                'total' => (float) $order->total,
                'user_name' => $order->user ? $order->user->name : 'Unknown',
                'restaurant_name' => $order->restaurant ? $order->restaurant->name : 'Unknown',
                'timestamp' => now()->timestamp * 1000, // For sorting in JS
                'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i') : now()->format('Y-m-d H:i'),
            ];

            Firebase::database()->getReference('orders/' . $order->id)->set($data);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Firebase sync failed for Order ' . $order->id . ': ' . $e->getMessage());
        }
    }
}
