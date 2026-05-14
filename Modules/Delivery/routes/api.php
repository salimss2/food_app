<?php

use Illuminate\Support\Facades\Route;
use Modules\Delivery\Http\Controllers\Api\DriverOrderController;
use Modules\Delivery\Http\Controllers\DriverStatusController;

/*
|--------------------------------------------------------------------------
| API Routes - Delivery Module
|--------------------------------------------------------------------------
*/

// 1. المسارات المحمية (التي يستخدمها التطبيق مع التوكن)
// تتطابق مع: static const String deliveryPrefix = "/v1/users/driver";
Route::middleware('auth:sanctum')->prefix('v1/users/driver')->group(function () {

    // جلب الطلبات المتاحة (GET)
    Route::get('/available-orders', [DriverOrderController::class, 'getAvailableOrders']);

    // تفاصيل الطلب (GET)
    Route::get('/orders/{id}', [DriverOrderController::class, 'getOrderDetails']);

    // قبول الطلب (POST)
    Route::post('/orders/{id}/accept', [DriverOrderController::class, 'acceptOrder']);

    // إتمام التسليم (POST)
    Route::post('/orders/{id}/complete', [DriverOrderController::class, 'completeOrder']);

    // سجل الطلبات (GET)
    Route::get('/order-history', [DriverOrderController::class, 'getHistory']);

    // تحديث الحالة (POST)
    Route::post('/update-status', [DriverStatusController::class, 'updateStatus']);
    Route::get('/profile-status', [DriverStatusController::class, 'getProfile']);
});

// 2. مسارات الاختبار (بدون توكن لفتحها في المتصفح والتأكد من الاتصال)
// هذه المسارات هي التي ستحل لك مشكلة الـ 404 في المتصفح حالياً
Route::prefix('v1/users/driver')->group(function () {

    // رابط الاختبار الأول: http://10.1.0.216:8000/api/v1/users/driver/test-connection
    Route::get('/test-connection', function () {
        return response()->json([
            'status' => true,
            'message' => 'API Connection is Successful!',
            'ip_used' => request()->ip(),
            'timestamp' => now()
        ]);
    });

    // مسار اختبار لقبول الطلب عبر المتصفح (GET بدلاً من POST لأغراض الفحص فقط)
    // رابط الاختبار الثالث: http://10.1.0.216:8000/api/v1/users/driver/orders/1/accept-test
    Route::get('/orders/{id}/accept-test', [DriverOrderController::class, 'acceptOrder']);
});