<?php

use Illuminate\Support\Facades\Route;
use Modules\Restaurants\Http\Controllers\Api\ProfileController;
use Modules\Restaurants\Http\Controllers\Api\MealController;
use Modules\Restaurants\Http\Controllers\Api\CategoryController;


use Modules\Restaurants\Http\Controllers\RestaurantController;

Route::prefix('v1')->group(function () {
    Route::get('/restaurants', [RestaurantController::class, 'index']);
});

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile/update', [ProfileController::class, 'update']);

    // Meals
    Route::get('meals', [MealController::class, 'index']);
    Route::post('meals', [MealController::class, 'store']);
    Route::post('meals/{id}', [MealController::class, 'update']); // Using POST for multipart support
    Route::delete('meals/{id}', [MealController::class, 'destroy']);
    Route::patch('meals/{id}/toggle-availability', [MealController::class, 'toggleAvailability']);

    // Categories
    Route::get('categories', [CategoryController::class, 'index']);
    Route::post('categories', [CategoryController::class, 'store']);

    
});
