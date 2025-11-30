@extends('layouts.app')
@section('title', 'My Dashboard')
@section('header', 'My Dashboard')

@section('content')

<body class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 min-h-screen">
    <div class="w-full px-4 py-6">
        
        <!-- Welcome Section -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome back, {{ Auth::user()->first_name }}!</h2>
            <p class="text-gray-600">Here's your fitness journey overview</p>
        </div>

        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            
            <!-- Membership Status Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 
                {{ $membershipStatus === 'expired' ? 'border-red-500' : ($membershipStatus === 'expiring_soon' ? 'border-orange-500' : 'border-green-500') }}">
                <div class="flex items-center justify-between">
                    <div class="w-full">
                        <p class="text-gray-500 text-sm font-medium mb-2">Membership Status</p>
                        @if($memberProfile)
                            <h3 class="text-2xl font-bold mb-2
                                {{ $membershipStatus === 'expired' ? 'text-red-600' : ($membershipStatus === 'expiring_soon' ? 'text-orange-600' : 'text-green-600') }}">
                                {{ ucfirst(str_replace('_', ' ', $membershipStatus)) }}
                            </h3>
                            <div class="mb-3">
                                <p class="text-sm text-gray-700 font-medium">{{ $memberProfile->plan->name ?? 'No Plan' }}</p>
                                <p class="text-xs text-gray-500">
                                    Started: {{ \Carbon\Carbon::parse($memberProfile->start_date)->format('M d, Y') }}
                                </p>
                            </div>
                            
                            @if($daysLeft !== null)
                                @if($daysLeft > 0)
                                    <div class="bg-blue-50 p-3 rounded-lg">
                                        <p class="text-sm text-gray-600">Days Remaining</p>
                                        <p class="text-3xl font-bold text-blue-600">{{ $daysLeft }}</p>
                                    </div>
                                @else
                                    <div class="bg-red-50 p-3 rounded-lg">
                                        <p class="text-sm text-red-600 font-medium">Membership Expired</p>
                                        <p class="text-xs text-gray-600">{{ abs($daysLeft) }} days ago</p>
                                    </div>
                                @endif
                            @endif

                            @if($isExpiringSoon && $daysLeft > 0)
                                <div class="mt-3 bg-orange-50 border border-orange-200 rounded-lg p-3">
                                    <p class="text-xs text-orange-700 font-medium">⚠️ Renew soon to avoid interruption</p>
                                </div>
                            @endif
                        @else
                            <p class="text-gray-600">No active membership</p>
                            <a href="#plans" class="mt-3 inline-block bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors text-sm font-medium">
                                View Plans
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Gym Occupancy Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Current Gym Occupancy</p>
                        <h3 class="text-4xl font-bold text-blue-600 mt-2">{{ $currentOccupancy }}</h3>
                        <p class="text-gray-600 text-sm mt-2">Members currently in gym</p>
                        <div class="mt-4">
                            @if($currentOccupancy < 10)
                                <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-medium">
                                    🟢 Low Traffic
                                </span>
                            @elseif($currentOccupancy < 25)
                                <span class="inline-block bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-medium">
                                    🟡 Moderate Traffic
                                </span>
                            @else
                                <span class="inline-block bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">
                                    🔴 High Traffic
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-blue-100 p-4 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- My Attendance Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">My Check-ins</p>
                        <h3 class="text-4xl font-bold text-purple-600 mt-2">{{ $thisMonthCheckIns }}</h3>
                        <p class="text-gray-600 text-sm mt-2">This month</p>
                    </div>
                    <div class="bg-purple-100 p-4 rounded-full">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="border-t pt-3">
                    <p class="text-xs text-gray-500">Total all-time check-ins</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalCheckIns }}</p>
                </div>
            </div>

        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column (2 columns) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- My Profile Information -->
                

                <!-- Membership Plans Section -->
                <div id="plans" class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Available Membership Plans</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($membershipPlans as $plan)
                        <div class="border-2 rounded-lg p-5 hover:shadow-xl transition-all hover:scale-105
                            {{ $memberProfile && $memberProfile->plan_id === $plan->plan_id ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                            @if($memberProfile && $memberProfile->plan_id === $plan->plan_id)
                                <span class="inline-block bg-green-500 text-white text-xs px-2 py-1 rounded-full mb-2">Current Plan</span>
                            @endif
                            <h4 class="text-xl font-bold text-gray-800 mb-2">{{ $plan->name }}</h4>
                            <p class="text-gray-600 text-sm mb-3">{{ $plan->details }}</p>
                            <div class="mb-3">
                                <p class="text-3xl font-bold text-green-600">₱{{ number_format($plan->price, 2) }}</p>
                                <p class="text-sm text-gray-500">{{ $plan->duration_days }} days</p>
                            </div>
                            @if(!$memberProfile || $memberProfile->plan_id !== $plan->plan_id)
                                <button class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors font-medium">
                                    Select Plan
                                </button>
                            @endif
                        </div>
                        @empty
                        <p class="text-gray-500 col-span-2 text-center py-4">No membership plans available</p>
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