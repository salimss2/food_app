<?php

namespace Modules\Users\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get authenticated user notifications.
     * GET /api/v1/notifications
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);

        // Optional: Mark all as read when fetching if requested
        if ($request->has('mark_as_read')) {
            $request->user()->unreadNotifications->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifications retrieved successfully',
            'data'    => $notifications,
        ]);
    }

    /**
     * Mark a specific notification as read.
     * POST /api/v1/notifications/{id}/read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }
}
