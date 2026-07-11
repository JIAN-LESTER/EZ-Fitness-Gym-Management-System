<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    /**
     * Display staff dashboard with key metrics
     * Shows today's sales, transactions, check-ins, and inventory alerts
     */
    public function dashboard()
    {
        $user = Auth::user();

        return view('staff.dashboard', compact(
            'user',
        ));
    }

    /**
     * Get quick stats for dashboard cards (AJAX endpoint)
     * Used for real-time dashboard updates
     */
    public function getQuickStats()
    {
        $today = Carbon::today();

        $stats = [
            'today_sales' => DB::table('sales')
                ->whereDate('created_at', $today)
                ->where('status', 'paid')
                ->sum('total_amount'),

            'today_orders' => DB::table('sales')
                ->whereDate('created_at', $today)
                ->where('status', 'paid')
                ->count(),

            'today_checkins' => DB::table('attendance_logs')
                ->whereDate('check_in', $today)
                ->count(),

            'low_stock_items' => DB::table('products')
                ->where('stock_quantity', '<=', 10)
                ->where('stock_quantity', '>', 0)
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get sales chart data for different periods
     * Supports week, month, year views for sales analytics
     */
    public function getSalesChartData(Request $request)
    {
        $period = $request->get('period', 'week');

        $query = DB::table('sales')->where('status', 'paid');

        switch ($period) {
            case 'week':
                $startDate = Carbon::now()->subWeek();
                $salesData = $query->where('created_at', '>=', $startDate)
                    ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                break;

            case 'month':
                $startDate = Carbon::now()->subMonth();
                $salesData = $query->where('created_at', '>=', $startDate)
                    ->select(DB::raw('EXTRACT(WEEK FROM created_at) as week'), DB::raw('SUM(total_amount) as total'))
                    ->groupBy('week')
                    ->orderBy('week')
                    ->get();
                break;

            case 'year':
                $startDate = Carbon::now()->subYear();
                $salesData = $query->where('created_at', '>=', $startDate)
                    ->select(DB::raw('EXTRACT(MONTH FROM created_at) as month'), DB::raw('SUM(total_amount) as total'))
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
                break;

            default:
                $salesData = collect();
        }

        return response()->json($salesData);
    }

    /**
     * Get recent activity feed for dashboard
     * Combines sales and check-in activities
     */
    public function getRecentActivity()
    {
        $activities = [];

        // Recent sales
        $recentSales = DB::table('sales')
            ->join('users', 'sales.user_id', '=', 'users.user_id')
            ->select('sales.*', 'users.first_name', 'users.last_name')
            ->where('sales.status', 'paid')
            ->orderBy('sales.created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($sale) {
                return [
                    'type' => 'sale',
                    'message' => "New sale #{$sale->sales_id} by {$sale->first_name} {$sale->last_name}",
                    'amount' => $sale->total_amount,
                    'time' => Carbon::parse($sale->created_at)->diffForHumans(),
                    'icon' => '💰',
                ];
            });

        // Recent check-ins
        $recentCheckins = DB::table('attendance_logs')
            ->join('member_profiles', 'attendance_logs.member_id', '=', 'member_profiles.member_id')
            ->join('users', 'member_profiles.user_id', '=', 'users.user_id')
            ->select('attendance_logs.*', 'users.first_name', 'users.last_name')
            ->whereDate('check_in', Carbon::today())
            ->orderBy('check_in', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($checkin) {
                return [
                    'type' => 'checkin',
                    'message' => "{$checkin->first_name} {$checkin->last_name} checked in",
                    'time' => Carbon::parse($checkin->check_in)->diffForHumans(),
                    'icon' => '✅',
                ];
            });

        // Merge and sort all activities
        $activities = $recentSales->merge($recentCheckins)
            ->sortByDesc(function ($activity) {
                return $activity['time'];
            })
            ->take(10)
            ->values();

        return response()->json($activities);
    }

    /**
     * Get inventory alerts for low stock and out of stock items
     * Used for inventory management alerts
     */
    public function getInventoryAlerts()
    {
        $alerts = [];

        // Low stock products
        $lowStock = DB::table('products')
            ->where('stock_quantity', '<=', 10)
            ->where('stock_quantity', '>', 0)
            ->select('name', 'stock_quantity')
            ->get()
            ->map(function ($product) {
                return [
                    'type' => 'low_stock',
                    'message' => "{$product->name} is low on stock ({$product->stock_quantity} left)",
                    'severity' => 'warning',
                ];
            });

        // Out of stock products
        $outOfStock = DB::table('products')
            ->where('stock_quantity', '<=', 0)
            ->select('name')
            ->get()
            ->map(function ($product) {
                return [
                    'type' => 'out_of_stock',
                    'message' => "{$product->name} is out of stock",
                    'severity' => 'danger',
                ];
            });

        $alerts = $lowStock->merge($outOfStock);

        return response()->json($alerts);
    }

    /**
     * Staff-only product management index
     * Shows products with pagination and search
     */
    public function productsIndex(Request $request)
    {
        $query = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.category_id')
            ->select('products.*', 'categories.name as category_name');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('products.name', 'like', "%{$search}%")
                ->orWhere('categories.name', 'like', "%{$search}%");
        }

        $products = $query->orderBy('products.created_at', 'desc')
            ->paginate(10);

        return view('staff.products.index', compact('products'));
    }

    /**
     * Staff sales report view
     * Shows sales with filtering options
     */
    public function salesReport(Request $request)
    {
        $query = DB::table('sales')
            ->join('users', 'sales.user_id', '=', 'users.user_id')
            ->select('sales.*', 'users.first_name', 'users.last_name')
            ->where('sales.status', 'paid');

        // Date filtering
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('sales.created_at', [
                $request->start_date,
                $request->end_date,
            ]);
        }

        $sales = $query->orderBy('sales.created_at', 'desc')
            ->paginate(15);

        return view('staff.sales.report', compact('sales'));
    }
}
