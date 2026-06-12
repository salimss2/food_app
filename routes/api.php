<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\FavoriteController;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Notifications\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Artisan;


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

Route::get('/run-background-tasks/{secret_key}', function ($secret_key) {
    // حماية المسار بكلمة سر بسيطة حتى لا يشغله أحد غير
    if ($secret_key !== 'salim-srdms-secret-2026') {
        abort(403);
    }

    try {
        // 1. تشغيل المهام المجدولة (Schedule)
        Artisan::call('schedule:run');

        // 2. معالجة الطوابير المعلقة (Queue) ثم التوقف فور الانتهاء
        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--timeout' => 60
        ]);

        return response()->json(['status' => 'success', 'message' => 'Tasks and Queues processed!']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});