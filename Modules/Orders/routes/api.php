<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\OrdersController;
use Modules\Orders\Http\Controllers\CartController;
use Modules\Orders\Http\Controllers\RestaurantOrderController;
use Modules\Delivery\Http\Controllers\Api\DriverOrderController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // --- Customer order routes ---
    Route::apiResource('orders', OrdersController::class)->names('orders');

    // --- Cart routes ---
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/add', [CartController::class, 'add']);
        Route::put('/update/{id}', [CartController::class, 'update']);
        Route::delete('/remove/{id}', [CartController::class, 'remove']);
        Route::delete('/clear', [CartController::class, 'clear']);
    });

    // --- Restaurant Owner: Orders Management ---
    // Base: GET  /api/v1/restaurant/orders?status={new|in_progress|ready|all}
    // Show: GET  /api/v1/restaurant/orders/{id}
    // Upd:  PATCH /api/v1/restaurant/orders/{id}/status
    Route::prefix('restaurant')->group(function () {
        Route::get('orders', [RestaurantOrderController::class, 'index']);
        Route::get('orders/{id}', [RestaurantOrderController::class, 'show']);
        Route::patch('orders/{id}/status', [RestaurantOrderController::class, 'updateStatus']);
    });
    //     Route::get('/', [CartController::class, 'index']);           // جلب محتويات السلة
    // Route::post('/add', [CartController::class, 'add']);         // إضافة وجبة للسلة
    // Route::put('/update/{id}', [CartController::class, 'update']); // تحديث كمية وجبة (id هو cart_item id)
    // Route::delete('/remove/{id}', [CartController::class, 'remove']); // حذف وجبة من السلة
    // Route::delete('/clear', [CartController::class, 'clear']);     // تفريغ السلة بالكامل

});

/*
|--------------------------------------------------------------------------
| مسارات السائق - نظام التوصيل
|--------------------------------------------------------------------------
*/

Route::prefix('v1/users/driver')->group(function () {

    // 1. رابط الفحص (يعمل في المتصفح)
    // http://10.1.0.216:8000/api/v1/users/driver/test-connection

    // 2. مسارات الاختبار بدون حماية (للمتصفح)
    Route::get('/orders/{id}/accept-test', [DriverOrderController::class, 'acceptOrder']);
    Route::get('/profile-status', [\Modules\Delivery\Http\Controllers\DriverStatusController::class, 'getProfile']); // تجربة جلب بيانات

    // 3. المسارات الحقيقية (التي سيستخدمها تطبيق Flutter)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/orders/{id}/accept', [DriverOrderController::class, 'acceptOrder']);
        Route::post('/orders/{id}/complete', [DriverOrderController::class, 'completeOrder']);
        Route::get('/order-history', [DriverOrderController::class, 'getHistory']);
    });
});