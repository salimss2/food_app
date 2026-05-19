<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Restaurants\Models\Restaurant;
use Modules\Restaurants\Models\Meal;

class SearchController extends Controller
{
    /**
     * Unified search for restaurants and meals.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1',
            'type' => 'required|string|in:restaurants,meals',
        ]);

        $query = $request->input('query');
        $type = $request->input('type');

        if ($type === 'restaurants') {
            $restaurants = Restaurant::where('name', 'LIKE', "%{$query}%")
                ->where('account_status', 'active') // Assuming we only want active restaurants
                ->get()
                ->map(function ($restaurant) {
                    return [
                        'id' => $restaurant->id,
                        'name' => $restaurant->name,
                        'logo' => $restaurant->logo,
                        'logo_url' => $restaurant->logo,
                        'location' => $restaurant->location,
                        'is_open' => $restaurant->is_open,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $restaurants,
            ]);
        }

        if ($type === 'meals') {
            $meals = Meal::whereHas('restaurant', function ($query) {
                $query->where('account_status', 'active');
            })
                ->with('restaurant')
                ->where('name', 'LIKE', "%{$query}%")
                ->where('available', true)
                ->get();

            $grouped = $meals->groupBy('restaurant_id')->map(function ($restaurantMeals) {
                $restaurant = $restaurantMeals->first()->restaurant;

                if (!$restaurant) {
                    return null;
                }

                return [
                    'restaurant' => [
                        'id' => $restaurant->id,
                        'name' => $restaurant->name,
                        'logo' => $restaurant->logo,
                        'logo_url' => $restaurant->logo,
                    ],
                    'products' => $restaurantMeals->map(function ($meal) {
                        return [
                            'id' => $meal->id,
                            'name' => $meal->name,
                            'price' => $meal->price,
                            'image' => $meal->image,
                            'image_url' => $meal->image,
                        ];
                    })->values(),
                ];
            })->filter()->values();

            return response()->json([
                'success' => true,
                'data' => $grouped,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid search type.',
        ], 400);
    }
}
