<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminController;
use Modules\Admin\Http\Controllers\AuthController;
use Modules\Admin\Http\Controllers\UserController;
use Modules\Admin\Http\Controllers\DriverController;
use Modules\Admin\Http\Controllers\RestaurantsController;
use Modules\Admin\Http\Controllers\DashboardController;
use Modules\Admin\Http\Controllers\AdminOrderController;
use Modules\Admin\Http\Controllers\AdminPaymentController;
use Modules\Admin\Http\Controllers\AdminCommissionController;
use Modules\Admin\Http\Controllers\AdminComplaintController;
use Modules\Admin\Http\Controllers\AdminRoleController;
use Modules\Admin\Http\Controllers\AdminNotificationController;
use Modules\Admin\Http\Controllers\AdminNotificationInboxController;
use Modules\Admin\Http\Controllers\FinancialReportController;


// Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
// Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
// Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->group(function () {
    // Guest Routes (Only accessible if NOT logged in)
    Route::middleware(['guest'])->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
        Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    });

    // Authenticated Routes
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout')->middleware('auth');
});

// Protected Admin API and Dashboard Routes
Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/debug-permissions', function () {
        $user = auth()->user();
        return response()->json([
            'auth_guard' => auth()->getDefaultDriver(),
            'user_guard' => $user->guard_name ?? 'not defined',
            'roles' => $user->roles->map(fn($role) => ['name' => $role->name, 'guard_name' => $role->guard_name]),
            'permissions' => $user->getAllPermissions()->map(fn($p) => ['name' => $p->name, 'guard_name' => $p->guard_name]),
            'can_view_drivers' => $user->can('view_drivers'),
            'has_permission_to_view_drivers' => $user->hasPermissionTo('view_drivers'),
        ]);
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Users CRUD (Permissions checked in Controller __construct)
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.users.show');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::patch('/users/{user}/toggle-block', [UserController::class, 'toggleBlock'])->name('admin.users.toggleBlock');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    // Restaurants CRUD (Permissions checked in Controller __construct)
    Route::resource('restaurants', RestaurantsController::class)->names('admin.restaurants')->except(['create', 'edit']);
    Route::post('/restaurants/{id}/toggle-block', [RestaurantsController::class, 'toggleBlock'])->name('admin.restaurants.toggle-block');
    Route::post('/restaurants/{id}/toggle-state', [RestaurantsController::class, 'toggleState'])->name('admin.restaurants.toggle-state');
    Route::post('/restaurants/store-meal', [RestaurantsController::class, 'storeMeal'])->name('admin.restaurants.store-meal');
    Route::post('/restaurants/store-category', [RestaurantsController::class, 'storeCategory'])->name('admin.restaurants.store-category');
    Route::put('/meals/{id}', [RestaurantsController::class, 'updateMeal'])->name('admin.meals.update');
    Route::delete('/meals/{id}', [RestaurantsController::class, 'destroyMeal'])->name('admin.meals.destroy');
    Route::post('/meals/{id}/toggle-availability', [RestaurantsController::class, 'toggleMealAvailability'])->name('admin.meals.toggle-availability');

    // Drivers CRUD (Permissions checked in Controller __construct)
    Route::resource('drivers', DriverController::class)->names('admin.drivers')->except(['create', 'edit']);
    Route::get('/drivers/{id}/details', [DriverController::class, 'show'])->name('admin.drivers.details');
    Route::post('/drivers/toggle-availability/{id}', [DriverController::class, 'toggleAvailability'])->name('admin.drivers.toggle-availability');

    // Admin Orders
    Route::get('/orders', [AdminOrderController::class, 'activeOrders'])->name('admin.orders.index')->middleware('permission:view_orders');
    Route::get('/scheduled-orders', [AdminOrderController::class, 'scheduledOrders'])->name('admin.scheduled-orders.index')->middleware('permission:view_orders');
    Route::get('/order-history', [AdminOrderController::class, 'orderHistory'])->name('admin.order-history.index')->middleware('permission:view_orders');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('admin.orders.show')->middleware('permission:view_orders');
    Route::post('/orders/{id}/force-cancel', [AdminOrderController::class, 'forceCancel'])->name('admin.orders.force-cancel')->middleware('permission:manage_order_status');
    Route::post('/orders/{id}/reassign', [AdminOrderController::class, 'reassignDriver'])->name('admin.orders.reassign')->middleware('permission:manage_order_status');
    Route::post('/orders/{id}/approve', [AdminOrderController::class, 'approvePayment'])->name('admin.orders.approve')->middleware('permission:manage_order_status');
    Route::post('/orders/{id}/reject', [AdminOrderController::class, 'rejectPayment'])->name('admin.orders.reject')->middleware('permission:manage_order_status');

    // Payments
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('admin.payments.index')->middleware('permission:view_financials');
    Route::get('/payments/filter', [AdminPaymentController::class, 'filter'])->name('admin.payments.filter')->middleware('permission:view_financials');
    Route::patch('/payments/{id}/approve', [AdminPaymentController::class, 'approve'])->name('admin.payments.approve')->middleware('permission:manage_payments');
    Route::patch('/payments/{id}/reject', [AdminPaymentController::class, 'reject'])->name('admin.payments.reject')->middleware('permission:manage_payments');
    Route::patch('/payments/{id}/cancel', [AdminPaymentController::class, 'cancel'])->name('admin.payments.cancel')->middleware('permission:manage_payments');
    Route::patch('/payments/{id}/refund', [AdminPaymentController::class, 'markAsRefunded'])->name('admin.payments.refund')->middleware('permission:manage_payments');

    // Commissions (New Placeholder Routes)
    Route::get('/commissions', [\Modules\Admin\Http\Controllers\AdminCommissionController::class, 'index'])->name('admin.commissions.index')->middleware('permission:view_financials');
    Route::post('/commissions/{id}/settle', [\Modules\Admin\Http\Controllers\AdminCommissionController::class, 'settle'])->name('admin.commissions.settle')->middleware('permission:manage_commissions');
    Route::get('/api/driver-wallets', [\Modules\Admin\Http\Controllers\AdminCommissionController::class, 'getDriverWalletSummaries'])->name('admin.api.driver-wallets')->middleware('permission:view_financials');
    Route::get('/api/driver-wallets/{driverId}/deliveries', [\Modules\Admin\Http\Controllers\AdminCommissionController::class, 'getDriverDeliveries'])->name('admin.api.driver-wallets.deliveries')->middleware('permission:view_financials');
    Route::post('/api/driver-wallets/{driverId}/settle', [\Modules\Admin\Http\Controllers\AdminCommissionController::class, 'settleDriverBalance'])->name('admin.api.driver-wallets.settle')->middleware('permission:manage_commissions');
    Route::get('/api/settlements', [\Modules\Admin\Http\Controllers\AdminCommissionController::class, 'getSettlementHistory'])->name('admin.api.settlements.history')->middleware('permission:view_financials');
    Route::get('/api/settlements/{settlementId}/details', [\Modules\Admin\Http\Controllers\AdminCommissionController::class, 'getSettlementDetails'])->name('admin.api.settlements.details')->middleware('permission:view_financials');

    // Restaurant Commissions Routes
    Route::get('/api/restaurant-wallets', [\Modules\Admin\Http\Controllers\AdminCommissionController::class, 'getRestaurantWallets'])->name('admin.api.restaurant-wallets');
    Route::get('/api/restaurant-wallets/{restaurantId}/orders', [\Modules\Admin\Http\Controllers\AdminCommissionController::class, 'getRestaurantOrders'])->name('admin.api.restaurant-wallets.orders');
    Route::post('/api/restaurant-wallets/{restaurantId}/settle', [\Modules\Admin\Http\Controllers\AdminCommissionController::class, 'settleRestaurantBalance'])->name('admin.api.restaurant-wallets.settle');
    Route::get('/api/restaurant-settlements', [\Modules\Admin\Http\Controllers\AdminCommissionController::class, 'getRestaurantSettlementHistory'])->name('admin.api.restaurant-settlements.history');
    Route::get('/api/restaurant-settlements/{settlementId}/details', [\Modules\Admin\Http\Controllers\AdminCommissionController::class, 'getRestaurantSettlementDetails'])->name('admin.api.restaurant-settlements.details');

    // Complaints (New Placeholder Routes)
    Route::get('/complaints', [\Modules\Admin\Http\Controllers\AdminComplaintController::class, 'index'])->name('admin.complaints.index')->middleware('permission:view_complaints');
    Route::post('/complaints/{id}/respond', [\Modules\Admin\Http\Controllers\AdminComplaintController::class, 'respond'])->name('admin.complaints.respond')->middleware('permission:respond_complaints');

    // Settings & Roles
    Route::get('/settings', function () {
        return view('admin::settings');
    })->name('admin.settings.index')->middleware('permission:manage_settings');
    Route::get('/roles-permissions', function () {
        return view('admin::roles-permissions');
    })->name('admin.roles-permissions.index')->middleware('permission:manage_roles');

    // Roles API Endpoints
    Route::prefix('api/roles')->middleware('permission:manage_roles')->group(function () {
        Route::get('/', [AdminRoleController::class, 'apiIndex']);
        Route::post('/', [AdminRoleController::class, 'apiStore']);
        Route::put('/{id}', [AdminRoleController::class, 'apiUpdate']);
        Route::delete('/{id}', [AdminRoleController::class, 'apiDestroy']);
        Route::post('/{id}/sync', [AdminRoleController::class, 'syncPermissions']);
    });

    // Profile Management (Accessible to ALL authenticated admins)
    Route::get('/profile', [\Modules\Admin\Http\Controllers\AdminProfileController::class, 'index'])->name('admin.profile');
    Route::put('/profile', [\Modules\Admin\Http\Controllers\AdminProfileController::class, 'updateProfile'])->name('admin.profile.update');

    // Financial Reports
    Route::get('/reports', [FinancialReportController::class, 'index'])->name('admin.reports')->middleware('permission:view_financials');
    Route::get('/reports/export/csv', [FinancialReportController::class, 'exportCsv'])->name('admin.reports.export.csv')->middleware('permission:view_financials');
    Route::get('/reports/export/pdf', [FinancialReportController::class, 'exportPdf'])->name('admin.reports.export.pdf')->middleware('permission:view_financials');

    // Discount Codes
    Route::get('/discount-codes', [\Modules\Admin\Http\Controllers\DiscountCodeController::class, 'index'])->name('admin.discount-codes.index');
    Route::post('/discount-codes', [\Modules\Admin\Http\Controllers\DiscountCodeController::class, 'store'])->name('admin.discount-codes.store');
    Route::delete('/discount-codes/{id}', [\Modules\Admin\Http\Controllers\DiscountCodeController::class, 'destroy'])->name('admin.discount-codes.destroy');

    // Promotional Offers
    Route::get('/offers', [\Modules\Admin\Http\Controllers\AdminOfferController::class, 'index'])->name('admin.offers.index')->middleware('permission:manage_settings');
    Route::post('/offers', [\Modules\Admin\Http\Controllers\AdminOfferController::class, 'store'])->name('admin.offers.store')->middleware('permission:manage_settings');
    Route::delete('/offers/{id}', [\Modules\Admin\Http\Controllers\AdminOfferController::class, 'destroy'])->name('admin.offers.destroy')->middleware('permission:manage_settings');
});

// Integrated Dummy Routes (Temporary Fallbacks to prevent crashes)
Route::prefix('admin')->group(function () {
    Route::get('/forgot-password', function () {
        return view('admin::forgot-password');
    })->name('admin.forgot-password');
    Route::get('/welcome', function () {
        return view('admin::welcome');
    })->name('admin.welcome');
    Route::get('/reset-password', function () {
        return view('admin::reset-password');
    })->name('admin.reset-password');
    Route::get('/commissions-driver', function () {
        return view('admin::commissions-driver');
    })->name('admin.commissions-driver.index');
    Route::get('/driver-details', function () {
        return view('admin::driver-details');
    })->name('admin.driver-details.index');
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/notifications', [AdminNotificationController::class, 'store'])->name('admin.notifications.store');
    Route::get('/notification-history', [AdminNotificationController::class, 'history'])->name('admin.notification-history.index');
    Route::get('/restaurant-details', function () {
        return view('admin::restaurant-details');
    })->name('admin.restaurant-details.index');
    Route::get('/withdrawals', function () {
        return view('admin::withdrawals');
    })->name('admin.withdrawals.index');
    Route::get('/register', function () {
        return view('admin::register');
    })->name('admin.register');
    Route::get('/otp-verification', function () {
        return view('admin::otp-verification');
    })->name('admin.otp-verification');
    Route::get('/discounts', function () {
        return view('admin::discounts');
    })->name('admin.discounts.index');
    Route::get('/feedback', function () {
        return view('admin::feedback');
    })->name('admin.feedback.index');
    Route::get('/revenue', function () {
        return view('admin::revenue');
    })->name('admin.revenue.index');

    // Notifications Management
    Route::get('/scheduled-notifications', [AdminNotificationController::class, 'scheduled'])->name('admin.scheduled-notifications.index');
    Route::delete('/notifications/{id}', [AdminNotificationController::class, 'destroy'])->name('admin.notifications.destroy');

    // System Alerts / Notification Inbox
    Route::get('/notifications/inbox', [AdminNotificationInboxController::class, 'index'])->name('admin.notifications.inbox');
    Route::get('/notifications/inbox/mark-all-read', [AdminNotificationInboxController::class, 'markAllAsRead'])->name('admin.notifications.inbox.mark-all-read');
    Route::get('/notifications/inbox/{id}/read', [AdminNotificationInboxController::class, 'readAndRedirect'])->name('admin.notifications.inbox.read');
    // Commissions Restaurant
    Route::get('/commissions-restaurant', function () {
        return view('admin::commissions-restaurant');
    })->name('admin.commissions-restaurant.index');
    Route::get('/commissions-settings', [\Modules\Admin\Http\Controllers\CommissionSettingsController::class, 'index'])->name('admin.commissions-settings.index');
    Route::post('/commissions-settings', [\Modules\Admin\Http\Controllers\CommissionSettingsController::class, 'store'])->name('admin.commissions-settings.store');
    Route::put('/commissions-settings/{id}', [\Modules\Admin\Http\Controllers\CommissionSettingsController::class, 'update'])->name('admin.commissions-settings.update');
    Route::delete('/commissions-settings/{id}', [\Modules\Admin\Http\Controllers\CommissionSettingsController::class, 'destroy'])->name('admin.commissions-settings.destroy');
});
