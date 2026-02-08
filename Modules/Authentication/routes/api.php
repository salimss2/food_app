<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\app\Http\Controllers\AuthenticationController;

Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/register', [AuthenticationController::class, 'register']);

// روابط محمية
// الطريقة الصحيحة للكتابة
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/delivery/complete-profile', [AuthenticationController::class, 'completeDeliveryProfile']);
});

// رابط اختبار للتأكد من أن الموديول يعمل
Route::get('/test-auth', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Authentication Module is working perfectly!',
        'framework' => 'Laravel 11',
        'php_version' => PHP_VERSION
    ]);
});

// الطريقة الأكثر أماناً في Laravel 11 لتجنب أخطاء الـ Reflection
Route::get('/test-auth', [AuthenticationController::class, 'index']);