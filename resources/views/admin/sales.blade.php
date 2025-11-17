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
                                placeholder="Search by sales ID or customer name..."
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
                        <div id="filterDropdown" class="hidden fixed mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 max-h-[calc(100vh-200px)] overflow-y-auto z-[9999]">
                            <div class="p-4 space-y-4">
                                <!-- Status Filter -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Filter by Status</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="status[]" value="paid" 
                                                {{ in_array('paid', request('status', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Paid</span>
                                            <span class="ml-auto px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">Paid</span>
                                        </label>

                                        <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="status[]" value="pending" 
                                                {{ in_array('pending', request('status', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-4 h-4 text-yellow-600 rounded focus:ring-2 focus:ring-yellow-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Pending</span>
                                            <span class="ml-auto px-2 py-0.5 text-xs bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                                        </label>

                                        <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="status[]" value="cancelled" 
                                                {{ in_array('cancelled', request('status', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-4 h-4 text-red-600 rounded focus:ring-2 focus:ring-red-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Cancelled</span>
                                            <span class="ml-auto px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">Cancelled</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200"></div>


                                <!-- Action Buttons -->
                                <div class="border-t border-gray-200 pt-4 flex gap-2">
                                    <button type="button" onclick="clearAllFilters()" 
                                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                        Clear All
                                    </button>
                                    <button type="submit" 
                                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
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
                    @if(request('search') || request('status') || request('payment_method'))
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

        <!-- Sales Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200">
                     
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cashier</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment</th>
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
                                    {{ $sale->payment_method === 'qr' ? 'bg-purple-100 text-purple-800' : '' }}">
                                    {{ $sale->payment_method === 'credit_card' ? 'Credit Card' : ucfirst($sale->payment_method) }}
                                </span>
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
                                <button onclick="showSale('{{ $sale->sales_id }}')" class="text-gray-500 hover:text-gray-700" title="View Details">
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

    <script>
    // Filter Functions
    function toggleFilterDropdown() {
        const dropdown = document.getElementById('filterDropdown');
        const button = event.target.closest('button');
        const icon = document.getElementById('filterDropdownIcon');
        
        const rect = button.getBoundingClientRect();
        dropdown.style.left = rect.left + 'px';
        dropdown.style.top = (rect.bottom + window.scrollY + 8) + 'px';
        
        dropdown.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }

    function updateFilterCount() {
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
    }

    function clearAllFilters() {
        const checkboxes = document.querySelectorAll('#filterDropdown input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = false);
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
                <div class=" dark:bg-gray-800 rounded-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800  dark:text-gray-200">Sale #${sale.sales_id}</h3>
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-blue-200 dark:border-gray-700">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Cashier</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">${sale.user.first_name} ${sale.user.last_name}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">@${sale.user.username}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Payment Method</p>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                ${sale.payment_method === 'cash' ? 'bg-green-100 text-green-800' : ''}
                                ${sale.payment_method === 'credit_card' ? 'bg-blue-100 text-blue-800' : ''}
                                ${sale.payment_method === 'qr' ? 'bg-purple-100 text-purple-800' : ''}">
                                ${sale.payment_method === 'credit_card' ? 'Credit Card' : sale.payment_method.charAt(0).toUpperCase() + sale.payment_method.slice(1)}
                            </span>
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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        `;

        if (sale.items && sale.items.length > 0) {
            sale.items.forEach(item => {
                html += `
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900 dark:text-gray-200">${item.product.name}</p>
                            ${item.product.description ? `<p class="text-sm text-gray-500">${item.product.description}</p>` : ''}
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
                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No items found</td>
                </tr>
            `;
        }

        html += `
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right font-semibold text-gray-800 dark:text-gray-200">Total Amount:</td>
                                    <td class="px-6 py-4 text-right text-xl font-bold text-blue-600 dark:text-blue-400">₱${parseFloat(sale.total_amount).toFixed(2)}</td>
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
    });
    </script>

@endsection