@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')

    <body class="bg-gradient-to-br from-green-50 via-blue-50 to-purple-50 min-h-screen">
        <div class="w-full px-4 py-6">

            {{-- Branch Filter Indicator (Only for Super Admin) --}}
            @if(auth()->user()->role === 'super_admin')
                <div class="mb-4">
                    @if($selectedBranchId)
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg flex items-center justify-between">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <strong>Viewing Branch:</strong>&nbsp;{{ session('selected_branch_name') }}
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-100 border border-gray-300 text-gray-700 px-4 py-3 rounded-lg flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <strong>Viewing:</strong>&nbsp;All Branches
                        </div>
                    @endif
                </div>
            @endif

            <!-- Top Stats Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

                {{-- Super Admin Only: Branch Stats --}}
                @if(auth()->user()->role === 'super_admin' && !$selectedBranchId)
                    <!-- Total Branches Card -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500 hover:shadow-xl transition-all hover:scale-105 duration-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm font-medium">Total Branches</p>
                                <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalBranches }}</h3>
                                <p class="text-indigo-600 text-sm mt-2">
                                    <span class="font-semibold">{{ $activeBranches }}</span> active
                                </p>
                            </div>
                            <div class="bg-indigo-100 p-4 rounded-full">
                                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Active Members Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-all hover:scale-105 duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Active Members</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalActiveMembers }}</h3>
                            <p class="text-green-600 text-sm mt-2">
                                <span class="font-semibold">+{{ $newMembersThisMonth }}</span> new this month
                            </p>
                            @if($expiringMemberships > 0)
                                <p class="text-orange-500 text-xs mt-1">
                                    <span class="font-semibold">{{ $expiringMemberships }}</span> expiring soon
                                </p>
                            @endif
                        </div>
                        <div class="bg-green-100 p-4 rounded-full">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Active Subscriptions Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-teal-500 hover:shadow-xl transition-all hover:scale-105 duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Active Subscriptions</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $activeSubscriptions }}</h3>
                            <p class="text-teal-600 text-sm mt-2">
                                <span class="font-semibold">{{ $totalSubscriptions }}</span> total plans
                            </p>
                        </div>
                        <div class="bg-teal-100 p-4 rounded-full">
                            <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Gym Occupancy Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-all hover:scale-105 duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Current Occupancy</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $currentOccupancy }}</h3>
                            <p class="text-blue-600 text-sm mt-2">Members in gym</p>
                        </div>
                        <div class="bg-blue-100 p-4 rounded-full">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Today's Revenue Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-all hover:scale-105 duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Today's Revenue</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-2">₱{{ number_format($todayRevenue, 2) }}</h3>
                            <p class="text-purple-600 text-sm mt-2">{{ $todaySales }} sales today</p>
                        </div>
                        <div class="bg-purple-100 p-4 rounded-full">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Inventory Status Card -->
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-xl transition-all hover:scale-105 duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-medium">Total Stock</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalItemsInStock }}</h3>
                            <p class="text-orange-600 text-sm mt-2">
                                <span class="font-semibold">{{ $lowStockItems }}</span> low stock items
                            </p>
                        </div>
                        <div class="bg-orange-100 p-4 rounded-full">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column - Charts (2 columns) -->
                <div class="lg:col-span-2 space-y-6">

                    {{-- Branch Performance (Super Admin Only, All Branches View) --}}
                    @if(auth()->user()->role === 'super_admin' && !$selectedBranchId && count($branchPerformance) > 0)
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Branch Performance Overview</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monthly Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($branchPerformance as $branch)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $branch['name'] }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $branch['member_count'] }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-semibold text-green-600">₱{{ number_format($branch['monthly_revenue'], 2) }}</div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Revenue & Sales Trend Chart -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Sales Trend</h3>
                                <p class="text-sm text-gray-500" id="salesPeriodLabel">
                                    @if($salesPeriod === 'today') Hourly breakdown for today
                                    @elseif($salesPeriod === 'week') Daily breakdown for this week
                                    @else Weekly breakdown for this month
                                    @endif
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <button data-sales-period="today"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ $salesPeriod === 'today' ? 'bg-blue-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    Today
                                </button>
                                <button data-sales-period="week"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ $salesPeriod === 'week' ? 'bg-blue-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    Week
                                </button>
                                <button data-sales-period="month"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ $salesPeriod === 'month' ? 'bg-blue-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    Month
                                </button>
                            </div>
                        </div>
                        <div class="flex gap-4 mb-4 justify-end">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Revenue</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Sales Count</span>
                            </div>
                        </div>
                        <div class="h-72">
                            <canvas id="salesTrendChart"></canvas>
                        </div>
                    </div>

                    <!-- Revenue Breakdown & Sales Breakdown -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Revenue Breakdown -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Monthly Revenue Breakdown</h3>
                            <div class="space-y-3">
                                <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-1">Product Sales</p>
                                    <p class="text-2xl font-bold text-blue-600">₱{{ number_format($revenueBreakdown['products'], 2) }}</p>
                                </div>
                                <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg">
                                    <p class="text-sm text-gray-600 mb-1">Membership Sales</p>
                                    <p class="text-2xl font-bold text-green-600">₱{{ number_format($revenueBreakdown['memberships'], 2) }}</p>
                                </div>
                                <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 rounded-lg border-2 border-purple-300">
                                    <p class="text-sm text-gray-600 mb-1">Total Revenue</p>
                                    <p class="text-2xl font-bold text-purple-600">₱{{ number_format($revenueBreakdown['total'], 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Count Breakdown -->
                        <div class="bg-white rounded-xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">Sales Breakdown</h3>
                            <div class="h-56">
                                <canvas id="salesBreakdownChart"></canvas>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <p class="text-sm text-gray-600">Product Sales</p>
                                    <p class="text-xl font-bold text-blue-600">{{ $productSalesCount }}</p>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <p class="text-sm text-gray-600">Memberships</p>
                                    <p class="text-xl font-bold text-green-600">{{ $membershipSalesCount }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product & Membership Performance -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Performance Overview</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Highest Selling Product -->
                            <div class="border-2 border-green-200 rounded-lg p-4 bg-gradient-to-br from-green-50 to-emerald-50">
                                <div class="flex items-center mb-2">
                                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                    <h4 class="font-semibold text-green-800">Top Product</h4>
                                </div>
                                @if($highestSellingProduct && $highestSellingProduct->product)
                                    <p class="text-lg font-bold text-gray-800">{{ $highestSellingProduct->product->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $highestSellingProduct->total_sold }} units sold</p>
                                @else
                                    <p class="text-gray-500">No data available</p>
                                @endif
                            </div>

                            <!-- Lowest Selling Product -->
                            <div class="border-2 border-red-200 rounded-lg p-4 bg-gradient-to-br from-red-50 to-orange-50">
                                <div class="flex items-center mb-2">
                                    <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                    </svg>
                                    <h4 class="font-semibold text-red-800">Needs Attention</h4>
                                </div>
                                @if($lowestSellingProduct && $lowestSellingProduct->product)
                                    <p class="text-lg font-bold text-red-600">{{ $lowestSellingProduct->product->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $lowestSellingProduct->total_sold }} units sold</p>
                                @else
                                    <p class="text-gray-500">No data available</p>
                                @endif
                            </div>

                            <!-- Most Popular Plan -->
                            <div class="border-2 border-purple-200 rounded-lg p-4 bg-gradient-to-br from-purple-50 to-pink-50">
                                <div class="flex items-center mb-2">
                                    <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                    <h4 class="font-semibold text-purple-800">Top Plan</h4>
                                </div>
                                @if($mostPopularPlan && $mostPopularPlan->plan)
                                    <p class="text-lg font-bold text-gray-800">{{ $mostPopularPlan->plan->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $mostPopularPlan->total_sales }} memberships sold</p>
                                @else
                                    <p class="text-gray-500">No data available</p>
                                @endif
                            </div>

                            <!-- Most Popular Subscription -->
                            <div class="border-2 border-teal-200 rounded-lg p-4 bg-gradient-to-br from-teal-50 to-cyan-50">
                                <div class="flex items-center mb-2">
                                    <svg class="w-6 h-6 text-teal-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h4 class="font-semibold text-teal-800">Top Subscription</h4>
                                </div>
                                @if($mostPopularSubscription)
                                    <p class="text-lg font-bold text-gray-800">{{ $mostPopularSubscription->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $mostPopularSubscription->member_count }} active members</p>
                                @else
                                    <p class="text-gray-500">No data available</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Membership Growth Trend -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Membership Growth Trend</h3>
                                <p class="text-sm text-gray-500" id="membershipPeriodLabel">
                                    @if($membershipPeriod === 'today') Hourly breakdown for today
                                    @elseif($membershipPeriod === 'week') Daily breakdown for this week
                                    @else Weekly breakdown for this month
                                    @endif
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <button data-membership-period="today"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ $membershipPeriod === 'today' ? 'bg-green-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    Today
                                </button>
                                <button data-membership-period="week"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ $membershipPeriod === 'week' ? 'bg-green-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    Week
                                </button>
                                <button data-membership-period="month"
                                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all {{ $membershipPeriod === 'month' ? 'bg-green-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                                    Month
                                </button>
                            </div>
                        </div>
                        <div class="h-64">
                            <canvas id="membershipTrendChart"></canvas>
                        </div>
                    </div>

                </div>

                <!-- Right Column - Stats & Activity (1 column) -->
                <div class="space-y-6">

                    <!-- Transaction Overview -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Monthly Overview</h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                                <div class="flex items-center">
                                    <div class="bg-green-500 p-2 rounded-full mr-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </div>
                                    <span class="text-gray-700 font-medium">Stock In</span>
                                </div>
                                <span class="text-xl font-bold text-green-600">{{ $stockInCount }}</span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-red-50 to-orange-50 rounded-lg border border-red-200">
                                <div class="flex items-center">
                                    <div class="bg-red-500 p-2 rounded-full mr-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </div>
                                    <span class="text-gray-700 font-medium">Stock Out</span>
                                </div>
                                <span class="text-xl font-bold text-red-600">{{ $stockOutCount }}</span>
                            </div>

                            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg border border-blue-200">
                                <div class="flex items-center">
                                    <div class="bg-blue-500 p-2 rounded-full mr-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                    <span class="text-gray-700 font-medium">Total Sales</span>
                                </div>
                                <span class="text-xl font-bold text-blue-600">{{ $todaySales }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Members by Plan -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Members by Plan</h3>
                        <div class="space-y-3 max-h-72 overflow-y-auto">
                            @forelse($membersByPlan as $planData)
                                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-green-50 rounded-lg hover:shadow-md transition-shadow">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $planData['plan_name'] }}</p>
                                        <p class="text-sm text-gray-600">Active Members</p>
                                    </div>
                                    <div class="bg-white rounded-full px-4 py-2 shadow-sm">
                                        <span class="font-bold text-green-600">{{ $planData['count'] }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No active members</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Active Subscriptions by Type -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Subscriptions by Type</h3>
                        <div class="space-y-3 max-h-72 overflow-y-auto">
                            @forelse($subscriptionsByType as $subscriptionData)
                                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-teal-50 to-cyan-50 rounded-lg hover:shadow-md transition-shadow">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $subscriptionData['subscription_name'] }}</p>
                                        <p class="text-sm text-gray-600">Active Subscribers</p>
                                    </div>
                                    <div class="bg-white rounded-full px-4 py-2 shadow-sm">
                                        <span class="font-bold text-teal-600">{{ $subscriptionData['count'] }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No active subscriptions</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Sales Activity -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Sales</h3>
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @forelse($recentSales as $sale)
                                <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all hover:shadow-md">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <p class="font-medium text-gray-800">Sale #{{ $sale->sales_id }}</p>
                                            <p class="text-xs text-gray-500">{{ $sale->created_at->diffForHumans() }}</p>
                                            @if($sale->branch)
                                                <p class="text-xs text-blue-600 mt-1">{{ $sale->branch->name }}</p>
                                            @endif
                                        </div>
                                        <span class="font-bold text-green-600">₱{{ number_format($sale->total_amount, 2) }}</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($sale->items as $item)
                                            @if($item->product)
                                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ $item->product->name }}</span>
                                            @elseif($item->plan)
                                                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded">{{ $item->plan->name }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-4">No recent sales</p>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <!-- Chart.js Script -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Store chart instances globally so we can update them
            let salesTrendChart = null;
            let membershipTrendChart = null;

            // Initialize charts on page load
            document.addEventListener('DOMContentLoaded', function () {
                initializeSalesTrendChart();
                initializeMembershipTrendChart();
                initializeSalesBreakdownChart();
                setupChartFilterButtons();
            });

            // Initialize Sales Trend Chart
            function initializeSalesTrendChart() {
                const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
                const salesData = @json($salesTrend);

                salesTrendChart = new Chart(salesCtx, {
                    type: 'line',
                    data: {
                        labels: salesData.map(item => item.label),
                        datasets: [{
                            label: 'Revenue (₱)',
                            data: salesData.map(item => item.total_revenue),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y',
                        }, {
                            label: 'Sales Count',
                            data: salesData.map(item => item.total_sales),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y1',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Revenue (₱)'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true,
                                grid: {
                                    drawOnChartArea: false,
                                },
                                title: {
                                    display: true,
                                    text: 'Sales Count'
                                }
                            }
                        }
                    }
                });
            }

            // Initialize Membership Trend Chart
            function initializeMembershipTrendChart() {
                const membershipCtx = document.getElementById('membershipTrendChart').getContext('2d');
                const membershipData = @json($membershipTrend);

                membershipTrendChart = new Chart(membershipCtx, {
                    type: 'line',
                    data: {
                        labels: membershipData.map(item => item.label),
                        datasets: [{
                            label: 'New Members',
                            data: membershipData.map(item => item.count),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: 'rgb(34, 197, 94)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // Initialize Sales Breakdown Chart
            function initializeSalesBreakdownChart() {
                const breakdownCtx = document.getElementById('salesBreakdownChart').getContext('2d');
                new Chart(breakdownCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Product Sales', 'Membership Sales'],
                        datasets: [{
                            data: [{{ $productSalesCount }}, {{ $membershipSalesCount }}],
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(34, 197, 94, 0.8)'
                            ],
                            borderColor: [
                                'rgb(59, 130, 246)',
                                'rgb(34, 197, 94)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Setup filter button click handlers
            function setupChartFilterButtons() {
                // Sales Trend filter buttons
                document.querySelectorAll('[data-sales-period]').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        const period = this.getAttribute('data-sales-period');
                        updateSalesTrendChart(period);
                    });
                });

                // Membership Trend filter buttons
                document.querySelectorAll('[data-membership-period]').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        const period = this.getAttribute('data-membership-period');
                        updateMembershipTrendChart(period);
                    });
                });
            }

            // Update Sales Trend Chart via AJAX
            function updateSalesTrendChart(period) {
                // Show loading state
                showChartLoading('salesTrendChart');

                // Update active button state
                document.querySelectorAll('[data-sales-period]').forEach(btn => {
                    if (btn.getAttribute('data-sales-period') === period) {
                        btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        btn.classList.add('bg-blue-500', 'text-white', 'shadow-md');
                    } else {
                        btn.classList.remove('bg-blue-500', 'text-white', 'shadow-md');
                        btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    }
                });

                // Fetch new data
                fetch(`{{ route('admin.dashboard') }}?ajax=1&sales_period=${period}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Update chart data
                        salesTrendChart.data.labels = data.salesTrend.map(item => item.label);
                        salesTrendChart.data.datasets[0].data = data.salesTrend.map(item => item.total_revenue);
                        salesTrendChart.data.datasets[1].data = data.salesTrend.map(item => item.total_sales);

                        // Update chart
                        salesTrendChart.update();

                        // Update period label
                        updatePeriodLabel('sales', period);

                        // Hide loading state
                        hideChartLoading('salesTrendChart');
                    })
                    .catch(error => {
                        console.error('Error updating sales trend:', error);
                        hideChartLoading('salesTrendChart');
                    });
            }

            // Update Membership Trend Chart via AJAX
            function updateMembershipTrendChart(period) {
                // Show loading state
                showChartLoading('membershipTrendChart');

                // Update active button state
                document.querySelectorAll('[data-membership-period]').forEach(btn => {
                    if (btn.getAttribute('data-membership-period') === period) {
                        btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        btn.classList.add('bg-green-500', 'text-white', 'shadow-md');
                    } else {
                        btn.classList.remove('bg-green-500', 'text-white', 'shadow-md');
                        btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    }
                });

                // Fetch new data
                fetch(`{{ route('admin.dashboard') }}?ajax=1&membership_period=${period}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Update chart data
                        membershipTrendChart.data.labels = data.membershipTrend.map(item => item.label);
                        membershipTrendChart.data.datasets[0].data = data.membershipTrend.map(item => item.count);

                        // Update chart
                        membershipTrendChart.update();

                        // Update period label
                        updatePeriodLabel('membership', period);

                        // Hide loading state
                        hideChartLoading('membershipTrendChart');
                    })
                    .catch(error => {
                        console.error('Error updating membership trend:', error);
                        hideChartLoading('membershipTrendChart');
                    });
            }

            // Update period label text
            function updatePeriodLabel(type, period) {
                let labelText = '';
                if (period === 'today') {
                    labelText = 'Hourly breakdown for today';
                } else if (period === 'week') {
                    labelText = 'Daily breakdown for this week';
                } else {
                    labelText = 'Weekly breakdown for this month';
                }

                const labelElement = document.querySelector(`#${type}PeriodLabel`);
                if (labelElement) {
                    labelElement.textContent = labelText;
                }
            }

            // Show loading overlay on chart
            function showChartLoading(canvasId) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) return;

                const container = canvas.parentElement;

                // Create loading overlay if it doesn't exist
                let overlay = container.querySelector('.chart-loading-overlay');
                if (!overlay) {
                    overlay = document.createElement('div');
                    overlay.className = 'chart-loading-overlay absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10';
                    overlay.innerHTML = `
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                            <p class="text-sm text-gray-600 mt-2">Loading...</p>
                        </div>
                    `;
                    container.style.position = 'relative';
                    container.appendChild(overlay);
                } else {
                    overlay.classList.remove('hidden');
                }
            }

            // Hide loading overlay
            function hideChartLoading(canvasId) {
                const canvas = document.getElementById(canvasId);
                if (!canvas) return;

                const container = canvas.parentElement;
                const overlay = container.querySelector('.chart-loading-overlay');

                if (overlay) {
                    overlay.classList.add('hidden');
                }
            }
        </script>
    </body>

@endsection