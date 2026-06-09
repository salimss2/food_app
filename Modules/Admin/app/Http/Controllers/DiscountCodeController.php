<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Models\DiscountCode;

class DiscountCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $discountCodes = DiscountCode::latest()->paginate(10);

        $activeCodesCount = DiscountCode::where('is_active', true)->where('expiry_date', '>=', now())->count();
        $totalRedemptions = DiscountCode::sum('used_count');
        $totalCodesCount = DiscountCode::count();

        return view('admin::discounts', compact('discountCodes', 'activeCodesCount', 'totalRedemptions', 'totalCodesCount'));
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
            'expiry_date' => 'required|date',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_usages' => 'nullable|integer|min:1',
        ]);

        DiscountCode::create([
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'expiry_date' => $request->expiry_date,
            'min_order_amount' => $request->min_order_amount ?? 0,
            'max_usages' => $request->max_usages ?? 100,
            'used_count' => 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.discount-codes.index')->with('success', 'تم إضافة كود الخصم بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $code = DiscountCode::findOrFail($id);
        $code->delete();

        return redirect()->route('admin.discount-codes.index')->with('success', 'تم حذف كود الخصم بنجاح.');
    }
}
