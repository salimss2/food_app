<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\Admin\Http\Controllers\AuthController;
use Modules\Admin\Http\Controllers\UserController;
use Modules\Admin\Http\Controllers\DriverController;
use Modules\Admin\Http\Controllers\RestaurantsController;
use Modules\Admin\Http\Controllers\DashboardController;


// Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
// Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
// Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->group(function () {
    // Route تسجيل الدخول
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
});

// User Management — Protected
Route::prefix('admin')->middleware(['auth','role:System Admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Users CRUD
    Route::get('/users', [UserController::class,'index'])->name('admin.users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}', [UserController::class,'show'])->name('admin.users.show');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    // Restaurants CRUD
    Route::resource('restaurants', RestaurantsController::class)->names('admin.restaurants')->except(['create', 'edit']);
    Route::post('/restaurants/{id}/toggle-block', [RestaurantsController::class, 'toggleBlock'])->name('admin.restaurants.toggle-block');
    Route::post('/restaurants/{id}/toggle-state', [RestaurantsController::class, 'toggleState'])->name('admin.restaurants.toggle-state');

    // Drivers CRUD
    Route::resource('drivers', DriverController::class)->names('admin.drivers')->except(['create', 'edit']);
    Route::get('/drivers/{id}/details', [DriverController::class, 'show'])->name('admin.drivers.details');
    Route::post('/drivers/toggle-availability/{id}', [DriverController::class, 'toggleAvailability'])->name('admin.drivers.toggle-availability');
});


// Driver Management routes are now in the protected middleware group above.


// Integrated Dummy Routes (Temporary Fallbacks to prevent crashes)
Route::prefix('admin')->group(function () {
    Route::get('/forgot-password', function () { return view('admin::forgot-password'); })->name('admin.forgot-password');
    Route::get('/commissions', function () { return view('admin::commissions'); })->name('admin.commissions.index');
    Route::get('/settings', function () { return view('admin::settings'); })->name('admin.settings.index');
    Route::get('/order-history', function () { return view('admin::order-history'); })->name('admin.order-history.index');
    Route::get('/reports', function () { return view('admin::reports'); })->name('admin.reports.index');
    Route::get('/welcome', function () { return view('admin::welcome'); })->name('admin.welcome');
    Route::get('/reset-password', function () { return view('admin::reset-password'); })->name('admin.reset-password');
    Route::get('/profile', function () { return view('admin::profile'); })->name('admin.profile');
    Route::get('/commissions-driver', function () { return view('admin::commissions-driver'); })->name('admin.commissions-driver.index');
    Route::get('/complaints', function () { return view('admin::complaints'); })->name('admin.complaints.index');
    Route::get('/offers', function () { return view('admin::offers'); })->name('admin.offers.index');
    Route::get('/driver-details', function () { return view('admin::driver-details'); })->name('admin.driver-details.index');
    Route::get('/notifications', function () { return view('admin::notifications'); })->name('admin.notifications.index');
    Route::get('/notification-history', function () { return view('admin::notification-history'); })->name('admin.notification-history.index');
    Route::get('/restaurant-details', function () { return view('admin::restaurant-details'); })->name('admin.restaurant-details.index');
    Route::get('/withdrawals', function () { return view('admin::withdrawals'); })->name('admin.withdrawals.index');
    Route::get('/scheduled-orders', function () { return view('admin::scheduled-orders'); })->name('admin.scheduled-orders.index');
    Route::get('/register', function () { return view('admin::register'); })->name('admin.register');
    Route::get('/otp-verification', function () { return view('admin::otp-verification'); })->name('admin.otp-verification');
    Route::get('/discounts', function () { return view('admin::discounts'); })->name('admin.discounts.index');
    Route::get('/feedback', function () { return view('admin::feedback'); })->name('admin.feedback.index');
    Route::get('/revenue', function () { return view('admin::revenue'); })->name('admin.revenue.index');
    Route::get('/scheduled-notifications', function () { return view('admin::scheduled-notifications'); })->name('admin.scheduled-notifications.index');
    Route::get('/commissions-restaurant', function () { return view('admin::commissions-restaurant'); })->name('admin.commissions-restaurant.index');
    Route::get('/roles-permissions', function () { return view('admin::roles-permissions'); })->name('admin.roles-permissions.index');
    Route::get('/payments', function () { return view('admin::payments'); })->name('admin.payments.index');
    Route::get('/orders', function () { return view('admin::orders'); })->name('admin.orders.index');
});
