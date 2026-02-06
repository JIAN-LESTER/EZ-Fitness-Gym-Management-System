@extends('layouts.app')

@section('title', 'Transactions')
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
</style>

@section('content')
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

        <!-- Header -->
        <div class="flex justify-between items-center p-4 sm:p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Transactions</h2>
        </div>

        <!-- Search Section -->
        <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('transactions.index') }}" class="space-y-4" role="search">
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
                                placeholder="Search by transaction ID, type, or customer..."
                                class="pl-10 w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-400 shadow-sm transition-all">
                        </div>
                    </div>

                    <!-- Filter Dropdown -->
                    <div class="relative w-full sm:w-auto" id="filterDropdownContainer">
                        <button type="button" onclick="toggleFilterDropdown(event)"
                            class="w-full sm:w-auto flex items-center justify-between gap-2 bg-white border-2 border-gray-200 text-gray-700 px-6 py-3 rounded-xl hover:border-gray-300 shadow-sm transition-all duration-300 font-semibold whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filters
                            <span id="filterCount" class="hidden ml-1 px-2 py-0.5 text-xs bg-gray-600 text-white rounded-full">0</span>
                            <svg class="w-4 h-4 transition-transform" id="filterDropdownIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Filter Dropdown Content -->
                        <div id="filterDropdown" class="hidden fixed mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 max-h-[calc(100vh-200px)] overflow-y-auto z-[9999]">
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
                    <div class="w-full sm:w-auto">
                        <button type="submit"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-700 shadow-md hover:shadow-lg transition-all duration-300 font-semibold whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Search
                        </button>
                    </div>

                    <!-- Clear Filters -->
                    @if(request('search') || request('status'))
                        <div class="w-full sm:w-auto">
                            <a href="{{ route('transactions.index') }}"
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

        <!-- Transactions Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200">
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Transaction ID</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Performed By</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        @if(Auth::user()->role === 'admin')
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        @endif
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

                            <!-- Amount (Only for Sales) -->
                            <td class="px-6 py-4 text-center">
                                @if($transaction->sale)
                                    <p class="font-semibold text-gray-900">₱{{ number_format($transaction->sale->total_amount, 2) }}</p>
                                @else
                                    <p class="text-gray-400">-</p>
                                @endif
                            </td>
                            <!-- Quantity Column -->
                            <td class="px-6 py-4 text-center">
                                @if($transaction->type === 'sales')
                                    <div class="flex flex-col items-center">
                                        <p class="font-semibold text-green-600 text-lg">
                                            {{ $transaction->quantity }}
                                        </p>
                                        @if($transaction->sale && $transaction->sale->items)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $transaction->sale->items->count() }} item(s)
                                            </p>
                                        @endif
                                    </div>
                                @elseif($transaction->type === 'memberships')
                                    <div class="flex flex-col items-center">
                                        <p class="font-semibold text-purple-600 text-lg">1</p>
                                        <p class="text-xs text-gray-500 mt-1">membership</p>
                                    </div>
                                @elseif(in_array($transaction->type, ['stock_in', 'stock_out']))
                                    <div class="flex flex-col items-center">
                                        <p class="font-semibold text-lg
                                            {{ $transaction->type === 'stock_in' ? 'text-blue-600' : 'text-red-600' }}">
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

                            <!-- Actions -->
                            @if(Auth::user()->role === 'admin')
                            <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                                <button onclick="confirmDelete('{{ $transaction->transaction_id }}')"
                                    class="text-red-500 hover:text-red-700"
                                    title="Delete Transaction">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3" />
                                    </svg>
                                </button>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
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

        <!-- Pagination -->
        @if($transactions->total() > 0)
            <div class="flex flex-col sm:flex-row items-center justify-between p-4 sm:p-6 bg-gray-50 border-t border-gray-200 gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-semibold text-gray-900">{{ $transactions->firstItem() }}</span> to
                    <span class="font-semibold text-gray-900">{{ $transactions->lastItem() }}</span> of
                    <span class="font-semibold text-gray-900">{{ $transactions->total() }}</span> transactions
                </div>

                <div class="flex gap-2">
                    @if($transactions->onFirstPage())
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Prev</span>
                    @else
                        <a href="{{ $transactions->previousPageUrl() }}" class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium">Prev</a>
                    @endif

                    <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium sm:hidden">
                        {{ $transactions->currentPage() }} / {{ $transactions->lastPage() }}
                    </span>

                    @if($transactions->hasMorePages())
                        <a href="{{ $transactions->nextPageUrl() }}" class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium">Next</a>
                    @else
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- View Transaction Modal -->
    <div id="transactionShowModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0 backdrop-blur bg-opacity-50" onclick="closeModal('transactionShowModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-5 rounded-t-2xl flex-shrink-0 flex justify-between items-center">
                <h2 class="text-xl font-semibold">Transaction Details</h2>
                <button onclick="closeModal('transactionShowModal')" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <div id="transactionShowContent" class="overflow-y-auto flex-1 modal-scrollbar p-6 bg-white">
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0 bg-black bg-opacity-50" onclick="closeDeleteModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-center text-gray-900 mb-2">
                    Delete Transaction
                </h3>

                <p class="text-center text-gray-600 mb-6">
                    Are you sure you want to delete this transaction? This action cannot be undone.
                </p>

                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')

                    <div class="flex gap-3">
                        <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
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

        window.confirmDelete = function(transactionId) {
            const form = document.getElementById('deleteForm');
            form.action = '/transactions/' + transactionId;

            const modal = document.getElementById('deleteModal');
            const scrollY = window.scrollY;

            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollY}px`;
            document.body.style.width = '100%';
            document.body.style.overflowY = 'scroll';

            modal.classList.remove('hidden');
        };

        window.closeDeleteModal = function() {
            const modal = document.getElementById('deleteModal');
            const scrollY = document.body.style.top;

            modal.classList.add('hidden');

            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            document.body.style.overflowY = '';

            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        };

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

       // FIXED renderTransactionDetails function with SUBSCRIPTION support
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

    let html = `<div class="space-y-5">`;

    /* HEADER */
    html += `
        <div class="bg-gray-50 p-5 rounded-lg">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800">Transaction #${transaction.transaction_id}</h3>
                    <p class="text-sm text-gray-500">${formatDate(transaction.created_at)}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold ${typeClass}">
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
            <div class="bg-white border border-gray-200 rounded-lg">
                <div class="px-5 py-3 border-b border-gray-200 ${headerBgClass}">
                    <h4 class="text-md font-semibold text-gray-800">${headerTitle}</h4>
                </div>

                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                        <div>
                            <p class="text-gray-500">Transaction ID</p>
                            <p class="font-semibold text-gray-800">#${transaction.transaction_id}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 mb-2">Status</p>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusClass}">
                                ${transaction.sale.status.charAt(0).toUpperCase() + transaction.sale.status.slice(1)}
                            </span>
                        </div>

                        <div>
                            <p class="text-gray-500">Member</p>
                            <p class="font-semibold text-gray-800">${transaction.sale.user.first_name} ${transaction.sale.user.last_name}</p>
                            <p class="text-gray-500">@${transaction.sale.user.username}</p>
                        </div>

                        <div>
                            <p class="text-gray-500">Email</p>
                            <p class="font-medium text-gray-800">${transaction.sale.user.email}</p>
                        </div>

                        ${transaction.performer ? `
                            <div>
                                <p class="text-gray-500">Approved By</p>
                                <p class="font-semibold text-gray-800">${transaction.performer.first_name} ${transaction.performer.last_name}</p>
                                <p class="text-gray-500">@${transaction.performer.username}</p>
                            </div>
                        ` : ''}

                        <div>
                            <p class="text-gray-500">Transaction Date</p>
                            <p class="font-medium text-gray-800">${formatDate(transaction.created_at)}</p>
                        </div>

                        <div>
                            <p class="text-gray-500">${transaction.type === 'memberships' ? 'Membership' : 'Subscription'} Amount</p>
                            <p class="text-xl font-bold ${transaction.type === 'memberships' ? 'text-purple-700' : 'text-orange-700'}">
                                ₱${parseFloat(transaction.sale.total_amount).toFixed(2)}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500 mb-2">Payment Method</p>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold ${paymentClass}">
                                    ${paymentLabel}
                                </span>

                                ${transaction.sale.payment_method === 'gcash' && transaction.sale.reference_code ? `
                                    <span class="text-sm text-gray-500">Ref:</span>
                                    <span class="font-semibold ${transaction.type === 'memberships' ? 'text-purple-600' : 'text-orange-600'} rounded-full px-3 py-1 ${paymentClass}">
                                        ${transaction.sale.reference_code}
                                    </span>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Member profile section (if exists)
        if (transaction.sale.user.member) {
            const member = transaction.sale.user.member;
            const memberStatusClass = {
                active: 'bg-green-100 text-green-800',
                expired: 'bg-red-100 text-red-800',
                inactive: 'bg-gray-100 text-gray-800'
            }[member.status] || '';

            html += `
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                        <h4 class="text-md font-semibold text-gray-800">Member Profile</h4>
                    </div>

                    <div class="p-5 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                            <div>
                                <p class="text-gray-500">Member ID</p>
                                <p class="font-semibold text-gray-800">#${member.member_id}</p>
                            </div>

                            <div>
                                <p class="text-gray-500 mb-2">Current Status</p>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold ${memberStatusClass}">
                                    ${member.status.charAt(0).toUpperCase() + member.status.slice(1)}
                                </span>
                            </div>

                            ${member.plan ? `
                                <div>
                                    <p class="text-gray-500">Membership Plan</p>
                                    <p class="font-semibold text-gray-800">${member.plan.name}</p>
                                    ${member.plan.duration_days ? `<p class="text-xs text-gray-500">${member.plan.duration_days} days</p>` : ''}
                                    ${member.plan.price ? `<p class="text-xs text-gray-600">₱${parseFloat(member.plan.price).toFixed(2)}</p>` : ''}
                                </div>
                            ` : ''}

                            ${member.subscription ? `
                                <div>
                                    <p class="text-gray-500">Subscription</p>
                                    <p class="font-semibold text-gray-800">${member.subscription.name}</p>
                                    ${member.subscription.duration_days ? `<p class="text-xs text-gray-500">${member.subscription.duration_days} days</p>` : ''}
                                    ${member.subscription.price ? `<p class="text-xs text-gray-600">₱${parseFloat(member.subscription.price).toFixed(2)}</p>` : ''}
                                </div>
                            ` : ''}

                            ${member.start_date ? `
                                <div>
                                    <p class="text-gray-500">Start Date</p>
                                    <p class="font-medium text-gray-800">${formatDate(member.start_date)}</p>
                                </div>
                            ` : ''}

                            ${member.end_date ? `
                                <div>
                                    <p class="text-gray-500">End Date</p>
                                    <p class="font-medium text-gray-800">${formatDate(member.end_date)}</p>
                                </div>
                            ` : ''}

                            ${member.mobile_number ? `
                                <div>
                                    <p class="text-gray-500">Mobile Number</p>
                                    <p class="font-medium text-gray-800">${member.mobile_number}</p>
                                </div>
                            ` : ''}

                            ${member.sex ? `
                                <div>
                                    <p class="text-gray-500">Sex</p>
                                    <p class="font-medium text-gray-800 capitalize">${member.sex}</p>
                                </div>
                            ` : ''}

                            ${member.birthday ? `
                                <div>
                                    <p class="text-gray-500">Birthday</p>
                                    <p class="font-medium text-gray-800">${formatDate(member.birthday)}</p>
                                </div>
                            ` : ''}

                            ${member.height ? `
                                <div>
                                    <p class="text-gray-500">Height</p>
                                    <p class="font-medium text-gray-800">${member.height} cm</p>
                                </div>
                            ` : ''}

                            ${member.weight ? `
                                <div>
                                    <p class="text-gray-500">Weight</p>
                                    <p class="font-medium text-gray-800">${member.weight} kg</p>
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
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                        <h4 class="text-md font-semibold text-gray-800">Purchased Items</h4>
                    </div>

                    <div class="overflow-x-auto">
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
                </div>
            `;
        }
    }
    /* PRODUCT INFORMATION (for stock movements) */
    if (transaction.product && (transaction.type === 'stock_in' || transaction.type === 'stock_out')) {
        html += `
            <div class="bg-white border border-gray-200 rounded-lg">
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-md font-semibold text-gray-800">Product Information</h4>
                </div>

                <div class="p-5 space-y-4">
                    <div class="flex flex-col md:flex-row gap-5">
                        ${transaction.product.image ? `
                            <img src="/storage/${transaction.product.image}"
                                class="w-28 h-28 object-cover rounded-lg border border-gray-200">
                        ` : ''}

                        <div class="flex-1 space-y-3">
                            <div>
                                <p class="text-sm text-gray-500">Product Name</p>
                                <p class="font-semibold text-gray-800">${transaction.product.name}</p>
                            </div>

                            ${transaction.product.description ? `
                                <div>
                                    <p class="text-sm text-gray-500">Description</p>
                                    <p class="text-gray-700">${transaction.product.description}</p>
                                </div>
                            ` : ''}

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Price</p>
                                    <p class="font-semibold text-gray-800">
                                        ₱${parseFloat(transaction.product.price).toFixed(2)}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Quantity</p>
                                    <p class="font-semibold text-gray-800">${transaction.quantity}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold ${
                                        transaction.product.status === 'available'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'
                                    }">
                                        ${transaction.product.status.charAt(0).toUpperCase() + transaction.product.status.slice(1)}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /* SALE INFORMATION (for regular sales, not memberships) */
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
            <div class="bg-white border border-gray-200 rounded-lg">
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-md font-semibold text-gray-800">Sale Information</h4>
                </div>

                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                        <div>
                            <p class="text-gray-500">Sale ID</p>
                            <p class="font-semibold text-gray-800">#${transaction.sale.sales_id}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 mb-2">Sale Status</p>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusClass}">
                                ${transaction.sale.status.charAt(0).toUpperCase() + transaction.sale.status.slice(1)}
                            </span>
                        </div>

                        <div>
                            <p class="text-gray-500">Customer</p>
                            <p class="font-semibold text-gray-800">${transaction.sale.user.first_name} ${transaction.sale.user.last_name}</p>
                            <p class="text-gray-500">@${transaction.sale.user.username}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 mb-2">Payment Method</p>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold ${paymentClass}">
                                    ${paymentLabel}
                                </span>

                                ${transaction.sale.payment_method === 'gcash' && transaction.sale.reference_code ? `
                                    <span class="text-sm text-gray-500">Ref:</span>
                                    <span class="font-semibold text-purple-600 rounded-full px-3 py-1 ${paymentClass}">
                                        ${transaction.sale.reference_code}
                                    </span>
                                ` : ''}
                            </div>
                        </div>

                        <div>
                            <p class="text-gray-500">Sale Date</p>
                            <p class="font-medium text-gray-800">${formatDate(transaction.sale.created_at)}</p>
                        </div>

                        <div>
                            <p class="text-gray-500">Total Amount</p>
                            <p class="text-xl font-bold text-gray-800">
                                ₱${parseFloat(transaction.sale.total_amount).toFixed(2)}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        /* SALE ITEMS TABLE */
        if (transaction.sale.items?.length > 0) {
            html += `
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                        <h4 class="text-md font-semibold text-gray-800">Items in Sale</h4>
                    </div>

                    <div class="overflow-x-auto">
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
                </div>
            `;
        }
    }

    /* NO PRODUCT/SALE FALLBACK */
    if (!transaction.product && !transaction.sale) {
        html += `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-yellow-800">No associated sale or product found for this transaction.</p>
            </div>
        `;
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
                    window.closeDeleteModal();
                }
            });
        });
    </script>

@endsection