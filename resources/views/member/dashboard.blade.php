@extends('layouts.app')
@section('title', 'Home')
@section('header', 'Home')

@section('content')

<body class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 min-h-screen">
    <div class="w-full px-4 py-6">
        
        <!-- Welcome Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome back, {{ Auth::user()->first_name }}!</h2>
            <p class="text-gray-600">Here's your fitness journey overview</p>
        </div>

        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            
            <!-- Membership Status Card - REDESIGNED -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 h-64
                {{ !$memberProfile ? 'border-gray-400' : ($membershipStatus === 'expired' ? 'border-red-500' : 'border-green-500') }}">
                
                @if($memberProfile)
                    <!-- Header with Status -->
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-gray-500 text-xs font-medium uppercase">Membership Status</p>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold
                            {{ $membershipStatus === 'expired' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $membershipStatus === 'expired' ? 'Expired' : 'Active' }}
                        </span>
                    </div>

                    <div class="space-y-3">
                        <!-- Subscription Info with Days Left -->
                        @if($memberProfile->subscription)
                            <div class="pb-3 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 mb-1">Subscription</p>
                                        <h5 class="text-base font-bold text-gray-800 truncate">{{ $memberProfile->subscription->name }}</h5>
                                        <p class="text-xs text-gray-600">₱{{ number_format($memberProfile->subscription->price, 2) }}</p>
                                    </div>
                                    @if($memberProfile->end_date_for_subscription)
                                        @php
                                            $now = \Carbon\Carbon::now();
                                            $endDate = \Carbon\Carbon::parse($memberProfile->end_date_for_subscription);
                                            $subDaysLeft = (int) ceil($now->diffInDays($endDate, false));
                                        @endphp
                                        <div class="text-right ml-2">
                                            <p class="text-2xl font-bold {{ $subDaysLeft <= 0 ? 'text-red-600' : 'text-blue-600' }}">
                                                {{ abs($subDaysLeft) }}
                                            </p>
                                            <p class="text-xs text-gray-500">days {{ $subDaysLeft > 0 ? 'left' : 'overdue' }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Membership Plan Info with Days Left -->
                        @if($memberProfile->plan)
                            <div class="pb-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 mb-1">Membership Plan</p>
                                        <h4 class="text-base font-bold text-gray-800 truncate">{{ $memberProfile->plan->name }}</h4>
                                        <p class="text-xs text-gray-600">₱{{ number_format($memberProfile->plan->price, 2) }}</p>
                                    </div>
                                    @if($memberProfile->end_date)
                                        @php
                                            $now = \Carbon\Carbon::now();
                                            $endDate = \Carbon\Carbon::parse($memberProfile->end_date);
                                            $planDaysLeft = (int) ceil($now->diffInDays($endDate, false));
                                        @endphp
                                        <div class="text-right ml-2">
                                            <p class="text-2xl font-bold {{ $planDaysLeft <= 0 ? 'text-red-600' : 'text-green-600' }}">
                                                {{ abs($planDaysLeft) }}
                                            </p>
                                            <p class="text-xs text-gray-500">days {{ $planDaysLeft > 0 ? 'left' : 'overdue' }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Action Button (only if expired) -->
                    @if($membershipStatus === 'expired')
                        <div class="mt-auto pt-3">
                            <a href="#plans" class="block w-full text-center bg-red-600 text-white py-2 px-3 rounded-lg hover:bg-red-700 transition-colors font-medium text-xs">
                                Renew Now
                            </a>
                        </div>
                    @endif

                @else
                    <!-- No Membership -->
                    <div class="flex flex-col items-center justify-center h-full text-center py-3">
                        <p class="text-gray-500 text-xs mb-3">Membership Status</p>
                        <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <p class="text-gray-600 font-medium text-base mb-3">No Active Membership</p>
                        <a href="#plans" class="inline-block bg-green-500 text-white px-5 py-2 rounded-lg hover:bg-green-600 transition-colors text-sm font-medium">
                            Get Started
                        </a>
                    </div>
                @endif
            </div>

            <!-- Gym Occupancy Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 h-64">
                <div class="flex flex-col h-full">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-gray-500 text-xs font-medium">Gym Occupancy</p>
                        <div class="bg-blue-100 p-2 rounded-full">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    
                    <div class="flex-1 flex flex-col justify-center">
                        <h3 class="text-4xl font-bold text-blue-600 mb-2">{{ $currentOccupancy }}</h3>
                        <p class="text-sm text-gray-600 mb-4">Members in gym now</p>
                    </div>
                    
                    <div class="mt-auto">
                        @if($currentOccupancy < 10)
                            <span class="inline-flex items-center bg-green-100 text-green-700 px-3 py-2 rounded-lg text-xs font-semibold w-full justify-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Low Traffic
                            </span>
                        @elseif($currentOccupancy < 25)
                            <span class="inline-flex items-center bg-yellow-100 text-yellow-700 px-3 py-2 rounded-lg text-xs font-semibold w-full justify-center">
                                <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                                Moderate Traffic
                            </span>
                        @else
                            <span class="inline-flex items-center bg-red-100 text-red-700 px-3 py-2 rounded-lg text-xs font-semibold w-full justify-center">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                High Traffic
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- My Attendance Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 h-64">
                <div class="flex flex-col h-full">
                    <div class="flex items-center justify-between mb-4">
                        <p class="text-gray-500 text-xs font-medium">My Check-ins</p>
                        <div class="bg-purple-100 p-2 rounded-full">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    
                    <div class="flex-1 flex flex-col justify-center">
                        <h3 class="text-4xl font-bold text-purple-600 mb-2">{{ $thisMonthCheckIns }}</h3>
                        <p class="text-sm text-gray-600 mb-4">This month</p>
                    </div>
                    
                    <div class="mt-auto bg-gray-50 p-3 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Total Check-ins</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalCheckIns }}</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column (2 columns) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Membership Plans Section -->
                <div id="plans" class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Available Membership Plans</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($plans as $plan)
                        <div class="bg-white border-2 border-gray-200 rounded-2xl p-5 hover:shadow-lg hover:border-gray-300 transition-all duration-200
                            {{ $memberProfile && $memberProfile->plan_id === $plan->plan_id ? 'border-green-500 bg-green-50' : '' }}">
                            
                            @if($memberProfile && $memberProfile->plan_id === $plan->plan_id)
                                <span class="inline-block bg-green-500 text-white text-xs px-2 py-1 rounded-full mb-2">Current Plan</span>
                            @endif
                            
                            <!-- Plan Header -->
                            <div class="mb-3">
                                <h4 class="text-xl font-bold text-gray-900 mb-1">{{ $plan->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $plan->details }}</p>
                            </div>

                            <!-- Plan Details -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600 font-medium">Price</span>
                                    <span class="text-lg font-bold text-gray-900">₱{{ number_format($plan->price) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-600 font-medium">Duration</span>
                                    <span class="text-sm font-semibold text-gray-700">{{ $plan->duration_days }} days</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 col-span-2 text-center py-4">No membership plans available</p>
                        @endforelse
                    </div>
                </div>

                <!-- Subscriptions Section -->
                <div id="subscriptions" class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Available Subscriptions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($subscriptions as $subscription)
                        <div class="bg-white border-2 border-gray-200 rounded-2xl p-5 hover:shadow-lg hover:border-gray-300 transition-all duration-200
                            {{ $memberProfile && $memberProfile->subscription_id === $subscription->subscription_id ? 'border-blue-500 bg-blue-50' : '' }}">
                            
                            @if($memberProfile && $memberProfile->subscription_id === $subscription->subscription_id)
                                <span class="inline-block bg-blue-500 text-white text-xs px-2 py-1 rounded-full mb-2">Current Subscription</span>
                            @endif
                            
                            <!-- Subscription Header -->
                            <div class="mb-3">
                                <h4 class="text-xl font-bold text-gray-900 mb-1">{{ $subscription->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $subscription->details }}</p>
                            </div>

                            <!-- Subscription Details -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600 font-medium">Price</span>
                                    <span class="text-lg font-bold text-gray-900">₱{{ number_format($subscription->price) }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-gray-600 font-medium">Duration</span>
                                    <span class="text-sm font-semibold text-gray-700">{{ $subscription->duration_days }} days</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 col-span-2 text-center py-4">No subscriptions available</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Right Column (1 column) -->
            <div class="space-y-6">
                
                <!-- Recent Check-ins -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Recent Check-ins</h3>
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @forelse($recentAttendance as $attendance)
                        <div class="p-3 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-100">
                            <div class="flex items-center justify-between mb-1">
                                <p class="font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('M d, Y') }}
                                </p>
                                @if($attendance->status === 'checked_in')
                                    <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full">Active</span>
                                @else
                                    <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">Completed</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600">
                                Check-in: {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}
                            </p>
                            @if($attendance->check_out_time)
                                <p class="text-sm text-gray-600">
                                    Check-out: {{ \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') }}
                                </p>
                            @endif
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">No check-in history yet</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>
</body>

@endsection