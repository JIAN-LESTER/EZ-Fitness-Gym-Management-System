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
        
        // Calculate monthly revenue from memberships based on plan prices
        $monthlyMembershipRevenue = MemberProfile::with('plan')
            ->where('status', 'active')
            ->whereMonth('start_date', Carbon::now()->month)
            ->whereYear('start_date', Carbon::now()->year)
            ->get()
            ->sum(function($member) {
                return $member->plan ? $member->plan->price : 0;
            });

        // Inventory Overview
        $totalItemsInStock = Inventory::sum('quantity');
        $lowStockThreshold = 10; // Define your threshold
        $lowStockItems = Inventory::where('quantity', '<=', $lowStockThreshold)->count();

        // Gym Occupancy (Current members in gym)
        $currentOccupancy = Attendance::whereDate('check_in_time', Carbon::today())
            ->where('status', 'checked_in')
            ->count();

        // Sales Overview (Today)
        $todaySales = Sales::whereDate('created_at', Carbon::today())->count();
        $todayRevenue = Sales::whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('total_amount');

        // Monthly Sales Statistics
        $monthlySales = Sales::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'completed')
            ->sum('total_amount');

        // Transaction Breakdown (This Month)
        $stockInCount = Transactions::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        $stockOutCount = Transactions::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Product Performance
 $highestSellingProduct = SalesItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->first();

        // Load product relationship if exists
        if ($highestSellingProduct) {
            $highestSellingProduct->load('product');
        }

        $lowestSellingProduct = SalesItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderBy('total_sold', 'asc')
            ->first();

        // Load product relationship if exists
        if ($lowestSellingProduct) {
            $lowestSellingProduct->load('product');
        }

        // Membership Trend Chart Data (Last 6 months) - using start_date
        $membershipTrend = MemberProfile::select(
                DB::raw('DATE_FORMAT(start_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('start_date', '>=', Carbon::now()->subMonths(6))
            ->whereNotNull('start_date')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Revenue Breakdown (This Month)
        $revenueBreakdown = [
            'sales' => $monthlySales,
            'memberships' => $monthlyMembershipRevenue,
            'total' => $monthlySales + $monthlyMembershipRevenue
        ];

        // Recent Activities
        $recentSales = Sales::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Active Members by Plan (for additional insight)
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

        // Expiring Memberships (Next 7 days)
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
            'stockInCount',
            'stockOutCount',
            'highestSellingProduct',
            'lowestSellingProduct',
            'membershipTrend',
            'revenueBreakdown',
            'recentSales',
            'membersByPlan',
            'expiringMemberships'
        ));
    }
}