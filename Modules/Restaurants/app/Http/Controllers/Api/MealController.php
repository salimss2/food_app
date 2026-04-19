<?php

namespace Modules\Restaurants\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Restaurants\Models\Meal;

class MealController extends Controller
{
    /**
     * Display a listing of meals, optionally filtered by category.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->restaurant) {
            ob_clean();
            return response()->json(['status' => false, 'message' => 'No restaurant found'], 404);
        }

        $query = Meal::where('restaurant_id', $user->restaurant->id);

        if ($request->has('category_id') && $request->category_id != 0) {
            $query->where('meal_category_id', $request->category_id);
        }

        $meals = $query->with('category')->latest()->get();

        ob_clean();
        return response()->json([
            'status' => true,
            'data' => $meals,
        ]);
    }

    /**
     * Store a newly created meal.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->restaurant) {
            ob_clean();
            return response()->json(['status' => false, 'message' => 'No restaurant found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'meal_category_id' => 'required|exists:meal_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            ob_clean();
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $mealData = $request->only(['name', 'description', 'price', 'discount_price', 'meal_category_id']);
        $mealData['restaurant_id'] = $user->restaurant->id;
        $mealData['available'] = true;

        if ($request->hasFile('image')) {
            $path = Storage::disk('public')->put('restaurants/meals', $request->file('image'));
            $mealData['image'] = $path;
        }

        $meal = Meal::create($mealData);

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Meal created successfully',
            'data' => $meal->load('category'),
        ]);
    }

    /**
     * Update the specified meal.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $meal = Meal::where('restaurant_id', $user->restaurant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'meal_category_id' => 'required|exists:meal_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            ob_clean();
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $mealData = $request->only(['name', 'description', 'price', 'discount_price', 'meal_category_id']);

        if ($request->hasFile('image')) {
            if ($meal->image && Storage::disk('public')->exists($meal->image)) {
                Storage::disk('public')->delete($meal->image);
            }
            $path = Storage::disk('public')->put('restaurants/meals', $request->file('image'));
            $mealData['image'] = $path;
        }

        $meal->update($mealData);

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Meal updated successfully',
            'data' => $meal->load('category'),
        ]);
    }

    /**
     * Toggle meal availability.
     */
    public function toggleAvailability($id)
    {
        $user = Auth::user();
        $meal = Meal::where('restaurant_id', $user->restaurant->id)->findOrFail($id);

        $meal->available = !$meal->available;
        $meal->save();

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Availability updated',
            'available' => (bool)$meal->available,
        ]);
    }

    /**
     * Remove the specified meal.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $meal = Meal::where('restaurant_id', $user->restaurant->id)->findOrFail($id);

        if ($meal->image && Storage::disk('public')->exists($meal->image)) {
            Storage::disk('public')->delete($meal->image);
        }

        $meal->delete();

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Meal deleted successfully',
        ]);
    }
}
