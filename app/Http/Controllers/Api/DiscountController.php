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
     * Validate and apply a coupon code for customer checkout.
     * Supports both POST /api/v1/coupons/validate and POST /api/v1/discount/apply
     */
    public function validateCoupon(Request $request)
    {
        // 1. Input Validation
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'cart_total' => 'nullable|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'restaurant_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $codeStr = strtoupper(trim($request->code));
        $cartTotal = (float) ($request->cart_total ?? $request->subtotal ?? 0);

        // 2. Fetch Discount Code
        $discountCode = DiscountCode::where('code', $codeStr)->first();

        if (!$discountCode) {
            return response()->json([
                'success' => false,
                'message' => 'كود الخصم المدخل غير موجود'
            ], 404);
        }

        // 3. Status Check
        if (!$discountCode->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'كود الخصم غير مفعل حالياً'
            ], 400);
        }

        // 4. Expiry Date Check
        $today = Carbon::today();
        if ($discountCode->expiry_date && $today->greaterThan(Carbon::parse($discountCode->expiry_date)->startOfDay())) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، انتهت صلاحية كود الخصم هذا'
            ], 400);
        }

        // 5. Global Usage Limit Check
        if ($discountCode->max_usages > 0 && $discountCode->used_count >= $discountCode->max_usages) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، تم استخدام هذا الكود بالكامل واستنفاذ الحد الأقصى'
            ], 400);
        }

        // 6. Minimum Order Amount Check
        $minOrder = (float) $discountCode->min_order_amount;
        if ($minOrder > 0 && $cartTotal < $minOrder) {
            return response()->json([
                'success' => false,
                'message' => "يجب أن تكون قيمة الطلب {$minOrder} YER أو أكثر لاستخدام كود الخصم"
            ], 400);
        }

        // 7. Restaurant Scope Check (if scope restricted to a specific restaurant)
        if ($discountCode->restaurant_id && $request->filled('restaurant_id')) {
            if ((int)$discountCode->restaurant_id !== (int)$request->restaurant_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'كود الخصم مخصص لمطعم محدد ولا ينطبق على هذا الطلب'
                ], 400);
            }
        }

        // 8. Calculate Discount Amount & Max Cap
        $discountValue = (float) $discountCode->discount_value;
        $discountAmount = 0.00;

        if ($discountCode->discount_type === 'fixed') {
            $discountAmount = $discountValue;
        } elseif ($discountCode->discount_type === 'percentage') {
            $discountAmount = ($cartTotal * $discountValue) / 100;
            // Apply max discount cap if defined
            if ($discountCode->max_discount_amount && (float)$discountCode->max_discount_amount > 0) {
                $discountAmount = min($discountAmount, (float)$discountCode->max_discount_amount);
            }
        }

        if ($cartTotal > 0) {
            $discountAmount = min($discountAmount, $cartTotal);
        }

        $newTotal = max(0, $cartTotal - $discountAmount);

        return response()->json([
            'success' => true,
            'code' => $discountCode->code,
            'discount_type' => $discountCode->discount_type,
            'discount_value' => $discountValue,
            'discount_amount' => (float) round($discountAmount, 2),
            'new_total' => (float) round($newTotal, 2),
            'min_order_amount' => (float) $discountCode->min_order_amount,
            'max_discount_amount' => $discountCode->max_discount_amount ? (float)$discountCode->max_discount_amount : null,
            'message' => 'تم تطبيق كود الخصم بنجاح'
        ], 200);
    }

    /**
     * Alias for apply method
     */
    public function apply(Request $request)
    {
        return $this->validateCoupon($request);
    }
}
