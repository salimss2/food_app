<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TicketRespondedNotification extends Notification
{
    use Queueable;

    public $ticket;
    public $adminResponse;

    /**
     * Create a new notification instance.
     */
    public function __construct($ticket, $adminResponse = null)
    {
        $this->ticket = $ticket;
        $this->adminResponse = $adminResponse ?? ($ticket->admin_response ?? '');
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toArray($notifiable): array
    {
        $ticketCode = $this->ticket->ticket_code ?? ('TK-' . $this->ticket->id);
        $responsePreview = 'قامت الإدارة بالرد على تذكرتك: "' . Str::limit($this->adminResponse, 100) . '"';

        return [
            'title'          => 'رد جديد على تذكرتك #' . $ticketCode,
            'message'        => $responsePreview,
            'body'           => $responsePreview,
            'description'    => $responsePreview,
            'subtitle'       => $responsePreview,
            'content'        => $responsePreview,
            'type'           => 'ticket_response',
            'ticket_id'      => $this->ticket->id,
            'ticket_code'    => $ticketCode,
            'admin_response' => $this->ticket->admin_response,
            'status'         => $this->ticket->status,
        ];
    }
}
