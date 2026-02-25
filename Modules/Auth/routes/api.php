<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;

// 1. مسارات عامة (لا تضعها داخل أي middleware)
Route::prefix('v1')->group(function () {
    Route::post('/auths', [AuthController::class, 'store']); // مسار إنشاء الحساب
    Route::post('/auths/login', [AuthController::class, 'login']); // مسار تسجيل الدخول
});

// 2. مسارات محمية (للمسجلين فقط)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/auths/logout', [AuthController::class, 'logout']);
});
