<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminCommissionController extends Controller
{
    /**
     * Display a listing of commissions.
     */
    public function index()
    {
        // Placeholder for displaying commissions
        return view('admin::commissions');
    }

    /**
     * Settle a specific commission.
     */
    public function settle(Request $request, $id)
    {
        // Placeholder for settling a commission
        return response()->json([
            'success' => true,
            'message' => 'Commission settled successfully (Placeholder)'
        ]);
    }

    /**
     * Get aggregated financial summaries for all drivers.
     */
    public function getDriverWalletSummaries()
    {
        // Fetch drivers and calculate aggregates using optimized subqueries
        $drivers = \App\Models\User::role('driver')
            ->withCount([
                'driverOrders as deliveries_count' => function ($query) {
                    $query->where('status', 'delivered');
                }
            ])
            ->withSum([
                'driverOrders as driver_earnings' => function ($query) {
                    $query->where('status', 'delivered');
                }
            ], 'driver_commission')
            ->withSum([
                'driverOrders as cash_in_hand' => function ($query) {
                    $query->where('status', 'delivered')->where('payment_method', 'cash');
                }
            ], 'total')
            ->get();

        // Format data for the frontend DataTables/Wallet UI
        $formattedData = $drivers->map(function ($driver) {
            $earnings = (float) ($driver->driver_earnings ?? 0);
            $cash = (float) ($driver->cash_in_hand ?? 0);

            return [
                'id' => $driver->id,
                'name' => $driver->name,
                'deliveries' => $driver->deliveries_count,
                'driverEarnings' => $earnings,
                'cashInHand' => $cash,
                'netBalance' => $earnings - $cash,
                'avatar' => $driver->profile_picture
                    ? asset('storage/' . $driver->profile_picture)
                    : "https://ui-avatars.com/api/?name=" . urlencode($driver->name) . "&color=fff&background=4f46e5"
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $formattedData
        ]);
    }
}
