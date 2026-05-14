<?php

namespace Modules\Restaurants\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Restaurants\Models\Restaurant;
use Modules\Restaurants\Http\Resources\RestaurantResource;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $restaurants = Restaurant::with(['meals', 'offers'])->get();

        return RestaurantResource::collection($restaurants);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('restaurants::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        // 1. جلب المطعم مع الأقسام ومع الوجبات داخل كل قسم بطلب واحد
        $restaurant = Restaurant::with(['meal_categories.meals'])->findOrFail($id);

        // 2. تحويل الكائن إلى مصفوفة للتأكد من المسميات
        $data = $restaurant->toArray();

        // 3. تأمين وصول الوجبات حتى لو كانت خارج الأقسام (خطة بديلة للفلاتر)
        // نجمع كل الوجبات من كل الأقسام في قائمة واحدة تسمى 'meals'
        $allMeals = [];
        foreach ($restaurant->meal_categories as $category) {
            foreach ($category->meals as $meal) {
                $allMeals[] = $meal;
            }
        }
        $data['meals'] = $allMeals;

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('restaurants::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
    }

    /**
     * Update the restaurant's open/closed status.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    // تحديث حالة المطعم مفتوح - مغلق
    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|string|in:open,closed',
        ]);

        $restaurant = $request->user()->restaurant;

        if (!$restaurant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Restaurant not found for this user.'
            ], 404);
        }

        $restaurant->update([
            'status' => $request->status
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Restaurant status updated successfully.',
            'restaurant_status' => $restaurant->status
        ]);
    }

    /**
     * تحديث موقع المطعم (Latitude & Longitude)
     */
    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $restaurant = $request->user()->restaurant;

        if (!$restaurant) {
            return response()->json([
                'status' => 'error',
                'message' => 'المطعم غير موجود لهذا المستخدم.'
            ], 404);
        }

        $restaurant->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث موقع المطعم بنجاح',
            'latitude' => $restaurant->latitude,
            'longitude' => $restaurant->longitude,
        ]);
    }
}
