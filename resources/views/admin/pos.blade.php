@extends('layouts.app')
@section('title', 'Point of Sale | EZ Fitness')
@section('header', 'Point of Sale')

@section('content')

    <div class="w-full h-full flex gap-3">

        {{-- LEFT SIDE – ITEMS / PRODUCTS --}}
        <div class="w-2/3 bg-white shadow rounded-lg p-3 flex flex-col" style="max-height: calc(100vh - 120px);">

            <h2 class="text-lg font-semibold mb-3 text-gray-800">Products</h2>

            {{-- Search --}}
            <form method="GET" id="searchForm" class="mb-3">
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                    placeholder="Search product by name or description..."
                    class="w-full px-3 py-2 border border-gray-200 text-gray-800 rounded-lg focus:ring-2 focus:ring-gray-500 focus:outline-none text-sm">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
            </form>

            <div class="mb-3 overflow-x-auto">
                <div class="flex gap-2 pb-2">
                    {{-- ALL PRODUCTS TAB --}}
                    <a href="{{ route('pos.index', ['search' => request('search')]) }}" class="px-4 py-1.5 rounded border transition-all duration-200 text-xs font-medium                                                                                                                                                                                  {{ !request('category')
    ? 'bg-gray-800 text-white '
    : 'bg-white text-gray-800 hover:bg-gray-50 border-gray-300' }}">
                        All
                    </a>

                    @foreach($categories as $category)
                                <a href="{{ route('pos.index', ['category' => $category->category_id, 'search' => request('search')]) }}"
                                    class="px-4 py-1.5 rounded border text-xs font-medium transition-all duration-200 whitespace-nowrap                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              {{ request('category') == $category->category_id
    ? 'bg-gray-800 text-white'
    : 'bg-white text-gray-800 hover:bg-gray-50 border-gray-300' }}">
                                    {{ $category->name }}
                                </a>
                    @endforeach
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div class="grid grid-cols-3 lg:grid-cols-5 gap-3 mb-3">
                    @forelse ($products as $item)
                        <div
                            class="border rounded-lg border-gray-200 p-2 hover:shadow-md cursor-pointer transition flex flex-col justify-between relative bg-white">
                         <div class="h-28 bg-white rounded mb-2 flex items-center justify-center overflow-hidden p-2">
    @if($item->product && $item->product->image)
        <img src="{{ asset('storage/' . $item->product->image) }}" 
             alt="{{ $item->product->name }}"
             class="w-full h-full object-contain">
    @else
        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    @endif
</div>

                            <span class="absolute top-1 right-1
                                                                @if($item->quantity == 0)
                                                                    bg-red-100 text-red-800
                                                                @elseif($item->quantity < 5)
                                                                    bg-yellow-100 text-yellow-800
                                                                @else
                                                                    bg-blue-100 text-blue-800
                                                                @endif
                                                                text-xs font-semibold px-1.5 py-0.5 rounded stock-badge"
                                data-product-id="{{ $item->product->product_id }}" data-initial-stock="{{ $item->quantity }}">
                                Stock: {{ $item->quantity }}
                            </span>

                            <h3 class="font-bold text-sm text-gray-900 truncate" title="{{ $item->product->name }}">
                                {{ $item->product->name }}
                            </h3>

                            <p class="text-xs text-gray-500 line-clamp-2 mb-3" title="{{ $item->product->description }}">
                                {{ $item->product->description ?? 'No description' }}
                            </p>

                            <div class="mt-auto flex justify-between items-center">
                                <p class="font-bold text-green-700 text-xs">₱{{ number_format($item->product->price, 2) }}</p>

                                <button onclick="addToCart({{ $item->product->product_id }})"
                                    class="bg-gray-800 hover:bg-gray-700 text-white px-2 py-1 rounded text-xs flex items-center gap-1 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                    title="Add to Cart" data-product-id="{{ $item->product->product_id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-gray-500 text-sm font-medium">No products available</p>
                            <p class="text-gray-400 text-xs mt-1">Try adjusting your search or filter</p>
                        </div>
                    @endforelse
                </div>

                @if($products->hasPages())
                    <div class="mt-3 pb-1">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- CART SECTION --}}
        <div class="w-1/3 bg-white shadow rounded-lg p-3 flex flex-col" style="max-height: calc(100vh - 120px);">

            <div class="flex justify-between items-center mb-3">
                <h2 class="text-lg font-semibold text-gray-800">Cart</h2>
                <span id="cart-count" class="bg-gray-800 text-white text-xs font-semibold px-2 py-1 rounded-full">0</span>
            </div>

            <div id="cart-items" class="flex-1 overflow-y-auto border-b pb-2 space-y-2">
                <div class="text-center py-6 text-gray-500">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6H19M7 13l-1.5 6m0 0H19m-11 0a1 1 0 11-2 0 1 1 0 012 0zm12 0a1 1 0 11-2 0 1 1 0 012 0z" />
                    </svg>
                    <p class="text-xs">Cart is empty</p>
                </div>
            </div>

            {{-- Payment Method Selection --}}
            <div class="mt-3 border-t pt-3">
                <h3 class="text-base font-semibold text-gray-800 mb-2">Payment Method</h3>
                <div class="flex flex-row gap-2">
                    <!-- Cash Payment Option -->
                    <button type="button" data-payment-method="cash"
                        class="payment-button flex flex-col items-center p-1 border-2 border-gray-200 rounded cursor-pointer transition-all duration-200 hover:border-gray-400 hover:bg-gray-50 flex-1 h-full">
                        <div class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full mb-1">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <span class="font-medium text-gray-800 text-xs">Cash</span>
                            <p class="text-xs text-gray-500 mt-0.5">Pay with cash</p>
                        </div>
                    </button>

                    <!-- GCash Code Payment Option -->
                    <button type="button" data-payment-method="gcash"
                        class="payment-button flex flex-col items-center p-1 border-2 border-gray-200 rounded cursor-pointer transition-all duration-200 hover:border-purple-400 hover:bg-purple-50 flex-1 h-full">
                        <div class="flex items-center justify-center w-8 h-8 bg-purple-100 rounded-full mb-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <span class="font-medium text-gray-800 text-xs">GCash</span>
                            <p class="text-xs text-gray-500 mt-0.5">Pay with Gcash</p>
                        </div>
                    </button>
                </div>
            </div>

            <div class="mt-3 space-y-2">
                <div class="mt-4 border-t border-gray-200 pt-3 space-y-2">
                    <!-- Total Amount -->
                    <div class="flex justify-between items-center bg-gray-50 rounded p-2 -mx-1">
                        <div>
                            <p class="text-base font-bold text-gray-800">Total</p>
                        </div>
                        <p id="cart-total" class="text-xl font-bold text-gray-800">₱0.00</p>
                    </div>

                    <!-- Optional: Items Count -->
                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span>Items in cart</span>
                        <span id="items-count">0 items</span>
                    </div>

                    <!-- Add this new section for total quantity -->
                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span class="font-bold">Total items</span>
                        <span id="total-items-count" class="font-bold">0</span>
                    </div>
                </div>

                <button id="checkoutBtn" onclick="checkout()"
                    class="w-full bg-gray-800 text-white py-2 rounded hover:bg-gray-700 transition font-semibold disabled:bg-gray-300 disabled:cursor-not-allowed text-sm"
                    disabled>
                    Checkout
                </button>

                <button onclick="clearCart()"
                    class="w-full bg-red-500 text-white py-1.5 rounded hover:bg-red-600 transition text-xs">
                    Clear Cart
                </button>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="fixed inset-0 bg-opacity-40 hidden backdrop-blur items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-80 p-5">
            <h2 class="text-base font-semibold text-gray-800 mb-2" id="confirmTitle">Confirm Action</h2>
            <p class="text-gray-600 mb-4 text-sm" id="confirmMessage">Are you sure?</p>

            <div class="flex justify-end gap-2">
                <button id="cancelConfirm"
                    class="px-3 py-1.5 rounded border text-gray-800 border-gray-300 hover:bg-gray-100 transition text-sm">
                    Cancel
                </button>

                <button id="okConfirm"
                    class="px-3 py-1.5 bg-gray-800 text-white rounded hover:bg-gray-700 transition text-sm">
                    Confirm
                </button>
            </div>
        </div>
    </div>
    {{-- GCash Modal --}}
    <div id="gcashModal" class="fixed inset-0 bg-opacity-40 hidden backdrop-blur items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-80 p-5">
            <h2 class="text-base font-semibold text-gray-800 mb-2">GCash Payment</h2>
            <p class="text-gray-600 mb-4 text-sm">Please enter the GCash reference code:</p>

            <div class="mb-4">
                <input type="text"
                    id="gcashReferenceCode"
                    placeholder="Enter reference code"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm font-semibold text-gray-800">
                <p class="text-xs text-gray-500 mt-1">Enter the transaction reference code from GCash</p>
            </div>

            <div class="flex justify-end gap-2">
                <button id="cancelGcash"
                    class="px-3 py-1.5 rounded border text-gray-800 border-gray-300 hover:bg-gray-100 transition text-sm">
                    Cancel
                </button>

                <button id="confirmGcash"
                    class="px-3 py-1.5 bg-purple-600 text-white rounded hover:bg-purple-700 transition text-sm">
                    Confirm Payment
                </button>
            </div>
        </div>
    </div>

    {{-- Cash Modal --}}
    {{-- Cash Payment Modal --}}
    <div id="cashModal" class="fixed inset-0 bg-opacity-40 hidden backdrop-blur items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-80 p-5">
            <h2 class="text-base font-semibold text-gray-800 mb-2">Cash Payment</h2>

            <!-- Display Total Amount -->
            <div class="mb-3 p-2 bg-gray-50 rounded-lg">
    <p class="text-xs text-gray-600">Total Amount Due</p>
    <p id="cashTotalAmount" class="text-lg font-bold text-gray-800">₱0.00</p>
            </div>

            <div class="mb-4">
                <label for="cashAmount" class="block text-sm font-medium text-gray-700 mb-1">
                    Amount Paid
                </label>
                <input type="number"
                    id="cashAmount"
                    placeholder="0.00"
                    step="0.01"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 text-sm text-gray-800 font-semibold"
                    oninput="calculateChange()">
                <p class="text-xs text-gray-500 mt-1">Enter the amount received from customer</p>
            </div>

            <!-- Change Calculation -->
            <div id="changeSection" class="hidden mb-4 p-2 bg-blue-50 rounded-lg">
                <p class="text-xs text-gray-600">Change</p>
                <p id="changeAmount" class="text-lg font-bold text-blue-700">₱0.00</p>
            </div>

            <!-- Warning for insufficient payment -->
            <div id="insufficientWarning" class="hidden mb-4 p-2 bg-red-50 rounded-lg border border-red-200">
                <p class="text-xs text-red-600 font-medium">Insufficient payment</p>
                <p id="remainingAmount" class="text-sm text-red-700">₱0.00</p>
            </div>

            <div class="flex justify-end gap-2">
                <button id="cancelCash"
                    class="px-3 py-1.5 rounded border text-gray-800 border-gray-300 hover:bg-gray-100 transition text-sm">
                    Cancel
                </button>

                <button id="confirmCash"
                    class="px-3 py-1.5 bg-gray-800 text-white rounded hover:bg-gray-700 transition text-sm disabled:bg-gray-300 disabled:cursor-not-allowed"
                    disabled>
                    Confirm Payment
                </button>
            </div>
        </div>
    </div>


    <script>


let cartItems   = [];          
let pendingUpdates = new Map(); 
let stockAdjustments = new Map(); 

document.addEventListener('DOMContentLoaded', function () {
    const paymentButtons = document.querySelectorAll('.payment-button');

    // Activate Cash by default
    const cashBtn = document.querySelector('[data-payment-method="cash"]');
    if (cashBtn) cashBtn.classList.add('border-gray-500', 'bg-gray-50');

    paymentButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const method = this.dataset.paymentMethod;

            paymentButtons.forEach(b => b.classList.remove(
                'border-gray-500', 'border-blue-500', 'border-purple-500',
                'bg-gray-50',      'bg-blue-50',      'bg-purple-50'
            ));

            if (method === 'cash')         this.classList.add('border-gray-500',   'bg-gray-50');
            else if (method === 'credit_card') this.classList.add('border-blue-500',  'bg-blue-50');
            else if (method === 'gcash')    this.classList.add('border-purple-500', 'bg-purple-50');

            updateCheckoutButtonState();
        });
    });

    window.getSelectedPaymentMethod = function () {
        const active = document.querySelector(
            '.payment-button.border-gray-500, .payment-button.border-blue-500, .payment-button.border-purple-500'
        );
        return active ? active.dataset.paymentMethod : null;
    };

    window.updateCheckoutButtonState = function () {
        const btn = document.getElementById('checkoutBtn');
        btn.disabled = !(cartItems.length > 0 && !!getSelectedPaymentMethod());
    };
});


function updateStockDisplay(productId, deltaQty) {

    const badge = document.querySelector(`.stock-badge[data-product-id="${productId}"]`);
    if (!badge) return;

    const initial = parseInt(badge.dataset.initialStock, 10) || 0;
    const prev    = stockAdjustments.get(productId) || 0;
    const next    = prev + deltaQty;
    stockAdjustments.set(productId, next);

    const displayed = Math.max(0, initial - next);
    badge.textContent = `Stock: ${displayed}`;

    badge.className = 'absolute top-1 right-1 text-xs font-semibold px-1.5 py-0.5 rounded stock-badge';
    if (displayed === 0)     badge.classList.add('bg-red-100',    'text-red-800');
    else if (displayed < 5)  badge.classList.add('bg-yellow-100', 'text-yellow-800');
    else                     badge.classList.add('bg-blue-100',   'text-blue-800');

    return displayed;
}

function getDisplayedStock(productId) {
    const badge = document.querySelector(`.stock-badge[data-product-id="${productId}"]`);
    if (!badge) return 0;
    const initial = parseInt(badge.dataset.initialStock, 10) || 0;
    return Math.max(0, initial - (stockAdjustments.get(productId) || 0));
}


function showConfirmModal(title, message, onConfirm) {
    const modal  = document.getElementById('confirmModal');
    document.getElementById('confirmTitle').textContent  = title;
    document.getElementById('confirmMessage').innerHTML  = message;
    modal.classList.replace('hidden', 'flex');

    document.getElementById('cancelConfirm').onclick = () => modal.classList.replace('flex', 'hidden');
    document.getElementById('okConfirm').onclick = () => {
        modal.classList.replace('flex', 'hidden');
        if (typeof onConfirm === 'function') onConfirm();
    };
}


let isAddingToCart = false;

function addToCart(productId) {
    if (isAddingToCart) return;

    if (getDisplayedStock(productId) <= 0) {
        showToast('error', 'Product is out of stock');
        return;
    }

    const btn = document.querySelector(`button[data-product-id="${productId}"]`);
    if (btn) btn.disabled = true;
    isAddingToCart = true;

    updateStockDisplay(productId, 1); // optimistic

    fetch('/pos/add-to-cart', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            loadCart(); // full sync only on add (new item row needed)
        } else {
            updateStockDisplay(productId, -1); // revert
            showToast('error', data.message || 'Failed to add to cart');
        }
    })
    .catch(() => {
        updateStockDisplay(productId, -1);
        showToast('error', 'An error occurred');
    })
    .finally(() => {
        isAddingToCart = false;
        if (btn) btn.disabled = false;
    });
}


function loadCart() {
    fetch('/pos/get-cart')
        .then(r => { if (!r.ok) throw new Error('Network error'); return r.json(); })
        .then(data => {
            if (data.success) {
                cartItems = data.items;
                // Re-sync stock adjustments from authoritative server data
                stockAdjustments.clear();
                data.items.forEach(item => {
                    stockAdjustments.set(item.product_id,
                        (stockAdjustments.get(item.product_id) || 0) + item.quantity);
                });
                // Re-render badges from new adjustments
                data.items.forEach(item => {
                    const badge = document.querySelector(`.stock-badge[data-product-id="${item.product_id}"]`);
                    if (badge) {
                        const init = parseInt(badge.dataset.initialStock, 10) || 0;
                        const shown = Math.max(0, init - (stockAdjustments.get(item.product_id) || 0));
                        badge.textContent = `Stock: ${shown}`;
                        badge.className = 'absolute top-1 right-1 text-xs font-semibold px-1.5 py-0.5 rounded stock-badge';
                        if (shown === 0)    badge.classList.add('bg-red-100',    'text-red-800');
                        else if (shown < 5) badge.classList.add('bg-yellow-100', 'text-yellow-800');
                        else                badge.classList.add('bg-blue-100',   'text-blue-800');
                    }
                });
                renderCart(data.items, data.total);
            }
        })
        .catch(err => console.error('loadCart error:', err));
}


function renderCart(items, total) {
    const container   = document.getElementById('cart-items');
    const cartTotal   = document.getElementById('cart-total');
    const cartCount   = document.getElementById('cart-count');
    const checkoutBtn = document.getElementById('checkoutBtn');

    cartCount.textContent = items.length;

    if (items.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6H19M7 13l-1.5 6m0 0H19m-11 0a1 1 0 11-2 0 1 1 0 012 0zm12 0a1 1 0 11-2 0 1 1 0 012 0z" />
                </svg>
                <p class="text-sm">Cart is empty</p>
            </div>`;
        cartTotal.textContent = '₱0.00';
        checkoutBtn.disabled  = true;
    } else {
        container.innerHTML = items.map(item => `
            <div class="bg-gray-50 rounded-lg p-3 flex items-center gap-3 hover:bg-gray-100 transition shadow-sm hover:shadow-md border border-gray-100"
                 data-cart-item-id="${item.cart_item_id}"
                 data-product-id="${item.product_id}">
                <div class="w-12 h-12 bg-gray-100 rounded flex-shrink-0 overflow-hidden shadow-sm">
                    ${item.image
                        ? `<img src="/storage/${item.image}" alt="${item.name}" class="w-full h-full object-cover">`
                        : `<svg class="w-6 h-6 text-gray-400 m-auto mt-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                   d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                           </svg>`}
                </div>
                <div class="flex-1 min-w-0">
                    <h4 class="font-semibold text-sm text-gray-900 truncate" title="${item.name}">${item.name}</h4>
                    <p class="text-xs text-gray-600">Qty: <span class="item-quantity">${item.quantity}</span></p>
                    <p class="text-sm font-semibold text-green-700 item-subtotal">₱${parseFloat(item.subtotal).toFixed(2)}</p>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="decreaseQuantity(${item.cart_item_id}, ${item.product_id})"
                            class="w-7 h-7 bg-white border border-gray-300 rounded hover:bg-gray-100 flex items-center justify-center transition shadow-sm">
                        <svg class="w-4 h-4 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </button>
                    <input type="number"
                           value="${item.quantity}"
                           onchange="handleQuantityInput(${item.cart_item_id}, ${item.product_id}, this.value, ${item.quantity})"
                           class="w-14 text-center border text-gray-800 border-gray-300 rounded px-1 py-1 text-sm focus:ring-2 focus:ring-gray-500 focus:outline-none shadow-sm item-quantity-input"
                           min="1">
                    <button onclick="increaseQuantity(${item.cart_item_id}, ${item.product_id})"
                            class="w-7 h-7 bg-white border border-gray-300 rounded hover:bg-gray-100 flex items-center justify-center transition shadow-sm">
                        <svg class="w-4 h-4 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>
                <button onclick="removeFromCart(${item.cart_item_id}, ${item.product_id})"
                        class="text-red-500 hover:text-red-700 ml-1 transition p-1 rounded hover:bg-red-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>`).join('');

        cartTotal.textContent = `₱${parseFloat(total).toFixed(2)}`;
    }

    refreshTotalsDisplay();
    updateCheckoutButtonState();
}


function increaseQuantity(cartItemId, productId) {
    const item = cartItems.find(i => i.cart_item_id === cartItemId);
    if (!item) return;
    if (getDisplayedStock(productId) < 1) {
        showToast('error', 'Not enough stock available');
        return;
    }
    applyQuantityChange(cartItemId, productId, item.quantity + 1); // item.quantity is always live
}

function decreaseQuantity(cartItemId, productId) {
    const item = cartItems.find(i => i.cart_item_id === cartItemId);
    if (!item) return;
    const currentQty = item.quantity; // always read from live in-memory state
    if (currentQty <= 1) {
        removeFromCart(cartItemId, productId, currentQty);
        return;
    }
    applyQuantityChange(cartItemId, productId, currentQty - 1);
}

function handleQuantityInput(cartItemId, productId, rawValue, oldQty) {
    const newQty = parseInt(rawValue, 10);
    const input  = document.querySelector(`[data-cart-item-id="${cartItemId}"] .item-quantity-input`);

    if (isNaN(newQty) || newQty < 1) {
        showToast('error', 'Invalid quantity');
        if (input) input.value = oldQty;
        return;
    }
    if (newQty === oldQty) return;

    const delta = newQty - oldQty;
    if (delta > 0 && getDisplayedStock(productId) < delta) {
        showToast('error', `Only ${getDisplayedStock(productId)} in stock`);
        if (input) input.value = oldQty;
        return;
    }

    applyQuantityChange(cartItemId, productId, newQty);
}


function applyQuantityChange(cartItemId, productId, newQty) {
    const item = cartItems.find(i => i.cart_item_id === cartItemId);
    if (!item) return;

    const oldQty   = item.quantity;
    const delta    = newQty - oldQty;               // +ve = more items taken
    const unitPrice = item.subtotal / oldQty;

    // ── 1. Mutate in-memory cart immediately ──────────────────────────────
    item.quantity = newQty;
    item.subtotal = unitPrice * newQty;

    // ── 2. Patch DOM for this row (no re-render) ──────────────────────────
    const row = document.querySelector(`[data-cart-item-id="${cartItemId}"]`);
    if (row) {
        const qtySpan    = row.querySelector('.item-quantity');
        const qtyInput   = row.querySelector('.item-quantity-input');
        const subtotalEl = row.querySelector('.item-subtotal');
        if (qtySpan)    qtySpan.textContent   = newQty;
        if (qtyInput)   qtyInput.value        = newQty;
        if (subtotalEl) subtotalEl.textContent = `₱${item.subtotal.toFixed(2)}`;
    }

    // ── 3. Update stock badge ─────────────────────────────────────────────
    updateStockDisplay(productId, delta);

    // ── 4. Refresh footer totals ──────────────────────────────────────────
    refreshTotalsDisplay();

    // ── 5. Debounce server call (200 ms) ──────────────────────────────────
    if (pendingUpdates.has(cartItemId)) clearTimeout(pendingUpdates.get(cartItemId));

    const tid = setTimeout(() => {
        pendingUpdates.delete(cartItemId);
        syncQtyWithServer(cartItemId, productId, newQty, oldQty, delta, unitPrice);
    }, 200);

    pendingUpdates.set(cartItemId, tid);
}


function syncQtyWithServer(cartItemId, productId, newQty, oldQty, delta, unitPrice) {
    fetch('/pos/update-cart-item', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ cart_item_id: cartItemId, quantity: newQty })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Only do a hard sync if server total diverges (e.g. concurrent change)
            const localTotal = cartItems.reduce((s, i) => s + i.subtotal, 0);
            if (Math.abs(data.total - localTotal) > 0.01) {
                loadCart();
            }
            // Otherwise: nothing — the DOM is already correct
        } else {
            revertQuantityChange(cartItemId, productId, oldQty, delta, unitPrice);
            showToast('error', data.message || 'Failed to update quantity');
        }
    })
    .catch(() => {
        revertQuantityChange(cartItemId, productId, oldQty, delta, unitPrice);
        showToast('error', 'Network error — quantity reverted');
    });
}


function revertQuantityChange(cartItemId, productId, oldQty, delta, unitPrice) {
    const item = cartItems.find(i => i.cart_item_id === cartItemId);
    if (!item) return;

    item.quantity = oldQty;
    item.subtotal = unitPrice * oldQty;

    const row = document.querySelector(`[data-cart-item-id="${cartItemId}"]`);
    if (row) {
        const qtySpan    = row.querySelector('.item-quantity');
        const qtyInput   = row.querySelector('.item-quantity-input');
        const subtotalEl = row.querySelector('.item-subtotal');
        if (qtySpan)    qtySpan.textContent   = oldQty;
        if (qtyInput)   qtyInput.value        = oldQty;
        if (subtotalEl) subtotalEl.textContent = `₱${item.subtotal.toFixed(2)}`;
    }

    updateStockDisplay(productId, -delta); // undo the badge change
    refreshTotalsDisplay();
}


function refreshTotalsDisplay() {
    const total     = cartItems.reduce((s, i) => s + i.subtotal, 0);
    const totalQty  = cartItems.reduce((s, i) => s + i.quantity, 0);
    const itemCount = cartItems.length;

    const cartTotalEl     = document.getElementById('cart-total');
    const itemsCountEl    = document.getElementById('items-count');
    const totalItemsEl    = document.getElementById('total-items-count');

    if (cartTotalEl)  cartTotalEl.textContent  = `₱${total.toFixed(2)}`;
    if (itemsCountEl) itemsCountEl.textContent = `${itemCount} ${itemCount === 1 ? 'item' : 'items'}`;
    if (totalItemsEl) totalItemsEl.textContent = totalQty;
}

function removeFromCart(cartItemId, productId) {
    const item = cartItems.find(i => i.cart_item_id === cartItemId);
    if (!item) { showToast('error', 'Item not found'); return; }
    const quantity = item.quantity; // always live

    // Fade row optimistically
    const row = document.querySelector(`[data-cart-item-id="${cartItemId}"]`);
    if (row) row.style.opacity = '0.4';

    updateStockDisplay(productId, -quantity); // put back on shelf display

    fetch('/pos/remove-cart-item', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ cart_item_id: cartItemId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Remove from in-memory array and reload render
            cartItems = cartItems.filter(i => i.cart_item_id !== cartItemId);
            const newTotal = cartItems.reduce((s, i) => s + i.subtotal, 0);
            renderCart(cartItems, newTotal);
        } else {
            if (row) row.style.opacity = '1';
            updateStockDisplay(productId, quantity); // revert badge
            showToast('error', data.message || 'Failed to remove item');
        }
    })
    .catch(() => {
        if (row) row.style.opacity = '1';
        updateStockDisplay(productId, quantity);
        showToast('error', 'An error occurred');
    });
}


function clearCart() {
    if (cartItems.length === 0) { showToast('error', 'Cart is already empty'); return; }

    showConfirmModal(
        'Clear Cart',
        'Are you sure you want to clear all items from the cart?',
        () => {
            const snapshot = [...cartItems];
            snapshot.forEach(item => updateStockDisplay(item.product_id, -item.quantity));

            Promise.all(snapshot.map(item =>
                fetch('/pos/remove-cart-item', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ cart_item_id: item.cart_item_id })
                })
            ))
            .then(() => {
                cartItems = [];
                renderCart([], 0);
                showToast('success', 'Cart cleared');
            })
            .catch(() => {
                showToast('error', 'Failed to clear cart');
                loadCart(); // fall back to hard sync
            });
        }
    );
}


function checkout() {
    if (cartItems.length === 0) { showToast('error', 'Cart is empty'); return; }

    const method = getSelectedPaymentMethod();
    if (!method) { showToast('error', 'Please select a payment method'); return; }

    if (method === 'gcash')     showGcashModal();
    else if (method === 'cash') showCashModal();
    else {
        showConfirmModal(
            'Confirm Checkout',
            `Proceed with ${method === 'credit_card' ? 'Credit/Debit Card' : method}?`,
            () => processCheckout(method)
        );
    }
}


function showGcashModal() {
    const modal  = document.getElementById('gcashModal');
    const input  = document.getElementById('gcashReferenceCode');
    const cancel = document.getElementById('cancelGcash');
    const confirm = document.getElementById('confirmGcash');

    input.value = '';
    modal.classList.replace('hidden', 'flex');

    cancel.onclick = () => modal.classList.replace('flex', 'hidden');

    confirm.onclick = () => {
        const code = input.value.trim();
        if (!code)          { showToast('error', 'Please enter GCash reference code'); return; }
        if (code.length < 6){ showToast('error', 'Reference code must be at least 6 characters'); return; }

        modal.classList.replace('flex', 'hidden');
        showConfirmModal(
            'Confirm GCash Payment',
            `Proceed with GCash payment?<br><strong>Reference Code: ${code}</strong>`,
            () => processCheckout('gcash', code)
        );
    };

    input.onkeypress = e => { if (e.key === 'Enter') confirm.click(); };
    setTimeout(() => input.focus(), 100);
}


function showCashModal() {
    const modal      = document.getElementById('cashModal');
    const cashInput  = document.getElementById('cashAmount');
    const totalLabel = document.getElementById('cashTotalAmount');
    const changeBox  = document.getElementById('changeSection');
    const warnBox    = document.getElementById('insufficientWarning');
    const cancelBtn  = document.getElementById('cancelCash');
    const confirmBtn = document.getElementById('confirmCash');

    const totalDue = cartItems.reduce((s, i) => s + i.subtotal, 0);

    totalLabel.textContent = `₱${totalDue.toFixed(2)}`;
    cashInput.value        = '';
    cashInput.min          = totalDue.toFixed(2);
    changeBox.classList.add('hidden');
    warnBox.classList.add('hidden');
    confirmBtn.disabled = true;

    modal.classList.replace('hidden', 'flex');

    cancelBtn.onclick = () => modal.classList.replace('flex', 'hidden');

    confirmBtn.onclick = () => {
        const paid   = parseFloat(cashInput.value);
        const change = paid - totalDue;
        if (!paid || paid < totalDue) { showToast('error', 'Insufficient payment'); return; }

        modal.classList.replace('flex', 'hidden');
        showConfirmModal(
            'Confirm Cash Payment',
            `<strong>Total: ₱${totalDue.toFixed(2)}</strong><br>Paid: ₱${paid.toFixed(2)}<br>Change: ₱${change.toFixed(2)}`,
            () => processCheckout('cash', null, paid, change)
        );
    };

    setTimeout(() => cashInput.focus(), 100);
}

function calculateChange() {
    const paid      = parseFloat(document.getElementById('cashAmount').value) || 0;
    const totalDue  = parseFloat(document.getElementById('cashTotalAmount').textContent.replace('₱','')) || 0;
    const changeBox = document.getElementById('changeSection');
    const warnBox   = document.getElementById('insufficientWarning');
    const confirmBtn = document.getElementById('confirmCash');

    if (paid >= totalDue && paid > 0) {
        document.getElementById('changeAmount').textContent = `₱${(paid - totalDue).toFixed(2)}`;
        changeBox.classList.remove('hidden');
        warnBox.classList.add('hidden');
        confirmBtn.disabled = false;
    } else if (paid > 0) {
        document.getElementById('remainingAmount').textContent = `₱${(totalDue - paid).toFixed(2)}`;
        changeBox.classList.add('hidden');
        warnBox.classList.remove('hidden');
        confirmBtn.disabled = true;
    } else {
        changeBox.classList.add('hidden');
        warnBox.classList.add('hidden');
        confirmBtn.disabled = true;
    }
}


function processCheckout(method, referenceCode = null, cashAmount = null, change = null) {
    const btn = document.getElementById('checkoutBtn');
    btn.disabled   = true;
    btn.innerHTML  = '<span class="animate-pulse">Processing…</span>';

    const payload = { payment_method: method };
    if (method === 'gcash' && referenceCode) payload.reference_code = referenceCode;
    if (method === 'cash'  && cashAmount !== null) {
        payload.cash_amount = cashAmount;
        payload.change      = change;
    }

    fetch('/pos/checkout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const msg = method === 'gcash'
                ? `GCash payment completed! Ref: ${referenceCode}`
                : method === 'cash'
                ? `Cash payment completed! Change: ₱${change.toFixed(2)}`
                : 'Checkout completed!';
            showToast('success', msg);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('error', data.message || 'Checkout failed');
            btn.disabled  = false;
            btn.textContent = 'Checkout';
        }
    })
    .catch(() => {
        showToast('error', 'An error occurred during checkout');
        btn.disabled    = false;
        btn.textContent = 'Checkout';
    });
}

function showToast(type, message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: type, title: message, toast: true, position: 'top-end',
                    showConfirmButton: false, timer: 3000, timerProgressBar: true });
    } else {
        alert(message);
    }
}


let searchTimeout;
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => document.getElementById('searchForm').submit(), 500);
    });
}
    </script>

@endsection