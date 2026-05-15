<?php

use Illuminate\Support\Facades\Route;
use Modules\Support\Http\Controllers\SupportTicketController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::post('/support', [SupportTicketController::class, 'store']);
    Route::apiResource('supports', SupportController::class)->names('support');
});
