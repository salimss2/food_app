<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Http\Controllers\DriverStatusController;
use Modules\Users\Http\Controllers\UsersController;
use Modules\Users\Http\Controllers;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('users', UsersController::class)->names('users');
});

// مسارات السائق بدون حماية التوكن مؤقتاً للتأكد من عمل الموديول
Route::prefix('v1/users/driver')->group(function () {
    
    Route::post('/update-status', [DriverStatusController::class, 'updateStatus']);
    
    // مسار تجريبي لجلب الحالة (GET)
    Route::get('/profile-status', [DriverStatusController::class, 'getProfile']);
});