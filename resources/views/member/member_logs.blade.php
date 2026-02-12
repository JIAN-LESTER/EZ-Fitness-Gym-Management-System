@extends('layouts.app')
@section('title', 'Attendance History')
@section('header', 'Attendance History')



@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="container-fluid px-6 py-6">
        <!-- Compact Header Section -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-1">My Attendance History</h1>
            <p class="text-gray-600">Track your gym journey and progress</p>
        </div>

        <!-- Compact Statistics Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <!-- This Month Card -->
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">This Month</p>
                        <p class="text-3xl font-bold text-gray-600 mt-1">{{ $thisMonthCount ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1">Check-ins in {{ now()->format('F') }}</p>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Last Check-in Card -->
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Last Check-in</p>
                        <p class="text-lg font-bold text-green-600 mt-1">
                            @if(isset($lastCheckIn) && $lastCheckIn && isset($lastCheckIn->check_in_time) && $lastCheckIn->check_in_time)
                                {{ \Carbon\Carbon::parse($lastCheckIn->check_in_time)->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            @if(isset($lastCheckIn) && $lastCheckIn && isset($lastCheckIn->check_in_time) && $lastCheckIn->check_in_time)
                                {{ \Carbon\Carbon::parse($lastCheckIn->check_in_time)->format('M d, h:i A') }}
                            @else
                                &nbsp;
                            @endif
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Check-ins Card -->
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Check-ins</p>
                        <p class="text-3xl font-bold text-purple-600 mt-1">{{ $attendances->total() }}</p>
                        <p class="text-xs text-gray-500 mt-1">All time records</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Current Plan Card -->
            <!-- <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Current Plan</p>
                        <p class="text-lg font-bold text-orange-600 mt-1">{{ $memberProfile->plan->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            @if($memberProfile->plan)
                                ₱{{ number_format($memberProfile->plan->price, 2) }}/{{ $memberProfile->plan->duration_days }}d
                            @else
                                No active plan
                            @endif
                        </p>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div> -->

            <!-- Subscription Card -->
            <!-- <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Subscription</p>
                        <p class="text-lg font-bold text-blue-600 mt-1">{{ $memberProfile->subscription->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            @if($memberProfile->subscription)
                                ₱{{ number_format($memberProfile->subscription->price, 2) }}/{{ $memberProfile->subscription->duration_days }}d
                            @else
                                No subscription
                            @endif
                        </p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div> -->
        </div>

        <!-- Compact Attendance Timeline -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Compact Table Header -->
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-white">Check-in Timeline</h2>
                        <p class="text-gray-100 text-xs">Your complete attendance history</p>
                    </div>
                    <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded px-3 py-1">
                        <p class="text-gray-900 dark:text-gray-900 text-xs font-medium">
                            {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }} of {{ $attendances->total() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Compact Attendance List -->
            <div class="divide-y divide-gray-200">
                @forelse($attendances as $index => $attendance)
                @if($attendance->check_in_time)
                <div class="p-5 hover:bg-gray-50 transition-colors duration-200 group">
                    <div class="flex items-center justify-between">
                        <!-- Left Section - Date & Time -->
                        <div class="flex items-center space-x-4">
                            <!-- Compact Icon -->
                            <div class="relative flex-shrink-0">
                                <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-full p-3 shadow group-hover:scale-105 transition-transform">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                @if($index === 0)
                                <span class="absolute -top-1 -right-1 bg-green-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                                    New
                                </span>
                                @endif
                            </div>

                            <!-- Compact Date Info -->
                            <div class="min-w-0">
                                <p class="text-lg font-bold text-gray-800 truncate">
                                    {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('D, M d, Y') }}
                                </p>
                                <div class="flex items-center space-x-3 text-sm text-gray-600 mt-1">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"></path>
                                        </svg>
                                        <span class="font-medium">In:</span>
                                        <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}</span>
                                    </div>
                                    
                                    @if($attendance->check_out_time)
                                    <span class="text-gray-400">•</span>
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                        <span class="font-medium">Out:</span>
                                        <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') }}</span>
                                    </div>
                                    
                                    @if($attendance->duration)
                                    <span class="text-gray-400">•</span>
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="font-semibold text-gray-800">
                                            @php
                                                $hours = floor($attendance->duration / 60);
                                                $mins = $attendance->duration % 60;
                                            @endphp
                                            @if($hours > 0){{ $hours }}h {{ $mins }}m @else {{ $mins }}m @endif
                                        </span>
                                    </div>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Right Section - Compact Status -->
                        <div class="text-right flex-shrink-0">
                            @if($attendance->status === 'checked_out')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Completed
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Active
                                </span>
                            @endif
                            <p class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($attendance->check_in_time)->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif
                @empty
                <div class="p-16 text-center">
                    <div class="bg-gray-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No attendance records yet</h3>
                    <p class="text-gray-600 mb-4">Your check-in history will appear here after your first gym visit</p>
                </div>
                @endforelse
            </div>

            <!-- Compact Pagination -->
            @if($attendances->hasPages())
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                <div class="flex items-center justify-between text-sm">
                    <div class="text-gray-600">
                        <span class="font-semibold text-gray-800">{{ $attendances->firstItem() ?? 0 }}</span> - 
                        <span class="font-semibold text-gray-800">{{ $attendances->lastItem() ?? 0 }}</span> of 
                        <span class="font-semibold text-gray-800">{{ $attendances->total() }}</span>
                    </div>
                    <div>
                        {{ $attendances->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>


    </div>
</div>

<style>
/* Smooth transitions */
.hover\:shadow-md {
    transition: box-shadow 0.2s ease-in-out;
}

.group:hover {
    transition: all 0.2s ease-out;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endsection