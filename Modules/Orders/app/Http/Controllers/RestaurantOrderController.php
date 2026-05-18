<?php

namespace Modules\Orders\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Orders\Models\Order;
use Modules\Orders\Http\Resources\OrderResource;

/**
 * RestaurantOrderController
 *
 * Handles all order operations scoped to the authenticated restaurant owner.
 * The owner can only view/manage orders that belong to their own restaurant.
 *
 * Routes (all protected by auth:sanctum):
 *   GET  /api/v1/restaurant/orders            — list with ?status= filter
 *   GET  /api/v1/restaurant/orders/{id}       — single order details
 *   PATCH /api/v1/restaurant/orders/{id}/status — update order status
 */
class RestaurantOrderController extends Controller
{
    /**
     * Allowed status filter values that match the Flutter tab names.
     */
    private const STATUS_MAP = [
        'new' => ['pending', 'pending_driver_acceptance'],
        'in_progress' => ['preparing', 'driver_assigned', 'picked_up'],
        'ready' => ['delivered'],
        'all' => null, // No filter applied
    ];

    /**
     * GET /api/v1/restaurant/orders
     *
     * Returns orders for the authenticated owner's restaurant,
     * optionally filtered by tab via ?status={pending|processing|ready|delivered|cancelled|all}
     *
     * CRITICAL LOGIC: If status=pending, ONLY return orders where scheduled_at is NULL
     * or scheduled_at is within the next 30 minutes.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user->restaurant) {
            return response()->json([
                'status' => false,
                'message' => 'No restaurant associated with this account.',
            ], 404);
        }

        $restaurantId = $user->restaurant->id;
        $statusParam = $request->query('status', 'all');

        $query = Order::with(['items.meal', 'items.offer', 'user'])
            ->where('restaurant_id', $restaurantId)
            ->latest();

        if ($statusParam === 'pending') {
            $query->where('status', 'pending')
                ->where(function ($q) {
                    $q->whereNull('scheduled_at')
                        ->orWhere('scheduled_at', '<=', now()->addMinutes(30));
                });
        } elseif ($statusParam !== 'all') {
            $query->where('status', $statusParam);
        }

        $orders = $query->get();

        return response()->json([
            'status' => true,
            'data' => OrderResource::collection($orders),
        ]);
    }

    /**
     * GET /api/v1/restaurant/orders/scheduled
     *
     * Fetch ALL future scheduled orders where scheduled_at is strictly
     * greater than 30 mins from now.
     *
     * @return JsonResponse
     */
    public function getScheduledOrders(): JsonResponse
    {
        $user = Auth::user();

        if (!$user->restaurant) {
            return response()->json([
                'status' => false,
                'message' => 'No restaurant associated with this account.',
            ], 404);
        }

        $orders = Order::with(['items.meal', 'items.offer', 'user'])
            ->where('restaurant_id', $user->restaurant->id)
            ->where('scheduled_at', '>', now()->addMinutes(30))
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => OrderResource::collection($orders),
        ]);
    }

    /**
     * GET /api/v1/restaurant/scheduled-orders
     *
     * Fetch all scheduled orders from the new scheduled_orders table with status 'scheduled'.
     *
     * @return JsonResponse
     */
    public function getRestaurantScheduledOrders(): JsonResponse
    {
        $user = Auth::user();

        if (!$user->restaurant) {
            return response()->json([
                'status' => false,
                'message' => 'No restaurant associated with this account.',
            ], 404);
        }

        $orders = \Modules\Scheduling\Models\ScheduledOrder::with(['user'])
            ->where('restaurant_id', $user->restaurant->id)
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'success' => true,
            'data' => $orders,
            'orders' => $orders,
        ]);
    }


    /**
     * GET /api/v1/restaurant/orders/{id}
     *
     * Returns full details for a single order, ensuring it belongs
     * to the authenticated owner's restaurant.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user->restaurant) {
            return response()->json([
                'status' => false,
                'message' => 'No restaurant associated with this account.',
            ], 404);
        }

        $order = Order::with(['items.meal', 'items.offer', 'user'])
            ->where('restaurant_id', $user->restaurant->id)
            ->findOrFail($id);

        ob_clean();
        return response()->json([
            'status' => true,
            'data' => new OrderResource($order),
        ]);
    }

    /**
     * PATCH /api/v1/restaurant/orders/{id}/status
     *
     * Allows the restaurant owner to update the status of an order
     * (e.g., from "pending" to "preparing" when they accept it).
     *
     * Allowed transitions:
     *   pending / pending_driver_acceptance → preparing
     *   preparing                           → ready (custom status, if needed)
     *
     * @param  Request  $request
     * @param  int      $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user->restaurant) {
            return response()->json([
                'status' => false,
                'message' => 'No restaurant associated with this account.',
            ], 404);
        }

        $request->validate([
            'status' => ['required', 'string', 'in:preparing,ready,canceled'],
        ]);

        $order = Order::where('restaurant_id', $user->restaurant->id)
            ->findOrFail($id);

        $order->update(['status' => $request->status]);

        Log::info("Restaurant owner updated order status", [
            'owner_id' => $user->id,
            'order_id' => $order->id,
            'new_status' => $request->status,
        ]);

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Order status updated successfully.',
            'data' => new OrderResource($order->load('items.meal', 'items.offer', 'user')),
        ]);
    }
}
