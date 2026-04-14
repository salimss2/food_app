<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Dummy arrays for stats
        $stats = [
            [
                'title' => 'Total Users',
                'value' => '12,408',
                'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                'color' => 'blue'
            ],
            [
                'title' => 'Active Drivers',
                'value' => '342',
                'icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                'color' => 'indigo'
            ],
            [
                'title' => 'Restaurants',
                'value' => '89',
                'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                'color' => 'green'
            ],
            [
                'title' => 'Pending Payments',
                'value' => '$24,930',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'color' => 'yellow'
            ]
        ];

        // Dummy arrays for quick links
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

        return view('admin::index', compact('stats', 'quickLinks'));
    }
}
