<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\OrdersController;
// تأكد من تعديل هذا المسار إذا كان CartController موجوداً في مكان آخر (مثلاً داخل مجلد Modules)
use Modules\Orders\Http\Controllers\CartController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // --- مسارات الطلبات (Orders) ---
    Route::apiResource('orders', OrdersController::class)->names('orders');

    // --- مسارات السلة (Cart) ---
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);           // جلب محتويات السلة
        Route::post('/add', [CartController::class, 'add']);         // إضافة وجبة للسلة
        Route::put('/update/{id}', [CartController::class, 'update']); // تحديث كمية وجبة (id هو cart_item id)
        Route::delete('/remove/{id}', [CartController::class, 'remove']); // حذف وجبة من السلة
        Route::delete('/clear', [CartController::class, 'clear']);     // تفريغ السلة بالكامل
    });

});