@extends('layouts.app')

@section('title', 'Sales')
@section('header', 'Sales')

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

.modal-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #9CA3AF #F3F4F6;
    scroll-behavior: smooth;
}
</style>

@section('content')
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

        <!-- Header -->
        <div class="flex justify-between items-center p-4 sm:p-6 border-b border-blue-200">
            <h2 class="text-2xl font-bold text-gray-800">Sales</h2>
        </div>

        <!-- Search Section -->
        <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('sales.index') }}" class="space-y-4" role="search">
                <div class="flex flex-wrap lg:flex-nowrap items-center gap-3">
                    <!-- Search Input -->
                    <div class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" 
                                placeholder="Search by sales ID, customer name, or reference code..."
                                class="pl-10 w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-400 shadow-sm transition-all">
                        </div>
                    </div>

                    <!-- Filter Dropdown -->
                    <div class="relative w-full sm:w-auto">
                        <button type="button" onclick="toggleFilterDropdown()" 
                            class="w-full sm:w-auto flex items-center justify-between gap-2 bg-white border-2 border-gray-200 text-gray-700 px-6 py-3 rounded-xl hover:border-gray-300 shadow-sm transition-all duration-300 font-semibold whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filters
                            <span id="filterCount" class="hidden ml-1 px-2 py-0.5 text-xs bg-blue-600 text-white rounded-full">0</span>
                            <svg class="w-4 h-4 transition-transform" id="filterDropdownIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Filter Dropdown Content -->
                        <div id="filterDropdown" class="hidden fixed mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 max-h-[85vh] overflow-y-auto z-[9999] right-4">
                            <div class="p-3 space-y-3">
                                <!-- Payment Method Filter -->
                                <div class="ml-1">
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Payment Method</label>
                                    <div class="grid grid-cols-1 gap-1">
                                        <label class="flex items-center px-1 py-1.5 hover:bg-gray-50 rounded cursor-pointer transition-colors">
                                            <input type="checkbox" name="payment_method[]" value="cash" 
                                                {{ in_array('cash', request('payment_method', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-3.5 h-3.5 text-green-600 rounded focus:ring-1 focus:ring-green-500 ml-1">
                                            <span class="ml-2 text-xs font-medium text-gray-700">Cash</span>
                                            <span class="ml-auto px-1.5 py-0.5 text-[10px] bg-green-100 text-green-700 rounded-full mr-1">Cash</span>
                                        </label>

                                        <label class="flex items-center px-1 py-1.5 hover:bg-gray-50 rounded cursor-pointer transition-colors">
                                            <input type="checkbox" name="payment_method[]" value="gcash" 
                                                {{ in_array('gcash', request('payment_method', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-3.5 h-3.5 text-purple-600 rounded focus:ring-1 focus:ring-purple-500 ml-1">
                                            <span class="ml-2 text-xs font-medium text-gray-700">GCash</span>
                                            <span class="ml-auto px-1.5 py-0.5 text-[10px] bg-purple-100 text-purple-700 rounded-full mr-1">GCash</span>
                                        </label>

                                        <label class="flex items-center px-1 py-1.5 hover:bg-gray-50 rounded cursor-pointer transition-colors">
                                            <input type="checkbox" name="payment_method[]" value="credit_card" 
                                                {{ in_array('credit_card', request('payment_method', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-3.5 h-3.5 text-blue-600 rounded focus:ring-1 focus:ring-blue-500 ml-1">
                                            <span class="ml-2 text-xs font-medium text-gray-700">Credit Card</span>
                                            <span class="ml-auto px-1.5 py-0.5 text-[10px] bg-blue-100 text-blue-700 rounded-full mr-1">Card</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 mx-1"></div>

                                <!-- Date Range Filter -->
                                <div class="ml-1">
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Date Range</label>
                                    <div class="space-y-2">
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-[10px] font-medium text-gray-600 mb-1">From</label>
                                                <input type="date" 
                                                    name="start_date" 
                                                    value="{{ request('start_date') }}"
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 ml-0">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-medium text-gray-600 mb-1">To</label>
                                                <input type="date" 
                                                    name="end_date" 
                                                    value="{{ request('end_date') }}"
                                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 ml-0">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-1">
                                            <button type="button" onclick="setDateRange('today')" 
                                                class="px-2 py-1 text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors ml-0">
                                                Today
                                            </button>
                                            <button type="button" onclick="setDateRange('yesterday')" 
                                                class="px-2 py-1 text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors ml-0">
                                                Yesterday
                                            </button>
                                            <button type="button" onclick="setDateRange('week')" 
                                                class="px-2 py-1 text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors ml-0">
                                                This Week
                                            </button>
                                            <button type="button" onclick="setDateRange('month')" 
                                                class="px-2 py-1 text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors ml-0">
                                                This Month
                                            </button>
                                        </div>
                                        <button type="button" onclick="clearDateRange()" 
                                            class="w-full px-2 py-1 text-[10px] bg-red-100 hover:bg-red-200 text-red-700 rounded transition-colors ml-0">
                                            Clear Dates
                                        </button>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 mx-1"></div>

                                <!-- Status Filter -->
                                <div class="ml-1">
                                    <label class="block text-xs font-semibold text-gray-700 mb-2">Status</label>
                                    <div class="grid grid-cols-1 gap-1">
                                        <label class="flex items-center px-1 py-1.5 hover:bg-gray-50 rounded cursor-pointer transition-colors">
                                            <input type="checkbox" name="status[]" value="paid" 
                                                {{ in_array('paid', request('status', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-3.5 h-3.5 text-green-600 rounded focus:ring-1 focus:ring-green-500 ml-1">
                                            <span class="ml-2 text-xs font-medium text-gray-700">Paid</span>
                                            <span class="ml-auto px-1.5 py-0.5 text-[10px] bg-green-100 text-green-700 rounded-full mr-1">Paid</span>
                                        </label>

                                        <label class="flex items-center px-1 py-1.5 hover:bg-gray-50 rounded cursor-pointer transition-colors">
                                            <input type="checkbox" name="status[]" value="pending" 
                                                {{ in_array('pending', request('status', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-3.5 h-3.5 text-yellow-600 rounded focus:ring-1 focus:ring-yellow-500 ml-1">
                                            <span class="ml-2 text-xs font-medium text-gray-700">Pending</span>
                                            <span class="ml-auto px-1.5 py-0.5 text-[10px] bg-yellow-100 text-yellow-700 rounded-full mr-1">Pending</span>
                                        </label>

                                        <label class="flex items-center px-1 py-1.5 hover:bg-gray-50 rounded cursor-pointer transition-colors">
                                            <input type="checkbox" name="status[]" value="cancelled" 
                                                {{ in_array('cancelled', request('status', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-3.5 h-3.5 text-red-600 rounded focus:ring-1 focus:ring-red-500 ml-1">
                                            <span class="ml-2 text-xs font-medium text-gray-700">Cancelled</span>
                                            <span class="ml-auto px-1.5 py-0.5 text-[10px] bg-red-100 text-red-700 rounded-full mr-1">Cancelled</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="border-t border-gray-200 pt-3 flex gap-2 mx-1">
                                    <button type="button" onclick="clearAllFilters()" 
                                        class="flex-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded transition-colors ml-0">
                                        Clear All
                                    </button>
                                    <button type="submit" 
                                        class="flex-1 px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded transition-colors ml-0">
                                        Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search Button -->
                    <div class="w-full sm:w-auto">
                        <button type="submit"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 bg-gradient-to-r from-gray-600 to-gray-700 text-white px-6 py-3 rounded-xl hover:from-gray-700 hover:to-gray-800 shadow-md hover:shadow-lg transition-all duration-300 font-semibold whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Search
                        </button>
                    </div>

                    <!-- Clear Filters -->
                    @if(request('search') || request('status') || request('payment_method') || request('start_date') || request('end_date'))
                        <div class="w-full sm:w-auto">
                            <a href="{{ route('sales.index') }}"
                                class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 transition-all duration-300 font-semibold shadow-md whitespace-nowrap">
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

        <!-- Active Filters Display -->
        @if(request('status') || request('payment_method') || request('start_date') || request('end_date'))
            <div class="p-4 bg-blue-50 border-b border-blue-200">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-medium text-blue-800">Active Filters:</span>
                    
                    @foreach(request('status', []) as $status)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Status: {{ ucfirst($status) }}
                            <a href="{{ $removeFilter('status', $status) }}" class="ml-1 text-green-600 hover:text-green-800">
                                &times;
                            </a>
                        </span>
                    @endforeach
                    
                    @foreach(request('payment_method', []) as $method)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            {{ ucfirst($method) }}
                            <a href="{{ $removeFilter('payment_method', $method) }}" class="ml-1 text-purple-600 hover:text-purple-800">
                                &times;
                            </a>
                        </span>
                    @endforeach
                    
                    @if(request('start_date'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            From: {{ request('start_date') }}
                            <a href="{{ $removeFilter('start_date') }}" class="ml-1 text-blue-600 hover:text-blue-800">
                                &times;
                            </a>
                        </span>
                    @endif
                    
                    @if(request('end_date'))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            To: {{ request('end_date') }}
                            <a href="{{ $removeFilter('end_date') }}" class="ml-1 text-blue-600 hover:text-blue-800">
                                &times;
                            </a>
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <!-- Sales Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200">
                     
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cashier</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Reference Code</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                         <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50 transition-colors group">
                    
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $sale->user->first_name }} {{ $sale->user->last_name }}</p>
                                <p class="text-sm text-gray-500">@&ZeroWidthSpace;{{ $sale->user->username }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <p class="font-semibold text-gray-900">₱{{ number_format($sale->total_amount, 2) }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $sale->payment_method === 'cash' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $sale->payment_method === 'credit_card' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $sale->payment_method === 'gcash' ? 'bg-purple-100 text-purple-800' : '' }}">
                                    {{ $sale->payment_method === 'credit_card' ? 'Credit Card' : ucfirst($sale->payment_method) }}
                                </span>
                            </td>
                            <!-- Reference Code Column -->
                            <td class="px-6 py-4 text-center">
                                @if($sale->payment_method === 'gcash' && $sale->reference_code)
                                    <div class="flex items-center justify-center gap-1" title="GCash Reference Code">
                                        <span class="text-xs font-mono bg-blue-50 text-blue-700 px-2 py-1 rounded border border-blue-200">
                                            {{ $sale->reference_code }}
                                        </span>
                                    </div>
                                @elseif($sale->payment_method === 'gcash')
                                    <span class="text-xs text-gray-400 italic">No reference</span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $sale->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $sale->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $sale->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                             <td class="px-6 py-4">
                                <p class="text-gray-900">{{ \Carbon\Carbon::parse($sale->created_at)->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($sale->created_at)->format('h:i A') }}</p>
                            </td>
      <td class="px-6 py-4 text-center">
    <div class="flex items-center justify-center gap-2">
        <button onclick="showSale('{{ $sale->sales_id }}')" 
            class="text-gray-500 hover:text-gray-700 transition-colors" 
            title="View Details">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </button>
        
        @if(Auth::user()->role === 'admin')
        <button onclick="confirmDelete('{{ $sale->sales_id }}')" 
            class="text-red-500 hover:text-red-700 transition-colors" 
            title="Delete Sale">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
        @endif
    </div>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500"> <!-- Change from 7 to 8 -->
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">No sales found</p>
                                    <p class="text-sm mt-1">Try adjusting your search criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($sales->total() > 0)
            <div class="flex flex-col sm:flex-row items-center justify-between p-4 sm:p-6 bg-gray-50 border-t border-gray-200 gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-semibold text-gray-900">{{ $sales->firstItem() }}</span> to
                    <span class="font-semibold text-gray-900">{{ $sales->lastItem() }}</span> of
                    <span class="font-semibold text-gray-900">{{ $sales->total() }}</span> sales
                </div>

                <div class="flex gap-2">
                    @if($sales->onFirstPage())
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Prev</span>
                    @else
                        <a href="{{ $sales->previousPageUrl() }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 shadow-md transition-all duration-200 font-medium">Prev</a>
                    @endif

                    <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium">
                        {{ $sales->currentPage() }} / {{ $sales->lastPage() }}
                    </span>

                    @if($sales->hasMorePages())
                        <a href="{{ $sales->nextPageUrl() }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 shadow-md transition-all duration-200 font-medium">Next</a>
                    @else
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- View Sale Modal -->
    <div id="saleShowModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0 bg-opacity-50" onclick="closeModal('saleShowModal')"></div>

        <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden">
            <header class="bg-blue-600 text-white p-5 rounded-t-2xl flex justify-between items-center">
                <h2 class="text-xl font-semibold">Sale Details</h2>
                <button onclick="closeModal('saleShowModal')" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <div id="saleShowContent" class="p-6 modal-scrollbar overflow-y-auto" style="max-height: calc(90vh - 80px);">
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
    <div class="absolute inset-0 backdrop-blur" onclick="closeDeleteModal()"></div>
    
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-center text-gray-900 dark:text-white mb-2">
                Delete Sale
            </h3>
            
            <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
                Are you sure you want to delete this sale? This action cannot be undone and will also delete all related transactions and sale items.
            </p>
            
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()" 
                        class="flex-1 px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="flex-1 px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-lg font-medium transition-colors">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script>
function confirmDelete(saleId) {
    const form = document.getElementById('deleteForm');
    form.action = '/sales/' + saleId;
    
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
    
    document.body.style.overflow = '';
}


    // Filter Functions
    function toggleFilterDropdown() {
        const dropdown = document.getElementById('filterDropdown');
        const icon = document.getElementById('filterDropdownIcon');
        
        dropdown.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }


    // Date Range Functions
    function setDateRange(range) {
        const today = new Date();
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');
        
        let startDate = new Date();
        let endDate = new Date();
        
        switch(range) {
            case 'today':
                // Start and end are today
                break;
            case 'yesterday':
                startDate.setDate(today.getDate() - 1);
                endDate.setDate(today.getDate() - 1);
                break;
            case 'week':
                startDate.setDate(today.getDate() - today.getDay()); // Start of week (Sunday)
                endDate.setDate(today.getDate() + (6 - today.getDay())); // End of week (Saturday)
                break;
            case 'month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1); // First day of month
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of month
                break;
        }
        
        // Format dates as YYYY-MM-DD
        const formatDate = (date) => date.toISOString().split('T')[0];
        
        startDateInput.value = formatDate(startDate);
        endDateInput.value = formatDate(endDate);
        
        updateFilterCount();
    }

    function clearDateRange() {
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');
        
        startDateInput.value = '';
        endDateInput.value = '';
        
        updateFilterCount();
    }


    function updateFilterCount() {
        const statusCheckboxes = document.querySelectorAll('input[name="status[]"]:checked');
        const paymentCheckboxes = document.querySelectorAll('input[name="payment_method[]"]:checked');
        const startDate = document.querySelector('input[name="start_date"]').value;
        const endDate = document.querySelector('input[name="end_date"]').value;
        
        const statusCount = statusCheckboxes.length;
        const paymentCount = paymentCheckboxes.length;
        const dateCount = (startDate || endDate) ? 1 : 0;
        
        const totalCount = statusCount + paymentCount + dateCount;
        const badge = document.getElementById('filterCount');
        
        if (badge) {
            if (totalCount > 0) {
                badge.textContent = totalCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
    }

    function clearAllFilters() {
        const checkboxes = document.querySelectorAll('#filterDropdown input[type="checkbox"]');
        const dateInputs = document.querySelectorAll('#filterDropdown input[type="date"]');
        
        checkboxes.forEach(cb => cb.checked = false);
        dateInputs.forEach(input => input.value = '');
        updateFilterCount();
        
        const form = document.querySelector('form[role="search"]');
        if (form) form.submit();
    }

    // Modal Functions
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const scrollY = window.scrollY;
        
        document.body.style.position = 'fixed';
        document.body.style.top = `-${scrollY}px`;
        document.body.style.width = '100%';
        
        modal.classList.remove('hidden');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const scrollY = document.body.style.top;
        
        modal.classList.add('hidden');
        
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        
        window.scrollTo(0, parseInt(scrollY || '0') * -1);
    }

    // Show Sale with Items
    function showSale(saleId) {
        openModal('saleShowModal');
        
        document.getElementById('saleShowContent').innerHTML = `
            <div class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>
        `;

        fetch(`/sales/${saleId}`)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(sale => {
                renderSaleDetails(sale);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('saleShowContent').innerHTML = `
                    <div class="text-center py-12">
                        <p class="text-red-600">Error loading sale details</p>
                    </div>
                `;
            });
    }

   function renderSaleDetails(sale) {
    const content = document.getElementById('saleShowContent');
    
    let html = `
        <div class="space-y-6">
            <!-- Sale Header -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Sale #${sale.sales_id}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">${new Date(sale.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-4 py-2 rounded-full text-sm font-semibold
                            ${sale.status === 'paid' ? 'bg-green-100 text-green-800' : ''}
                            ${sale.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ''}
                            ${sale.status === 'cancelled' ? 'bg-red-100 text-red-800' : ''}">
                            ${sale.status.charAt(0).toUpperCase() + sale.status.slice(1)}
                        </span>
                    </div>
                </div>

                <!-- Customer & Payment Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Cashier</p>
                        <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">${sale.user.first_name} ${sale.user.last_name}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">@${sale.user.username}</p>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Payment Method</p>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                ${sale.payment_method === 'cash' ? 'bg-green-100 text-green-800' : ''}
                                ${sale.payment_method === 'credit_card' ? 'bg-blue-100 text-blue-800' : ''}
                                ${sale.payment_method === 'gcash' ? 'bg-purple-100 text-purple-800' : ''}">
                                ${sale.payment_method === 'credit_card' ? 'Credit Card' : sale.payment_method.charAt(0).toUpperCase() + sale.payment_method.slice(1)}
                            </span>
                        </div>
                        ${sale.payment_method === 'gcash' && sale.reference_code ? `
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">GCash Reference Code</p>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-sm bg-blue-50 text-blue-700 px-3 py-1 rounded-lg border border-blue-200">
                                    ${sale.reference_code}
                                </span>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>

            <!-- Sale Items -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Items Purchased</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
    `;

    if (sale.items && sale.items.length > 0) {
        sale.items.forEach(item => {
            // Determine if it's a product or membership plan
            const isProduct = item.product !== null;
            const isPlan = item.plan !== null;
            
            let itemName = '';
            let itemDescription = '';
            let itemType = '';
            let itemTypeBadge = '';
            
            if (isProduct) {
                itemName = item.product.name;
                itemDescription = item.product.description || '';
                itemType = 'Product';
                itemTypeBadge = 'bg-blue-100 text-blue-800';
            } else if (isPlan) {
                itemName = item.plan.name;
                itemDescription = item.plan.details || '';
                if (item.plan.duration_days) {
                    itemDescription += ` (${item.plan.duration_days} days)`;
                }
                itemType = 'Membership Plan';
                itemTypeBadge = 'bg-purple-100 text-purple-800';
            } else {
                itemName = 'Unknown Item';
                itemDescription = '';
                itemType = 'Unknown';
                itemTypeBadge = 'bg-gray-100 text-gray-800';
            }
            
            html += `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900 dark:text-gray-200">${itemName}</p>
                        ${itemDescription ? `<p class="text-sm text-gray-500">${itemDescription}</p>` : ''}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold ${itemTypeBadge}">
                            ${itemType}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-gray-900 dark:text-gray-200">${item.quantity}</td>
                    <td class="px-6 py-4 text-right text-gray-900 dark:text-gray-200">₱${parseFloat(item.price).toFixed(2)}</td>
                    <td class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-gray-200">₱${parseFloat(item.sub_total).toFixed(2)}</td>
                </tr>
            `;
        });
    } else {
        html += `
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">No items found</td>
            </tr>
        `;
    }

    html += `
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right font-semibold text-gray-800 dark:text-gray-200">
                                    Total Amount:
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-xl text-gray-900 dark:text-gray-200">
                                    ₱${parseFloat(sale.total_amount).toFixed(2)}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    `;

    content.innerHTML = html;
}

    // Initialize on DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
        updateFilterCount();
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('filterDropdown');
            const button = event.target.closest('button[onclick="toggleFilterDropdown()"]');
            
            if (!button && dropdown && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
                const icon = document.getElementById('filterDropdownIcon');
                if (icon) icon.classList.remove('rotate-180');
            }
        });

        // Handle Escape key to close modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('.backdrop-blur-sm:not(.hidden)');
                openModals.forEach(modal => {
                    const modalId = modal.id;
                    if (modalId) closeModal(modalId);
                });
            }
        });
    });document.addEventListener('DOMContentLoaded', function() {
    updateFilterCount();
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('filterDropdown');
        const button = event.target.closest('button[onclick="toggleFilterDropdown()"]');
        
        if (!button && dropdown && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
            const icon = document.getElementById('filterDropdownIcon');
            if (icon) icon.classList.remove('rotate-180');
        }
    });

    // Handle Escape key to close modals AND delete modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            // Close sale details modal
            const openModals = document.querySelectorAll('.backdrop-blur-sm:not(.hidden)');
            openModals.forEach(modal => {
                const modalId = modal.id;
                if (modalId) closeModal(modalId);
            });
            
            // Close delete modal
            closeDeleteModal();
        }
    });
});
    
    </script>

@endsection