@extends('layouts.app')

@section('title', 'Activity Logs | EZ Fitness')
@section('header', 'Logs')

@php
    function getActionBadge(string $action): array
    {
        $a = strtolower($action);

        if (str_contains($a, 'logged in'))
            return ['label' => 'Login', 'classes' => 'bg-blue-100 text-blue-700'];
        if (str_contains($a, 'logged out'))
            return ['label' => 'Logout', 'classes' => 'bg-gray-100 text-gray-600'];
        if (str_contains($a, 'register'))
            return ['label' => 'Register', 'classes' => 'bg-indigo-100 text-indigo-700'];
        if (str_contains($a, 'reset password') || str_contains($a, 'forgot password'))
            return ['label' => 'Password Reset', 'classes' => 'bg-yellow-100 text-yellow-700'];
        if (str_contains($a, 'verified') || str_contains($a, 'forgot password'))
            return ['label' => 'Email Verified', 'classes' => 'bg-green-200 text-green-700'];
        if (str_contains($a, 'approved') || str_contains($a, 'approve'))
            return ['label' => 'Approved', 'classes' => 'bg-green-50 text-green-700'];
        if (str_contains($a, 'denied') || str_contains($a, 'deny'))
            return ['label' => 'Denied', 'classes' => 'bg-red-100 text-red-700'];
        if (str_contains($a, 'suspend'))
            return ['label' => 'Suspended', 'classes' => 'bg-orange-100 text-orange-700'];
        if (str_contains($a, 'resume'))
            return ['label' => 'Resumed', 'classes' => 'bg-teal-100 text-teal-700'];
        if (str_contains($a, 'cancel'))
            return ['label' => 'Cancelled', 'classes' => 'bg-red-100 text-red-600'];
        if (str_contains($a, 'checked in') || str_contains($a, 'check in') || str_contains($a, 'check-in'))
            return ['label' => 'Check In', 'classes' => 'bg-emerald-100 text-emerald-700'];
        if (str_contains($a, 'checked out') || str_contains($a, 'check out') || str_contains($a, 'check-out'))
            return ['label' => 'Check Out', 'classes' => 'bg-slate-100 text-slate-600'];
        if (str_contains($a, 'deleted') || str_contains($a, 'delete'))
            return ['label' => 'Deleted', 'classes' => 'bg-red-100 text-red-700'];
        if (str_contains($a, 'updated') || str_contains($a, 'update'))
            return ['label' => 'Updated', 'classes' => 'bg-yellow-100 text-yellow-700'];
        if (str_contains($a, 'added') || str_contains($a, 'add') || str_contains($a, 'created'))
            return ['label' => 'Added', 'classes' => 'bg-green-100 text-green-700'];
        if ( str_contains($a, 'membership') || str_contains($a, 'plan'))
            return ['label' => 'Membership', 'classes' => 'bg-purple-100 text-purple-700'];
        if (str_contains($a, 'subscription'))
            return ['label' => 'Subscription', 'classes' => 'bg-purple-200 text-purple-700'];
        if (str_contains($a, 'sale') || str_contains($a, 'checkout') || str_contains($a, 'purchased') || str_contains($a, 'processed'))
            return ['label' => 'Sale', 'classes' => 'bg-cyan-100 text-cyan-700'];

        return ['label' => 'Action', 'classes' => 'bg-gray-100 text-gray-500'];
    }
@endphp

@section('content')
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

        <!-- Search Section -->
        <div class="p-4 sm:p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <form method="GET" action="{{ route('logs.show') }}" class="space-y-4">
                <div class="flex flex-wrap lg:flex-nowrap items-end gap-3">

                    <!-- Search Input -->
                    <div class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" value="{{ $search }}"
                                placeholder="Search by user or action..."
                                class="pl-10 w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-400 shadow-sm transition-all">
                        </div>
                    </div>

                    <!-- Filter Dropdown -->
                    <div class="w-full sm:w-auto sm:min-w-[150px]">
                        <label for="filter" class="block text-sm font-semibold text-gray-700 mb-2">Filter By</label>
                        <select name="filter" id="filter"
                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 shadow-sm transition-all">
                            <option value="action" {{ ($filter ?? 'action') === 'action' ? 'selected' : '' }}>Action</option>
                            <option value="user" {{ ($filter ?? '') === 'user' ? 'selected' : '' }}>User</option>
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div class="w-full sm:w-auto sm:min-w-[150px]">
                        <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 shadow-sm transition-all">
                    </div>

                    <!-- End Date -->
                    <div class="w-full sm:w-auto sm:min-w-[150px]">
                        <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 shadow-sm transition-all">
                    </div>

                    <!-- Search Button -->
                    <div class="w-full sm:w-auto">
                        <button type="submit"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 bg-gradient-to-r from-gray-600 to-gray-700 text-white px-6 py-3 rounded-xl hover:from-gray-700 hover:to-gray-800 shadow-md hover:shadow-lg transition-all duration-300 font-semibold whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Search
                        </button>
                    </div>

                    <!-- Clear Button -->
                    @if(request()->query())
                        <div class="w-full sm:w-auto">
                            <a href="{{ route('logs.show') }}"
                                class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 transition-all duration-300 font-semibold shadow-md whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Type
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date &
                            Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        @php $badge = getActionBadge($log->action); @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="log-badge {{ $badge['classes'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-800 font-bold text-sm shadow-md">
                                        {{ strtoupper(substr($log->user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($log->user->last_name ?? 'N', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 truncate">
                                            {{ $log->user->first_name ?? 'Unknown' }} {{ $log->user->last_name ?? '' }}</p>
                                        <p class="text-gray-500 text-sm truncate">{{ $log->user->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-700 font-medium">{{ $log->action }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-gray-900 font-medium">{{ $log->created_at->format('M d, Y') }}</span>
                                    <span class="text-gray-500 text-sm">{{ $log->created_at->format('h:i A') }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-lg font-medium">No logs found</p>
                                    <p class="text-sm mt-1">Try adjusting your search criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile View -->
        <div class="md:hidden divide-y divide-gray-100">
            @forelse($logs as $log)
                @php $badge = getActionBadge($log->action); @endphp
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 rounded-xl bg-gray-200 flex items-center justify-center text-gray-800 font-bold shadow-lg">
                                    {{ strtoupper(substr($log->user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($log->user->last_name ?? 'N', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 truncate">{{ $log->user->first_name ?? 'Unknown' }}
                                        {{ $log->user->last_name ?? '' }}</p>
                                    <p class="text-gray-500 text-sm truncate">{{ $log->user->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <span class="log-badge {{ $badge['classes'] }} flex-shrink-0">
                                {{ $badge['label'] }}
                            </span>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3">
                            <p class="text-xs font-semibold text-gray-500 mb-1">ACTION</p>
                            <p class="text-sm font-medium text-gray-900">{{ $log->action }}</p>
                        </div>
                        <div class="flex items-center text-sm text-gray-600 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $log->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-lg font-medium">No logs found</p>
                        <p class="text-sm mt-1">Try adjusting your search criteria</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($logs->total() > 0)
            <div
                class="flex flex-col sm:flex-row items-center justify-between p-4 sm:p-6 bg-gray-50 border-t border-gray-200 gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-semibold text-gray-900">{{ $logs->firstItem() }}</span> to
                    <span class="font-semibold text-gray-900">{{ $logs->lastItem() }}</span> of
                    <span class="font-semibold text-gray-900">{{ $logs->total() }}</span> logs
                </div>
                <div class="flex gap-2">
                    @if($logs->onFirstPage())
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Prev</span>
                    @else
                        <a href="{{ $logs->previousPageUrl() }}"
                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-gray-600 to-gray-700 text-white hover:from-gray-700 hover:to-gray-800 shadow-md transition-all duration-200 font-medium">Prev</a>
                    @endif

                    <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium sm:hidden">
                        {{ $logs->currentPage() }} / {{ $logs->lastPage() }}
                    </span>

                    @if($logs->hasMorePages())
                        <a href="{{ $logs->nextPageUrl() }}"
                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-gray-600 to-gray-700 text-white hover:from-gray-700 hover:to-gray-800 shadow-md transition-all duration-200 font-medium">Next</a>
                    @else
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <style>
        .log-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.5);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(107, 114, 128, 0.7);
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
@endsection