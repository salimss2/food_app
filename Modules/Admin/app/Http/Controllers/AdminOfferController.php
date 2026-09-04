<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Models\AdminOffer;
use App\Models\Restaurant;
use Modules\Restaurants\Models\Meal;
use Modules\Restaurants\Models\Offer as RestaurantCombo;

class AdminOfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offers = AdminOffer::with(['restaurant', 'meal'])->latest()->paginate(10, ['*'], 'offers_page');
        
        $restaurantCombos = RestaurantCombo::with('restaurant')->latest()->paginate(10, ['*'], 'combos_page');
        
        // Fetch active restaurants and meals for modal selects
        $restaurants = Restaurant::all();
        $meals = Meal::select('id', 'name', 'price', 'restaurant_id')->get();
        
        // Dynamic KPIs
        $liveOffersCount = AdminOffer::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()->toDateString());
            })
            ->count();

        $totalOffersCount = AdminOffer::count() + RestaurantCombo::count();

        $expiredOffersCount = AdminOffer::where('status', 'inactive')
            ->orWhere(function ($q) {
                $q->whereNotNull('expiry_date')->where('expiry_date', '<', now()->toDateString());
            })
            ->count();

        return view('admin::offers', compact(
            'offers',
            'restaurants',
            'meals',
            'liveOffersCount',
            'totalOffersCount',
            'expiredOffersCount',
            'restaurantCombos'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'nullable|in:banner,direct_cart',
            'click_action' => 'nullable|in:cart,restaurant,coupon',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'original_price' => 'nullable|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'restaurant_id' => 'nullable',
            'meal_id' => 'nullable',
            'banner_image' => 'nullable|image|max:3072',
            'status' => 'nullable|in:active,inactive',
        ]);

        $expiryDate = $request->expiry_date ?? $request->end_date ?? now()->addDays(30)->toDateString();
        $startDate = $request->start_date ?? now()->toDateString();

        $restaurantId = ($request->restaurant_id === 'all' || empty($request->restaurant_id)) ? null : $request->restaurant_id;
        if (empty($restaurantId) && !empty($request->meal_id)) {
            $restaurantId = Meal::find($request->meal_id)?->restaurant_id;
        }

        $originalPrice = $request->original_price ?? 0;
        $discount = $request->discount_percentage ?? 0;
        $offerPrice = $request->offer_price ?? ($originalPrice > 0 ? ($originalPrice - ($originalPrice * ($discount / 100))) : 0);

        $imagePath = null;
        if ($request->hasFile('banner_image')) {
            $imagePath = $request->file('banner_image')->store('offers', 'public');
        }

        AdminOffer::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type ?? 'banner',
            'click_action' => $request->click_action ?? 'restaurant',
            'banner_image' => $imagePath,
            'discount_percentage' => $discount,
            'original_price' => $originalPrice,
            'offer_price' => $offerPrice,
            'restaurant_id' => $restaurantId,
            'meal_id' => $request->meal_id,
            'start_date' => $startDate,
            'expiry_date' => $expiryDate,
            'status' => $request->has('status') ? $request->status : ($request->has('is_active') ? 'active' : 'active'),
        ]);

        return redirect()->route('admin.offers.index')->with('success', 'تم إضافة العرض الترويجي بنجاح.');
    }

    /**
     * Update specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $offer = AdminOffer::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'nullable|in:banner,direct_cart',
            'click_action' => 'nullable|in:cart,restaurant,coupon',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'original_price' => 'nullable|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'restaurant_id' => 'nullable',
            'meal_id' => 'nullable',
            'banner_image' => 'nullable|image|max:3072',
            'status' => 'nullable|in:active,inactive',
        ]);

        $expiryDate = $request->expiry_date ?? $request->end_date ?? $offer->expiry_date ?? now()->addDays(30)->toDateString();
        $startDate = $request->start_date ?? $offer->start_date ?? now()->toDateString();

        $restaurantId = ($request->restaurant_id === 'all' || empty($request->restaurant_id)) ? null : $request->restaurant_id;
        if (empty($restaurantId) && !empty($request->meal_id)) {
            $restaurantId = Meal::find($request->meal_id)?->restaurant_id;
        }

        $originalPrice = $request->original_price ?? $offer->original_price ?? 0;
        $discount = $request->discount_percentage ?? $offer->discount_percentage ?? 0;
        $offerPrice = $request->offer_price ?? ($originalPrice > 0 ? ($originalPrice - ($originalPrice * ($discount / 100))) : 0);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type ?? $offer->type,
            'click_action' => $request->click_action ?? $offer->click_action,
            'discount_percentage' => $discount,
            'original_price' => $originalPrice,
            'offer_price' => $offerPrice,
            'restaurant_id' => $restaurantId,
            'meal_id' => $request->meal_id,
            'start_date' => $startDate,
            'expiry_date' => $expiryDate,
            'status' => $request->status ?? $offer->status,
        ];

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('offers', 'public');
        }

        $offer->update($data);

        return redirect()->route('admin.offers.index')->with('success', 'تم تحديث العرض الترويجي بنجاح.');
    }

    /**
     * AJAX Toggle Status for Admin Offer
     */
    public function toggleStatus($id)
    {
        $offer = AdminOffer::findOrFail($id);
        $newStatus = $offer->status === 'active' ? 'inactive' : 'active';
        $offer->update(['status' => $newStatus]);

        return response()->json([
            'status' => true,
            'message' => 'تم تغيير حالة العرض الترويجي بنجاح.',
            'new_status' => $newStatus
        ]);
    }

    /**
     * Remove specified offer.
     */
    public function destroy($id)
    {
        $offer = AdminOffer::findOrFail($id);
        $offer->delete();

        if (request()->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'تم حذف العرض بنجاح.']);
        }

        return redirect()->route('admin.offers.index')->with('success', 'تم حذف العرض بنجاح.');
    }

    /**
     * AJAX Toggle Status for Restaurant Combo
     */
    public function toggleComboStatus($id)
    {
        $combo = RestaurantCombo::findOrFail($id);
        $newStatus = ($combo->status ?? 'active') === 'active' ? 'inactive' : 'active';
        if (\Illuminate\Support\Facades\Schema::hasColumn('offers', 'status')) {
            $combo->update(['status' => $newStatus]);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم تغيير حالة توفر الوجبة المجمعة بنجاح.',
            'new_status' => $newStatus
        ]);
    }

    /**
     * Delete Restaurant Combo
     */
    public function destroyCombo($id)
    {
        $combo = RestaurantCombo::findOrFail($id);
        $combo->delete();

        if (request()->wantsJson()) {
            return response()->json(['status' => true, 'message' => 'تم حذف الوجبة المجمعة بنجاح.']);
        }

        return redirect()->route('admin.offers.index')->with('success', 'تم حذف الوجبة المجمعة بنجاح.');
    }
}
