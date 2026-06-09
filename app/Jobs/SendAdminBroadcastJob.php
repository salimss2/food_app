<?php

namespace App\Jobs;

use App\Models\AdminNotification;
use App\Models\User;
use App\Notifications\AdminBroadcastNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAdminBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $adminNotification;
    protected $senderId;

    /**
     * Create a new job instance.
     */
    public function __construct(AdminNotification $adminNotification, $senderId = null)
    {
        $this->adminNotification = $adminNotification;
        $this->senderId = $senderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Avoid sending twice
        if ($this->adminNotification->status === 'sent') {
            return;
        }

        $query = User::query();

        // Exclude currently authenticated user who sent the notification
        if ($this->senderId) {
            $query->where('id', '!=', $this->senderId);
        }

        // Exclude system administrators (System Admin role) from receiving promotional/broadcast notifications
        $query->whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['System Admin']);
        });

        // If target_role is not 'all', filter users by that specific role
        if ($this->adminNotification->target_role !== 'all') {
            $query->role($this->adminNotification->target_role);
        }

        // Chunk to process in small steps and prevent execution timeouts
        $query->chunk(200, function ($users) {
            foreach ($users as $user) {
                try {
                    $user->notify(new AdminBroadcastNotification($this->adminNotification));
                } catch (\Exception $e) {
                    Log::error("Failed to notify user {$user->id} in SendAdminBroadcastJob: " . $e->getMessage());
                }
            }
        });

        // Mark the broadcast notification record as successfully sent
        $this->adminNotification->update([
            'status' => 'sent'
        ]);
    }
}
