<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Services\FcmService;
use Illuminate\Support\Facades\Log;

class OrderRejectedNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $rejection_reason;

    public function __construct($order, $rejection_reason)
    {
        $this->order = $order;
        $this->rejection_reason = $rejection_reason;
    }

    public function via($notifiable)
    {
        // Still trigger push manually as requested/designed
        $this->sendPushNotification($notifiable);

        return ['database'];
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toDatabase($notifiable)
    {
        return [
            'title'      => 'تم إلغاء الطلب',
            'message'    => 'عذراً، تم رفض طلبك رقم #' . $this->order->id . ' بسبب: ' . $this->rejection_reason,
            'order_id'   => $this->order->id,
            'status'     => 'rejected',
        ];
    }

    /**
     * Internal helper to trigger FCM push.
     */
    protected function sendPushNotification($notifiable)
    {
        // Try to get token from user or profile
        $deviceToken = $notifiable->fcm_token ?? $notifiable->device_token ?? ($notifiable->profile->fcm_token ?? null);

        if ($deviceToken) {
            try {
                $body = "عذراً، تم رفض إيصال الدفع الخاص بك. السبب: " . $this->rejection_reason;
                app(FcmService::class)->sendNotification(
                    $deviceToken,
                    "تنبيه بخصوص عملية الدفع",
                    $body,
                    [
                        'order_id' => (string) $this->order->id,
                        'status' => 'payment_rejected',
                        'type' => 'payment_rejected',
                    ]
                );
            } catch (\Exception $e) {
                Log::error('FCM sending failed in notification: ' . $e->getMessage());
            }
        }
    }
}
