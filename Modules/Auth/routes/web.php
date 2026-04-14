<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\PasswordResetController;

Route::get('/admin/forgot-password', function () {
    return view('auth::forgot-password');
})->name('password.request');

Route::post('/admin/forgot-password', [PasswordResetController::class,'sendResetLink'])
    ->name('password.email');

Route::get('/admin/reset-password/{token}', [PasswordResetController::class,'showResetForm'])
    ->name('password.reset');

Route::post('/admin/reset-password', [PasswordResetController::class,'resetPassword'])
    ->name('password.update');

    

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('auths', AuthController::class)->names('auth');
});
