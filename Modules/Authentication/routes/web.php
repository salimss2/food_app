<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\app\Http\Controllers\AuthenticationController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('authentications', AuthenticationController::class)->names('authentication');
});
