<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Models\DiscountCode;
use App\Models\Restaurant;

class DiscountCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $discountCodes = DiscountCode::with('restaurant')->latest()->paginate(10);
        $activeCodesCount = DiscountCode::where('is_active', true)->count();
        $totalRedemptions = (int) DiscountCode::sum('used_count');
        $totalCodesCount = DiscountCode::count();
        $restaurants = Restaurant::all();

        return view('admin::discounts', compact(
            'discountCodes',
            'activeCodesCount',
            'totalRedemptions',
            'totalCodesCount',
            'restaurants'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:discount_codes,code|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'restaurant_id' => 'nullable',
            'expiry_date' => 'required|date',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_usages' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'is_active' => 'nullable',
        ]);

        $restaurantId = ($request->restaurant_id === 'all' || empty($request->restaurant_id)) ? null : $request->restaurant_id;

        DiscountCode::create([
            'code' => strtoupper(trim($request->code)),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'max_discount_amount' => $request->max_discount_amount,
            'restaurant_id' => $restaurantId,
            'expiry_date' => $request->expiry_date,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_usages' => $request->max_usages ?? 100,
            'per_user_limit' => $request->per_user_limit ?? 1,
            'used_count' => 0,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->route('admin.discounts.index')->with('success', 'تم إضافة كود الخصم بنجاح.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $code = DiscountCode::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:255|unique:discount_codes,code,' . $id,
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'restaurant_id' => 'nullable',
            'expiry_date' => 'required|date',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_usages' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'is_active' => 'nullable',
        ]);

        $restaurantId = ($request->restaurant_id === 'all' || empty($request->restaurant_id)) ? null : $request->restaurant_id;

        $code->update([
            'code' => strtoupper(trim($request->code)),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'max_discount_amount' => $request->max_discount_amount,
            'restaurant_id' => $restaurantId,
            'expiry_date' => $request->expiry_date,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_usages' => $request->max_usages ?? $code->max_usages,
            'per_user_limit' => $request->per_user_limit ?? $code->per_user_limit,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : $code->is_active,
        ]);

        return redirect()->route('admin.discounts.index')->with('success', 'تم تحديث كود الخصم بنجاح.');
    }

    /**
     * Toggle active status via AJAX.
     */
    public function toggleStatus($id)
    {
        $code = DiscountCode::findOrFail($id);
        $code->is_active = !$code->is_active;
        $code->save();

        return response()->json([
            'status' => true,
            'message' => 'تم تغيير حالة كود الخصم بنجاح.',
            'is_active' => $code->is_active
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $code = DiscountCode::findOrFail($id);
        $code->delete();

        if (request()->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'تم حذف كود الخصم بنجاح.']);
        }

        return redirect()->route('admin.discounts.index')->with('success', 'تم حذف كود الخصم بنجاح.');
    }
}
