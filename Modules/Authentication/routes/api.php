<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\app\Http\Controllers\AuthenticationController; // استدعاء واحد فقط ونظيف

// روابط عامة
Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/register', [AuthenticationController::class, 'register']);

// روابط محمية
// الطريقة الصحيحة للكتابة
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/delivery/complete-profile', [AuthenticationController::class, 'completeDeliveryProfile']);
});