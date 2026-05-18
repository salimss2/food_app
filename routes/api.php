<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\FavoriteController;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Notifications\Http\Controllers\NotificationController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\SearchController;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/user/update-fcm-token', [AuthController::class, 'updateFcmToken']);
    Route::post('/v1/update-fcm-token', [AuthController::class, 'updateFcmToken']);

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::post('/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead']);
    });

    Route::prefix('v1/favorites')->group(function () {
        Route::get('/', [FavoriteController::class, 'index']);
        Route::post('/toggle-meal', [FavoriteController::class, 'toggleMeal']);
        Route::post('/toggle-restaurant', [FavoriteController::class, 'toggleRestaurant']);
    });



});

Route::post('/delivery/calculate-fee', [\App\Http\Controllers\Api\DeliveryCalculationController::class, 'calculate']);

Route::prefix('v1')->group(function () {
    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/privacy-policy', [SettingController::class, 'getPrivacyPolicy']);
    Route::get('/about-app', [SettingController::class, 'getAboutAppData']);
});
