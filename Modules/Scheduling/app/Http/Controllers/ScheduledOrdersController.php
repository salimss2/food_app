<?php

namespace Modules\Scheduling\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Scheduling\Models\ScheduledOrder;

class ScheduledOrdersController extends Controller
{
    /**
     * GET /api/v1/scheduled-orders
     *
     * Fetch all scheduled orders for the authenticated user.
     */
    public function index()
    {
        $orders = ScheduledOrder::where('user_id', Auth::id())
            ->orderBy('scheduled_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'orders'  => $orders,
        ], 200);
    }

    /**
     * DELETE /api/v1/scheduled-orders/{id}
     *
     * Cancel/Delete a scheduled order.
     */
    public function destroy($id)
    {
        $order = ScheduledOrder::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الطلب المجدول بنجاح',
        ], 200);
    }
}
