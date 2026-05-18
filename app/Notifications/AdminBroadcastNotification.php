<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Services\FcmService;
use Illuminate\Support\Facades\Log;

class AdminBroadcastNotification extends Notification
{
    use Queueable;

    protected $adminNotification;

    /**
     * Create a new notification instance.
     */
    public function __construct($adminNotification)
    {
        $this->adminNotification = $adminNotification;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        // Trigger manual FCM push notification using the existing FcmService
        $this->sendPushNotification($notifiable);

        return ['database'];
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toDatabase($notifiable): array
    {
        return [
            'title' => $this->adminNotification->title,
            'message' => $this->adminNotification->body,
            'type' => 'admin_broadcast',
            'admin_notification_id' => $this->adminNotification->id,
        ];
    }

    /**
     * Internal helper to trigger FCM push.
     */
    protected function sendPushNotification($notifiable): void
    {
        $deviceToken = $notifiable->fcm_token ?? $notifiable->device_token ?? ($notifiable->profile->fcm_token ?? null);

        if ($deviceToken) {
            try {
                app(FcmService::class)->sendNotification(
                    $deviceToken,
                    $this->adminNotification->title,
                    $this->adminNotification->body,
                    [
                        'type' => 'admin_broadcast',
                        'admin_notification_id' => (string) $this->adminNotification->id,
                    ]
                );
            } catch (\Exception $e) {
                Log::error('FCM sending failed in admin broadcast notification: ' . $e->getMessage());
            }
        }
    }
}
