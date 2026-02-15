<?php

use Illuminate\Support\Facades\Route;
use Modules\Restaurants\Http\Controllers\RestaurantsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('restaurants', RestaurantsController::class)->names('restaurants');
});
