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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            
            <!-- Membership Status Card -->
            <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 
                {{ !$memberProfile ? 'border-gray-400' : ($membershipStatus === 'expired' ? 'border-red-500' : 'border-green-500') }}">
                
                @if($memberProfile)
                    <!-- Header with Status -->
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-gray-500 text-xs font-medium">Membership Status</p>
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold
                            {{ $membershipStatus === 'expired' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $membershipStatus === 'expired' ? 'Expired' : 'Active' }}
                        </span>
                    </div>

                    <!-- Plan Info -->
                    <div class="mb-3">
                        <h4 class="text-lg font-bold text-gray-800">{{ $memberProfile->plan->name ?? 'No Plan' }}</h4>
                        @if($memberProfile->plan)
                            <p class="text-xs text-gray-600">₱{{ number_format($memberProfile->plan->price, 2) }} / {{ $memberProfile->plan->duration_days }} days</p>
                        @endif
                    </div>

                    <!-- Days/Hours Remaining -->
                    @if($daysLeft !== null || ($memberProfile->plan && strtolower($memberProfile->plan->name) === 'walk-in'))
                        @php
                            $isWalkIn = $memberProfile->plan && strtolower($memberProfile->plan->name) === 'walk-in';
                            
                            if ($isWalkIn) {
                                // Calculate hours remaining for walk-in
                                $now = \Carbon\Carbon::now();
                                $endDateTime = \Carbon\Carbon::parse($memberProfile->end_date);
                                $hoursLeft = $now->diffInHours($endDateTime, false);
                                $hoursLeft = (int) ceil($hoursLeft);
                            }
                        @endphp
                        
                        <div class="flex items-center justify-between bg-gradient-to-r {{ ($isWalkIn ? $hoursLeft : $daysLeft) <= 0 ? 'from-red-50 to-red-100' : 'from-blue-50 to-blue-100' }} p-3 rounded-lg mb-2">
                            <div>
                                <p class="text-xs text-gray-600 font-medium">
                                    {{ ($isWalkIn ? $hoursLeft : $daysLeft) > 0 ? ($isWalkIn ? 'Hours Left' : 'Days Left') : 'Overdue' }}
                                </p>
                                <p class="text-2xl font-bold {{ ($isWalkIn ? $hoursLeft : $daysLeft) <= 0 ? 'text-red-600' : 'text-blue-600' }}">
                                    {{ $isWalkIn ? abs($hoursLeft) : abs($daysLeft) }}
                                </p>
                            </div>
                            <svg class="w-8 h-8 {{ ($isWalkIn ? $hoursLeft : $daysLeft) <= 0 ? 'text-red-400' : 'text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($isWalkIn)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                @endif
                            </svg>
                        </div>
                    @endif

                    <!-- Expiring Soon Warning (only for non-walk-in) -->
                    @if($isExpiringSoon && $daysLeft > 0 && $daysLeft <= 7 && !($memberProfile->plan && strtolower($memberProfile->plan->name) === 'walk-in'))
                        <div class="bg-orange-50 border-l-2 border-orange-400 p-2 rounded text-xs text-orange-800 mb-2">
                            ⚠️ Membership expiring soon
                        </div>
                    @endif

                    <!-- Action Button -->
                    @if($daysLeft !== null && $daysLeft <= 0)
                        <a href="#plans" class="block w-full text-center bg-red-600 text-white py-2 px-3 rounded-lg hover:bg-red-700 transition-colors font-medium text-xs">
                            Renew Now
                        </a>
                    @endif

                @else
                    <!-- No Membership -->
                    <div class="text-center py-3">
                        <p class="text-gray-500 text-xs mb-2">Membership Status</p>
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <p class="text-gray-600 font-medium text-sm mb-2">No Active Membership</p>
                        <a href="#plans" class="inline-block bg-green-500 text-white px-4 py-1.5 rounded-lg hover:bg-green-600 transition-colors text-xs font-medium">
                            Get Started
                        </a>
                    </div>
                @endif
            </div>

            <!-- Gym Occupancy Card -->
            <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-blue-500">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-gray-500 text-xs font-medium">Gym Occupancy</p>
                    <div class="bg-blue-100 p-2 rounded-full">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                
                <h3 class="text-3xl font-bold text-blue-600 mb-1">{{ $currentOccupancy }}</h3>
                <p class="text-xs text-gray-600 mb-3">Members in gym now</p>
                
                @if($currentOccupancy < 10)
                    <span class="inline-flex items-center bg-green-100 text-green-700 px-3 py-1.5 rounded-lg text-xs font-semibold w-full justify-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                        Low Traffic
                    </span>
                @elseif($currentOccupancy < 25)
                    <span class="inline-flex items-center bg-yellow-100 text-yellow-700 px-3 py-1.5 rounded-lg text-xs font-semibold w-full justify-center">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></span>
                        Moderate Traffic
                    </span>
                @else
                    <span class="inline-flex items-center bg-red-100 text-red-700 px-3 py-1.5 rounded-lg text-xs font-semibold w-full justify-center">
                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                        High Traffic
                    </span>
                @endif
            </div>

            <!-- My Attendance Card -->
            <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-purple-500">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-gray-500 text-xs font-medium">My Check-ins</p>
                    <div class="bg-purple-100 p-2 rounded-full">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                
                <h3 class="text-3xl font-bold text-purple-600 mb-1">{{ $thisMonthCheckIns }}</h3>
                <p class="text-xs text-gray-600 mb-3">This month</p>
                
                <div class="bg-gray-50 p-2.5 rounded-lg">
                    <p class="text-xs text-gray-500">Total Check-ins</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalCheckIns }}</p>
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