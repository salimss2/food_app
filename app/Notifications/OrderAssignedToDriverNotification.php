<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Services\FcmService;
use Illuminate\Support\Facades\Log;

class OrderAssignedToDriverNotification extends Notification
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
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Send push notification immediately via custom service
        $this->sendPushNotification($notifiable);

        return ['database'];
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $restaurantName = $this->order->restaurant->name ?? 'N/A';

        return [
            'title' => 'New Delivery Assigned! 🛵',
            'message' => "Order #{$this->order->id} from {$restaurantName} has been assigned to you.",
            'order_id' => $this->order->id,
            'restaurant_name' => $restaurantName,
            'type' => 'order_assigned'
        ];
    }

    /**
     * Send the notification via FCM using our custom service.
     */
    protected function sendPushNotification($notifiable)
    {
        // Check for FCM token in user or profile
        $fcmToken = $notifiable->fcm_token ?? ($notifiable->profile->fcm_token ?? null);

        if ($fcmToken) {
            try {
                $restaurantName = $this->order->restaurant->name ?? 'N/A';
                
                app(FcmService::class)->sendNotification(
                    $fcmToken,
                    'New Delivery Assigned! 🛵',
                    "Order #{$this->order->id} from {$restaurantName} has been assigned to you.",
                    [
                        'order_id' => (string) $this->order->id,
                        'type' => 'order_assigned',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK' // Optional: for Flutter background handling
                    ]
                );
            } catch (\Exception $e) {
                Log::error("FCM Order Assigned Notification failed: " . $e->getMessage());
            }
        } else {
            Log::warning("No FCM token found for user ID: " . $notifiable->id);
        }
    }
}
