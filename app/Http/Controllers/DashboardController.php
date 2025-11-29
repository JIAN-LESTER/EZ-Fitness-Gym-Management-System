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
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
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

        // Sales Overview (Today) - Including both product and membership sales
        $todaySales = Sales::whereDate('created_at', Carbon::today())->count();
        $todayRevenue = Sales::whereDate('created_at', Carbon::today())
            ->where('status', 'paid')
            ->sum('total_amount');

        // Monthly Sales Statistics - Total from all sales
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
        $stockInCount = Transactions::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        $stockOutCount = Transactions::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Product Performance (Excluding Memberships)
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

        // Sales Trend Chart Data (Last 6 months) - Combined
        $salesTrend = Sales::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('COUNT(*) as total_sales')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(1))
            ->where('status', 'paid')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Membership Trend Chart Data (Last 6 months)
        $membershipTrend = MemberProfile::select(
                DB::raw('DATE_FORMAT(start_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('start_date', '>=', Carbon::now()->subMonths(1))
            ->whereNotNull('start_date')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Revenue Breakdown (This Month)
        $revenueBreakdown = [
            'products' => $monthlyProductSales,
            'memberships' => $monthlyMembershipRevenue,
            'total' => $monthlySales
        ];

        // Recent Activities - Combined Sales
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
            'membershipSalesCount'
        ));
    }
}