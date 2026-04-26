<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\FavoriteController;

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('v1/favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']);
        Route::post('/toggle-meal', [FavoriteController::class, 'toggleMeal']);
        Route::post('/toggle-restaurant', [FavoriteController::class, 'toggleRestaurant']);
    });

});
