<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\DriverStatusController;
use Modules\Users\Http\Controllers\UsersController;
use Modules\Users\Http\Controllers;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('users', UsersController::class)->names('users');
    Route::get('/notifications', [Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [Controllers\Api\NotificationController::class, 'markAsRead']);
});

// مسارات السائق محمية بـ Sanctum لضمان جلب التوكن ومعرف المستخدم (auth()->id())
Route::middleware(['auth:sanctum'])->prefix('v1/users/driver')->group(function () {
    Route::post('/update-status', [DriverStatusController::class, 'updateStatus']);
    Route::get('/profile-status', [DriverStatusController::class, 'getProfile']);
});