<?php

use Illuminate\Support\Facades\Route;
use Modules\Restaurants\Http\Controllers\Api\ProfileController;
use Modules\Restaurants\Http\Controllers\Api\MealController;
use Modules\Restaurants\Http\Controllers\Api\CategoryController;


use Modules\Restaurants\Http\Controllers\RestaurantController;

use Modules\Restaurants\Http\Controllers\Api\RestaurantCategoryController;


Route::prefix('v1')->group(function () {
    Route::get('/app-categories', function () {
        $categories = \Illuminate\Support\Facades\DB::table('categories')->get();
        return response()->json([
            'status' => true,
            'data' => $categories
        ], 200);
    });
    Route::get('/restaurants', [RestaurantController::class, 'index']);
    Route::get('/restaurants/{id}', [RestaurantController::class, 'show']);
    Route::get('/customer/offers', [\Modules\Restaurants\Http\Controllers\Api\CustomerOfferController::class, 'getActiveOffers']);
    Route::get('/offers', [\Modules\Restaurants\Http\Controllers\Api\OfferController::class, 'index']);
});

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile/update', [ProfileController::class, 'update']);
    Route::post('restaurant/update', [ProfileController::class, 'updateRestaurant']);
    Route::patch('profile/toggle-restaurant-status', [ProfileController::class, 'toggleStatus']);

    // Meals
    Route::get('meals', [MealController::class, 'index']);
    Route::post('meals', [MealController::class, 'store']);
    Route::post('meals/{id}', [MealController::class, 'update']); // Using POST for multipart support
    Route::delete('meals/{id}', [MealController::class, 'destroy']);
    Route::post('meals/{id}/toggle-availability', [MealController::class, 'toggleAvailability']);

    // Meal Discounts
    Route::match(['post', 'put'], 'restaurant/meals/{meal}/discount', [MealController::class, 'updateDiscount']);

    // Combo Offers CRUD
    Route::get('restaurant/offers', [\Modules\Restaurants\Http\Controllers\Api\OfferController::class, 'index']);
    Route::post('restaurant/offers', [\Modules\Restaurants\Http\Controllers\Api\OfferController::class, 'store']);
    Route::get('restaurant/offers/{id}', [\Modules\Restaurants\Http\Controllers\Api\OfferController::class, 'show']);
    Route::put('restaurant/offers/{id}', [\Modules\Restaurants\Http\Controllers\Api\OfferController::class, 'update']);
    Route::post('restaurant/offers/{id}', [\Modules\Restaurants\Http\Controllers\Api\OfferController::class, 'update']); // Support multipart POST update
    Route::delete('restaurant/offers/{id}', [\Modules\Restaurants\Http\Controllers\Api\OfferController::class, 'destroy']);

    // Categories
    Route::get('categories', [CategoryController::class, 'index']);
    Route::post('categories', [CategoryController::class, 'store']);
    Route::post('categories/{id}', [CategoryController::class, 'update']); // Using POST for multipart support
    Route::delete('categories/{id}', [CategoryController::class, 'destroy']);

    // Restaurant Management
    Route::post('restaurant/update-location', [ProfileController::class, 'updateLocation']);
    Route::post('restaurant/toggle-status', [ProfileController::class, 'toggleStatus']);
});
