<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderAdminNotification extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for the database.
     */
    public function toDatabase($notifiable): array
    {
        $customerName = $this->order->user->name ?? 'Customer';
        $orderNumber = $this->order->order_number ?? $this->order->id;

        return [
            'title' => 'طلب جديد من زبون! 🍔',
            'body' => 'Order #' . $orderNumber . ' has been placed by ' . $customerName,
            'source' => 'Customer',
            'action_url' => route('admin.orders.index'),
            'resource_id' => $this->order->id,
        ];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
