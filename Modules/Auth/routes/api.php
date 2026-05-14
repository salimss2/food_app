<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\Api\DriverAuthController;
use Modules\Auth\Http\Controllers\PasswordResetController;




// Route::post('/register',[AuthController::class,'register']);
// Route::post('/login',[AuthController::class,'login']);


// use App\Http\Controllers\AdminController;

// مسارات للكل ماعدا الموصل
Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/google-signin', [AuthController::class, 'googleSignIn']);
    // Update Location of Customer
    Route::post('/profile/update-location', [\Modules\Users\Http\Controllers\ProfileController::class, 'updateLocation'])->middleware('auth:sanctum');

    // OTP Password Reset Flow
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);
});
// مسارات تحتاج تسجيل دخول (Sanctum)
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/update', [AuthController::class, 'update']); // سيصبح الرابط: api/auth/update
    Route::post('/logout', [AuthController::class, 'logout']); // سيصبح الرابط: api/auth/logout
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/getProfile', [AuthController::class, 'getProfile']);
});

// // مسارات الموصل فقط
// Route::prefix('v1/auth')->group(function () {

//     // 1. المسارات العامة (بدون تسجيل دخول)

//     Route::post('/login', [DriverAuthController::class, 'login']);

//     // 2. المسارات المحمية (تتطلب Authorization: Bearer {token})
//     Route::middleware('auth:sanctum')->group(function () {

//         // تسجيل الخروج
//         Route::post('/logout', [AuthController::class, 'logout']);

//         // 🔥 تحديث الصورة الشخصية (الأفاتار)
//         // أضفنا هذا المسار ليتطابق مع طلب Flutter في AuthService
//         Route::post('/update-avatar', [DriverAuthController::class, 'updateAvatar']);

//         // تحديث البيانات الشخصية (البريد، الهاتف، العنوان)
//         Route::post('/update-profile', [DriverAuthController::class, 'updateProfile']);

//         // تحديث بيانات المركبة
//         Route::post('/update-vehicle', [DriverAuthController::class, 'updateVehicle']);
//     });
// مسارات الموصل فقط
Route::prefix('v1/auth')->group(function () {

    // 1. المسارات العامة (بدون تسجيل دخول)
    Route::post('/login', [DriverAuthController::class, 'login']);

    // 2. المسارات المحمية (تتطلب Token)
    Route::middleware('auth:sanctum')->group(function () {

        // تسجيل الخروج
        Route::post('/logout', [AuthController::class, 'logout']);

        // 🔥 جلب بيانات المستخدم (مهم جداً لحل مشكلة العنوان)
        Route::get('/profile', [DriverAuthController::class, 'getProfile']);

        // تحديث الصورة الشخصية
        Route::post('/update-avatar', [DriverAuthController::class, 'updateAvatar']);

        // تحديث البيانات الشخصية
        Route::post('/update-profile', [DriverAuthController::class, 'updateProfile']);

        // تحديث بيانات المركبة
        Route::post('/update-vehicle', [DriverAuthController::class, 'updateVehicle']);

    });
});
