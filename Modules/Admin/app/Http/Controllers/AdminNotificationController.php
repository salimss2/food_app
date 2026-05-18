<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminNotification;
use App\Jobs\SendAdminBroadcastJob;
use Carbon\Carbon;

class AdminNotificationController extends Controller
{
    /**
     * Show the send notification form.
     */
    public function index()
    {
        return redirect()->route('admin.notification-history.index');
    }

    /**
     * Show sent notifications (History).
     */
    public function history()
    {
        $notifications = AdminNotification::where('status', 'sent')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin::notification-history', compact('notifications'));
    }

    /**
     * Show scheduled pending notifications.
     */
    public function scheduled()
    {
        $notifications = AdminNotification::where('status', 'pending')
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return view('admin::scheduled-notifications', compact('notifications'));
    }

    /**
     * Store a newly created notification.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'target_role' => 'required|string|in:Customer,Driver,Restaurant Admin,all',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        $scheduledAt = $request->scheduled_at ? Carbon::parse($request->scheduled_at) : null;

        $notification = AdminNotification::create([
            'title' => $request->title,
            'body' => $request->body,
            'target_role' => $request->target_role,
            'scheduled_at' => $scheduledAt,
            'status' => 'pending',
        ]);

        if (!$scheduledAt) {
            // Dispatch immediately in background
            SendAdminBroadcastJob::dispatch($notification);
        } else {
            // Dispatch with delay
            $delay = now()->diffInSeconds($scheduledAt);
            SendAdminBroadcastJob::dispatch($notification)->delay($delay);
        }

        return redirect()->back()->with('success', 'Notification processed successfully.');
    }

    /**
     * Cancel/delete a scheduled pending notification.
     */
    public function destroy($id)
    {
        $notification = AdminNotification::findOrFail($id);

        if ($notification->status === 'pending') {
            $notification->delete();
            return redirect()->back()->with('success', 'Scheduled notification cancelled.');
        }

        return redirect()->back()->with('error', 'Cannot delete sent notification.');
    }
}
