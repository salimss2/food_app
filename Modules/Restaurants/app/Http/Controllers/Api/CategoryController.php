<?php

namespace Modules\Restaurants\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Restaurants\Models\MealCategory;

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

        $data = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'image_url' => $category->image ? asset('storage/' . $category->image) : asset('assets/default-category.png'),
            ];
        });

        ob_clean();
        return response()->json([
            'status' => true,
            'data' => $data,
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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
            $path = Storage::disk('public')->put('restaurants/categories', $request->file('image'));
            $categoryData['image'] = $path;
        }

        $category = MealCategory::create($categoryData);

        ob_clean();
        return response()->json([
            'status' => true,
            'message' => 'Category created successfully',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'image_url' => $category->image ? asset('storage/' . $category->image) : asset('assets/default-category.png'),
            ],
        ]);
    }
}
