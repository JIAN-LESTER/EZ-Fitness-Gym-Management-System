@extends('layouts.app')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')

<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen p-6">
    <div class="max-w-7xl mx-auto">
        
        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            
            <!-- Active Members Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-shadow">
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

            <!-- Gym Occupancy Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-shadow">
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
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Today's Revenue</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-2">₱{{ number_format($todayRevenue, 2) }}</h3>
                        <p class="text-purple-600 text-sm mt-2">{{ $todaySales }} sales</p>
                    </div>
                    <div class="bg-purple-100 p-4 rounded-full">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Inventory Status Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Stock</p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalItemsInStock }}</h3>
                        <p class="text-orange-600 text-sm mt-2">
                            <span class="font-semibold">{{ $lowStockItems }}</span> low stock
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
            
            <!-- Left Column - Charts -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Membership Trend Chart -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Membership Growth Trend</h3>
                    <div class="h-64">
                        <canvas id="membershipTrendChart"></canvas>
                    </div>
                </div>

                <!-- Revenue Breakdown -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Monthly Revenue Breakdown</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">Sales</p>
                            <p class="text-2xl font-bold text-blue-600">₱{{ number_format($revenueBreakdown['sales'], 2) }}</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">Memberships</p>
                            <p class="text-2xl font-bold text-green-600">₱{{ number_format($revenueBreakdown['memberships'], 2) }}</p>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">Total</p>
                            <p class="text-2xl font-bold text-purple-600">₱{{ number_format($revenueBreakdown['total'], 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Product Performance -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Product Performance</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Highest Selling -->
                        <div class="border-2 border-green-200 rounded-lg p-4 bg-green-50">
                            <div class="flex items-center mb-2">
                                <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                <h4 class="font-semibold text-green-800">Top Seller</h4>
                            </div>
                            @if($highestSellingProduct)
                                <p class="text-lg font-bold text-gray-800">{{ $highestSellingProduct->product->name }}</p>
                                <p class="text-sm text-gray-600">{{ $highestSellingProduct->total_sold }} units sold</p>
                            @else
                                <p class="text-gray-500">No data available</p>
                            @endif
                        </div>

                        <!-- Lowest Selling -->
                        <div class="border-2 border-red-200 rounded-lg p-4 bg-red-50">
                            <div class="flex items-center mb-2">
                                <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                </svg>
                                <h4 class="font-semibold text-red-800">Needs Attention</h4>
                            </div>
                            @if($lowestSellingProduct)
                                <p class="text-lg font-bold text-gray-800">{{ $lowestSellingProduct->product->name }}</p>
                                <p class="text-sm text-gray-600">{{ $lowestSellingProduct->total_sold }} units sold</p>
                            @else
                                <p class="text-gray-500">No data available</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column - Stats & Activity -->
            <div class="space-y-6">
                
                <!-- Transaction Overview -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Transaction Overview</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
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

                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
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

                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-blue-500 p-2 rounded-full mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <span class="text-gray-700 font-medium">Sales</span>
                            </div>
                            <span class="text-xl font-bold text-blue-600">{{ $todaySales }}</span>
                        </div>
                    </div>
                </div>

                <!-- Members by Plan -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Members by Plan</h3>
                    <div class="space-y-3">
                        @forelse($membersByPlan as $planData)
                        <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-green-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-800">{{ $planData['plan_name'] }}</p>
                                <p class="text-sm text-gray-600">Active Members</p>
                            </div>
                            <div class="bg-white rounded-full px-4 py-2">
                                <span class="font-bold text-green-600">{{ $planData['count'] }}</span>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">No active members</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Sales Activity -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Sales</h3>
                    <div class="space-y-3">
                        @forelse($recentSales as $sale)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div>
                                <p class="font-medium text-gray-800">Sale #{{ $sale->sales_id }}</p>
                                <p class="text-sm text-gray-500">{{ $sale->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="font-bold text-green-600">₱{{ number_format($sale->total_amount, 2) }}</span>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">No recent sales</p>
                        @endforelse
                    </div>
                </div>

              

            </div>

        </div>
xamxa
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Membership Trend Chart
        const ctx = document.getElementById('membershipTrendChart').getContext('2d');
        const membershipData = @json($membershipTrend);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: membershipData.map(item => item.month),
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
    </script>
</body>

@endsection