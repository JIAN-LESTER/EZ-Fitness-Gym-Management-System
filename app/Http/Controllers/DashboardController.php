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
use App\Models\User;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedBranchId = session('selected_branch_id');
        $salesPeriod = $request->input('sales_period', 'month');
        $membershipPeriod = $request->input('membership_period', 'month');
        $subscriptionPeriod = $request->input('subscription_period', 'month');

        // === CACHED BRANCH STATISTICS (Super Admin Only) ===
        if (auth()->user()->role === 'super_admin') {
            $branchStats = $this->getBranchStats();
            extract($branchStats);
        } else {
            $totalBranches = null;
            $activeBranches = null;
            $totalUsers = null;
            $activeUsers = null;
        }

        // === CACHED MEMBERSHIP OVERVIEW ===
        $membershipOverview = $this->getMembershipOverview($selectedBranchId);
        extract($membershipOverview);

        // === CACHED SUBSCRIPTION STATISTICS ===
        $subscriptionStats = $this->getSubscriptionStats($selectedBranchId);
        extract($subscriptionStats);

        // === CACHED INVENTORY OVERVIEW ===
        $inventoryStats = $this->getInventoryStats($selectedBranchId);
        extract($inventoryStats);

        // === REAL-TIME GYM OCCUPANCY (Short cache) ===
        $currentOccupancy = CacheService::remember(
            'occupancy',
            'realtime',
            fn() => Attendance::whereDate('check_in_time', Carbon::today())
                ->where('status', 'checked_in')
                ->when($selectedBranchId, fn($q) => $q->where('branch_id', $selectedBranchId))
                ->count(),
            'current'
        );

        // === CACHED SALES OVERVIEW ===
        $salesOverview = $this->getSalesOverview($selectedBranchId);
        extract($salesOverview);

        // === CACHED MONTHLY SALES STATISTICS ===
        $monthlySalesStats = $this->getMonthlySalesStats($selectedBranchId);
        extract($monthlySalesStats);

        // === CACHED TRANSACTION BREAKDOWN ===
        $transactionStats = $this->getTransactionStats($selectedBranchId);
        extract($transactionStats);

        // === CACHED PRODUCT PERFORMANCE ===
        $productPerformance = $this->getProductPerformance($selectedBranchId);
        extract($productPerformance);

        // === CACHED PLAN & SUBSCRIPTION PERFORMANCE ===
        $planPerformance = $this->getPlanPerformance($selectedBranchId);
        extract($planPerformance);

        // === CHART DATA (Cached separately by period) ===
        $salesTrend = $this->getSalesTrend($salesPeriod, $selectedBranchId);
        $membershipTrend = $this->getMembershipTrend($membershipPeriod, $selectedBranchId);
        $subscriptionTrend = $this->getSubscriptionTrend($subscriptionPeriod, $selectedBranchId);

        // === CACHED REVENUE BREAKDOWN ===
        $revenueBreakdown = [
            'products' => $monthlyProductSales,
            'memberships' => $monthlyMembershipRevenue,
            'subscriptions' => $monthlySubscriptionRevenue,
            'total' => $monthlySales + $monthlySubscriptionRevenue
        ];

        // === RECENT SALES (Short cache) ===
        $recentSales = CacheService::remember(
            'recent_sales',
            'realtime',
            fn() => Sales::with(['user', 'items.product', 'items.plan', 'branch'])
                ->when($selectedBranchId, fn($q) => $q->where('branch_id', $selectedBranchId))
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        );

        // === CACHED SALES BY TYPE ===
        $salesByType = $this->getSalesByType($selectedBranchId);
        extract($salesByType);

        // === CACHED SUBSCRIPTIONS BY TYPE ===
        $subscriptionsByType = CacheService::remember(
            'subscriptions_by_type',
            'hourly',
            fn() => Subscriptions::select('subscriptions.*', DB::raw('COUNT(member_profiles.member_id) as member_count'))
                ->leftJoin('member_profiles', 'subscriptions.subscription_id', '=', 'member_profiles.subscription_id')
                ->where('member_profiles.status', 'active')
                ->when($selectedBranchId, fn($q) => $q->where('subscriptions.branch_id', $selectedBranchId))
                ->groupBy('subscriptions.subscription_id', 'subscriptions.branch_id', 'subscriptions.name', 
                         'subscriptions.details', 'subscriptions.price', 'subscriptions.duration_days', 
                         'subscriptions.created_at', 'subscriptions.updated_at')
                ->orderBy('member_count', 'desc')
                ->get()
                ->map(fn($subscription) => [
                    'subscription_name' => $subscription->name,
                    'count' => $subscription->member_count
                ])
        );

        // === CACHED EXPIRING MEMBERSHIPS ===
        $expiringMemberships = CacheService::remember(
            'expiring_memberships',
            'hourly',
            fn() => MemberProfile::where('status', 'active')
                ->whereBetween('end_date', [Carbon::now(), Carbon::now()->addDays(7)])
                ->when($selectedBranchId, fn($q) => $q->where('branch_id', $selectedBranchId))
                ->count()
        );

        // === BRANCH PERFORMANCE (Super Admin Only) ===
        $branchPerformance = [];
        if (auth()->user()->role === 'super_admin' && !$selectedBranchId) {
            $branchPerformance = $this->getBranchPerformance();
        }

        // Check if AJAX request for chart updates
        if ($request->ajax() || $request->get('ajax')) {
            return response()->json([
                'salesTrend' => $salesTrend,
                'membershipTrend' => $membershipTrend,
                'subscriptionTrend' => $subscriptionTrend,
            ]);
        }

        return view('admin.dashboard', compact(
            'totalActiveMembers',
            'newMembersThisMonth',
            'monthlyMembershipRevenue',
            'monthlySubscriptionRevenue',
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
            'subscriptionTrend',
            'revenueBreakdown',
            'recentSales',
            'subscriptionsByType',
            'expiringMemberships',
            'productSalesCount',
            'membershipSalesCount',
            'salesPeriod',
            'membershipPeriod',
            'subscriptionPeriod',
            'totalBranches',
            'activeBranches',
            'totalSubscriptions',
            'activeSubscriptions',
            'branchPerformance',
            'selectedBranchId',
            'totalUsers',
            'activeUsers'
        ));
    }

    // ========== CACHED DATA METHODS ==========

    private function getBranchStats(): array
    {
        return CacheService::remember('branch_stats', 'stats', function() {
            return [
                'totalBranches' => Branches::count(),
                'activeBranches' => Branches::whereHas('users', function ($query) {
                    $query->where('created_at', '>=', Carbon::now()->subDays(30));
                })->count(),
                'totalUsers' => User::count(),
                'activeUsers' => User::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            ];
        });
    }

    private function getMembershipOverview($branchId): array
    {
        return CacheService::remember('membership_overview', 'stats', function() use ($branchId) {
            return [
                'totalActiveMembers' => MemberProfile::where('status', 'active')
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->count(),
                'newMembersThisMonth' => MemberProfile::whereMonth('start_date', Carbon::now()->month)
                    ->whereYear('start_date', Carbon::now()->year)
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->count(),
            ];
        }, $branchId);
    }

    private function getSubscriptionStats($branchId): array
    {
        return CacheService::remember('subscription_stats', 'stats', function() use ($branchId) {
            $totalSubscriptions = Subscriptions::when($branchId, fn($q) => $q->where('branch_id', $branchId))->count();
            
            $activeSubscriptions = MemberProfile::where('status', 'active')
                ->whereNotNull('subscription_id')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->count();

            $monthlySubscriptionRevenue = MemberProfile::where('status', 'active')
                ->whereNotNull('member_profiles.subscription_id')
                ->whereMonth('start_date', '<=', Carbon::now()->month)
                ->whereYear('start_date', '<=', Carbon::now()->year)
                ->when($branchId, fn($q) => $q->where('member_profiles.branch_id', $branchId))
                ->join('subscriptions', 'member_profiles.subscription_id', '=', 'subscriptions.subscription_id')
                ->sum('subscriptions.price');

            return compact('totalSubscriptions', 'activeSubscriptions', 'monthlySubscriptionRevenue');
        }, $branchId);
    }

    private function getInventoryStats($branchId): array
    {
        return CacheService::remember('inventory_stats', 'hourly', function() use ($branchId) {
            $lowStockThreshold = 10;
            
            return [
                'totalItemsInStock' => Inventory::when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->sum('quantity'),
                'lowStockItems' => Inventory::where('quantity', '<=', $lowStockThreshold)
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->count(),
            ];
        }, $branchId);
    }

    private function getSalesOverview($branchId): array
    {
        return CacheService::remember('sales_overview', 'realtime', function() use ($branchId) {
            return [
                'todaySales' => Sales::whereDate('created_at', Carbon::today())
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->count(),
                'todayRevenue' => Sales::whereDate('created_at', Carbon::today())
                    ->where('status', 'paid')
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->sum('total_amount'),
            ];
        }, $branchId);
    }

    private function getMonthlySalesStats($branchId): array
    {
        return CacheService::remember('monthly_sales_stats', 'stats', function() use ($branchId) {
            $monthlySales = Sales::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('status', 'paid')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->sum('total_amount');

            $monthlyProductSales = SalesItem::whereHas('sale', function ($query) use ($branchId) {
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->where('status', 'paid')
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId));
            })
                ->whereNotNull('product_id')
                ->whereNull('plan_id')
                ->sum('sub_total');

            $monthlyMembershipRevenue = SalesItem::whereHas('sale', function ($query) use ($branchId) {
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->where('status', 'paid')
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId));
            })
                ->whereNotNull('plan_id')
                ->sum('sub_total');

            return compact('monthlySales', 'monthlyProductSales', 'monthlyMembershipRevenue');
        }, $branchId);
    }

    private function getTransactionStats($branchId): array
    {
        return CacheService::remember('transaction_stats', 'stats', function() use ($branchId) {
            return [
                'stockInCount' => Transactions::where('type', 'stock_in')
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->count(),
                'stockOutCount' => Transactions::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->count(),
            ];
        }, $branchId);
    }

    private function getProductPerformance($branchId): array
    {
        return CacheService::remember('product_performance', 'hourly', function() use ($branchId) {
            $highestSellingProduct = SalesItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
                ->whereNotNull('product_id')
                ->whereNull('plan_id')
                ->whereHas('sale', fn($q) => $q->when($branchId, fn($query) => $query->where('branch_id', $branchId)))
                ->groupBy('product_id')
                ->orderBy('total_sold', 'desc')
                ->first();

            if ($highestSellingProduct) {
                $highestSellingProduct->load('product');
            }

            $lowestSellingProduct = SalesItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
                ->whereNotNull('product_id')
                ->whereNull('plan_id')
                ->whereHas('sale', fn($q) => $q->when($branchId, fn($query) => $query->where('branch_id', $branchId)))
                ->groupBy('product_id')
                ->orderBy('total_sold', 'asc')
                ->first();

            if ($lowestSellingProduct) {
                $lowestSellingProduct->load('product');
            }

            return compact('highestSellingProduct', 'lowestSellingProduct');
        }, $branchId);
    }

    private function getPlanPerformance($branchId): array
    {
        return CacheService::remember('plan_performance', 'hourly', function() use ($branchId) {
            $mostPopularPlan = SalesItem::select('plan_id', DB::raw('COUNT(*) as total_sales'))
                ->whereNotNull('plan_id')
                ->whereHas('sale', function ($query) use ($branchId) {
                    $query->where('status', 'paid')
                        ->when($branchId, fn($q) => $q->where('branch_id', $branchId));
                })
                ->groupBy('plan_id')
                ->orderBy('total_sales', 'desc')
                ->first();

            if ($mostPopularPlan) {
                $mostPopularPlan->load('plan');
            }

            $mostPopularSubscription = Subscriptions::select('subscriptions.*', DB::raw('COUNT(member_profiles.member_id) as member_count'))
                ->leftJoin('member_profiles', 'subscriptions.subscription_id', '=', 'member_profiles.subscription_id')
                ->when($branchId, fn($q) => $q->where('subscriptions.branch_id', $branchId))
                ->groupBy('subscriptions.subscription_id', 'subscriptions.branch_id', 'subscriptions.name', 
                         'subscriptions.details', 'subscriptions.price', 'subscriptions.duration_days', 
                         'subscriptions.created_at', 'subscriptions.updated_at')
                ->orderBy('member_count', 'desc')
                ->first();

            return compact('mostPopularPlan', 'mostPopularSubscription');
        }, $branchId);
    }

    private function getSalesByType($branchId): array
    {
        return CacheService::remember('sales_by_type', 'stats', function() use ($branchId) {
            $productSalesCount = SalesItem::whereHas('sale', function ($query) use ($branchId) {
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->where('status', 'paid')
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId));
            })
                ->whereNotNull('product_id')
                ->whereNull('plan_id')
                ->count();

            $membershipSalesCount = SalesItem::whereHas('sale', function ($query) use ($branchId) {
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->where('status', 'paid')
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId));
            })
                ->whereNotNull('plan_id')
                ->count();

            return compact('productSalesCount', 'membershipSalesCount');
        }, $branchId);
    }

    private function getBranchPerformance(): array
    {
        return CacheService::remember('branch_performance', 'stats', function() {
            return Branches::select('branches.*')
                ->withCount([
                    'users as member_count' => function ($query) {
                        $query->whereHas('member', fn($q) => $q->where('status', 'active'));
                    }
                ])
                ->with([
                    'sales' => function ($query) {
                        $query->whereMonth('created_at', Carbon::now()->month)
                            ->whereYear('created_at', Carbon::now()->year)
                            ->where('status', 'paid');
                    }
                ])
                ->get()
                ->map(function ($branch) {
                    return [
                        'branch_id' => $branch->branch_id,
                        'name' => $branch->name,
                        'member_count' => $branch->member_count,
                        'monthly_revenue' => $branch->sales->sum('total_amount'),
                    ];
                })
                ->toArray();
        });
    }

    // ========== CHART DATA METHODS (WITH CACHING) ==========

    private function getSalesTrend($period, $branchId = null)
    {
        return CacheService::remember('sales_trend', 'stats', function() use ($period, $branchId) {
            switch ($period) {
                case 'today':
                    // Generate all 24 hours with 0 values for missing hours
                    $hourlyData = collect(range(0, 23))->mapWithKeys(function ($hour) {
                        return [$hour => ['total_revenue' => 0, 'total_sales' => 0]];
                    });

                    $sales = Sales::select(
                        DB::raw('HOUR(created_at) as hour'),
                        DB::raw('SUM(total_amount) as total_revenue'),
                        DB::raw('COUNT(*) as total_sales')
                    )
                        ->whereDate('created_at', Carbon::today())
                        ->where('status', 'paid')
                        ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                        ->groupBy('hour')
                        ->get()
                        ->keyBy('hour');

                    // Merge actual sales data with all hours
                    return $hourlyData->map(function ($defaultValue, $hour) use ($sales) {
                        $data = $sales->get($hour, (object) $defaultValue);
                        return (object) [
                            'label' => Carbon::today()->setHour($hour)->format('h A'),
                            'total_revenue' => $data->total_revenue ?? 0,
                            'total_sales' => $data->total_sales ?? 0,
                        ];
                    })->values();

                case 'week':
                    // Generate all 7 days
                    $days = collect(range(0, 6))->map(function ($i) {
                        return Carbon::now()->subDays(6 - $i)->format('Y-m-d');
                    })->mapWithKeys(function ($date) {
                        return [$date => ['total_revenue' => 0, 'total_sales' => 0]];
                    });

                    $sales = Sales::select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(total_amount) as total_revenue'),
                        DB::raw('COUNT(*) as total_sales')
                    )
                        ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
                        ->where('status', 'paid')
                        ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                        ->groupBy('date')
                        ->get()
                        ->keyBy('date');

                    return $days->map(function ($defaultValue, $date) use ($sales) {
                        $data = $sales->get($date, (object) $defaultValue);
                        return (object) [
                            'label' => Carbon::parse($date)->format('D'),
                            'total_revenue' => $data->total_revenue ?? 0,
                            'total_sales' => $data->total_sales ?? 0,
                        ];
                    })->values();

                case 'month':
                default:
                    // Generate 4 weeks
                    return collect(range(1, 4))->map(function ($weekNum) use ($branchId) {
                        $weekStart = Carbon::now()->subWeeks(4 - $weekNum)->startOfWeek();
                        $weekEnd = $weekStart->copy()->endOfWeek();
                        
                        $revenue = Sales::whereDate('created_at', '>=', $weekStart)
                            ->whereDate('created_at', '<=', $weekEnd)
                            ->where('status', 'paid')
                            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                            ->sum('total_amount');
                            
                        $sales = Sales::whereDate('created_at', '>=', $weekStart)
                            ->whereDate('created_at', '<=', $weekEnd)
                            ->where('status', 'paid')
                            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                            ->count();
                        
                        return (object) [
                            'label' => 'Week ' . $weekNum,
                            'total_revenue' => $revenue ?? 0,
                            'total_sales' => $sales ?? 0,
                        ];
                    });
            }
        }, $period, $branchId);
    }

    private function getMembershipTrend($period, $branchId = null)
    {
        return CacheService::remember('membership_trend', 'stats', function() use ($period, $branchId) {
            switch ($period) {
                case 'today':
                    // Generate all 24 hours
                    $hourlyData = collect(range(0, 23))->mapWithKeys(function ($hour) {
                        return [$hour => ['count' => 0]];
                    });

                    $members = MemberProfile::select(
                        DB::raw('HOUR(start_date) as hour'),
                        DB::raw('COUNT(*) as count')
                    )
                        ->whereDate('start_date', Carbon::today())
                        ->whereNotNull('start_date')
                        ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                        ->groupBy('hour')
                        ->get()
                        ->keyBy('hour');

                    return $hourlyData->map(function ($defaultValue, $hour) use ($members) {
                        $data = $members->get($hour, (object) $defaultValue);
                        return (object) [
                            'label' => Carbon::today()->setHour($hour)->format('h A'),
                            'count' => $data->count ?? 0,
                        ];
                    })->values();

                case 'week':
                    // Generate all 7 days
                    $days = collect(range(0, 6))->map(function ($i) {
                        return Carbon::now()->subDays(6 - $i)->format('Y-m-d');
                    })->mapWithKeys(function ($date) {
                        return [$date => ['count' => 0]];
                    });

                    $members = MemberProfile::select(
                        DB::raw('DATE(start_date) as date'),
                        DB::raw('COUNT(*) as count')
                    )
                        ->where('start_date', '>=', Carbon::now()->subDays(6)->startOfDay())
                        ->whereNotNull('start_date')
                        ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                        ->groupBy('date')
                        ->get()
                        ->keyBy('date');

                    return $days->map(function ($defaultValue, $date) use ($members) {
                        $data = $members->get($date, (object) $defaultValue);
                        return (object) [
                            'label' => Carbon::parse($date)->format('D'),
                            'count' => $data->count ?? 0,
                        ];
                    })->values();

                case 'month':
                default:
                    // Generate 4 weeks
                    return collect(range(1, 4))->map(function ($weekNum) use ($branchId) {
                        $weekStart = Carbon::now()->subWeeks(4 - $weekNum)->startOfWeek();
                        $weekEnd = $weekStart->copy()->endOfWeek();
                        
                        $count = MemberProfile::whereDate('start_date', '>=', $weekStart)
                            ->whereDate('start_date', '<=', $weekEnd)
                            ->whereNotNull('start_date')
                            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                            ->count();
                        
                        return (object) [
                            'label' => 'Week ' . $weekNum,
                            'count' => $count ?? 0,
                        ];
                    });
            }
        }, $period, $branchId);
    }

    private function getSubscriptionTrend($period, $branchId = null)
    {
        return CacheService::remember('subscription_trend', 'stats', function() use ($period, $branchId) {
            switch ($period) {
                case 'today':
                    // Generate all 24 hours
                    $hourlyData = collect(range(0, 23))->mapWithKeys(function ($hour) {
                        return [$hour => ['count' => 0, 'total_revenue' => 0]];
                    });

                    $subscriptions = MemberProfile::select(
                        DB::raw('HOUR(member_profiles.start_date) as hour'),
                        DB::raw('COUNT(*) as count'),
                        DB::raw('SUM(subscriptions.price) as total_revenue')
                    )
                        ->join('subscriptions', 'member_profiles.subscription_id', '=', 'subscriptions.subscription_id')
                        ->whereDate('member_profiles.start_date', Carbon::today())
                        ->whereNotNull('member_profiles.subscription_id')
                        ->when($branchId, fn($q) => $q->where('member_profiles.branch_id', $branchId))
                        ->groupBy('hour')
                        ->get()
                        ->keyBy('hour');

                    return $hourlyData->map(function ($defaultValue, $hour) use ($subscriptions) {
                        $data = $subscriptions->get($hour, (object) $defaultValue);
                        return (object) [
                            'label' => Carbon::today()->setHour($hour)->format('h A'),
                            'count' => $data->count ?? 0,
                            'total_revenue' => $data->total_revenue ?? 0,
                        ];
                    })->values();

                case 'week':
                    // Generate all 7 days
                    $days = collect(range(0, 6))->map(function ($i) {
                        return Carbon::now()->subDays(6 - $i)->format('Y-m-d');
                    })->mapWithKeys(function ($date) {
                        return [$date => ['count' => 0, 'total_revenue' => 0]];
                    });

                    $subscriptions = MemberProfile::select(
                        DB::raw('DATE(member_profiles.start_date) as date'),
                        DB::raw('COUNT(*) as count'),
                        DB::raw('SUM(subscriptions.price) as total_revenue')
                    )
                        ->join('subscriptions', 'member_profiles.subscription_id', '=', 'subscriptions.subscription_id')
                        ->where('member_profiles.start_date', '>=', Carbon::now()->subDays(6)->startOfDay())
                        ->whereNotNull('member_profiles.subscription_id')
                        ->when($branchId, fn($q) => $q->where('member_profiles.branch_id', $branchId))
                        ->groupBy('date')
                        ->get()
                        ->keyBy('date');

                    return $days->map(function ($defaultValue, $date) use ($subscriptions) {
                        $data = $subscriptions->get($date, (object) $defaultValue);
                        return (object) [
                            'label' => Carbon::parse($date)->format('D'),
                            'count' => $data->count ?? 0,
                            'total_revenue' => $data->total_revenue ?? 0,
                        ];
                    })->values();

                case 'month':
                default:
                    // Generate 4 weeks
                    return collect(range(1, 4))->map(function ($weekNum) use ($branchId) {
                        $weekStart = Carbon::now()->subWeeks(4 - $weekNum)->startOfWeek();
                        $weekEnd = $weekStart->copy()->endOfWeek();
                        
                        $data = MemberProfile::select(
                            DB::raw('COUNT(*) as count'),
                            DB::raw('SUM(subscriptions.price) as total_revenue')
                        )
                            ->join('subscriptions', 'member_profiles.subscription_id', '=', 'subscriptions.subscription_id')
                            ->whereDate('member_profiles.start_date', '>=', $weekStart)
                            ->whereDate('member_profiles.start_date', '<=', $weekEnd)
                            ->whereNotNull('member_profiles.subscription_id')
                            ->when($branchId, fn($q) => $q->where('member_profiles.branch_id', $branchId))
                            ->first();
                        
                        return (object) [
                            'label' => 'Week ' . $weekNum,
                            'count' => $data->count ?? 0,
                            'total_revenue' => $data->total_revenue ?? 0,
                        ];
                    });
            }
        }, $period, $branchId);
    }
}