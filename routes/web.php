<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.login');
});
Route::get('/debug-test', function () {
    throw new Exception("Laravel is working!");
});