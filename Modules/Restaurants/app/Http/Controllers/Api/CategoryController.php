<?php

namespace Modules\Restaurants\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Restaurants\Models\MealCategory;
use Modules\Restaurants\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories for the authenticated restaurant.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user->restaurant) {
            ob_clean();
            return response()->json(['status' => false, 'message' => 'No restaurant found'], 404);
        }

        $categories = MealCategory::where('restaurant_id', $user->restaurant->id)->get();

        ob_clean();
        return response()->json([
            'status' => true,
            'data' => CategoryResource::collection($categories),
        ]);
    }

    /**
     * Store a newly created category.
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
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($validator->fails()) {
            ob_clean();
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $categoryData = [
            'restaurant_id' => $user->restaurant->id,
            'name' => $request->name,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $categoryData['image'] = $path;
        }

        $category = MealCategory::create($categoryData);

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category),
        ]);
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $category = MealCategory::where('id', $id)
            ->where('restaurant_id', $user->restaurant->id)
            ->first();

        if (!$category) {
            ob_clean();
            return response()->json(['status' => false, 'message' => 'Category not found or unauthorized'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        if ($validator->fails()) {
            ob_clean();
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $category->name = $request->name;

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $path = $request->file('image')->store('categories', 'public');
            $category->image = $path;
        }

        $category->save();

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully',
            'data' => new CategoryResource($category),
        ]);
    }


    /**
     * Remove the specified category.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $category = MealCategory::where('id', $id)
            ->where('restaurant_id', $user->restaurant->id)
            ->first();

        if (!$category) {
            ob_clean();
            return response()->json(['status' => false, 'message' => 'Category not found or unauthorized'], 404);
        }

        // Optional: Check if category has meals before deleting
        if ($category->meals()->count() > 0) {
            ob_clean();
            return response()->json(['status' => false, 'message' => 'Cannot delete category with associated meals'], 422);
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully'
        ]);
    }
}
