<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\OrdersController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/orders-command-center', [OrdersController::class, 'commandCenter'])->name('orders.command-center');
    Route::resource('orders', OrdersController::class)->names('orders');
});
