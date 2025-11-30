<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\MemberProfile;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Attendance;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\SalesItem;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get separate period filters for each chart
        $salesPeriod = $request->input('sales_period', 'month');
        $membershipPeriod = $request->input('membership_period', 'month');

        // Membership Overview
        $totalActiveMembers = MemberProfile::where('status', 'active')->count();
        $newMembersThisMonth = MemberProfile::whereMonth('start_date', Carbon::now()->month)
            ->whereYear('start_date', Carbon::now()->year)
            ->count();
        
        // Calculate monthly revenue from memberships via sales_items
        $monthlyMembershipRevenue = SalesItem::whereHas('sale', function($query) {
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->where('status', 'paid');
            })
            ->whereNotNull('plan_id')
            ->sum('sub_total');

        // Inventory Overview
        $totalItemsInStock = Inventory::sum('quantity');
        $lowStockThreshold = 10;
        $lowStockItems = Inventory::where('quantity', '<=', $lowStockThreshold)->count();

        // Gym Occupancy
        $currentOccupancy = Attendance::whereDate('check_in_time', Carbon::today())
            ->where('status', 'checked_in')
            ->count();

        // Sales Overview (Today)
        $todaySales = Sales::whereDate('created_at', Carbon::today())->count();
        $todayRevenue = Sales::whereDate('created_at', Carbon::today())
            ->where('status', 'paid')
            ->sum('total_amount');

        // Monthly Sales Statistics
        $monthlySales = Sales::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'paid')
            ->sum('total_amount');

        // Product sales only (excluding memberships)
        $monthlyProductSales = SalesItem::whereHas('sale', function($query) {
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->where('status', 'paid');
            })
            ->whereNotNull('product_id')
            ->whereNull('plan_id')
            ->sum('sub_total');

        // Transaction Breakdown
        $stockInCount = Transactions::where('type', 'stock_in')->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        $stockOutCount = Transactions::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Product Performance
        $highestSellingProduct = SalesItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereNotNull('product_id')
            ->whereNull('plan_id')
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->first();

        if ($highestSellingProduct) {
            $highestSellingProduct->load('product');
        }

        $lowestSellingProduct = SalesItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereNotNull('product_id')
            ->whereNull('plan_id')
            ->groupBy('product_id')
            ->orderBy('total_sold', 'asc')
            ->first();

        if ($lowestSellingProduct) {
            $lowestSellingProduct->load('product');
        }

        // Most Popular Membership Plan
        $mostPopularPlan = SalesItem::select('plan_id', DB::raw('COUNT(*) as total_sales'))
            ->whereNotNull('plan_id')
            ->whereHas('sale', function($query) {
                $query->where('status', 'paid');
            })
            ->groupBy('plan_id')
            ->orderBy('total_sales', 'desc')
            ->first();

        if ($mostPopularPlan) {
            $mostPopularPlan->load('plan');
        }

        // Sales Trend Chart Data - Based on its own period
        $salesTrend = $this->getSalesTrend($salesPeriod);
        
        // Membership Trend Chart Data - Based on its own period
        $membershipTrend = $this->getMembershipTrend($membershipPeriod);

        // Revenue Breakdown (This Month)
        $revenueBreakdown = [
            'products' => $monthlyProductSales,
            'memberships' => $monthlyMembershipRevenue,
            'total' => $monthlySales
        ];

        // Recent Activities
        $recentSales = Sales::with(['user', 'items.product', 'items.plan'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Sales by Type
        $productSalesCount = SalesItem::whereHas('sale', function($query) {
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->where('status', 'paid');
            })
            ->whereNotNull('product_id')
            ->whereNull('plan_id')
            ->count();

        $membershipSalesCount = SalesItem::whereHas('sale', function($query) {
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->where('status', 'paid');
            })
            ->whereNotNull('plan_id')
            ->count();

        // Active Members by Plan
        $membersByPlan = MemberProfile::with('plan')
            ->where('status', 'active')
            ->get()
            ->groupBy('plan_id')
            ->map(function($members) {
                return [
                    'count' => $members->count(),
                    'plan_name' => $members->first()->plan ? $members->first()->plan->name : 'No Plan'
                ];
            });

        // Expiring Memberships
        $expiringMemberships = MemberProfile::where('status', 'active')
            ->whereBetween('end_date', [Carbon::now(), Carbon::now()->addDays(7)])
            ->count();

        return view('admin.dashboard', compact(
            'totalActiveMembers',
            'newMembersThisMonth',
            'monthlyMembershipRevenue',
            'totalItemsInStock',
            'lowStockItems',
            'currentOccupancy',
            'todaySales',
            'todayRevenue',
            'monthlySales',
            'monthlyProductSales',
            'stockInCount',
            'stockOutCount',
            'highestSellingProduct',
            'lowestSellingProduct',
            'mostPopularPlan',
            'membershipTrend',
            'salesTrend',
            'revenueBreakdown',
            'recentSales',
            'membersByPlan',
            'expiringMemberships',
            'productSalesCount',
            'membershipSalesCount',
            'salesPeriod',
            'membershipPeriod'
        ));
    }

    private function getSalesTrend($period)
    {
        switch ($period) {
            case 'today':
                return Sales::select(
                        DB::raw('HOUR(created_at) as hour'),
                        DB::raw('SUM(total_amount) as total_revenue'),
                        DB::raw('COUNT(*) as total_sales')
                    )
                    ->whereDate('created_at', Carbon::today())
                    ->where('status', 'paid')
                    ->groupBy('hour')
                    ->orderBy('hour', 'asc')
                    ->get()
                    ->map(function($item) {
                        $item->label = Carbon::today()->setHour($item->hour)->format('h A');
                        return $item;
                    });

            case 'week':
                return Sales::select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(total_amount) as total_revenue'),
                        DB::raw('COUNT(*) as total_sales')
                    )
                    ->where('created_at', '>=', Carbon::now()->subDays(7))
                    ->where('status', 'paid')
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get()
                    ->map(function($item) {
                        $item->label = Carbon::parse($item->date)->format('D, M d');
                        return $item;
                    });

            case 'month':
            default:
                return Sales::select(
                        DB::raw('YEARWEEK(created_at, 1) as week'),
                        DB::raw('DATE(MIN(created_at)) as week_start'),
                        DB::raw('SUM(total_amount) as total_revenue'),
                        DB::raw('COUNT(*) as total_sales')
                    )
                    ->where('created_at', '>=', Carbon::now()->subWeeks(4))
                    ->where('status', 'paid')
                    ->groupBy('week')
                    ->orderBy('week', 'asc')
                    ->get()
                    ->map(function($item) {
                        $weekStart = Carbon::parse($item->week_start);
                        $weekEnd = $weekStart->copy()->addDays(6);
                        $item->label = $weekStart->format('M d') . ' - ' . $weekEnd->format('M d');
                        return $item;
                    });
        }
    }

    private function getMembershipTrend($period)
    {
        switch ($period) {
            case 'today':
                return MemberProfile::select(
                        DB::raw('HOUR(start_date) as hour'),
                        DB::raw('COUNT(*) as count')
                    )
                    ->whereDate('start_date', Carbon::today())
                    ->whereNotNull('start_date')
                    ->groupBy('hour')
                    ->orderBy('hour', 'asc')
                    ->get()
                    ->map(function($item) {
                        $item->label = Carbon::today()->setHour($item->hour)->format('h A');
                        return $item;
                    });

            case 'week':
                return MemberProfile::select(
                        DB::raw('DATE(start_date) as date'),
                        DB::raw('COUNT(*) as count')
                    )
                    ->where('start_date', '>=', Carbon::now()->subDays(7))
                    ->whereNotNull('start_date')
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get()
                    ->map(function($item) {
                        $item->label = Carbon::parse($item->date)->format('D, M d');
                        return $item;
                    });

            case 'month':
            default:
                return MemberProfile::select(
                        DB::raw('YEARWEEK(start_date, 1) as week'),
                        DB::raw('DATE(MIN(start_date)) as week_start'),
                        DB::raw('COUNT(*) as count')
                    )
                    ->where('start_date', '>=', Carbon::now()->subWeeks(4))
                    ->whereNotNull('start_date')
                    ->groupBy('week')
                    ->orderBy('week', 'asc')
                    ->get()
                    ->map(function($item) {
                        $weekStart = Carbon::parse($item->week_start);
                        $weekEnd = $weekStart->copy()->addDays(6);
                        $item->label = $weekStart->format('M d') . ' - ' . $weekEnd->format('M d');
                        return $item;
                    });
        }
    }
}