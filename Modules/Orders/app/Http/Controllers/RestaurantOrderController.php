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
     * optionally filtered by tab via ?status={new|in_progress|ready|all}
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Guard: ensure this user has a restaurant.
        if (!$user->restaurant) {
            return response()->json([
                'status' => false,
                'message' => 'No restaurant associated with this account.',
            ], 404);
        }

        $restaurantId = $user->restaurant->id;

        $query = Order::with(['items.meal', 'user'])
            ->where('restaurant_id', $restaurantId)
            ->latest();

        // Apply status filter based on the Flutter tab parameter.
        $statusParam = $request->query('status', 'all');
        $statusFilter = self::STATUS_MAP[$statusParam] ?? null;

        if ($statusFilter !== null) {
            $query->whereIn('status', $statusFilter);
        }

        $orders = $query->get();

        ob_clean();
        return response()->json([
            'status' => true,
            'data' => OrderResource::collection($orders),
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

        $order = Order::with(['items.meal', 'user'])
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
            'data' => new OrderResource($order->load('items.meal', 'user')),
        ]);
    }
}
