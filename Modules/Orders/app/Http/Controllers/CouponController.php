<?php

namespace Modules\Orders\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Orders\Models\Coupon;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CouponController extends Controller
{
    /**
     * Validate and apply a coupon code to the cart.
     *
     * POST /api/v1/coupons/apply
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apply(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'cart_total' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        // 2. Fetch the coupon code and check if active
        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon || !$coupon->status) {
            return response()->json([
                'success' => false,
                'message' => 'كود الخصم غير صحيح أو غير مفعل'
            ], 400);
        }

        // 3. Check expiry_date
        $today = Carbon::today();
        if ($coupon->expires_at && $today->greaterThan(Carbon::parse($coupon->expires_at)->startOfDay())) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، انتهت صلاحية هذا الكود'
            ], 400);
        }

        // 4. Mathematical Calculation (if cart_total is provided)
        $discountAmount = 0.00;
        $newTotal = 0.00;
        $cartTotal = $request->filled('cart_total') ? (float) $request->cart_total : null;

        if ($cartTotal !== null) {
            if ($coupon->type === 'fixed') {
                $discountAmount = (float) $coupon->discount;
            } elseif ($coupon->type === 'percent') {
                $discountAmount = ($cartTotal * (float) $coupon->discount) / 100;
            }

            // Ensure the discount amount does not exceed the cart total
            if ($discountAmount > $cartTotal) {
                $discountAmount = $cartTotal;
            }

            $newTotal = $cartTotal - $discountAmount;
        }

        // 5. Success Response
        return response()->json([
            'success' => true,
            'code' => $coupon->code,
            'type' => $coupon->type,
            'discount' => (float) $coupon->discount,
            'discount_amount' => $cartTotal !== null ? (float) round($discountAmount, 2) : null,
            'new_total' => $cartTotal !== null ? (float) round($newTotal, 2) : null,
        ], 200);
    }
}
