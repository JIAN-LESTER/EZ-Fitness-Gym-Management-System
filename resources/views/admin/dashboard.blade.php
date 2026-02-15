@extends('layouts.app')
@section('title', 'Dashboard | EZ Fitness')
@section('header', 'Dashboard')

@section('content')

    <div class="min-h-screen">
        <div class="w-full px-3 sm:px-6 py-5 sm:py-8 max-w-[1600px] mx-auto">

            {{-- Welcome Header --}}
            <div class="mb-6 sm:mb-8">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                    Welcome back, {{ auth()->user()->first_name ?? 'Admin' }} 👋
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ now()->format('l, F j, Y') }} &mdash; Here's your gym overview</p>
            </div>

            @if(auth()->user()->role === 'super_admin' && !$selectedBranchId)
                {{-- ═══════════════ SUPER ADMIN LAYOUT ═══════════════ --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 mb-6">
                    
                    {{-- Branch Stats --}}
                    <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-indigo-50 to-transparent rounded-bl-full"></div>
                        <div class="relative">
                            <div class="w-11 h-11 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-indigo-200 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Branches</p>
                            <h3 class="text-3xl font-black text-gray-900">{{ $totalBranches }}</h3>
                            <p class="text-xs text-indigo-600 font-semibold mt-1">{{ $activeBranches }} active</p>
                        </div>
                    </div>

                    {{-- Active Members --}}
                    <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-emerald-50 to-transparent rounded-bl-full"></div>
                        <div class="relative">
                            <div class="w-11 h-11 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Active Members</p>
                            <h3 class="text-3xl font-black text-gray-900">{{ $totalActiveMembers }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">+{{ $newMembersThisMonth }} this month</span>
                            </div>
                            @if($expiringMemberships > 0)
                                <p class="text-[11px] text-orange-500 font-semibold mt-1">⚠ {{ $expiringMemberships }} expiring soon</p>
                            @endif
                        </div>
                    </div>

                    {{-- Subscriptions --}}
                    <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-blue-50 to-transparent rounded-bl-full"></div>
                        <div class="relative">
                            <div class="w-11 h-11 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-blue-200 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Subscriptions</p>
                            <h3 class="text-3xl font-black text-gray-900">{{ $activeSubscriptions }}</h3>
                            <p class="text-xs text-blue-600 font-semibold mt-1">{{ $totalSubscriptions }} total plans</p>
                        </div>
                    </div>

                    {{-- Total Users --}}
                    <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-teal-50 to-transparent rounded-bl-full"></div>
                        <div class="relative">
                            <div class="w-11 h-11 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-teal-200 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Users</p>
                            <h3 class="text-3xl font-black text-gray-900">{{ $totalUsers }}</h3>
                            <p class="text-xs text-teal-600 font-semibold mt-1">{{ $activeUsers }} active</p>
                        </div>
                    </div>
                </div>

                {{-- Second Row: 3 Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5 mb-6">
                    {{-- Gym Occupancy --}}
                    <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-violet-50 to-transparent rounded-bl-full"></div>
                        <div class="relative">
                            <div class="w-11 h-11 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-violet-200 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Occupancy</p>
                            <h3 class="text-3xl font-black text-gray-900">{{ $currentOccupancy }}</h3>
                            <p class="text-xs text-violet-600 font-semibold mt-1">Members in gym</p>
                        </div>
                    </div>

                    {{-- Total Stock --}}
                    <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-orange-50 to-transparent rounded-bl-full"></div>
                        <div class="relative">
                            <div class="w-11 h-11 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-orange-200 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Stock</p>
                            <h3 class="text-3xl font-black text-gray-900">{{ $totalItemsInStock }}</h3>
                            @if($lowStockItems > 0)
                                <p class="text-[11px] text-orange-500 font-semibold mt-1">⚠ {{ $lowStockItems }} low stock</p>
                            @else
                                <p class="text-xs text-emerald-600 font-semibold mt-1">All stocked</p>
                            @endif
                        </div>
                    </div>

                    {{-- Today's Revenue --}}
                    <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-purple-50 to-transparent rounded-bl-full"></div>
                        <div class="relative">
                            <div class="w-11 h-11 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-purple-200 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Today's Revenue</p>
                            <h3 class="text-3xl font-black text-gray-900">₱{{ number_format($todayRevenue, 2) }}</h3>
                            <p class="text-xs text-purple-600 font-semibold mt-1">{{ $todaySales }} sales today</p>
                        </div>
                    </div>
                </div>

            @else
                {{-- ═══════════════ ADMIN LAYOUT ═══════════════ --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-5 mb-6">
                    
                    {{-- Active Members --}}
                    <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-emerald-50 to-transparent rounded-bl-full"></div>
                        <div class="relative">
                            <div class="w-11 h-11 bg-gradient-to-br from-emerald-500 to-green-600 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Active Members</p>
                            <h3 class="text-3xl font-black text-gray-900">{{ $totalActiveMembers }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">+{{ $newMembersThisMonth }} this month</span>
                            </div>
                            @if($expiringMemberships > 0)
                                <p class="text-[11px] text-orange-500 font-semibold mt-1">⚠ {{ $expiringMemberships }} expiring soon</p>
                            @endif
                        </div>
                    </div>

                    {{-- Subscriptions --}}
                    <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-blue-50 to-transparent rounded-bl-full"></div>
                        <div class="relative">
                            <div class="w-11 h-11 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-blue-200 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Subscriptions</p>
                            <h3 class="text-3xl font-black text-gray-900">{{ $activeSubscriptions }}</h3>
                            <p class="text-xs text-blue-600 font-semibold mt-1">{{ $totalSubscriptions }} total plans</p>
                        </div>
                    </div>

                    {{-- Occupancy --}}
                    <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-violet-50 to-transparent rounded-bl-full"></div>
                        <div class="relative">
                            <div class="w-11 h-11 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-violet-200 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Occupancy</p>
                            <h3 class="text-3xl font-black text-gray-900">{{ $currentOccupancy }}</h3>
                            <p class="text-xs text-violet-600 font-semibold mt-1">In gym now</p>
                        </div>
                    </div>

                    {{-- Total Stock --}}
                    <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-orange-50 to-transparent rounded-bl-full"></div>
                        <div class="relative">
                            <div class="w-11 h-11 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-orange-200 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Stock</p>
                            <h3 class="text-3xl font-black text-gray-900">{{ $totalItemsInStock }}</h3>
                            @if($lowStockItems > 0)
                                <p class="text-[11px] text-orange-500 font-semibold mt-1">⚠ {{ $lowStockItems }} low stock</p>
                            @else
                                <p class="text-xs text-emerald-600 font-semibold mt-1">All stocked</p>
                            @endif
                        </div>
                    </div>

                    {{-- Today's Revenue --}}
                    <div class="group relative bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 border border-gray-100 overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-purple-50 to-transparent rounded-bl-full"></div>
                        <div class="relative">
                            <div class="w-11 h-11 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-purple-200 group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Today's Revenue</p>
                            <h3 class="text-3xl font-black text-gray-900">₱{{ number_format($todayRevenue, 2) }}</h3>
                            <p class="text-xs text-purple-600 font-semibold mt-1">{{ $todaySales }} sales</p>
                        </div>
                    </div>
                </div>

            @endif

            {{-- ═══════════════ MAIN CONTENT GRID ═══════════════ --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-6">

                {{-- Left Column - Charts (2 columns) --}}
                <div class="lg:col-span-2 space-y-5 lg:space-y-6">

                    {{-- Branch Performance (Super Admin Only) --}}
                    @if(auth()->user()->role === 'super_admin' && !$selectedBranchId && count($branchPerformance) > 0)
                        <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow duration-300 p-5 sm:p-6 border border-gray-100">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Branch Performance</h3>
                            </div>
                            <div class="overflow-x-auto -mx-5 sm:-mx-6">
                                <div class="inline-block min-w-full align-middle px-5 sm:px-6">
                                    <table class="min-w-full">
                                        <thead>
                                            <tr class="border-b border-gray-100">
                                                <th class="pb-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Branch</th>
                                                <th class="pb-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Members</th>
                                                <th class="pb-3 text-right text-[11px] font-bold text-gray-400 uppercase tracking-wider">Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            @foreach($branchPerformance as $branch)
                                                <tr class="hover:bg-gray-50/50 transition-colors">
                                                    <td class="py-3.5">
                                                        <span class="text-sm font-semibold text-gray-800">{{ $branch['name'] }}</span>
                                                    </td>
                                                    <td class="py-3.5">
                                                        <span class="inline-flex items-center text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-full">{{ $branch['member_count'] }}</span>
                                                    </td>
                                                    <td class="py-3.5 text-right">
                                                        <span class="text-sm font-bold text-emerald-600">₱{{ number_format($branch['monthly_revenue'], 2) }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Revenue & Sales Trend Chart --}}
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow duration-300 p-5 sm:p-6 border border-gray-100">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Sales Trend</h3>
                                    <p class="text-xs text-gray-400" id="salesPeriodLabel">
                                        @if($salesPeriod === 'today') Hourly breakdown for today
                                        @elseif($salesPeriod === 'week') Daily breakdown for this week
                                        @else Weekly breakdown for this month
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-1.5 bg-gray-100 p-1 rounded-xl">
                                <button data-sales-period="today" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $salesPeriod === 'today' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Today</button>
                                <button data-sales-period="week" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $salesPeriod === 'week' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Week</button>
                                <button data-sales-period="month" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $salesPeriod === 'month' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Month</button>
                            </div>
                        </div>
                        <div class="h-64 sm:h-72">
                            <canvas id="salesTrendChart"></canvas>
                        </div>
                    </div>

                    {{-- Membership & Subscription Growth Trends --}}
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 lg:gap-6">
                        
                        {{-- Membership Growth --}}
                        <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow duration-300 p-5 sm:p-6 border border-gray-100">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-800">Membership Growth</h3>
                                        <p class="text-[11px] text-gray-400" id="membershipPeriodLabel">
                                            @if($membershipPeriod === 'today') Hourly breakdown for today
                                            @elseif($membershipPeriod === 'week') Daily breakdown for this week
                                            @else Weekly breakdown for this month
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-1.5 bg-gray-100 p-1 rounded-xl">
                                    <button data-membership-period="today" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $membershipPeriod === 'today' ? 'bg-white text-emerald-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Today</button>
                                    <button data-membership-period="week" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $membershipPeriod === 'week' ? 'bg-white text-emerald-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Week</button>
                                    <button data-membership-period="month" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $membershipPeriod === 'month' ? 'bg-white text-emerald-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Month</button>
                                </div>
                            </div>
                            <div class="h-56 sm:h-64">
                                <canvas id="membershipTrendChart"></canvas>
                            </div>
                        </div>

                        {{-- Subscription Growth --}}
                        <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow duration-300 p-5 sm:p-6 border border-gray-100">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-800">Subscription Growth</h3>
                                        <p class="text-[11px] text-gray-400" id="subscriptionPeriodLabel">
                                            @if($subscriptionPeriod === 'today') Hourly breakdown for today
                                            @elseif($subscriptionPeriod === 'week') Daily breakdown for this week
                                            @else Weekly breakdown for this month
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-1.5 bg-gray-100 p-1 rounded-xl">
                                    <button data-subscription-period="today" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $subscriptionPeriod === 'today' ? 'bg-white text-teal-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Today</button>
                                    <button data-subscription-period="week" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $subscriptionPeriod === 'week' ? 'bg-white text-teal-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Week</button>
                                    <button data-subscription-period="month" class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $subscriptionPeriod === 'month' ? 'bg-white text-teal-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">Month</button>
                                </div>
                            </div>
                            <div class="h-56 sm:h-64">
                                <canvas id="subscriptionTrendChart"></canvas>
                            </div>
                        </div>

                    </div>

                    <!-- Product & Membership Performance -->
                    {{-- Performance Overview --}}
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow duration-300 p-5 sm:p-6 border border-gray-100">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-9 h-9 bg-gradient-to-br from-amber-500 to-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Performance Overview</h3>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Top Product --}}
                            <div class="rounded-xl p-4 bg-gradient-to-br from-emerald-50 to-green-50 border border-emerald-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 bg-emerald-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                                    </div>
                                    <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Top Product</span>
                                </div>
                                @if($highestSellingProduct && $highestSellingProduct->product)
                                    <p class="text-base font-bold text-gray-800 truncate">{{ $highestSellingProduct->product->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $highestSellingProduct->total_sold }} units sold</p>
                                @else
                                    <p class="text-sm text-gray-400">No data available</p>
                                @endif
                            </div>

                            {{-- Needs Attention --}}
                            <div class="rounded-xl p-4 bg-gradient-to-br from-red-50 to-orange-50 border border-red-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 bg-red-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" /></svg>
                                    </div>
                                    <span class="text-xs font-bold text-red-700 uppercase tracking-wider">Needs Attention</span>
                                </div>
                                @if($lowestSellingProduct && $lowestSellingProduct->product)
                                    <p class="text-base font-bold text-red-600 truncate">{{ $lowestSellingProduct->product->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $lowestSellingProduct->total_sold }} units sold</p>
                                @else
                                    <p class="text-sm text-gray-400">No data available</p>
                                @endif
                            </div>

                            {{-- Top Plan --}}
                            <div class="rounded-xl p-4 bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 bg-purple-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                                    </div>
                                    <span class="text-xs font-bold text-purple-700 uppercase tracking-wider">Top Plan</span>
                                </div>
                                @if($mostPopularPlan && $mostPopularPlan->plan)
                                    <p class="text-base font-bold text-gray-800 truncate">{{ $mostPopularPlan->plan->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $mostPopularPlan->total_sales }} memberships sold</p>
                                @else
                                    <p class="text-sm text-gray-400">No data available</p>
                                @endif
                            </div>

                            {{-- Top Subscription --}}
                            <div class="rounded-xl p-4 bg-gradient-to-br from-teal-50 to-cyan-50 border border-teal-100">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 bg-teal-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <span class="text-xs font-bold text-teal-700 uppercase tracking-wider">Top Subscription</span>
                                </div>
                                @if($mostPopularSubscription)
                                    <p class="text-base font-bold text-gray-800 truncate">{{ $mostPopularSubscription->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $mostPopularSubscription->member_count }} active members</p>
                                @else
                                    <p class="text-sm text-gray-400">No data available</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right Column - Stats & Activity --}}
                <div class="space-y-5 lg:space-y-6">

                    {{-- Monthly Overview --}}
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow duration-300 p-5 sm:p-6 border border-gray-100">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-9 h-9 bg-gradient-to-br from-slate-600 to-gray-800 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Monthly Overview</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">Stock In</span>
                                </div>
                                <span class="text-xl font-black text-emerald-600">{{ $stockInCount }}</span>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl bg-red-50 border border-red-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">Stock Out</span>
                                </div>
                                <span class="text-xl font-black text-red-600">{{ $stockOutCount }}</span>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl bg-blue-50 border border-blue-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700">Total Sales</span>
                                </div>
                                <span class="text-xl font-black text-blue-600">{{ $todaySales }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Revenue Breakdown --}}
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow duration-300 p-5 sm:p-6 border border-gray-100">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-9 h-9 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Revenue Breakdown</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="p-3 rounded-xl bg-blue-50 border border-blue-100">
                                <p class="text-[11px] font-bold text-blue-400 uppercase tracking-wider mb-0.5">Product Sales</p>
                                <p class="text-xl font-black text-gray-900">₱{{ number_format($revenueBreakdown['products'], 2) }}</p>
                            </div>
                            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100">
                                <p class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider mb-0.5">Membership Plans</p>
                                <p class="text-xl font-black text-gray-900">₱{{ number_format($revenueBreakdown['memberships'], 2) }}</p>
                            </div>
                            <div class="p-3 rounded-xl bg-teal-50 border border-teal-100">
                                <p class="text-[11px] font-bold text-teal-400 uppercase tracking-wider mb-0.5">Subscriptions</p>
                                <p class="text-xl font-black text-gray-900">₱{{ number_format($revenueBreakdown['subscriptions'], 2) }}</p>
                            </div>
                            <div class="p-4 rounded-xl bg-gradient-to-r from-purple-500 to-pink-500 text-white">
                                <p class="text-[11px] font-bold text-purple-100 uppercase tracking-wider mb-0.5">Total Revenue</p>
                                <p class="text-2xl font-black">₱{{ number_format($revenueBreakdown['total'], 2) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Sales --}}
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-shadow duration-300 p-5 sm:p-6 border border-gray-100">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-9 h-9 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">Recent Sales</h3>
                        </div>
                        <div class="space-y-3 max-h-80 sm:max-h-96 overflow-y-auto pr-1">
                            @forelse($recentSales as $sale)
                                <div class="p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-all border border-gray-100">
                                    <div class="flex items-center justify-between mb-2 gap-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-bold text-gray-800 text-sm truncate">Sale #{{ $sale->sales_id }}</p>
                                            <p class="text-[11px] text-gray-400">{{ $sale->created_at->diffForHumans() }}</p>
                                            @if($sale->branch)
                                                <p class="text-[11px] text-blue-500 font-semibold mt-0.5 truncate">{{ $sale->branch->name }}</p>
                                            @endif
                                        </div>
                                        <span class="font-black text-emerald-600 text-sm whitespace-nowrap">₱{{ number_format($sale->total_amount, 2) }}</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($sale->items as $item)
                                            @if($item->product)
                                                <span class="text-[10px] font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full truncate max-w-full">{{ $item->product->name }}</span>
                                            @elseif($item->plan)
                                                <span class="text-[10px] font-semibold bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full truncate max-w-full">{{ $item->plan->name }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                    <p class="text-sm text-gray-400 font-medium">No recent sales</p>
                                </div>
                            @endforelse
                        </div>
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
            let subscriptionTrendChart = null;

            // Initialize charts on page load
            document.addEventListener('DOMContentLoaded', function () {
                initializeSalesTrendChart();
                initializeMembershipTrendChart();
                initializeSubscriptionTrendChart();
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
                            borderWidth: 3,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                        }, {
                            label: 'Sales Count',
                            data: salesData.map(item => item.total_sales),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y1',
                            borderWidth: 3,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointBackgroundColor: 'rgb(34, 197, 94)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
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
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: window.innerWidth < 640 ? 10 : 12,
                                        weight: 'bold'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            if (context.datasetIndex === 0) {
                                                label += '₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                            } else {
                                                label += context.parsed.y;
                                            }
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                beginAtZero: true,
                                suggestedMax: Math.max(...salesData.map(item => item.total_revenue)) * 1.2 || 100,
                                ticks: {
                                    callback: function(value) {
                                        return '₱' + value.toLocaleString();
                                    },
                                    font: {
                                        size: window.innerWidth < 640 ? 9 : 11
                                    }
                                },
                                title: {
                                    display: window.innerWidth >= 640,
                                    text: 'Revenue (₱)',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    drawBorder: false,
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true,
                                suggestedMax: Math.max(...salesData.map(item => item.total_sales)) * 1.2 || 10,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: window.innerWidth < 640 ? 9 : 11
                                    }
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
                                title: {
                                    display: window.innerWidth >= 640,
                                    text: 'Sales Count',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: window.innerWidth < 640 ? 9 : 11
                                    },
                                    maxRotation: window.innerWidth < 640 ? 45 : 0,
                                    minRotation: window.innerWidth < 640 ? 45 : 0
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
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: Math.max(...membershipData.map(item => item.count)) * 1.2 || 10,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: window.innerWidth < 640 ? 9 : 11
                                    }
                                },
                                grid: {
                                    drawBorder: false,
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: window.innerWidth < 640 ? 9 : 11
                                    },
                                    maxRotation: window.innerWidth < 640 ? 45 : 0,
                                    minRotation: window.innerWidth < 640 ? 45 : 0
                                }
                            }
                        }
                    }
                });
            }

            // Initialize Subscription Trend Chart
            function initializeSubscriptionTrendChart() {
                const subscriptionCtx = document.getElementById('subscriptionTrendChart').getContext('2d');
                const subscriptionData = @json($subscriptionTrend);

                subscriptionTrendChart = new Chart(subscriptionCtx, {
                    type: 'line',
                    data: {
                        labels: subscriptionData.map(item => item.label),
                        datasets: [{
                            label: 'New Subscriptions',
                            data: subscriptionData.map(item => item.count),
                            borderColor: 'rgb(20, 184, 166)',
                            backgroundColor: 'rgba(20, 184, 166, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y',
                            borderWidth: 3,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointBackgroundColor: 'rgb(20, 184, 166)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                        }, {
                            label: 'Revenue (₱)',
                            data: subscriptionData.map(item => item.total_revenue || 0),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            yAxisID: 'y1',
                            borderWidth: 3,
                            pointRadius: 6,
                            pointHoverRadius: 8,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
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
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: window.innerWidth < 640 ? 10 : 12,
                                        weight: 'bold'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            if (context.datasetIndex === 1) {
                                                label += '₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                            } else {
                                                label += context.parsed.y;
                                            }
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                beginAtZero: true,
                                suggestedMax: Math.max(...subscriptionData.map(item => item.count)) * 1.2 || 10,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: window.innerWidth < 640 ? 9 : 11
                                    }
                                },
                                title: {
                                    display: window.innerWidth >= 640,
                                    text: 'Subscriptions',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    }
                                },
                                grid: {
                                    drawBorder: false,
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true,
                                suggestedMax: Math.max(...subscriptionData.map(item => item.total_revenue || 0)) * 1.2 || 100,
                                ticks: {
                                    callback: function(value) {
                                        return '₱' + value.toLocaleString();
                                    },
                                    font: {
                                        size: window.innerWidth < 640 ? 9 : 11
                                    }
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
                                title: {
                                    display: window.innerWidth >= 640,
                                    text: 'Revenue (₱)',
                                    font: {
                                        size: 12,
                                        weight: 'bold'
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: window.innerWidth < 640 ? 9 : 11
                                    },
                                    maxRotation: window.innerWidth < 640 ? 45 : 0,
                                    minRotation: window.innerWidth < 640 ? 45 : 0
                                }
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

                // Subscription Trend filter buttons
                document.querySelectorAll('[data-subscription-period]').forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        const period = this.getAttribute('data-subscription-period');
                        updateSubscriptionTrendChart(period);
                    });
                });
            }

            // Update Sales Trend Chart via AJAX
            function updateSalesTrendChart(period) {
                showChartLoading('salesTrendChart');
                document.querySelectorAll('[data-sales-period]').forEach(btn => {
                    if (btn.getAttribute('data-sales-period') === period) {
                        btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        btn.classList.add('bg-blue-500', 'text-white', 'shadow-md');
                    } else {
                        btn.classList.remove('bg-blue-500', 'text-white', 'shadow-md');
                        btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    }
                });

                const url = new URL(window.location.href);
                url.searchParams.set('ajax', '1');
                url.searchParams.set('sales_period', period);

                fetch(url.toString(), {
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.salesTrend) {
                            salesTrendChart.data.labels = data.salesTrend.map(item => item.label);
                            salesTrendChart.data.datasets[0].data = data.salesTrend.map(item => item.total_revenue);
                            salesTrendChart.data.datasets[1].data = data.salesTrend.map(item => item.total_sales);
                            
                            // Update scale max values dynamically
                            const maxRevenue = Math.max(...data.salesTrend.map(item => item.total_revenue));
                            const maxSales = Math.max(...data.salesTrend.map(item => item.total_sales));
                            salesTrendChart.options.scales.y.suggestedMax = maxRevenue * 1.2 || 100;
                            salesTrendChart.options.scales.y1.suggestedMax = maxSales * 1.2 || 10;
                            
                            salesTrendChart.update();
                            updatePeriodLabel('sales', period);
                        }
                        hideChartLoading('salesTrendChart');
                    })
                    .catch(error => {
                        console.error('Error updating sales trend:', error);
                        hideChartLoading('salesTrendChart');
                        alert('Failed to update chart. Please try again.');
                    });
            }

            // Update Membership Trend Chart via AJAX
            function updateMembershipTrendChart(period) {
                showChartLoading('membershipTrendChart');
                document.querySelectorAll('[data-membership-period]').forEach(btn => {
                    if (btn.getAttribute('data-membership-period') === period) {
                        btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        btn.classList.add('bg-green-500', 'text-white', 'shadow-md');
                    } else {
                        btn.classList.remove('bg-green-500', 'text-white', 'shadow-md');
                        btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    }
                });

                const url = new URL(window.location.href);
                url.searchParams.set('ajax', '1');
                url.searchParams.set('membership_period', period);

                fetch(url.toString(), {
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.membershipTrend) {
                            membershipTrendChart.data.labels = data.membershipTrend.map(item => item.label);
                            membershipTrendChart.data.datasets[0].data = data.membershipTrend.map(item => item.count);
                            
                            // Update scale max value dynamically
                            const maxCount = Math.max(...data.membershipTrend.map(item => item.count));
                            membershipTrendChart.options.scales.y.suggestedMax = maxCount * 1.2 || 10;
                            
                            membershipTrendChart.update();
                            updatePeriodLabel('membership', period);
                        }
                        hideChartLoading('membershipTrendChart');
                    })
                    .catch(error => {
                        console.error('Error updating membership trend:', error);
                        hideChartLoading('membershipTrendChart');
                        alert('Failed to update chart. Please try again.');
                    });
            }

            // Update Subscription Trend Chart via AJAX
            function updateSubscriptionTrendChart(period) {
                showChartLoading('subscriptionTrendChart');
                document.querySelectorAll('[data-subscription-period]').forEach(btn => {
                    if (btn.getAttribute('data-subscription-period') === period) {
                        btn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                        btn.classList.add('bg-teal-500', 'text-white', 'shadow-md');
                    } else {
                        btn.classList.remove('bg-teal-500', 'text-white', 'shadow-md');
                        btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
                    }
                });

                const url = new URL(window.location.href);
                url.searchParams.set('ajax', '1');
                url.searchParams.set('subscription_period', period);

                fetch(url.toString(), {
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.subscriptionTrend) {
                            subscriptionTrendChart.data.labels = data.subscriptionTrend.map(item => item.label);
                            subscriptionTrendChart.data.datasets[0].data = data.subscriptionTrend.map(item => item.count);
                            subscriptionTrendChart.data.datasets[1].data = data.subscriptionTrend.map(item => item.total_revenue || 0);
                            
                            // Update scale max values dynamically
                            const maxCount = Math.max(...data.subscriptionTrend.map(item => item.count));
                            const maxRevenue = Math.max(...data.subscriptionTrend.map(item => item.total_revenue || 0));
                            subscriptionTrendChart.options.scales.y.suggestedMax = maxCount * 1.2 || 10;
                            subscriptionTrendChart.options.scales.y1.suggestedMax = maxRevenue * 1.2 || 100;
                            
                            subscriptionTrendChart.update();
                            updatePeriodLabel('subscription', period);
                        }
                        hideChartLoading('subscriptionTrendChart');
                    })
                    .catch(error => {
                        console.error('Error updating subscription trend:', error);
                        hideChartLoading('subscriptionTrendChart');
                        alert('Failed to update chart. Please try again.');
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