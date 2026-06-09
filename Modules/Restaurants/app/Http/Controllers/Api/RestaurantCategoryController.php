<?php

namespace Modules\Restaurants\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Restaurants\Models\Category;

class RestaurantCategoryController extends Controller
{
    /**
     * Display a listing of all global categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // جلب جميع الأقسام من جدول categories
        $categories = \Illuminate\Support\Facades\DB::table('categories')->get();

        return response()->json([
            'status' => true,
            'data' => $categories
        ], 200);
    }
}
