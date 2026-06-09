<?php

use Illuminate\Support\Facades\Route;
use Modules\Scheduling\Http\Controllers\SchedulingController;
use Modules\Scheduling\Http\Controllers\ScheduledOrdersController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    // Existing resource routes
    Route::apiResource('schedulings', SchedulingController::class)->names('scheduling');

    // Scheduled Orders — Flutter app endpoint
    // GET /api/v1/scheduled-orders
    Route::get('/scheduled-orders', [ScheduledOrdersController::class, 'index'])
        ->name('scheduled-orders.index');

    // DELETE /api/v1/scheduled-orders/{id}
    Route::delete('/scheduled-orders/{id}', [ScheduledOrdersController::class, 'destroy'])
        ->name('scheduled-orders.destroy');
});
