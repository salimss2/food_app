<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Orders\Models\Order;
use Modules\Restaurants\Models\Restaurant;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    private function applyFilters($query, Request $request)
    {
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }
        if ($request->filled('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }
        return $query;
    }

    public function index(Request $request)
    {
        $restaurants = Restaurant::select('id', 'name')->get();

        // Base Query
        $baseQuery = Order::where('payment_status', 'completed');
        $this->applyFilters($baseQuery, $request);

        // 1. KPI Metrics (Optimized single query)
        $kpiQuery = (clone $baseQuery)->selectRaw("
            SUM(total) as total_sales,
            COUNT(id) as orders_count,
            COUNT(DISTINCT user_id) as active_customers,
            SUM(CASE WHEN payment_method = 'bank_transfer' THEN total ELSE 0 END) as bank_transfer_total,
            SUM(CASE WHEN payment_method = 'cash' THEN total ELSE 0 END) as cash_total
        ")->first();

        $totalSales = $kpiQuery->total_sales ?? 0;
        $ordersCount = $kpiQuery->orders_count ?? 0;
        $averageOrderValue = $ordersCount > 0 ? $totalSales / $ordersCount : 0;
        $activeCustomers = $kpiQuery->active_customers ?? 0;
        $platformRevenue = $totalSales * 0.10;
        $pendingRestaurantPayouts = ($kpiQuery->bank_transfer_total ?? 0) * 0.90;
        $pendingDriverCash = $kpiQuery->cash_total ?? 0;

        // 2. Chart Data (Last 6 Months - Optimized single query)
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $chartDataQuery = (clone $baseQuery)
            ->where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month_key,
                SUM(total) as gmv,
                SUM(CASE WHEN payment_method = 'bank_transfer' THEN total ELSE 0 END) as payouts_base,
                SUM(delivery_fee) as driver_earnings
            ")
            ->groupBy('month_key')
            ->get();

        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthKey = $monthStart->format('Y-m');

            $monthRecord = $chartDataQuery->firstWhere('month_key', $monthKey);

            $gmv = $monthRecord->gmv ?? 0;
            $revenue = $gmv * 0.10;
            $payouts = ($monthRecord->payouts_base ?? 0) * 0.90;
            $driverEarnings = $monthRecord->driver_earnings ?? 0;

            $chartData[] = [
                'month_name' => $monthStart->format('M'),
                'gmv' => $gmv,
                'revenue' => $revenue,
                'payouts' => $payouts,
                'driver_earnings' => $driverEarnings,
            ];
        }

        // 3. Ledgers Table (Grouped by month)
        $ledgersQuery = (clone $baseQuery)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as report_date, COUNT(id) as transactions_count, SUM(total) as recorded_volume")
            ->groupBy('report_date')
            ->orderBy('report_date', 'desc');

        $ledgers = $ledgersQuery->paginate(10);

        return view('admin::reports', compact(
            'restaurants',
            'totalSales',
            'ordersCount',
            'averageOrderValue',
            'activeCustomers',
            'platformRevenue',
            'pendingRestaurantPayouts',
            'pendingDriverCash',
            'chartData',
            'ledgers'
        ));
    }

    public function exportCsv(Request $request)
    {
        $baseQuery = Order::with('restaurant')->where('payment_status', 'completed');
        $this->applyFilters($baseQuery, $request);
        $orders = $baseQuery->latest()->get();

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=financial_reports_" . date('Y-m-d_H-i-s') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 Arabic support in Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, ['رقم الطلب', 'التاريخ', 'المطعم', 'طريقة الدفع', 'المبلغ']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number ?? $order->id,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->restaurant->name ?? 'N/A',
                    $order->payment_method == 'bank_transfer' ? 'حوالة بنكية' : ($order->payment_method == 'cash' ? 'كاش' : $order->payment_method),
                    number_format($order->total, 2)
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $baseQuery = Order::with('restaurant')->where('payment_status', 'completed');
        $this->applyFilters($baseQuery, $request);
        $orders = $baseQuery->latest()->get();

        // Print-optimized HTML Layout
        $html = '<!DOCTYPE html>
        <html dir="rtl" lang="ar">
        <head>
            <meta charset="UTF-8">
            <title>تقرير مالي</title>
            <style>
                body { font-family: "DejaVu Sans", sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
                th { background-color: #f2f2f2; }
                h2 { text-align: center; margin-bottom: 20px; }
            </style>
        </head>
        <body onload="window.print()">
            <h2>التقرير المالي - منصة التوصيل</h2>
            <p>تاريخ الإصدار: ' . now()->format('Y-m-d H:i') . '</p>
            <table>
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>التاريخ</th>
                        <th>المطعم</th>
                        <th>طريقة الدفع</th>
                        <th>المبلغ</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($orders as $order) {
            $method = $order->payment_method == 'bank_transfer' ? 'حوالة بنكية' : ($order->payment_method == 'cash' ? 'كاش' : $order->payment_method);
            $restaurantName = htmlspecialchars($order->restaurant->name ?? 'N/A');
            $html .= "<tr>
                <td>{$order->order_number}</td>
                <td>{$order->created_at->format('Y-m-d H:i')}</td>
                <td>{$restaurantName}</td>
                <td>{$method}</td>
                <td>$" . number_format($order->total, 2) . "</td>
            </tr>";
        }

        $html .= '</tbody></table></body></html>';

        return response($html)->header('Content-Type', 'text/html');
    }
}
