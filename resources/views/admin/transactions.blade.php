@extends('layouts.app')

@section('title', 'Transactions | EZ Fitness')
@section('header', 'Transactions')

<style>
    /* Custom Scrollbar for Modals */
    .modal-scrollbar::-webkit-scrollbar {
        width: 8px;
    }

    .modal-scrollbar::-webkit-scrollbar-track {
        background: #F3F4F6;
        border-radius: 10px;
    }

    .modal-scrollbar::-webkit-scrollbar-thumb {
        background: #9CA3AF;
        border-radius: 10px;
    }

    .modal-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #6B7280;
    }

    /* Firefox */
    .modal-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #9CA3AF #F3F4F6;
        scroll-behavior: smooth;
    }

    /* Fix for input text visibility */
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="tel"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
        color: #111827 !important;
    }

    input::placeholder,
    textarea::placeholder {
        color: #9CA3AF !important;
    }

    /* Clickable Row */
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .clickable-row:hover {
        background-color: #f9fafb;
    }

    /* Mobile Responsive Utilities */
    @media (max-width: 640px) {
        #filterDropdown {
            position: fixed;
            left: 0;
            right: 0;
            width: 100%;
            margin: 0;
            border-radius: 0;
            max-height: 90vh;
        }
    }
</style>

@section('content')
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

        <!-- Header -->
        <div class="flex justify-between items-center p-4 sm:p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Transactions</h2>
        </div>

        <!-- Search Section -->
        <div class="p-3 sm:p-6 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('transactions.index') }}" class="space-y-3 sm:space-y-4" role="search">
                <div class="flex flex-col sm:flex-row sm:flex-wrap lg:flex-nowrap items-stretch sm:items-center gap-3">
                    <!-- Search Input -->
                    <div class="flex-1 min-w-full sm:min-w-[200px]">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                placeholder="Search by transaction ID, type, or customer..."
                                class="pl-10 w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-400 shadow-sm transition-all text-sm sm:text-base">
                        </div>
                    </div>

                    <!-- Filter and Search Buttons Row -->
                    <div class="flex gap-2 sm:gap-3 w-full sm:w-auto">
                        <!-- Filter Dropdown -->
                        <div class="relative flex-1 sm:flex-initial" id="filterDropdownContainer">
                            <button type="button" onclick="toggleFilterDropdown(event)"
                                class="w-full flex items-center justify-center gap-2 bg-white border-2 border-gray-200 text-gray-700 px-4 sm:px-6 py-3 rounded-xl hover:border-gray-300 shadow-sm transition-all duration-300 font-semibold whitespace-nowrap text-sm sm:text-base">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                <span class="hidden sm:inline">Filters</span>
                                <span class="sm:hidden">Filter</span>
                                <span id="filterCount" class="hidden ml-1 px-2 py-0.5 text-xs bg-gray-600 text-white rounded-full">0</span>
                                <svg class="w-4 h-4 transition-transform" id="filterDropdownIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Filter Dropdown Content -->
                            <div id="filterDropdown" class="hidden fixed sm:absolute mt-2 w-full sm:w-80 bg-white rounded-xl sm:rounded-xl shadow-xl border border-gray-200 max-h-[calc(100vh-200px)] overflow-y-auto z-[9999] left-0 sm:left-auto right-0 sm:right-0">
                                <div class="p-4 space-y-4">
                                    <!-- Transaction Type Filter -->
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-3">Filter by Type</label>
                                        <div class="space-y-2">
                                            <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                                <input type="checkbox" name="status[]" value="sales"
                                                    {{ in_array('sales', request('status', [])) ? 'checked' : '' }}
                                                    onchange="updateFilterCount()"
                                                    class="w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                                                <span class="ml-3 text-sm font-medium text-gray-700">Sales</span>
                                                <span class="ml-auto px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">Sales</span>
                                            </label>

                                            <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                                <input type="checkbox" name="status[]" value="stock_in"
                                                    {{ in_array('stock_in', request('status', [])) ? 'checked' : '' }}
                                                    onchange="updateFilterCount()"
                                                    class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                                <span class="ml-3 text-sm font-medium text-gray-700">Stock In</span>
                                                <span class="ml-auto px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded-full">Stock In</span>
                                            </label>

                                            <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                                <input type="checkbox" name="status[]" value="stock_out"
                                                    {{ in_array('stock_out', request('status', [])) ? 'checked' : '' }}
                                                    onchange="updateFilterCount()"
                                                    class="w-4 h-4 text-red-600 rounded focus:ring-2 focus:ring-red-500">
                                                <span class="ml-3 text-sm font-medium text-gray-700">Stock Out</span>
                                                <span class="ml-auto px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">Stock Out</span>
                                            </label>
                                            
                                            <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                                <input type="checkbox" name="status[]" value="memberships"
                                                    {{ in_array('memberships', request('status', [])) ? 'checked' : '' }}
                                                    onchange="updateFilterCount()"
                                                    class="w-4 h-4 text-purple-600 rounded focus:ring-2 focus:ring-purple-500">
                                                <span class="ml-3 text-sm font-medium text-gray-700">Memberships</span>
                                                <span class="ml-auto px-2 py-0.5 text-xs bg-purple-100 text-purple-700 rounded-full">Memberships</span>
                                            </label>

                                            <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                                <input type="checkbox" name="status[]" value="subscriptions"
                                                    {{ in_array('subscriptions', request('status', [])) ? 'checked' : '' }}
                                                    onchange="updateFilterCount()"
                                                    class="w-4 h-4 text-orange-600 rounded focus:ring-2 focus:ring-orange-500">
                                                <span class="ml-3 text-sm font-medium text-gray-700">Subscriptions</span>
                                                <span class="ml-auto px-2 py-0.5 text-xs bg-orange-100 text-orange-700 rounded-full">Subscriptions</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="border-t border-gray-200 pt-4 flex gap-2">
                                        <button type="button" onclick="clearAllFilters()"
                                            class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                            Clear All
                                        </button>
                                        <button type="submit"
                                            class="flex-1 px-4 py-2 text-sm font-medium text-white bg-gray-800 hover:bg-gray-700 rounded-lg transition-colors">
                                            Apply Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Search Button -->
                        <div class="flex-1 sm:flex-initial">
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 bg-gray-800 text-white px-4 sm:px-6 py-3 rounded-xl hover:bg-gray-700 shadow-md hover:shadow-lg transition-all duration-300 font-semibold whitespace-nowrap text-sm sm:text-base">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <span class="hidden sm:inline">Search</span>
                            </button>
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    @if(request('search') || request('status'))
                        <div class="w-full sm:w-auto">
                            <a href="{{ route('transactions.index') }}"
                                class="w-full flex items-center justify-center gap-2 px-4 sm:px-6 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 transition-all duration-300 font-semibold shadow-md whitespace-nowrap text-sm sm:text-base">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Clear Filters
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <!-- Transactions Table - Desktop View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200">
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Transaction ID</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Performed By</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $transaction)
                        <tr class="clickable-row hover:bg-gray-50 transition-colors group" onclick="showTransaction('{{ $transaction->transaction_id }}')">
                            <!-- Transaction ID -->
                            <td class="px-6 py-4 text-center">
                                <p class="font-medium text-gray-900">#{{ $transaction->transaction_id }}</p>
                            </td>

                            <!-- Transaction Type -->
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $transaction->type === 'sales' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $transaction->type === 'stock_in' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $transaction->type === 'stock_out' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $transaction->type === 'memberships' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $transaction->type === 'subscriptions' ? 'bg-orange-100 text-orange-800' : '' }}">
                                    @if($transaction->type === 'stock_in')
                                        Stock In
                                    @elseif($transaction->type === 'stock_out')
                                        Stock Out
                                    @elseif($transaction->type === 'memberships')
                                        Membership
                                    @elseif($transaction->type === 'subscriptions')
                                        Subscription
                                    @else
                                        {{ ucfirst($transaction->type) }}
                                    @endif
                                </span>
                            </td>

                            <!-- Performed By -->
                            <td class="px-6 py-4 text-center">
                                @if($transaction->performer)
                                    <p class="font-medium text-gray-900">{{ $transaction->performer->first_name }} {{ $transaction->performer->last_name }}</p>
                                    <p class="text-sm text-gray-500">@&ZeroWidthSpace;{{ $transaction->performer->username }}</p>
                                @elseif($transaction->type === 'sales' && $transaction->sale && $transaction->sale->user)
                                    <p class="font-medium text-gray-900">{{ $transaction->sale->user->first_name }} {{ $transaction->sale->user->last_name }}</p>
                                    <p class="text-sm text-gray-500">@&ZeroWidthSpace;{{ $transaction->sale->user->username }}</p>
                                @else
                                    <p class="text-gray-400">System</p>
                                @endif
                            </td>

                            <!-- Amount -->
                            <td class="px-6 py-4 text-center">
                                @if($transaction->sale)
                                    <p class="font-semibold text-gray-900">₱{{ number_format($transaction->sale->total_amount, 2) }}</p>
                                @else
                                    <p class="text-gray-400">-</p>
                                @endif
                            </td>

                            <!-- Quantity -->
                            <td class="px-6 py-4 text-center">
                                @if($transaction->type === 'sales')
                                    <div class="flex flex-col items-center">
                                        <p class="font-semibold text-green-600 text-lg">{{ $transaction->quantity }}</p>
                                        @if($transaction->sale && $transaction->sale->items)
                                            <p class="text-xs text-gray-500 mt-1">{{ $transaction->sale->items->count() }} item(s)</p>
                                        @endif
                                    </div>
                                @elseif($transaction->type === 'memberships')
                                    <div class="flex flex-col items-center">
                                        <p class="font-semibold text-purple-600 text-lg">1</p>
                                        <p class="text-xs text-gray-500 mt-1">membership</p>
                                    </div>
                                @elseif(in_array($transaction->type, ['stock_in', 'stock_out']))
                                    <div class="flex flex-col items-center">
                                        <p class="font-semibold text-lg {{ $transaction->type === 'stock_in' ? 'text-blue-600' : 'text-red-600' }}">
                                            {{ $transaction->quantity }}
                                        </p>
                                        @if($transaction->product)
                                            <p class="text-xs text-gray-500 mt-1">{{ $transaction->product->name }}</p>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-gray-400">-</p>
                                @endif
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4 text-center">
                                <p class="text-gray-900">{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($transaction->created_at)->format('h:i A') }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">No transactions found</p>
                                    <p class="text-sm mt-1">Try adjusting your search criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Transactions Cards - Mobile View -->
        <div class="md:hidden divide-y divide-gray-200">
            @forelse($transactions as $transaction)
                <div class="p-4 hover:bg-gray-50 transition-colors cursor-pointer" onclick="showTransaction('{{ $transaction->transaction_id }}')">
                    <!-- Header Row -->
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">#{{ $transaction->transaction_id }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $transaction->type === 'sales' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $transaction->type === 'stock_in' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $transaction->type === 'stock_out' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $transaction->type === 'memberships' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $transaction->type === 'subscriptions' ? 'bg-orange-100 text-orange-800' : '' }}">
                                @if($transaction->type === 'stock_in')
                                    Stock In
                                @elseif($transaction->type === 'stock_out')
                                    Stock Out
                                @elseif($transaction->type === 'memberships')
                                    Membership
                                @elseif($transaction->type === 'subscriptions')
                                    Subscription
                                @else
                                    {{ ucfirst($transaction->type) }}
                                @endif
                            </span>
                        </div>
                        @if($transaction->sale)
                            <p class="font-bold text-gray-900 text-base">₱{{ number_format($transaction->sale->total_amount, 2) }}</p>
                        @endif
                    </div>

                    <!-- Info Grid -->
                    <div class="space-y-2 mb-3">
                        <div>
                            <p class="text-xs text-gray-500">Performed By</p>
                            @if($transaction->performer)
                                <p class="text-sm font-medium text-gray-900">{{ $transaction->performer->first_name }} {{ $transaction->performer->last_name }}</p>
                            @elseif($transaction->type === 'sales' && $transaction->sale && $transaction->sale->user)
                                <p class="text-sm font-medium text-gray-900">{{ $transaction->sale->user->first_name }} {{ $transaction->sale->user->last_name }}</p>
                            @else
                                <p class="text-sm text-gray-400">System</p>
                            @endif
                        </div>

                        @if($transaction->quantity)
                            <div>
                                <p class="text-xs text-gray-500">Quantity</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $transaction->quantity }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Date -->
                    <div class="flex items-center text-xs text-gray-600">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($transaction->created_at)->format('h:i A') }}
                    </div>
                </div>
            @empty
                <div class="px-4 py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-lg font-medium">No transactions found</p>
                        <p class="text-sm mt-1">Try adjusting your search criteria</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($transactions->total() > 0)
            <div class="flex flex-col sm:flex-row items-center justify-between p-4 sm:p-6 bg-gray-50 border-t border-gray-200 gap-4">
                <div class="text-xs sm:text-sm text-gray-600 text-center sm:text-left">
                    Showing <span class="font-semibold text-gray-900">{{ $transactions->firstItem() }}</span> to
                    <span class="font-semibold text-gray-900">{{ $transactions->lastItem() }}</span> of
                    <span class="font-semibold text-gray-900">{{ $transactions->total() }}</span> transactions
                </div>

                <div class="flex gap-2">
                    @if($transactions->onFirstPage())
                        <span class="px-3 sm:px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium text-sm">Prev</span>
                    @else
                        <a href="{{ $transactions->previousPageUrl() }}" class="px-3 sm:px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium text-sm">Prev</a>
                    @endif

                    <span class="px-3 sm:px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium text-sm">
                        {{ $transactions->currentPage() }} / {{ $transactions->lastPage() }}
                    </span>

                    @if($transactions->hasMorePages())
                        <a href="{{ $transactions->nextPageUrl() }}" class="px-3 sm:px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium text-sm">Next</a>
                    @else
                        <span class="px-3 sm:px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium text-sm">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- View Transaction Modal -->
    <div id="transactionShowModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0 backdrop-blur bg-opacity-50" onclick="closeModal('transactionShowModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-4 sm:p-5 rounded-t-2xl flex-shrink-0 flex justify-between items-center">
                <h2 class="text-lg sm:text-xl font-semibold">Transaction Details</h2>
                <button onclick="closeModal('transactionShowModal')" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <div id="transactionShowContent" class="overflow-y-auto flex-1 modal-scrollbar p-4 sm:p-6 bg-white">
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        @if(session('success'))
            if (typeof toastr !== 'undefined') {
                toastr.success('{{ session('success') }}');
            }
        @endif

        @if(session('error'))
            if (typeof toastr !== 'undefined') {
                toastr.error('{{ session('error') }}');
            }
        @endif

        window.toggleFilterDropdown = function(event) {
            event.preventDefault();
            event.stopPropagation();

            const dropdown = document.getElementById('filterDropdown');
            const icon = document.getElementById('filterDropdownIcon');

            if (!dropdown || !icon) {
                console.error('Dropdown elements not found');
                return;
            }

            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                dropdown.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        };

        window.updateFilterCount = function() {
            const checkboxes = document.querySelectorAll('#filterDropdown input[type="checkbox"]:checked');
            const count = checkboxes.length;
            const badge = document.getElementById('filterCount');

            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        };

        window.clearAllFilters = function() {
            const checkboxes = document.querySelectorAll('#filterDropdown input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = false);

            window.updateFilterCount();

            const form = document.querySelector('form[role="search"]');
            if (form) {
                const searchInput = form.querySelector('input[name="search"]');
                if (searchInput) searchInput.value = '';
                form.submit();
            }
        };

        window.openModal = function(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) {
                console.error('Modal not found:', modalId);
                return;
            }

            const scrollY = window.scrollY;

            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollY}px`;
            document.body.style.width = '100%';
            document.body.style.overflowY = 'scroll';

            modal.classList.remove('hidden');
        };

        window.closeModal = function(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            const scrollY = document.body.style.top;

            modal.classList.add('hidden');

            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            document.body.style.overflowY = '';

            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        };

        window.showTransaction = function(transactionId) {
            console.log('Opening transaction:', transactionId);

            window.openModal('transactionShowModal');

            const contentDiv = document.getElementById('transactionShowContent');
            if (!contentDiv) {
                console.error('Content div not found');
                return;
            }

            contentDiv.innerHTML = `
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
                </div>
            `;

            fetch('/transactions/' + transactionId)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('HTTP error! status: ' + res.status);
                    }
                    return res.json();
                })
                .then(transaction => {
                    console.log('Transaction loaded:', transaction);
                    renderTransactionDetails(transaction);
                })
                .catch(error => {
                    console.error('Error loading transaction:', error);
                    contentDiv.innerHTML = `
                        <div class="text-center py-12">
                            <div class="text-red-600 mb-2">
                                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-red-600 font-semibold">Error loading transaction details</p>
                            <p class="text-gray-500 text-sm mt-2">` + error.message + `</p>
                            <button onclick="closeModal('transactionShowModal')" class="mt-4 px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg">
                                Close
                            </button>
                        </div>
                    `;
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to load transaction details');
                    }
                });
        };

       // FIXED renderTransactionDetails function with SUBSCRIPTION support and mobile responsiveness
function renderTransactionDetails(transaction) {
    const content = document.getElementById('transactionShowContent');
    if (!content) return;

    const typeLabel = transaction.type === 'stock_in' ? 'Stock In' :
                    transaction.type === 'stock_out' ? 'Stock Out' :
                    transaction.type === 'memberships' ? 'Membership' :
                    transaction.type === 'subscriptions' ? 'Subscription' :
                    transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1);

    const formatDate = (dateStr) => {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const typeClass = {
        sales: 'bg-green-100 text-green-800',
        stock_in: 'bg-blue-100 text-blue-800',
        stock_out: 'bg-red-100 text-red-800',
        memberships: 'bg-purple-100 text-purple-800',
        subscriptions: 'bg-orange-100 text-orange-800'
    }[transaction.type] || '';

    let html = `<div class="space-y-4 sm:space-y-5">`;

    /* HEADER */
    html += `
        <div class="bg-gray-50 p-4 sm:p-5 rounded-lg">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                <div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800">Transaction #${transaction.transaction_id}</h3>
                    <p class="text-xs sm:text-sm text-gray-500">${formatDate(transaction.created_at)}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold ${typeClass} self-start">
                    ${typeLabel}
                </span>
            </div>
        </div>
    `;

    /* MEMBERSHIP OR SUBSCRIPTION INFORMATION */
    if ((transaction.type === 'memberships' || transaction.type === 'subscriptions') && transaction.sale) {
        const statusClass = {
            paid: 'bg-green-100 text-green-800',
            pending: 'bg-yellow-100 text-yellow-800',
            cancelled: 'bg-red-100 text-red-800'
        }[transaction.sale.status] || '';

        const paymentClass = {
            cash: 'bg-green-100 text-green-800',
            credit_card: 'bg-blue-100 text-blue-800',
            gcash: 'bg-purple-100 text-purple-800'
        }[transaction.sale.payment_method] || '';

        const paymentLabel = transaction.sale.payment_method === 'credit_card' ? 'Credit Card' :
                            transaction.sale.payment_method === 'gcash' ? 'GCash' :
                            transaction.sale.payment_method?.charAt(0).toUpperCase() + transaction.sale.payment_method?.slice(1);

        const headerBgClass = transaction.type === 'memberships' ? 'bg-purple-50' : 'bg-orange-50';
        const headerTitle = transaction.type === 'memberships' ? 'Membership Transaction' : 'Subscription Transaction';

        html += `
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-4 sm:px-5 py-3 border-b border-gray-200 ${headerBgClass}">
                    <h4 class="text-sm sm:text-md font-semibold text-gray-800">${headerTitle}</h4>
                </div>

                <div class="p-4 sm:p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs sm:text-sm">Transaction ID</p>
                            <p class="font-semibold text-gray-800">#${transaction.transaction_id}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 mb-2 text-xs sm:text-sm">Status</p>
                            <span class="px-2 sm:px-3 py-1 rounded-full text-xs font-semibold ${statusClass}">
                                ${transaction.sale.status.charAt(0).toUpperCase() + transaction.sale.status.slice(1)}
                            </span>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs sm:text-sm">Member</p>
                            <p class="font-semibold text-gray-800 text-sm sm:text-base">${transaction.sale.user.first_name} ${transaction.sale.user.last_name}</p>
                            <p class="text-gray-500 text-xs">@${transaction.sale.user.username}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs sm:text-sm">Email</p>
                            <p class="font-medium text-gray-800 text-xs sm:text-sm break-all">${transaction.sale.user.email}</p>
                        </div>

                        ${transaction.performer ? `
                            <div>
                                <p class="text-gray-500 text-xs sm:text-sm">Approved By</p>
                                <p class="font-semibold text-gray-800 text-sm sm:text-base">${transaction.performer.first_name} ${transaction.performer.last_name}</p>
                                <p class="text-gray-500 text-xs">@${transaction.performer.username}</p>
                            </div>
                        ` : ''}

                        <div>
                            <p class="text-gray-500 text-xs sm:text-sm">Transaction Date</p>
                            <p class="font-medium text-gray-800 text-xs sm:text-sm">${formatDate(transaction.created_at)}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs sm:text-sm">${transaction.type === 'memberships' ? 'Membership' : 'Subscription'} Amount</p>
                            <p class="text-lg sm:text-xl font-bold ${transaction.type === 'memberships' ? 'text-purple-700' : 'text-orange-700'}">
                                ₱${parseFloat(transaction.sale.total_amount).toFixed(2)}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500 mb-2 text-xs sm:text-sm">Payment Method</p>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2 sm:px-3 py-1 rounded-full text-xs font-semibold ${paymentClass}">
                                    ${paymentLabel}
                                </span>

                                ${transaction.sale.payment_method === 'gcash' && transaction.sale.reference_code ? `
                                    <span class="text-xs text-gray-500">Ref:</span>
                                    <span class="font-semibold text-xs ${transaction.type === 'memberships' ? 'text-purple-600' : 'text-orange-600'} rounded-full px-2 sm:px-3 py-1 ${paymentClass} break-all">
                                        ${transaction.sale.reference_code}
                                    </span>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Member profile section
        if (transaction.sale.user.member) {
            const member = transaction.sale.user.member;
            const memberStatusClass = {
                active: 'bg-green-100 text-green-800',
                expired: 'bg-red-100 text-red-800',
                inactive: 'bg-gray-100 text-gray-800'
            }[member.status] || '';

            html += `
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="px-4 sm:px-5 py-3 border-b border-gray-200 bg-gray-50">
                        <h4 class="text-sm sm:text-md font-semibold text-gray-800">Member Profile</h4>
                    </div>

                    <div class="p-4 sm:p-5 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 text-sm">
                            <div>
                                <p class="text-gray-500 text-xs sm:text-sm">Member ID</p>
                                <p class="font-semibold text-gray-800">#${member.member_id}</p>
                            </div>

                            <div>
                                <p class="text-gray-500 mb-2 text-xs sm:text-sm">Current Status</p>
                                <span class="px-2 sm:px-3 py-1 rounded-full text-xs font-semibold ${memberStatusClass}">
                                    ${member.status.charAt(0).toUpperCase() + member.status.slice(1)}
                                </span>
                            </div>

                            ${member.plan ? `
                                <div>
                                    <p class="text-gray-500 text-xs sm:text-sm">Membership Plan</p>
                                    <p class="font-semibold text-gray-800 text-sm sm:text-base">${member.plan.name}</p>
                                    ${member.plan.duration_days ? `<p class="text-xs text-gray-500">${member.plan.duration_days} days</p>` : ''}
                                    ${member.plan.price ? `<p class="text-xs text-gray-600">₱${parseFloat(member.plan.price).toFixed(2)}</p>` : ''}
                                </div>
                            ` : ''}

                            ${member.subscription ? `
                                <div>
                                    <p class="text-gray-500 text-xs sm:text-sm">Subscription</p>
                                    <p class="font-semibold text-gray-800 text-sm sm:text-base">${member.subscription.name}</p>
                                    ${member.subscription.duration_days ? `<p class="text-xs text-gray-500">${member.subscription.duration_days} days</p>` : ''}
                                    ${member.subscription.price ? `<p class="text-xs text-gray-600">₱${parseFloat(member.subscription.price).toFixed(2)}</p>` : ''}
                                </div>
                            ` : ''}

                            ${member.start_date ? `
                                <div>
                                    <p class="text-gray-500 text-xs sm:text-sm">Start Date</p>
                                    <p class="font-medium text-gray-800 text-xs sm:text-sm">${formatDate(member.start_date)}</p>
                                </div>
                            ` : ''}

                            ${member.end_date ? `
                                <div>
                                    <p class="text-gray-500 text-xs sm:text-sm">End Date</p>
                                    <p class="font-medium text-gray-800 text-xs sm:text-sm">${formatDate(member.end_date)}</p>
                                </div>
                            ` : ''}

                            ${member.mobile_number ? `
                                <div>
                                    <p class="text-gray-500 text-xs sm:text-sm">Mobile Number</p>
                                    <p class="font-medium text-gray-800 text-xs sm:text-sm">${member.mobile_number}</p>
                                </div>
                            ` : ''}

                            ${member.sex ? `
                                <div>
                                    <p class="text-gray-500 text-xs sm:text-sm">Sex</p>
                                    <p class="font-medium text-gray-800 capitalize text-xs sm:text-sm">${member.sex}</p>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }

        // Sale items section
        if (transaction.sale.items && transaction.sale.items.length > 0) {
            html += `
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="px-4 sm:px-5 py-3 border-b border-gray-200 bg-gray-50">
                        <h4 class="text-sm sm:text-md font-semibold text-gray-800">Purchased Items</h4>
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th class="px-5 py-2 text-left text-xs">Item</th>
                                    <th class="px-5 py-2 text-center text-xs">Type</th>
                                    <th class="px-5 py-2 text-right text-xs">Price</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                ${transaction.sale.items.map(item => {
                                    let itemName = 'Unknown Item';
                                    let itemType = 'Unknown';
                                    let itemTypeBadge = 'bg-gray-100 text-gray-800';
                                    let itemPrice = item.price || 0;
                                    let durationInfo = '';

                                    if (item.plan_id && item.plan) {
                                        itemName = item.plan.name;
                                        itemType = 'Membership Plan';
                                        itemTypeBadge = 'bg-purple-100 text-purple-800';
                                        if (item.plan.duration_days) {
                                            durationInfo = ` (${item.plan.duration_days} days)`;
                                        }
                                    }
                                    else if (item.subscription_id && item.subscription) {
                                        itemName = item.subscription.name;
                                        itemType = 'Subscription';
                                        itemTypeBadge = 'bg-orange-100 text-orange-800';
                                        if (item.subscription.duration_days) {
                                            durationInfo = ` (${item.subscription.duration_days} days)`;
                                        }
                                    }

                                    return `
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-5 py-3">
                                                <p class="font-medium text-gray-800">${itemName}</p>
                                                ${durationInfo ? `<p class="text-xs text-gray-500">${durationInfo}</p>` : ''}
                                            </td>
                                            <td class="px-5 py-3 text-center">
                                                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold ${itemTypeBadge}">
                                                    ${itemType}
                                                </span>
                                            </td>
                                            <td class="px-5 py-3 text-right font-semibold text-gray-800">₱${parseFloat(itemPrice).toFixed(2)}</td>
                                        </tr>
                                    `;
                                }).join('')}
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="sm:hidden divide-y divide-gray-200">
                        ${transaction.sale.items.map(item => {
                            let itemName = 'Unknown Item';
                            let itemType = 'Unknown';
                            let itemTypeBadge = 'bg-gray-100 text-gray-800';
                            let itemPrice = item.price || 0;
                            let durationInfo = '';

                            if (item.plan_id && item.plan) {
                                itemName = item.plan.name;
                                itemType = 'Membership Plan';
                                itemTypeBadge = 'bg-purple-100 text-purple-800';
                                if (item.plan.duration_days) {
                                    durationInfo = ` (${item.plan.duration_days} days)`;
                                }
                            }
                            else if (item.subscription_id && item.subscription) {
                                itemName = item.subscription.name;
                                itemType = 'Subscription';
                                itemTypeBadge = 'bg-orange-100 text-orange-800';
                                if (item.subscription.duration_days) {
                                    durationInfo = ` (${item.subscription.duration_days} days)`;
                                }
                            }

                            return `
                                <div class="p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <p class="font-medium text-gray-800 text-sm">${itemName}</p>
                                            ${durationInfo ? `<p class="text-xs text-gray-500 mt-0.5">${durationInfo}</p>` : ''}
                                        </div>
                                        <span class="ml-2 inline-block px-2 py-0.5 rounded-full text-xs font-semibold ${itemTypeBadge} whitespace-nowrap">
                                            ${itemType}
                                        </span>
                                    </div>
                                    <p class="text-sm font-bold text-gray-900">₱${parseFloat(itemPrice).toFixed(2)}</p>
                                </div>
                            `;
                        }).join('')}
                    </div>
                </div>
            `;
        }
    }
    
    /* PRODUCT INFORMATION (for stock movements) */
    if (transaction.product && (transaction.type === 'stock_in' || transaction.type === 'stock_out')) {
        html += `
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-4 sm:px-5 py-3 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-sm sm:text-md font-semibold text-gray-800">Product Information</h4>
                </div>

                <div class="p-4 sm:p-5 space-y-4">
                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-5">
                        ${transaction.product.image ? `
                            <img src="/storage/${transaction.product.image}"
                                class="w-full sm:w-28 h-48 sm:h-28 object-cover rounded-lg border border-gray-200">
                        ` : ''}

                        <div class="flex-1 space-y-3">
                            <div>
                                <p class="text-xs sm:text-sm text-gray-500">Product Name</p>
                                <p class="font-semibold text-gray-800 text-sm sm:text-base">${transaction.product.name}</p>
                            </div>

                            ${transaction.product.description ? `
                                <div>
                                    <p class="text-xs sm:text-sm text-gray-500">Description</p>
                                    <p class="text-gray-700 text-xs sm:text-sm">${transaction.product.description}</p>
                                </div>
                            ` : ''}

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs sm:text-sm text-gray-500">Price</p>
                                    <p class="font-semibold text-gray-800 text-sm sm:text-base">
                                        ₱${parseFloat(transaction.product.price).toFixed(2)}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs sm:text-sm text-gray-500">Quantity</p>
                                    <p class="font-semibold text-gray-800 text-sm sm:text-base">${transaction.quantity}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /* SALE INFORMATION (for regular sales) */
    if (transaction.sale && transaction.type === 'sales') {
        const statusClass = {
            paid: 'bg-green-100 text-green-800',
            pending: 'bg-yellow-100 text-yellow-800',
            cancelled: 'bg-red-100 text-red-800'
        }[transaction.sale.status] || '';

        const paymentClass = {
            cash: 'bg-green-100 text-green-800',
            credit_card: 'bg-blue-100 text-blue-800',
            gcash: 'bg-purple-100 text-purple-800'
        }[transaction.sale.payment_method] || '';

        const paymentLabel = transaction.sale.payment_method === 'credit_card'
            ? 'Credit Card'
            : transaction.sale.payment_method.charAt(0).toUpperCase() + transaction.sale.payment_method.slice(1);

        html += `
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-4 sm:px-5 py-3 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-sm sm:text-md font-semibold text-gray-800">Sale Information</h4>
                </div>

                <div class="p-4 sm:p-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs sm:text-sm">Sale ID</p>
                            <p class="font-semibold text-gray-800">#${transaction.sale.sales_id}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 mb-2 text-xs sm:text-sm">Sale Status</p>
                            <span class="px-2 sm:px-3 py-1 rounded-full text-xs font-semibold ${statusClass}">
                                ${transaction.sale.status.charAt(0).toUpperCase() + transaction.sale.status.slice(1)}
                            </span>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs sm:text-sm">Customer</p>
                            <p class="font-semibold text-gray-800 text-sm sm:text-base">${transaction.sale.user.first_name} ${transaction.sale.user.last_name}</p>
                            <p class="text-gray-500 text-xs">@${transaction.sale.user.username}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 mb-2 text-xs sm:text-sm">Payment Method</p>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-2 sm:px-3 py-1 rounded-full text-xs font-semibold ${paymentClass}">
                                    ${paymentLabel}
                                </span>

                                ${transaction.sale.payment_method === 'gcash' && transaction.sale.reference_code ? `
                                    <span class="text-xs text-gray-500">Ref:</span>
                                    <span class="font-semibold text-purple-600 rounded-full px-2 sm:px-3 py-1 ${paymentClass} text-xs break-all">
                                        ${transaction.sale.reference_code}
                                    </span>
                                ` : ''}
                            </div>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs sm:text-sm">Sale Date</p>
                            <p class="font-medium text-gray-800 text-xs sm:text-sm">${formatDate(transaction.sale.created_at)}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-xs sm:text-sm">Total Amount</p>
                            <p class="text-lg sm:text-xl font-bold text-gray-800">
                                ₱${parseFloat(transaction.sale.total_amount).toFixed(2)}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        /* SALE ITEMS */
        if (transaction.sale.items?.length > 0) {
            html += `
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="px-4 sm:px-5 py-3 border-b border-gray-200 bg-gray-50">
                        <h4 class="text-sm sm:text-md font-semibold text-gray-800">Items in Sale</h4>
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden sm:block overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500">
                                <tr>
                                    <th class="px-5 py-2 text-left text-xs">Product</th>
                                    <th class="px-5 py-2 text-center text-xs">Qty</th>
                                    <th class="px-5 py-2 text-right text-xs">Price</th>
                                    <th class="px-5 py-2 text-right text-xs">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                ${transaction.sale.items.map(item => `
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-3">
                                            <p class="font-medium text-gray-800">${item.product.name}</p>
                                            ${item.product.description ? `<p class="text-xs text-gray-500">${item.product.description}</p>` : ''}
                                        </td>
                                        <td class="px-5 py-3 text-center text-gray-800">${item.quantity}</td>
                                        <td class="px-5 py-3 text-right text-gray-800">₱${parseFloat(item.price).toFixed(2)}</td>
                                        <td class="px-5 py-3 text-right font-semibold text-gray-800">₱${parseFloat(item.sub_total).toFixed(2)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="sm:hidden divide-y divide-gray-200">
                        ${transaction.sale.items.map(item => `
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-800 text-sm">${item.product.name}</p>
                                        ${item.product.description ? `<p class="text-xs text-gray-500 mt-0.5">${item.product.description}</p>` : ''}
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-xs">
                                    <div>
                                        <p class="text-gray-500">Qty</p>
                                        <p class="font-semibold text-gray-900">${item.quantity}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Price</p>
                                        <p class="font-semibold text-gray-900">₱${parseFloat(item.price).toFixed(2)}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-gray-500">Total</p>
                                        <p class="font-bold text-gray-900">₱${parseFloat(item.sub_total).toFixed(2)}</p>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }
    }

    html += `</div>`;
    content.innerHTML = html;
}

        // Initialize on DOM Ready
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Transaction page initialized');

            // Toastr Configuration
            if (typeof toastr !== 'undefined') {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "timeOut": "3000",
                    "extendedTimeOut": "1000",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
            }

            window.updateFilterCount();

            const dropdown = document.getElementById('filterDropdown');
            if (dropdown) {
                dropdown.addEventListener('click', function(event) {
                    event.stopPropagation();
                });
            }

            document.addEventListener('click', function (event) {
                const dropdown = document.getElementById('filterDropdown');
                const filterButton = document.querySelector('[onclick*="toggleFilterDropdown"]');

                if (!dropdown) return;

                const clickedInsideDropdown = dropdown.contains(event.target);
                const clickedButton = filterButton && filterButton.contains(event.target);

                if (!clickedInsideDropdown && !clickedButton) {
                    dropdown.classList.add('hidden');
                    const icon = document.getElementById('filterDropdownIcon');
                    if (icon) icon.classList.remove('rotate-180');
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    const dropdown = document.getElementById('filterDropdown');
                    if (dropdown && !dropdown.classList.contains('hidden')) {
                        dropdown.classList.add('hidden');
                        const icon = document.getElementById('filterDropdownIcon');
                        if (icon) icon.classList.remove('rotate-180');
                    }

                    window.closeModal('transactionShowModal');
                }
            });
        });
    </script>

@endsection