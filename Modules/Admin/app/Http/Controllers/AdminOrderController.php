<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Orders\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Modules\Orders\Events\OrderBroadcasted;

class AdminOrderController extends Controller
{
    /**
     * Active Orders View
     */
    public function activeOrders(Request $request)
    {
        $activeStatuses = ['pending_admin_approval', 'pending_driver_acceptance', 'driver_assigned', 'ready_for_pickup', 'on_the_way'];
        $query = Order::with(['user', 'restaurant', 'driver'])
            ->whereIn('status', $activeStatuses);

        // Apply Filters
        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', strtolower($request->status));
        }
        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }
        if ($request->filled('phone')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->phone . '%');
            });
        }

        if ($request->filled('customer_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%');
            });
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        // Calculate Global KPIs
        $salesToday = Order::whereIn('status', ['delivered'])
            ->whereDate('updated_at', Carbon::today())
            ->sum('total');

        $activeOrdersCount = Order::whereIn('status', $activeStatuses)->count();

        // Delayed/Critical Orders KPI (> 45 mins)
        $delayedOrdersCount = Order::whereIn('status', $activeStatuses)
            ->where('created_at', '<=', Carbon::now()->subMinutes(45))
            ->count();

        // Fleet availability
        $fleetAvailableCount = User::role('Driver')
            ->whereHas('availability', function($q) {
                $q->where('is_online', 1);
            })->count();

        // Dropdown Data
        $restaurants = \Modules\Restaurants\Models\Restaurant::all();
        $drivers = User::role('Driver')->get();

        return view('admin::orders', compact('orders', 'salesToday', 'activeOrdersCount', 'delayedOrdersCount', 'fleetAvailableCount', 'restaurants', 'drivers'));
    }

    /**
     * Scheduled Orders View
     */
    public function scheduledOrders()
    {
        $orders = \Modules\Scheduling\Models\ScheduledOrder::with(['user', 'restaurant'])
            ->whereIn('status', ['scheduled', 'pending'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        // Group by Date for better UI organization
        $groupedOrders = $orders->groupBy(function($order) {
            return Carbon::parse($order->scheduled_at)->format('Y-m-d');
        });

        return view('admin::scheduled-orders', compact('groupedOrders'));
    }

    /**
     * Order History View
     */
    public function orderHistory(Request $request)
    {
        $query = Order::with(['user', 'restaurant', 'driver', 'logs'])
            ->whereIn('status', ['delivered', 'canceled']);

        // Filters
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('customer_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->customer_name . '%')
                  ->orWhere('phone', 'like', '%' . $request->customer_name . '%');
            });
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        $restaurants = \Modules\Restaurants\Models\Restaurant::all();
        $drivers     = User::role('Driver')->get();

        return view('admin::order-history', compact('orders', 'restaurants', 'drivers'));
    }

    /**
     * God Mode: Force Cancel
     */
    public function forceCancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $reason = $request->input('cancellation_reason', 'Canceled by Administrator');

        $order->status = 'canceled';
        $order->cancellation_reason = $reason;

        // Smart Refund Logic
        if ($order->payment_method === 'bank_transfer') {
            if (in_array($order->payment_status, ['completed', 'pending_verification'])) {
                $order->payment_status = 'pending_refund';
            }
        } elseif ($order->payment_method === 'cod') {
            $order->payment_status = 'canceled';
        }

        $order->save();

        return redirect()->back()->with('success', 'Order #' . $order->id . ' has been canceled. Refund status: ' . $order->payment_status);
    }

    /**
     * God Mode: Reassign Driver
     */
    public function reassignDriver(Request $request, $id)
    {
        $request->validate([
            'driver_id' => 'required|exists:users,id'
        ]);

        // Ensure user is driver
        $driver = User::role('Driver')->findOrFail($request->driver_id);

        $order = Order::findOrFail($id);
        $order->driver_id = $driver->id;

        // Smart State Transition: auto-assign when a driver is manually assigned by admin
        if ($order->status === 'pending_driver_acceptance') {
            $order->status = 'driver_assigned';
        }

        $order->save();

        return redirect()->back()->with('success', 'Order #' . $order->id . ' driver reassigned to ' . $driver->name . '.');
    }

    /**
     * Approve Bank Transfer Payment
     */
    public function approvePayment($id)
    {
        $order = Order::findOrFail($id);

        // Update statuses strictly adhering to DB ENUMs
        $order->payment_status = 'completed';
        $order->status = 'pending_driver_acceptance';
        $order->save();

        // Trigger driver assignment logic / broadcasting
        try {
            event(new OrderBroadcasted($order));
        } catch (\Exception $e) {
            \Log::error("Broadcasting failed during admin approval: " . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Order #' . $order->order_number . ' payment approved. Now pending driver acceptance.');
    }

    /**
     * Reject Bank Transfer Payment
     */
    public function rejectPayment(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $order = Order::findOrFail($id);

        // Update statuses strictly adhering to DB ENUMs
        $order->payment_status = 'rejected';
        $order->status = 'canceled';
        $order->rejection_reason = $request->rejection_reason;
        $order->cancellation_reason = 'Payment Rejected: ' . $request->rejection_reason;
        $order->save();

        return redirect()->back()->with('warning', 'Order #' . $order->order_number . ' payment rejected and order canceled.');
    }
}
