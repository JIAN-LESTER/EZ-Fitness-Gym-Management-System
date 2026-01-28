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
use App\Models\Subscriptions;
use App\Models\Branches;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get selected branch from session (null means "All Branches")
        $selectedBranchId = session('selected_branch_id');
        
        // Get separate period filters for each chart
        $salesPeriod = $request->input('sales_period', 'month');
        $membershipPeriod = $request->input('membership_period', 'month');

        // === BRANCH STATISTICS ===
        if (auth()->user()->role === 'super_admin') {
            $totalBranches = Branches::count();
            $activeBranches = Branches::
                whereHas('users', function($query) {
                    $query->where('created_at', '>=', Carbon::now()->subDays(30));
                })
                ->count();
        } else {
            $totalBranches = null;
            $activeBranches = null;
        }

        // === MEMBERSHIP OVERVIEW (with branch filter) ===
        $totalActiveMembers = MemberProfile::where('status', 'active')
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->count();
            
        $newMembersThisMonth = MemberProfile::whereMonth('start_date', Carbon::now()->month)
            ->whereYear('start_date', Carbon::now()->year)
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->count();

        // Calculate monthly revenue from memberships via sales_items
        $monthlyMembershipRevenue = SalesItem::whereHas('sale', function ($query) use ($selectedBranchId) {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('status', 'paid')
                ->when($selectedBranchId, function($q) use ($selectedBranchId) {
                    $q->where('branch_id', $selectedBranchId);
                });
        })
            ->whereNotNull('plan_id')
            ->sum('sub_total');

        // === INVENTORY OVERVIEW (with branch filter) ===
        $totalItemsInStock = Inventory::when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->sum('quantity');
            
        $lowStockThreshold = 10;
        $lowStockItems = Inventory::where('quantity', '<=', $lowStockThreshold)
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->count();

        // === GYM OCCUPANCY (with branch filter) ===
        $currentOccupancy = Attendance::whereDate('check_in_time', Carbon::today())
            ->where('status', 'checked_in')
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->count();

        // === SALES OVERVIEW (with branch filter) ===
        $todaySales = Sales::whereDate('created_at', Carbon::today())
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->count();
            
        $todayRevenue = Sales::whereDate('created_at', Carbon::today())
            ->where('status', 'paid')
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->sum('total_amount');

        // === MONTHLY SALES STATISTICS (with branch filter) ===
        $monthlySales = Sales::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'paid')
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->sum('total_amount');

        // Product sales only (excluding memberships)
        $monthlyProductSales = SalesItem::whereHas('sale', function ($query) use ($selectedBranchId) {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('status', 'paid')
                ->when($selectedBranchId, function($q) use ($selectedBranchId) {
                    $q->where('branch_id', $selectedBranchId);
                });
        })
            ->whereNotNull('product_id')
            ->whereNull('plan_id')
            ->sum('sub_total');

        // === SUBSCRIPTION STATISTICS (with branch filter) ===
        $totalSubscriptions = Subscriptions::when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->count();
            
        $activeSubscriptions = MemberProfile::where('status', 'active')
            ->whereNotNull('subscription_id')
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->count();

        // === TRANSACTION BREAKDOWN (with branch filter) ===
        $stockInCount = Transactions::where('type', 'stock_in')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->count();

        $stockOutCount = Transactions::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->count();

        // === PRODUCT PERFORMANCE (with branch filter) ===
        $highestSellingProduct = SalesItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereNotNull('product_id')
            ->whereNull('plan_id')
            ->whereHas('sale', function($query) use ($selectedBranchId) {
                $query->when($selectedBranchId, function($q) use ($selectedBranchId) {
                    $q->where('branch_id', $selectedBranchId);
                });
            })
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->first();

        if ($highestSellingProduct) {
            $highestSellingProduct->load('product');
        }

        $lowestSellingProduct = SalesItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->whereNotNull('product_id')
            ->whereNull('plan_id')
            ->whereHas('sale', function($query) use ($selectedBranchId) {
                $query->when($selectedBranchId, function($q) use ($selectedBranchId) {
                    $q->where('branch_id', $selectedBranchId);
                });
            })
            ->groupBy('product_id')
            ->orderBy('total_sold', 'asc')
            ->first();

        if ($lowestSellingProduct) {
            $lowestSellingProduct->load('product');
        }

        // === MOST POPULAR PLAN (with branch filter) ===
        $mostPopularPlan = SalesItem::select('plan_id', DB::raw('COUNT(*) as total_sales'))
            ->whereNotNull('plan_id')
            ->whereHas('sale', function ($query) use ($selectedBranchId) {
                $query->where('status', 'paid')
                    ->when($selectedBranchId, function($q) use ($selectedBranchId) {
                        $q->where('branch_id', $selectedBranchId);
                    });
            })
            ->groupBy('plan_id')
            ->orderBy('total_sales', 'desc')
            ->first();

        if ($mostPopularPlan) {
            $mostPopularPlan->load('plan');
        }

        // === MOST POPULAR SUBSCRIPTION (with branch filter) ===
        $mostPopularSubscription = Subscriptions::select('subscriptions.*', DB::raw('COUNT(member_profiles.member_id) as member_count'))
            ->leftJoin('member_profiles', 'subscriptions.subscription_id', '=', 'member_profiles.subscription_id')
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('subscriptions.branch_id', $selectedBranchId);
            })
            ->groupBy('subscriptions.subscription_id', 'subscriptions.branch_id', 'subscriptions.name', 'subscriptions.details', 'subscriptions.price', 'subscriptions.duration_days', 'subscriptions.created_at', 'subscriptions.updated_at')
            ->orderBy('member_count', 'desc')
            ->first();

        // === CHART DATA (with branch filter) ===
        $salesTrend = $this->getSalesTrend($salesPeriod, $selectedBranchId);
        $membershipTrend = $this->getMembershipTrend($membershipPeriod, $selectedBranchId);

        // === REVENUE BREAKDOWN (with branch filter) ===
        $revenueBreakdown = [
            'products' => $monthlyProductSales,
            'memberships' => $monthlyMembershipRevenue,
            'total' => $monthlySales
        ];

        // === RECENT SALES (with branch filter) ===
        $recentSales = Sales::with(['user', 'items.product', 'items.plan', 'branch'])
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // === SALES BY TYPE (with branch filter) ===
        $productSalesCount = SalesItem::whereHas('sale', function ($query) use ($selectedBranchId) {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('status', 'paid')
                ->when($selectedBranchId, function($q) use ($selectedBranchId) {
                    $q->where('branch_id', $selectedBranchId);
                });
        })
            ->whereNotNull('product_id')
            ->whereNull('plan_id')
            ->count();

        $membershipSalesCount = SalesItem::whereHas('sale', function ($query) use ($selectedBranchId) {
            $query->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('status', 'paid')
                ->when($selectedBranchId, function($q) use ($selectedBranchId) {
                    $q->where('branch_id', $selectedBranchId);
                });
        })
            ->whereNotNull('plan_id')
            ->count();

        // === MEMBERS BY PLAN (with branch filter) ===
        $membersByPlan = MemberProfile::with('plan')
            ->where('status', 'active')
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->get()
            ->groupBy('plan_id')
            ->map(function ($members) {
                return [
                    'count' => $members->count(),
                    'plan_name' => $members->first()->plan ? $members->first()->plan->name : 'No Plan'
                ];
            });

        // === SUBSCRIPTIONS BY TYPE (with branch filter) ===
        $subscriptionsByType = Subscriptions::select('subscriptions.*', DB::raw('COUNT(member_profiles.member_id) as member_count'))
            ->leftJoin('member_profiles', 'subscriptions.subscription_id', '=', 'member_profiles.subscription_id')
            ->where('member_profiles.status', 'active')
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('subscriptions.branch_id', $selectedBranchId);
            })
            ->groupBy('subscriptions.subscription_id', 'subscriptions.branch_id', 'subscriptions.name', 'subscriptions.details', 'subscriptions.price', 'subscriptions.duration_days', 'subscriptions.created_at', 'subscriptions.updated_at')
            ->orderBy('member_count', 'desc')
            ->get()
            ->map(function($subscription) {
                return [
                    'subscription_name' => $subscription->name,
                    'count' => $subscription->member_count
                ];
            });

        // === EXPIRING MEMBERSHIPS (with branch filter) ===
        $expiringMemberships = MemberProfile::where('status', 'active')
            ->whereBetween('end_date', [Carbon::now(), Carbon::now()->addDays(7)])
            ->when($selectedBranchId, function($query) use ($selectedBranchId) {
                $query->where('branch_id', $selectedBranchId);
            })
            ->count();

        // === BRANCH PERFORMANCE (only for super_admin) ===
        $branchPerformance = [];
        if (auth()->user()->role === 'super_admin' && !$selectedBranchId) {
            $branchPerformance = Branches::select('branches.*')
                ->withCount(['users as member_count' => function($query) {
                    $query->whereHas('member', function($q) {
                        $q->where('status', 'active');
                    });
                }])
                ->with(['sales' => function($query) {
                    $query->whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year)
                          ->where('status', 'paid');
                }])
             
                ->get()
                ->map(function($branch) {
                    return [
                        'branch_id' => $branch->branch_id,
                        'name' => $branch->name,
                        'member_count' => $branch->member_count,
                        'monthly_revenue' => $branch->sales->sum('total_amount'),
                    ];
                });
        }

        // Check if this is an AJAX request
        if ($request->ajax() || $request->get('ajax')) {
            return response()->json([
                'salesTrend' => $salesTrend,
                'membershipTrend' => $membershipTrend,
            ]);
        }

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
            'mostPopularSubscription',
            'membershipTrend',
            'salesTrend',
            'revenueBreakdown',
            'recentSales',
            'membersByPlan',
            'subscriptionsByType',
            'expiringMemberships',
            'productSalesCount',
            'membershipSalesCount',
            'salesPeriod',
            'membershipPeriod',
            'totalBranches',
            'activeBranches',
            'totalSubscriptions',
            'activeSubscriptions',
            'branchPerformance',
            'selectedBranchId'
        ));
    }

    private function getSalesTrend($period, $branchId = null)
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
                    ->when($branchId, function($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->groupBy('hour')
                    ->orderBy('hour', 'asc')
                    ->get()
                    ->map(function ($item) {
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
                    ->when($branchId, function($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get()
                    ->map(function ($item) {
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
                    ->when($branchId, function($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->groupBy('week')
                    ->orderBy('week', 'asc')
                    ->get()
                    ->map(function ($item) {
                        $weekStart = Carbon::parse($item->week_start);
                        $weekEnd = $weekStart->copy()->addDays(6);
                        $item->label = $weekStart->format('M d') . ' - ' . $weekEnd->format('M d');
                        return $item;
                    });
        }
    }

    private function getMembershipTrend($period, $branchId = null)
    {
        switch ($period) {
            case 'today':
                return MemberProfile::select(
                    DB::raw('HOUR(start_date) as hour'),
                    DB::raw('COUNT(*) as count')
                )
                    ->whereDate('start_date', Carbon::today())
                    ->whereNotNull('start_date')
                    ->when($branchId, function($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->groupBy('hour')
                    ->orderBy('hour', 'asc')
                    ->get()
                    ->map(function ($item) {
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
                    ->when($branchId, function($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->groupBy('date')
                    ->orderBy('date', 'asc')
                    ->get()
                    ->map(function ($item) {
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
                    ->when($branchId, function($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })
                    ->groupBy('week')
                    ->orderBy('week', 'asc')
                    ->get()
                    ->map(function ($item) {
                        $weekStart = Carbon::parse($item->week_start);
                        $weekEnd = $weekStart->copy()->addDays(6);
                        $item->label = $weekStart->format('M d') . ' - ' . $weekEnd->format('M d');
                        return $item;
                    });
        }
    }
}