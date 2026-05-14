<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Orders\Models\Order;
use Modules\Orders\Events\OrderCreated;
use Modules\Orders\Events\OrderBroadcasted;
use App\Notifications\OrderAcceptedNotification;

class AdminPaymentController extends Controller
{
    /**
     * Display a paginated list of payments and quick metrics.
     */
    public function index(Request $request)
    {
        return $this->renderView($request, false);
    }

    /**
     * Filter payments using AJAX (returns partial view).
     */
    public function filter(Request $request)
    {
        return $this->renderView($request, true);
    }

    private function renderView(Request $request, $isAjax = false)
    {
        $query = Order::query()
            ->with(['user', 'restaurant', 'payment'])
            ->orderBy('created_at', 'desc');

        // ── Order Status filter ───────────────────────────────
        if ($request->filled('order_status') && $request->order_status !== 'all') {
            $query->where('status', $request->order_status);
        }

        // ── Payment Status filter (with fallback logic) ───────
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $pStatus = $request->payment_status;
            $query->where(function ($q) use ($pStatus) {
                $q->whereHas('payment', function ($q2) use ($pStatus) {
                    $q2->where('payment_status', $pStatus);
                })->orWhere(function ($q3) use ($pStatus) {
                    $q3->doesntHave('payment')->where('payment_status', $pStatus);
                });
            });
        }

        // ── Date Range filter ─────────────────────────────────
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // ── Min Amount filter ─────────────────────────────────
        if ($request->filled('min_amount') && is_numeric($request->min_amount)) {
            $query->where('total', '>=', (float) $request->min_amount);
        }

        // ── Quick Metrics (always unfiltered counts) ──────────
        $totalPending = $this->countByPaymentStatus('pending_verification');
        $totalPendingCollection = $this->countByPaymentStatus('pending_collection');
        $totalCanceled = $this->countByPaymentStatus('canceled');
        $totalProcessed = Order::where(function ($q) {
            $q->whereHas('payment', function ($q2) {
                $q2->whereIn('payment_status', ['completed', 'rejected']);
            })->orWhere(function ($q3) {
                $q3->doesntHave('payment')->whereIn('payment_status', ['completed', 'rejected']);
            });
        })->count();

        $orders = $query->paginate(15)->appends($request->all());

        if ($isAjax) {
            return view('admin::partials.payments-table-body', compact('orders'));
        }

        return view('admin::payments', compact('orders', 'totalPending', 'totalPendingCollection', 'totalCanceled', 'totalProcessed'));
    }

    /** Reusable metric counter with fallback logic */
    private function countByPaymentStatus(string $status): int
    {
        return Order::where(function ($q) use ($status) {
            $q->whereHas('payment', fn($q2) => $q2->where('payment_status', $status))
                ->orWhere(fn($q3) => $q3->doesntHave('payment')->where('payment_status', $status));
        })->count();
    }

    /**
     * Get actual payment status (considering fallback)
     */
    private function getPaymentStatus($order)
    {
        return $order->payment ? $order->payment->payment_status : $order->payment_status;
    }

    /**
     * Get actual payment method (considering fallback)
     */
    private function getPaymentMethod($order)
    {
        return $order->payment ? $order->payment->payment_method : $order->payment_method;
    }

    /**
     * Set actual payment status (syncing both if needed)
     */
    private function setPaymentStatus($order, $status)
    {
        $order->payment_status = $status;
        $order->save();

        if ($order->payment) {
            $order->payment->payment_status = $status;
            $order->payment->save();
        } else {
            // Keep in sync by creating a record if needed
            $order->payment()->create([
                'payment_method' => $order->payment_method,
                'payment_status' => $status,
                'total_amount' => $order->total,
                'currency_type' => 'YER',
            ]);
        }
    }

    /**
     * Approve the payment.
     */
    public function approve(Request $request, $id)
    {
        $order = Order::with(['payment', 'user'])->findOrFail($id);

        if ($this->getPaymentStatus($order) !== 'pending_verification') {
            return response()->json(['success' => false, 'message' => 'Payment is already processed.'], 400);
        }

        $this->setPaymentStatus($order, 'completed');
        $order->status = 'pending_driver_acceptance';
        $order->save();

        event(new OrderBroadcasted($order));

        // Trigger Notification for Payment Acceptance (FCM + Database)
        try {
            $order->user->notify(new OrderAcceptedNotification($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Acceptance Notification failed for order #{$order->id}: " . $e->getMessage());
        }

        return response()->json(['success' => true, 'message' => 'Payment approved successfully.']);
    }

    /**
     * Reject the payment.
     */
    public function reject(Request $request, $id)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $order = Order::with('payment')->findOrFail($id);

        if ($this->getPaymentStatus($order) !== 'pending_verification') {
            return response()->json(['success' => false, 'message' => 'Payment is already processed.'], 400);
        }

        $this->setPaymentStatus($order, 'rejected');
        $order->status = 'canceled';
        $order->rejection_reason = $request->rejection_reason;
        $order->cancellation_reason = 'Payment Rejected: ' . $request->rejection_reason;
        $order->save();

        // Notify User
        try {
            $user = $order->user;
            $fcmToken = $user->fcm_token ?? ($user->profile->fcm_token ?? 'No Token Found');
            \Illuminate\Support\Facades\Log::info('FCM Token found for rejection: ' . $fcmToken);

            $user->notify(new \App\Notifications\OrderRejectedNotification($order, $request->rejection_reason));
            \Illuminate\Support\Facades\Log::info("OrderRejectedNotification successfully dispatched for order #{$order->id}");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Notification failed for order #{$order->id}: " . $e->getMessage() . " in file: " . $e->getFile() . " on line: " . $e->getLine());
        }

        return response()->json(['success' => true, 'message' => 'Payment rejected successfully.']);
    }

    /**
     * Cancel the order.
     */
    public function cancel(Request $request, $id)
    {
        $request->validate(['cancellation_reason' => 'required|string|max:500']);

        $order = Order::with('payment')->findOrFail($id);

        $order->status = 'canceled';
        $order->cancellation_reason = $request->cancellation_reason;
        $order->save();

        $method = $this->getPaymentMethod($order);
        if ($method === 'cod') {
            $this->setPaymentStatus($order, 'canceled');
        } else {
            $this->setPaymentStatus($order, 'pending_refund');
        }

        return response()->json(['success' => true, 'message' => 'Order canceled successfully.']);
    }

    /**
     * Mark as Refunded
     */
    public function markAsRefunded(Request $request, $id)
    {
        $order = Order::with('payment')->findOrFail($id);

        if ($this->getPaymentStatus($order) !== 'pending_refund') {
            return response()->json(['success' => false, 'message' => 'Order is not pending refund.'], 400);
        }

        $this->setPaymentStatus($order, 'refunded');

        return response()->json(['success' => true, 'message' => 'Order marked as refunded.']);
    }
}
