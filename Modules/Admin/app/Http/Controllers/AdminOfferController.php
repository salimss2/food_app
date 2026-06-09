<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Admin\Models\AdminOffer;
use App\Models\Restaurant;

class AdminOfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offers = AdminOffer::with('restaurant')->latest()->paginate(10, ['*'], 'offers_page');
        
        $restaurantCombos = \Modules\Restaurants\Models\Offer::with('restaurant')->latest()->paginate(10, ['*'], 'combos_page');
        
        // Fetch active restaurants
        $restaurants = Restaurant::all();
        
        // Dynamic KPIs
        $liveOffersCount = AdminOffer::where('status', 'active')->where('expiry_date', '>=', now())->count();
        $totalOffersCount = AdminOffer::count();
        $expiredOffersCount = AdminOffer::where('status', 'inactive')->orWhere('expiry_date', '<', now())->count();
        
        return view('admin::offers', compact('offers', 'restaurants', 'liveOffersCount', 'totalOffersCount', 'expiredOffersCount', 'restaurantCombos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'expiry_date' => 'required|date',
            'restaurant_id' => 'nullable', // 'all' handled below
            'status' => 'required|in:active,inactive',
        ]);

        $restaurantId = $request->restaurant_id === 'all' ? null : $request->restaurant_id;

        AdminOffer::create([
            'title' => $request->title,
            'discount_percentage' => $request->discount_percentage,
            'expiry_date' => $request->expiry_date,
            'restaurant_id' => $restaurantId,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.offers.index')->with('success', 'تم إضافة العرض بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $offer = AdminOffer::findOrFail($id);
        $offer->delete();

        return redirect()->route('admin.offers.index')->with('success', 'تم حذف العرض بنجاح.');
    }
}
