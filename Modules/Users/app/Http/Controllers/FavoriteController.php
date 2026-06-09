<?php

namespace Modules\Users\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Users\Models\Favorite;

class FavoriteController extends Controller
{
    /**
     * Fetch all favorites for the authenticated user.
     * Structured as: ['data' => ['restaurants' => [...], 'meals' => [...]]]
     */
    public function index()
    {
        $favorites = auth()->user()->favorites()->with(['meal', 'restaurant'])->get();

        $restaurants = $favorites->whereNotNull('restaurant_id')->pluck('restaurant')->values();
        $meals = $favorites->whereNotNull('meal_id')->pluck('meal')->values();

        return response()->json([
            'status' => 'success',
            'data' => [
                'restaurants' => $restaurants,
                'meals' => $meals
            ]
        ]);
    }

    /**
     * Toggle favorite status for a meal.
     */
    public function toggleMeal(Request $request)
    {
        $request->validate([
            'meal_id' => 'required|exists:meals,id'
        ]);

        $userId = auth()->id();
        $mealId = $request->meal_id;

        $favorite = Favorite::where('user_id', $userId)
            ->where('meal_id', $mealId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Meal removed from favorites',
                'is_favorite' => false
            ], 200);
        }

        Favorite::create([
            'user_id' => $userId,
            'meal_id' => $mealId
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Meal added to favorites',
            'is_favorite' => true
        ], 200);
    }

    /**
     * Toggle favorite status for a restaurant.
     */
    public function toggleRestaurant(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id'
        ]);

        $userId = auth()->id();
        $restaurantId = $request->restaurant_id;

        $favorite = Favorite::where('user_id', $userId)
            ->where('restaurant_id', $restaurantId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Restaurant removed from favorites',
                'is_favorite' => false
            ], 200);
        }

        Favorite::create([
            'user_id' => $userId,
            'restaurant_id' => $restaurantId
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Restaurant added to favorites',
            'is_favorite' => true
        ], 200);
    }
}
