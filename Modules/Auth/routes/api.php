<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    // 1. المسارات التي لا تحتاج تسجيل دخول
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google-signin', [AuthController::class, 'googleSignIn']);
    // 2. المسارات التي تحتاج أن يكون المستخدم مسجل الدخول (محمية بالتوكن)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/update', [AuthController::class, 'update']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});