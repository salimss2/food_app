<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderAcceptedNotification extends Notification
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
        return [
            'title' => 'تم تأكيد الدفع بنجاح',
            'message' => 'شكراً لك، تم قبول إيصال الدفع لطلبك رقم #' . $this->order->id,
            'order_id' => $this->order->id,
            'status' => 'verified',
        ];
    }

    /**
     * Send the notification via FCM using our custom service.
     */
    protected function sendPushNotification($notifiable)
    {
        $fcmToken = $notifiable->fcm_token ?? ($notifiable->profile->fcm_token ?? null);

        if ($fcmToken) {
            try {
                app(\App\Services\FcmService::class)->sendNotification(
                    $fcmToken,
                    'تم تأكيد الدفع بنجاح',
                    'شكراً لك، تم قبول إيصال الدفع لطلبك رقم #' . $this->order->id,
                    [
                        'order_id' => (string) $this->order->id,
                        'status' => 'verified',
                        'type' => 'order_accepted'
                    ]
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("FCM Accept Notification failed: " . $e->getMessage());
            }
        }
    }
}
