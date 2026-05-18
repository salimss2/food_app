<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Admin\Models\DiscountCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DiscountController extends Controller
{
    /**
     * Validate and apply a discount code to the cart.
     *
     * POST /api/v1/discount/apply
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apply(Request $request)
    {
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'cart_total' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        // 2. Fetch the discount code and check if active
        $discountCode = DiscountCode::where('code', $request->code)->first();

        if (!$discountCode || !$discountCode->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'كود الخصم غير صحيح أو غير مفعل'
            ], 400);
        }

        // 3. Check expiry_date
        $today = Carbon::today();
        // Compare dates directly (ignoring time) using Carbon comparison or format comparison
        if ($today->greaterThan(Carbon::parse($discountCode->expiry_date)->startOfDay())) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، انتهت صلاحية هذا الكود'
            ], 400);
        }

        // 4. Check usage limit
        if ($discountCode->used_count >= $discountCode->max_usages) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، تم الوصول للحد الأقصى لاستخدام هذا الكود'
            ], 400);
        }

        // 5. Check Minimum Order
        $cartTotal = (float) $request->cart_total;
        $minOrderAmount = (float) $discountCode->min_order_amount;

        if ($cartTotal < $minOrderAmount) {
            return response()->json([
                'success' => false,
                'message' => "يجب أن تكون قيمة الطلب أعلى من {$minOrderAmount} لاستخدام هذا الكود"
            ], 400);
        }

        // 6. Mathematical Calculation
        $discountValue = (float) $discountCode->discount_value;
        $discountAmount = 0.00;

        if ($discountCode->discount_type === 'fixed') {
            $discountAmount = $discountValue;
        } elseif ($discountCode->discount_type === 'percentage') {
            $discountAmount = ($cartTotal * $discountValue) / 100;
        }

        // Ensure the discount amount does not exceed the cart total
        if ($discountAmount > $cartTotal) {
            $discountAmount = $cartTotal;
        }

        $newTotal = $cartTotal - $discountAmount;

        // 7. Success Response
        return response()->json([
            'success' => true,
            'discount_amount' => (float) round($discountAmount, 2),
            'new_total' => (float) round($newTotal, 2),
            'code_id' => $discountCode->id
        ], 200);
    }
}
