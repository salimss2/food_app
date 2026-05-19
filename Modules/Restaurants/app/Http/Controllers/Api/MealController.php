<?php

namespace Modules\Restaurants\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Restaurants\Models\Meal;
use Modules\Restaurants\Http\Resources\MealResource;

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

        $meals = $query->with(['category', 'variants'])->latest()->get();

        ob_clean();
        return response()->json([
            'status' => true,
            'data' => MealResource::collection($meals),
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            ob_clean();
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $mealData = $request->only(['name', 'description', 'price', 'discount_price', 'meal_category_id']);
        $mealData['restaurant_id'] = $user->restaurant->id;
        $mealData['available'] = true;

        $file = $request->file('image') ?? $request->file('logo');
        if ($file) {
            $disk = config('filesystems.default') ?: 's3';
            $filename = time() . '_' . $file->getClientOriginalName();

            // Store in the meals directory within the Cloud disk
            $file->storeAs('restaurants/meals', $filename, $disk);

            // Save the full path to the database
            $mealData['image'] = 'restaurants/meals/' . $filename;
        }

        $meal = Meal::create($mealData);

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Meal created successfully',
            'data' => new MealResource($meal->load('category')),
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            ob_clean();
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $mealData = $request->only(['name', 'description', 'price', 'discount_price', 'meal_category_id']);

        $file = $request->file('image') ?? $request->file('logo');
        if ($file) {
            $disk = config('filesystems.default') ?: 's3';
            // Delete the old image if it exists
            if ($meal->image) {
                \Illuminate\Support\Facades\Storage::disk($disk)->delete($meal->image);
            }

            $filename = time() . '_' . $file->getClientOriginalName();

            // Store the new image
            $file->storeAs('restaurants/meals', $filename, $disk);

            // Update the path in the database
            $mealData['image'] = 'restaurants/meals/' . $filename;
        }

        $meal->update($mealData);

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Meal updated successfully',
            'data' => new MealResource($meal->load('category')),
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
            'available' => (bool) $meal->available,
        ]);
    }

    /**
     * Remove the specified meal.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $meal = Meal::where('restaurant_id', $user->restaurant->id)->findOrFail($id);

        $disk = config('filesystems.default') ?: 's3';
        if ($meal->image && Storage::disk($disk)->exists($meal->image)) {
            Storage::disk($disk)->delete($meal->image);
        }

        $meal->delete();

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Meal deleted successfully',
        ]);
    }

    /**
     * Set/update individual meal discount.
     */
    public function updateDiscount(Request $request, $mealId)
    {
        $user = Auth::user();
        if (!$user->restaurant) {
            ob_clean();
            return response()->json(['status' => false, 'message' => 'No restaurant found'], 404);
        }

        $meal = Meal::where('restaurant_id', $user->restaurant->id)->findOrFail($mealId);

        $validator = Validator::make($request->all(), [
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
        ]);

        if ($validator->fails()) {
            ob_clean();
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $meal->update([
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'discount_start' => $request->discount_start,
            'discount_end' => $request->discount_end,
        ]);

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Meal discount updated successfully',
            'data' => new MealResource($meal->load('category')),
        ]);
    }
}
