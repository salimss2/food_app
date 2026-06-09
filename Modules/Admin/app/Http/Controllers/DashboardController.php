<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Orders\Models\Order;
use App\Models\User;
use Modules\Restaurants\Models\Restaurant;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Fetch real counts for stats cards using full namespace
        $activeOrdersCount = \Modules\Orders\Models\Order::whereNotIn('status', ['delivered', 'canceled'])->count();
        
        $pendingPaymentsTotal = \Modules\Orders\Models\Order::where('payment_status', 'pending_verification')->sum('total');
        
        $restaurantsCount = Restaurant::count();
        
        // Assuming Spatie Roles is used for drivers
        $activeDriversCount = User::role('driver')->where('status', 'active')->count();
        
        $totalUsersCount = User::count();

        // 1.1 New Stats: Today's Revenue & Online Drivers
        $todayRevenue = \Modules\Orders\Models\Order::whereDate('created_at', \Carbon\Carbon::today())
            ->where('status', 'delivered')
            ->sum('total');

        $onlineDriversCount = User::role('driver')->where('status', 'online')->count();

        // 2. Fetch Latest 5 Orders for the real-time table placeholder
        $latestOrders = \Modules\Orders\Models\Order::with(['user', 'restaurant'])
            ->latest()
            ->take(5)
            ->get();

        // Dummy arrays for quick links (keeping as requested)
        $quickLinks = [
            [
                'title' => 'Manage Users',
                'description' => 'View, edit, or block users.',
                'route' => 'admin.users.index'
            ],
            [
                'title' => 'Manage Drivers',
                'description' => 'Onboard new delivery drivers.',
                'route' => 'admin.drivers.index'
            ],
            [
                'title' => 'Manage Restaurants',
                'description' => 'Add food menus and restaurants.',
                'route' => 'admin.restaurants.index'
            ],
            [
                'title' => 'Approve Payments',
                'description' => 'Process wire deposits manually.',
                'route' => 'admin.payments.index'
            ]
        ];

        return view('admin::index', compact(
            'activeOrdersCount', 
            'pendingPaymentsTotal', 
            'activeDriversCount', 
            'restaurantsCount',
            'totalUsersCount',
            'latestOrders',
            'quickLinks',
            'todayRevenue',
            'onlineDriversCount'
        ));
    }
}
