<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminNotificationInboxController extends Controller
{
    /**
     * Show all incoming notifications for the authenticated admin user.
     */
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->paginate(15);

        return view('admin::inbox', compact('notifications'));
    }

    /**
     * Mark a single notification as read and dynamically redirect based on its data payload.
     */
    public function readAndRedirect($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        $notification->markAsRead();

        $data = $notification->data ?? [];
        $actionUrl = $data['action_url'] ?? null;
        $resourceId = $data['resource_id'] ?? null;

        // Dynamic redirection logic based on notification data payload
        if ($actionUrl) {
            return redirect($actionUrl);
        }

        // If a specific order ID or resource ID is provided, try to redirect appropriately
        if ($resourceId) {
            // Check if it's an order resource notification
            if (isset($data['order_id']) || stripos($notification->type, 'order') !== false) {
                // If there's a show route, we could redirect there. Otherwise, standard order listing.
                return redirect()->route('admin.orders.index');
            }
            
            // Check if it's a complaint resource notification
            if (stripos($notification->type, 'complaint') !== false || stripos($notification->type, 'feedback') !== false) {
                return redirect()->route('admin.complaints.index');
            }
        }

        return redirect()->route('admin.notifications.inbox')->with('success', 'Alert marked as read.');
    }

    /**
     * Mark all unread notifications for the admin as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All alerts marked as read.');
    }
}
