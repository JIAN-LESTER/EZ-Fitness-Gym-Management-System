@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">My Attendance History</h1>
            <p class="text-gray-600">View your gym check-in records</p>
        </div>

        <!-- Statistics -->
        <div class="grid md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">This Month</p>
                        <p class="text-3xl font-bold text-blue-600">
                            {{ $attendances->where('check_in_time', '>=', now()->startOfMonth())->count() }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Check-ins</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Last Check-in</p>
                        <p class="text-lg font-bold text-green-600">
                            @if($attendances->first())
                                {{ $attendances->first()->check_in_time->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            @if($attendances->first())
                                {{ $attendances->first()->check_in_time->format('M d, Y') }}
                            @endif
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Check-ins</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $attendances->total() }}</p>
                        <p class="text-xs text-gray-500 mt-1">All time</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Check-in History</h2>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse($attendances as $attendance)
                <div class="p-6 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="bg-blue-600 rounded-full p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-gray-800">
                                    {{ $attendance->check_in_time->format('l, F d, Y') }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    Check-in at {{ $attendance->check_in_time->format('h:i A') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ ucfirst($attendance->status) }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $attendance->check_in_time->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <p class="text-gray-600 text-lg mb-2">No attendance records yet</p>
                    <p class="text-gray-500 text-sm">Your check-in history will appear here</p>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($attendances->hasPages())
            <div class="bg-gray-50 px-6 py-4">
                {{ $attendances->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection