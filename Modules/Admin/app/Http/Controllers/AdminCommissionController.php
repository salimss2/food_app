<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\Settlement;
use Modules\Orders\Models\RestaurantSettlement;

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
     * Filtered to only include delivered orders where settlement_id is NULL.
     */
    public function getDriverWalletSummaries()
    {
        // Fetch drivers and calculate aggregates using optimized subqueries
        $drivers = \App\Models\User::role('driver')
            ->withCount([
                'driverOrders as deliveries_count' => function ($query) {
                    $query->where('status', 'delivered')->whereNull('settlement_id');
                }
            ])
            ->withSum([
                'driverOrders as driver_earnings' => function ($query) {
                    $query->where('status', 'delivered')->whereNull('settlement_id');
                }
            ], 'driver_commission')
            ->withSum([
                'driverOrders as cash_in_hand' => function ($query) {
                    $query->where('status', 'delivered')->where('payment_method', 'cash')->whereNull('settlement_id');
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
                    ? \Illuminate\Support\Facades\Storage::url($driver->profile_picture)
                    : "https://ui-avatars.com/api/?name=" . urlencode($driver->name) . "&color=fff&background=4f46e5"
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $formattedData
        ]);
    }

    /**
     * Get unsettled deliveries for a specific driver.
     */
    public function getDriverDeliveries($driverId)
    {
        $orders = Order::where('driver_id', $driverId)
            ->where('status', 'delivered')
            ->whereNull('settlement_id')
            ->select('id', 'created_at', 'payment_method', 'delivery_distance', 'delivery_distance as distance', 'delivery_fee', 'platform_commission', 'driver_commission')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    /**
     * Settle driver balance and create a Settlement Receipt.
     * Uses database transactions to ensure audit safety and data consistency.
     */
    public function settleDriverBalance(Request $request, $driverId)
    {
        $admin = $request->user() ?? \App\Models\User::role('Admin')->first();

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: No active admin session found.'
            ], 403);
        }

        $result = DB::transaction(function () use ($driverId, $admin) {
            // 1. Fetch all unsettled delivered orders for this driver
            $orders = Order::where('driver_id', $driverId)
                ->where('status', 'delivered')
                ->whereNull('settlement_id')
                ->get();

            if ($orders->isEmpty()) {
                return [
                    'status' => false,
                    'message' => 'No unsettled orders found for this driver.',
                    'code' => 400
                ];
            }

            // 2. Calculate aggregates
            $totalEarnings = (float) $orders->sum('driver_commission');
            $totalCash = (float) $orders->filter(function ($o) {
                return $o->payment_method === 'cash';
            })->sum('total');
            $netAmount = $totalEarnings - $totalCash;

            // 3. Generate unique settlement number (e.g. SET-171583091)
            do {
                $settlementNumber = 'SET-' . time() . rand(10, 99);
            } while (Settlement::where('settlement_number', $settlementNumber)->exists());

            // 4. Create Settlement Receipt record
            $settlement = Settlement::create([
                'settlement_number' => $settlementNumber,
                'driver_id' => $driverId,
                'admin_id' => $admin->id,
                'total_driver_earnings' => $totalEarnings,
                'total_cash_collected' => $totalCash,
                'net_settlement_amount' => $netAmount,
            ]);

            // 5. Link orders to the settlement and mark as settled
            Order::whereIn('id', $orders->pluck('id'))
                ->update([
                    'settlement_id' => $settlement->id,
                    'is_settled' => true
                ]);

            return [
                'status' => true,
                'success' => true,
                'message' => "Settlement receipt {$settlementNumber} created successfully.",
                'settlement' => $settlement,
                'code' => 200
            ];
        });

        if ($result['status']) {
            // Dispatch Laravel Reverb broadcast event to instantly notify Flutter
            event(new \Modules\Orders\Events\SettlementCompleted($driverId));

            return response()->json([
                'status' => true,
                'success' => true,
                'message' => $result['message'],
                'settlement' => $result['settlement']
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => $result['message']
        ], $result['code']);
    }

    /**
     * Get list of all past settlements (Archive).
     */
    public function getSettlementHistory()
    {
        $settlements = Settlement::with(['driver', 'admin'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'settlement_number' => $s->settlement_number,
                    'date' => $s->created_at->toISOString(),
                    'driver_name' => $s->driver->name ?? 'Deleted Driver',
                    'admin_name' => $s->admin->name ?? 'System',
                    'total_earnings' => (float) $s->total_driver_earnings,
                    'total_cash' => (float) $s->total_cash_collected,
                    'net_amount' => (float) $s->net_settlement_amount,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $settlements
        ]);
    }

    /**
     * Get specific orders linked to a settlement.
     */
    public function getSettlementDetails($settlementId)
    {
        $orders = Order::where('settlement_id', $settlementId)
            ->select('id', 'created_at', 'payment_method', 'delivery_distance', 'delivery_distance as distance', 'delivery_fee', 'platform_commission', 'driver_commission')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get aggregated financial summaries for all restaurants.
     * Filtered to only include delivered orders where restaurant_settlement_id is NULL.
     * Calculated using: Gross Revenue = total - COALESCE(delivery_fee, 0)
     */
    public function getRestaurantWallets()
    {
        // Fetch restaurants and eager load only unsettled delivered orders
        $restaurants = \Modules\Restaurants\Models\Restaurant::with([
            'orders' => function ($query) {
                $query->where('status', 'delivered')
                    ->whereNull('restaurant_settlement_id');
            }
        ])->get();

        // Format data for the frontend DataTables/Wallet UI
        $formattedData = $restaurants->map(function ($restaurant) {
            $orders = $restaurant->orders;
            $ordersCount = $orders->count();

            // True meals revenue using formula: total - COALESCE(delivery_fee, 0)
            $grossRevenue = (float) $orders->sum(function ($order) {
                return (float) $order->total - (float) ($order->delivery_fee ?? 0);
            });

            $commissionRate = (float) ($restaurant->commission_rate ?? 15.00);
            $systemCut = $grossRevenue * ($commissionRate / 100);
            $netPayable = $grossRevenue - $systemCut;

            return [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'orders_count' => $ordersCount,
                'gross_revenue' => $grossRevenue,
                'commission_rate' => $commissionRate,
                'system_cut' => $systemCut,
                'net_payable' => $netPayable,
                'logo' => $restaurant->logo_url
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $formattedData
        ]);
    }

    /**
     * Get unsettled delivered orders for a specific restaurant.
     */
    public function getRestaurantOrders($restaurantId)
    {
        $restaurant = \Modules\Restaurants\Models\Restaurant::findOrFail($restaurantId);
        $commissionRate = (float) ($restaurant->commission_rate ?? 15.00);

        $orders = Order::where('restaurant_id', $restaurantId)
            ->where('status', 'delivered')
            ->whereNull('restaurant_settlement_id')
            ->select('id', 'order_number', 'created_at', 'payment_method', 'total', 'delivery_fee')
            ->get()
            ->map(function ($order) use ($commissionRate) {
                // True meals revenue using formula: total - COALESCE(delivery_fee, 0)
                $mealSubtotal = (float) $order->total - (float) ($order->delivery_fee ?? 0);
                $systemCut = $mealSubtotal * ($commissionRate / 100);
                $netPayable = $mealSubtotal - $systemCut;

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number ?? ('ORD-' . $order->id),
                    'created_at' => $order->created_at->toISOString(),
                    'payment_method' => $order->payment_method,
                    'total' => $mealSubtotal, // Meals subtotal only
                    'system_cut' => $systemCut,
                    'net_payable' => $netPayable,
                    'delivery_fee' => (float) $order->delivery_fee
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    /**
     * Settle restaurant balance and create a Settlement Receipt.
     * Uses DB transactions to ensure audit safety and data consistency.
     */
    public function settleRestaurantBalance(Request $request, $restaurantId)
    {
        $admin = $request->user() ?? \App\Models\User::role('Admin')->first();

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: No active admin session found.'
            ], 403);
        }

        return DB::transaction(function () use ($restaurantId, $admin) {
            // 1. Fetch restaurant
            $restaurant = \Modules\Restaurants\Models\Restaurant::findOrFail($restaurantId);

            // 2. Fetch all unsettled delivered orders for this restaurant
            $orders = Order::where('restaurant_id', $restaurantId)
                ->where('status', 'delivered')
                ->whereNull('restaurant_settlement_id')
                ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'No unsettled orders found for this restaurant.'
                ], 400);
            }

            // 3. Calculate true aggregates (meals subtotal only!) using total - COALESCE(delivery_fee, 0)
            $grossRevenue = (float) $orders->sum(function ($order) {
                return (float) $order->total - (float) ($order->delivery_fee ?? 0);
            });
            $commissionRate = (float) ($restaurant->commission_rate ?? 15.00);
            $systemCut = $grossRevenue * ($commissionRate / 100);
            $netPayable = $grossRevenue - $systemCut;

            // 4. Generate unique settlement number (e.g. RSET-171583091)
            do {
                $settlementNumber = 'RSET-' . time() . rand(10, 99);
            } while (RestaurantSettlement::where('settlement_number', $settlementNumber)->exists());

            // 5. Create Settlement Receipt record
            $settlement = RestaurantSettlement::create([
                'settlement_number' => $settlementNumber,
                'restaurant_id' => $restaurantId,
                'admin_id' => $admin->id,
                'gross_revenue' => $grossRevenue,
                'system_cut' => $systemCut,
                'net_payable' => $netPayable,
            ]);

            // 6. Link orders to the settlement
            Order::whereIn('id', $orders->pluck('id'))
                ->update([
                    'restaurant_settlement_id' => $settlement->id
                ]);

            return response()->json([
                'status' => true,
                'success' => true,
                'message' => "Settlement receipt {$settlementNumber} created successfully.",
                'settlement' => $settlement
            ]);
        });
    }

    /**
     * Get list of all past restaurant settlements (Archive).
     */
    public function getRestaurantSettlementHistory()
    {
        $settlements = RestaurantSettlement::with(['restaurant', 'admin'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'settlement_number' => $s->settlement_number,
                    'date' => $s->created_at->toISOString(),
                    'restaurant_name' => $s->restaurant->name ?? 'Deleted Restaurant',
                    'admin_name' => $s->admin->name ?? 'System',
                    'gross_revenue' => (float) $s->gross_revenue,
                    'system_cut' => (float) $s->system_cut,
                    'net_payable' => (float) $s->net_payable,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $settlements
        ]);
    }

    /**
     * Get specific orders linked to a restaurant settlement.
     */
    public function getRestaurantSettlementDetails($settlementId)
    {
        $settlement = RestaurantSettlement::findOrFail($settlementId);
        $restaurant = \Modules\Restaurants\Models\Restaurant::find($settlement->restaurant_id);
        $commissionRate = $restaurant ? (float) ($restaurant->commission_rate ?? 15.00) : 15.00;

        $orders = Order::where('restaurant_settlement_id', $settlementId)
            ->select('id', 'order_number', 'created_at', 'payment_method', 'total', 'delivery_fee')
            ->get()
            ->map(function ($order) use ($commissionRate) {
                // True meals revenue using formula: total - COALESCE(delivery_fee, 0)
                $mealSubtotal = (float) $order->total - (float) ($order->delivery_fee ?? 0);
                $systemCut = $mealSubtotal * ($commissionRate / 100);
                $netPayable = $mealSubtotal - $systemCut;

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number ?? ('ORD-' . $order->id),
                    'created_at' => $order->created_at->toISOString(),
                    'payment_method' => $order->payment_method,
                    'total' => $mealSubtotal, // Meals subtotal only
                    'system_cut' => $systemCut,
                    'net_payable' => $netPayable,
                    'delivery_fee' => (float) $order->delivery_fee
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }
}
