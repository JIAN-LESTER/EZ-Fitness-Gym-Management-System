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

.modal-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #9CA3AF #F3F4F6;
    scroll-behavior: smooth;
}
</style>

@section('content')
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

        <!-- Header -->
        <div class="flex justify-between items-center p-4 sm:p-6 border-b border-purple-200">
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
                                class="pl-10 w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-400 shadow-sm transition-all">
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
        <span id="filterCount" class="hidden ml-1 px-2 py-0.5 text-xs bg-purple-600 text-white rounded-full">0</span>
        <svg class="w-4 h-4 transition-transform" id="filterDropdownIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Filter Dropdown Content - Changed to absolute positioning within relative container -->
   
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
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="border-t border-gray-200 pt-4 flex gap-2">
                <button type="button" onclick="clearAllFilters()" 
                    class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Clear All
                </button>
                <button type="submit" 
                    class="flex-1 px-4 py-2 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-lg transition-colors">
                    Apply Filters
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
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Transaction Type</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Sale ID</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Cashier</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50 transition-colors group">
                     
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $transaction->type === 'sales' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $transaction->type === 'stock_in' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $transaction->type === 'stock_out' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $transaction->type === 'stock_in' ? 'Stock In' : ($transaction->type === 'stock_out' ? 'Stock Out' : ucfirst($transaction->type)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($transaction->sale)
                                    <p class="font-medium text-gray-900">#{{ $transaction->sale->sales_id }}</p>
                                @else
                                    <p class="text-gray-400">N/A</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($transaction->sale && $transaction->sale->user)
                                    <p class="font-medium text-gray-900">{{ $transaction->sale->user->first_name }} {{ $transaction->sale->user->last_name }}</p>
                                    <p class="text-sm text-gray-500">@&ZeroWidthSpace;{{ $transaction->sale->user->username }}</p>
                                @else
                                    <p class="text-gray-400">N/A</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($transaction->sale)
                                    <p class="font-semibold text-gray-900">₱{{ number_format($transaction->sale->total_amount, 2) }}</p>
                                @else
                                    <p class="text-gray-400">N/A</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <p class="text-gray-900">{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y') }}</p>
                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($transaction->created_at)->format('h:i A') }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button onclick="showTransaction('{{ $transaction->transaction_id }}')" class="text-gray-500 hover:text-gray-700" title="View Details">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </td>
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
                        <a href="{{ $transactions->previousPageUrl() }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-purple-700 text-white hover:from-purple-700 hover:to-purple-800 shadow-md transition-all duration-200 font-medium">Prev</a>
                    @endif

                    <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium">
                        {{ $transactions->currentPage() }} / {{ $transactions->lastPage() }}
                    </span>

                    @if($transactions->hasMorePages())
                        <a href="{{ $transactions->nextPageUrl() }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-purple-600 to-purple-700 text-white hover:from-purple-700 hover:to-purple-800 shadow-md transition-all duration-200 font-medium">Next</a>
                    @else
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- View Transaction Modal -->
    <div id="transactionShowModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0 bg-opacity-50" onclick="closeModal('transactionShowModal')"></div>

        <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden">
            <header class="bg-blue-800 text-white p-5 rounded-t-2xl flex justify-between items-center">
                <h2 class="text-xl font-semibold">Transaction Details</h2>
                <button onclick="closeModal('transactionShowModal')" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <div id="transactionShowContent" class="p-6 modal-scrollbar overflow-y-auto" style="max-height: calc(90vh - 80px);">
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
                </div>
            </div>
        </div>
    </div>

    <script>


// Make functions globally accessible
window.toggleFilterDropdown = function(event) {
    event.preventDefault();
    event.stopPropagation(); 
    
    const dropdown = document.getElementById('filterDropdown');
    const icon = document.getElementById('filterDropdownIcon');

    if (!dropdown || !icon) {
        console.error('Dropdown elements not found');
        return;
    }

    // Simply toggle visibility - let CSS handle positioning
    if (dropdown.classList.contains('hidden')) {
        // Show dropdown
        dropdown.classList.remove('hidden');
        icon.classList.add('rotate-180');
        console.log('Dropdown opened');
    } else {
        // Hide dropdown
        dropdown.classList.add('hidden');
        icon.classList.remove('rotate-180');
        console.log('Dropdown closed');
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

    // Submit form to clear filters
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
    document.body.style.overflow = 'hidden';
    
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
};

window.closeModal = function(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    const scrollY = document.body.style.top;
    
    modal.classList.add('hidden');
    modal.style.display = 'none';
    
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.width = '';
    document.body.style.overflow = '';
    
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
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
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
        });
};

function renderTransactionDetails(transaction) {
    const content = document.getElementById('transactionShowContent');
    if (!content) return;
    
    const typeLabel = transaction.type === 'stock_in' ? 'Stock In' : 
                     (transaction.type === 'stock_out' ? 'Stock Out' : 
                      transaction.type.charAt(0).toUpperCase() + transaction.type.slice(1));
    
    const formatDate = function(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    };
    
    let html = '<div class="space-y-6">';
    
    // Transaction Header
    html += '<div class=" dark:bg-gray-800 rounded-lg p-6">';
    html += '<div class="flex justify-between items-start mb-4">';
    html += '<div>';
    html += '<h3 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Transaction #' + transaction.transaction_id + '</h3>';
    html += '<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">' + formatDate(transaction.created_at) + '</p>';
    html += '</div>';
    html += '<div class="text-right">';
    
    let typeClass = '';
    if (transaction.type === 'sales') typeClass = 'bg-green-100 text-green-800';
    if (transaction.type === 'stock_in') typeClass = 'bg-blue-100 text-blue-800';
    if (transaction.type === 'stock_out') typeClass = 'bg-red-100 text-red-800';
    
    html += '<span class="px-4 py-2 rounded-full text-sm font-semibold ' + typeClass + '">' + typeLabel + '</span>';
    html += '</div></div></div>';

    // Sale Information
    if (transaction.sale) {
        html += '<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">';
        html += '<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">';
        html += '<h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Related Sale Information</h4>';
        html += '</div>';
        html += '<div class="p-6 space-y-4">';
        html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
        
        // Sale ID
        html += '<div><p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Sale ID</p>';
        html += '<p class="text-lg font-semibold text-gray-800 dark:text-gray-200">#' + transaction.sale.sales_id + '</p></div>';
        
        // Sale Status
        html += '<div><p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Sale Status</p>';
        let statusClass = '';
        if (transaction.sale.status === 'paid') statusClass = 'bg-green-100 text-green-800';
        if (transaction.sale.status === 'pending') statusClass = 'bg-yellow-100 text-yellow-800';
        if (transaction.sale.status === 'cancelled') statusClass = 'bg-red-100 text-red-800';
        html += '<span class="inline-block px-3 py-1 rounded-full text-sm font-semibold ' + statusClass + '">';
        html += transaction.sale.status.charAt(0).toUpperCase() + transaction.sale.status.slice(1);
        html += '</span></div>';
        
        // Customer
        html += '<div><p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Cashier</p>';
        html += '<p class="text-lg font-semibold text-gray-800 dark:text-gray-200">' + transaction.sale.user.first_name + ' ' + transaction.sale.user.last_name + '</p>';
        html += '<p class="text-sm text-gray-500">@' + transaction.sale.user.username + '</p></div>';
        
        // Payment Method
        html += '<div><p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Payment Method</p>';
        let paymentClass = '';
        if (transaction.sale.payment_method === 'cash') paymentClass = 'bg-green-100 text-green-800';
        if (transaction.sale.payment_method === 'credit_card') paymentClass = 'bg-blue-100 text-blue-800';
        if (transaction.sale.payment_method === 'qr') paymentClass = 'bg-purple-100 text-purple-800';
        
        let paymentLabel = transaction.sale.payment_method === 'credit_card' ? 'Credit Card' : 
                          transaction.sale.payment_method.charAt(0).toUpperCase() + transaction.sale.payment_method.slice(1);
        html += '<span class="inline-block px-3 py-1 rounded-full text-sm font-semibold ' + paymentClass + '">' + paymentLabel + '</span></div>';
        
        // Sale Date
        html += '<div><p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Sale Date</p>';
        html += '<p class="text-gray-800 dark:text-gray-200 font-medium">' + formatDate(transaction.sale.created_at) + '</p></div>';
        
        // Total Amount
        html += '<div><p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Amount with VAT (12%)</p>';
        html += '<p class="text-2xl font-bold text-purple-600 dark:text-purple-400">₱' + parseFloat(transaction.sale.total_amount).toFixed(2) + '</p></div>';
        
        html += '</div></div></div>';

        // Sale Items
        if (transaction.sale.items && transaction.sale.items.length > 0) {
            html += '<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">';
            html += '<div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">';
            html += '<h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Items in Sale</h4></div>';
            html += '<div class="overflow-x-auto"><table class="min-w-full">';
            html += '<thead class="bg-gray-50 dark:bg-gray-900"><tr>';
            html += '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>';
            html += '<th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>';
            html += '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>';
            html += '<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>';
            html += '</tr></thead><tbody class="divide-y divide-gray-200 dark:divide-gray-700">';

            transaction.sale.items.forEach(function(item) {
                html += '<tr class="hover:bg-gray-50 dark:hover:bg-gray-900">';
                html += '<td class="px-6 py-4">';
                html += '<p class="font-medium text-gray-900 dark:text-gray-200">' + item.product.name + '</p>';
                if (item.product.description) {
                    html += '<p class="text-sm text-gray-500">' + item.product.description + '</p>';
                }
                html += '</td>';
                html += '<td class="px-6 py-4 text-center text-gray-900 dark:text-gray-200">' + item.quantity + '</td>';
                html += '<td class="px-6 py-4 text-right text-gray-900 dark:text-gray-200">₱' + parseFloat(item.price).toFixed(2) + '</td>';
                html += '<td class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-gray-200">₱' + parseFloat(item.sub_total).toFixed(2) + '</td>';
                html += '</tr>';
            });

            html += '</tbody></table></div></div>';
        }
    } else {
        html += '<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">';
        html += '<p class="text-yellow-800">No associated sale found for this transaction.</p></div>';
    }

    

    content.innerHTML = html;
}

// Initialize on DOM Ready
document.addEventListener('DOMContentLoaded', function () {
    console.log('Transaction page initialized');
    
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