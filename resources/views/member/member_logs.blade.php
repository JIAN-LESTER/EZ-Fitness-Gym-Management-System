@extends('layouts.app')
@section('title', 'Attendance History | EZ Fitness')
@section('header', 'Attendance History')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="w-full px-4 sm:px-6 lg:px-10 py-4 sm:py-6">

        <!-- Header Section -->
        <div class="mb-5 sm:mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-1 leading-tight">My Attendance History</h1>
            <p class="text-sm sm:text-base text-gray-600">Track your gym journey and progress</p>
        </div>

        <!-- Statistics Dashboard -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-5 sm:mb-6">

            <!-- This Month Card -->
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 pr-3">
                        <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">This Month</p>
                        <p class="text-3xl font-bold text-gray-600 mt-1">{{ $thisMonthCount ?? 0 }}</p>
                        <p class="text-xs text-gray-500 mt-1 truncate">Check-ins in {{ now()->format('F') }}</p>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-lg flex-shrink-0">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Last Check-in Card -->
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 pr-3">
                        <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Last Check-in</p>
                        <p class="text-base sm:text-lg font-bold text-green-600 mt-1 truncate">
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
                    <div class="bg-green-100 p-3 rounded-lg flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Check-ins Card -->
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 pr-3">
                        <p class="text-gray-600 text-xs font-medium uppercase tracking-wide">Total Check-ins</p>
                        <p class="text-3xl font-bold text-purple-600 mt-1">{{ $attendances->total() }}</p>
                        <p class="text-xs text-gray-500 mt-1">All time records</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg flex-shrink-0">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Timeline -->
        <div class="bg-white rounded-lg shadow overflow-hidden">

            <!-- Table Header -->
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-4 sm:px-6 py-3 sm:py-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="text-lg sm:text-xl font-bold text-white leading-tight">Check-in Timeline</h2>
                        <p class="text-gray-100 text-xs mt-0.5">Your complete attendance history</p>
                    </div>
                    <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded px-2 sm:px-3 py-1 flex-shrink-0">
                        <p class="text-white text-xs font-medium whitespace-nowrap">
                            {{ $attendances->firstItem() ?? 0 }}–{{ $attendances->lastItem() ?? 0 }} of {{ $attendances->total() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Attendance List -->
            <div class="divide-y divide-gray-200">
                @forelse($attendances as $index => $attendance)
                @if($attendance->check_in_time)
                <div class="px-4 sm:px-5 py-4 sm:py-5 hover:bg-gray-50 transition-colors duration-200 group">
                    <div class="flex items-start sm:items-center justify-between gap-3">

                        <!-- Left Section - Date & Time -->
                        <div class="flex items-start sm:items-center gap-3 sm:gap-4 min-w-0">

                            <!-- Icon -->
                            <div class="relative flex-shrink-0 mt-0.5 sm:mt-0">
                                <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-full p-2.5 sm:p-3 shadow group-hover:scale-105 transition-transform">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                @if($index === 0)
                                <span class="absolute -top-1 -right-1 bg-green-500 text-white text-[9px] sm:text-[10px] font-bold px-1 sm:px-1.5 py-0.5 rounded-full leading-none">
                                    New
                                </span>
                                @endif
                            </div>

                            <!-- Date & Time Info -->
                            <div class="min-w-0">
                                <p class="text-base sm:text-lg font-bold text-gray-800 truncate">
                                    {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('D, M d, Y') }}
                                </p>

                                <!-- On mobile: stack time info vertically; on sm+: inline -->
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs sm:text-sm text-gray-600 mt-1">

                                    <!-- Check In -->
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"></path>
                                        </svg>
                                        <span class="font-medium">In:</span>
                                        <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}</span>
                                    </div>

                                    @if($attendance->check_out_time)
                                    <span class="text-gray-300 hidden sm:inline">•</span>

                                    <!-- Check Out -->
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                        <span class="font-medium">Out:</span>
                                        <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') }}</span>
                                    </div>

                                    @if($attendance->duration)
                                    <span class="text-gray-300 hidden sm:inline">•</span>

                                    <!-- Duration -->
                                    <div class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                        <!-- Right Section - Status -->
                        <div class="text-right flex-shrink-0">
                            @if($attendance->status === 'checked_out')
                                <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 whitespace-nowrap">
                                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Completed
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 whitespace-nowrap">
                                    <svg class="w-3 h-3 mr-1 flex-shrink-0 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Active
                                </span>
                            @endif
                            <p class="text-xs text-gray-500 mt-1 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($attendance->check_in_time)->diffForHumans() }}
                            </p>
                        </div>

                    </div>
                </div>
                @endif
                @empty
                <div class="py-12 sm:py-16 px-4 text-center">
                    <div class="bg-gray-100 rounded-full w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">No attendance records yet</h3>
                    <p class="text-sm sm:text-base text-gray-600">Your check-in history will appear here after your first gym visit</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($attendances->hasPages())
            <div class="bg-gray-50 px-4 sm:px-6 py-3 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-sm">
                    <div class="text-gray-600 text-xs sm:text-sm">
                        Showing
                        <span class="font-semibold text-gray-800">{{ $attendances->firstItem() ?? 0 }}</span>–<span class="font-semibold text-gray-800">{{ $attendances->lastItem() ?? 0 }}</span>
                        of
                        <span class="font-semibold text-gray-800">{{ $attendances->total() }}</span> records
                    </div>
                    <div class="pagination-wrapper">
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
    width: 6px;
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

/* Pagination mobile tweaks */
.pagination-wrapper nav {
    display: flex;
    justify-content: center;
}

.pagination-wrapper span[aria-current="page"] span,
.pagination-wrapper a {
    font-size: 0.75rem;
    padding: 0.3rem 0.6rem;
}

@media (max-width: 480px) {
    /* Ensure stat cards never overflow on very small screens */
    .grid > div {
        min-width: 0;
    }
}
</style>
@endsection