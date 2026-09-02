<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Orders\Models\Order;

class RatingController extends Controller
{
    /**
     * Submit a review for a delivered order.
     * POST /api/v1/ratings
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'meals_rating' => 'required|integer|between:1,5',
            'restaurant_rating' => 'required|integer|between:1,5',
            'driver_rating' => 'nullable|integer|between:1,5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات التقييم غير صالحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $order = Order::find($validated['order_id']);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'الطلب غير موجود'
            ], 444);
        }

        // 1. Must verify order.user_id === auth()->id()
        if ((int)$order->user_id !== (int)auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح لك بتقييم هذا الطلب'
            ], 403);
        }

        // 2. Must verify order.status === 'delivered'
        if (strtolower($order->status) !== 'delivered') {
            return response()->json([
                'status' => 'error',
                'message' => 'يمكنك تقييم الطلبات فقط بعد اكتمال التوصيل'
            ], 422);
        }

        // 3. Must verify order hasn't already been rated
        $existingRating = OrderRating::where('order_id', $order->id)->first();
        if ($existingRating) {
            return response()->json([
                'status' => 'error',
                'message' => 'لقد قمت بتقييم هذا الطلب من قبل'
            ], 409);
        }

        // 4. Create rating record
        $rating = OrderRating::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'restaurant_id' => $order->restaurant_id,
            'driver_id' => $order->driver_id,
            'meals_rating' => $validated['meals_rating'],
            'driver_rating' => $validated['driver_rating'] ?? null,
            'restaurant_rating' => $validated['restaurant_rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تقديم التقييم بنجاح',
            'data' => $rating
        ], 201);
    }
}
