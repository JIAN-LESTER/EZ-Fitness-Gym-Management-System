@extends('layouts.app')
@section('title', 'POS')
@section('header', 'Point of Sale')

@section('content')

    <div class="w-full h-full flex gap-4">

        {{-- LEFT SIDE – ITEMS / PRODUCTS --}}
        <div class="w-2/3 bg-white shadow rounded-lg p-4 flex flex-col" style="max-height: calc(100vh - 150px);">

            <h2 class="text-xl font-semibold mb-4 text-gray-800">Products</h2>

            {{-- Search --}}
            <form method="GET" id="searchForm" class="mb-4">
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                    placeholder="Search product by name or description..."
                    class="w-full px-3 py-2 border border-gray-200 text-gray-800 rounded-lg focus:ring-2 focus:ring-green-600 focus:outline-none">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
            </form>


            <div class="mb-4 overflow-x-auto">
                <div class="flex gap-3 pb-2">

                    {{-- ALL PRODUCTS TAB --}}
                    <a href="{{ route('pos.index', ['search' => request('search')]) }}" class="px-5 py-2 rounded border 
                                                                                          transition-all duration-200 text-sm font-medium
                                                                                          {{ !request('category')
        ? 'bg-green-600 text-white '
        : 'bg-white text-gray-800 hover:bg-green-50 border-gray-300' }}">
                        All Products
                    </a>


                    @foreach($categories as $category)
                                <a href="{{ route('pos.index', ['category' => $category->category_id, 'search' => request('search')]) }}"
                                    class="px-5 py-2 rounded border text-sm font-medium
                                                                                                                                                                                                                                                                                                                      transition-all duration-200 whitespace-nowrap
                                                                                                                                                                                                                                                                                                                      {{ request('category') == $category->category_id
                        ? 'bg-green-600 text-white'
                        : 'bg-white text-gray-800 hover:bg-green-50 border-gray-300' }}">
                                    {{ $category->name }}
                                </a>
                    @endforeach

                </div>
            </div>


            <div class="flex-1 overflow-y-auto">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                    @forelse ($products as $item)
                        <div
                            class="border rounded-lg border-gray-200 p-3 hover:shadow-lg cursor-pointer transition flex flex-col justify-between relative bg-white">


                            <div class="h-24 bg-gray-100 rounded mb-3 flex items-center justify-center overflow-hidden">
                                @if($item->product && $item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                @endif
                            </div>


                            <span
                                class="absolute top-2 right-2 bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded">
                                Stock: {{ $item->quantity }}
                            </span>


                            <h3 class="font-bold text-sm text-gray-900 truncate mb-2" title="{{ $item->product->name }}">
                                {{ $item->product->name }}
                            </h3>


                            <p class="text-xs text-gray-500 line-clamp-2 mb-3" title="{{ $item->product->description }}">
                                {{ $item->product->description ?? 'No description' }}
                            </p>


                            <div class="mt-auto flex justify-between items-center">
                                <p class="font-bold text-green-700">₱{{ number_format($item->product->price, 2) }}</p>

                                <button onclick="addToCart({{ $item->product->product_id }})"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg flex items-center gap-1 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                    title="Add to Cart" data-product-id="{{ $item->product->product_id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add
                                </button>
                            </div>

                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-gray-500 text-lg font-medium">No products available</p>
                            <p class="text-gray-400 text-sm mt-1">Try adjusting your search or filter</p>
                        </div>
                    @endforelse
                </div>


                @if($products->hasPages())
                    <div class="mt-4 pb-2">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- CART SECTION --}}
        <div class="w-1/3 bg-white shadow rounded-lg p-4 flex flex-col" style="max-height: calc(100vh - 150px);">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Cart</h2>
                <span id="cart-count" class="bg-green-600 text-white text-xs font-semibold px-2 py-1 rounded-full">0</span>
            </div>

            <div id="cart-items" class="flex-1 overflow-y-auto border-b pb-3 space-y-2">
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6H19M7 13l-1.5 6m0 0H19m-11 0a1 1 0 11-2 0 1 1 0 012 0zm12 0a1 1 0 11-2 0 1 1 0 012 0z" />
                    </svg>
                    <p class="text-sm">Cart is empty</p>
                </div>
            </div>

            {{-- Payment Method Selection --}}
            <div class="mt-4 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Payment Method</h3>
                <div class="flex flex-row gap-3">
                    <!-- Cash Payment Option -->
                    <button type="button" data-payment-method="cash"
                        class="payment-button flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:border-green-400 hover:bg-green-50 flex-1 h-full">
                        <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <span class="font-medium text-gray-800">Cash</span>
                            <p class="text-xs text-gray-500 mt-1">Pay with cash</p>
                        </div>
                    </button>

                    <!-- Credit/Debit Card Payment Option -->
                    <button type="button" data-payment-method="credit_card"
                        class="payment-button flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:border-blue-400 hover:bg-blue-50 flex-1 h-full">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <span class="font-medium text-gray-800">Card</span>
                            <p class="text-xs text-gray-500 mt-1">Credit/Debit</p>
                        </div>
                    </button>

                    <!-- QR Code Payment Option -->
                    <button type="button" data-payment-method="qr"
                        class="payment-button flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer transition-all duration-200 hover:border-purple-400 hover:bg-purple-50 flex-1 h-full">
                        <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-full mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <span class="font-medium text-gray-800">QR Code</span>
                            <p class="text-xs text-gray-500 mt-1">Scan to pay</p>
                        </div>
                    </button>
                </div>
            </div>


            {{-- Discount Section --}}
            <div class="mt-4 border-t pt-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Discount</h3>
                <div class="flex gap-2">
                    <input type="number" id="discount-input" placeholder="0" min="0" max="100"
                        class="w-20 px-3 py-2 text-black border border-gray-300 rounded-lg text-center focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <span class="flex items-center text-gray-600">%</span>
                    <button onclick="applyDiscount()"
                        class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition font-medium">
                        Apply
                    </button>
                    <button onclick="clearDiscount()"
                        class="px-3 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition">
                        Clear
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-2">Enter discount percentage (0-100%)</p>
            </div>

            <div class="mt-4 space-y-3">
                <div class="mt-6 border-t border-gray-200 pt-4 space-y-3">
                    <!-- Subtotal -->
                    <div class="flex justify-between items-center">
                        <p class="text-sm font-medium text-gray-600">Subtotal</p>
                        <p class="text-sm font-semibold text-gray-800" id="subtotal-amount">₱0.00</p>
                    </div>

                    <!-- Discount Row (Updated) -->
                    <div class="flex justify-between items-center" id="discount-row" style="display: none;">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-medium text-gray-600">Discount</p>
                            <span id="discount-percentage" class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded"></span>
                        </div>
                        <p class="text-sm font-semibold text-red-600" id="discount-amount">-₱0.00</p>
                    </div>

                    <!-- VAT -->
                    <div class="flex justify-between items-center">
                        <p class="text-sm font-medium text-gray-600">VAT 12%</p>
                        <p class="text-sm font-semibold text-gray-800" id="vat-amount">₱0.00</p>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-300 my-2"></div>

                    <!-- Total Amount -->
                    <div
                        class="flex justify-between items-center bg-linear-to-r from-green-50 to-emerald-50 rounded-lg p-3 -mx-2">
                        <div>
                            <p class="text-lg font-bold text-gray-800">Total Amount</p>
                            <p class="text-xs text-gray-500 mt-1">Inclusive of VAT & Discount</p>
                        </div>
                        <p id="cart-total" class="text-2xl font-bold text-green-700">₱0.00</p>
                    </div>

                    <!-- Optional: Items Count -->
                    <div class="flex justify-between items-center text-xs text-gray-500 pt-1">
                        <span>Items in cart</span>
                        <span id="items-count">0 items</span>
                    </div>
                </div>

                <button id="checkoutBtn" onclick="checkout()"
                    class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition font-semibold disabled:bg-gray-300 disabled:cursor-not-allowed"
                    disabled>
                    Checkout
                </button>

                <button onclick="clearCart()"
                    class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition text-sm">
                    Clear Cart
                </button>
            </div>
        </div>



    </div>


    <div id="confirmModal" class="fixed inset-0 bg-opacity-40 hidden backdrop-blur items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-96 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-2" id="confirmTitle">Confirm Action</h2>
            <p class="text-gray-600 mb-6" id="confirmMessage">Are you sure?</p>

            <div class="flex justify-end gap-3">
                <button id="cancelConfirm"
                    class="px-4 py-2 rounded-lg border text-gray-800 border-gray-300 hover:bg-gray-100 transition">
                    Cancel
                </button>

                <button id="okConfirm" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Confirm
                </button>
            </div>
        </div>
    </div>


    <script>
        let cartItems = [];
        let isAddingToCart = false;
        let stockData = {};
        let currentDiscount = 0;


        // Add payment method functionality
        document.addEventListener('DOMContentLoaded', function () {
            const paymentButtons = document.querySelectorAll('.payment-button');
            let selectedPaymentMethod = 'cash'; // Set cash as default

            // Set cash as default selected on page load
            const cashButton = document.querySelector('[data-payment-method="cash"]');
            if (cashButton) {
                cashButton.classList.add('border-green-500', 'bg-green-50');
            }

            paymentButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const paymentMethod = this.getAttribute('data-payment-method');

                    // Remove active styles from all payment buttons
                    paymentButtons.forEach(btn => {
                        btn.classList.remove(
                            'border-green-500', 'border-blue-500', 'border-purple-500',
                            'bg-green-50', 'bg-blue-50', 'bg-purple-50'
                        );
                    });

                    // Add active style to selected button
                    if (paymentMethod === 'cash') {
                        this.classList.add('border-green-500', 'bg-green-50');
                    } else if (paymentMethod === 'credit_card') {
                        this.classList.add('border-blue-500', 'bg-blue-50');
                    } else if (paymentMethod === 'qr') {
                        this.classList.add('border-purple-500', 'bg-purple-50');
                    }

                    // Store the selected payment method
                    selectedPaymentMethod = paymentMethod;

                    // Update checkout button state if cart has items
                    updateCheckoutButtonState();
                });
            });

            // Function to get the selected payment method from buttons
            function getSelectedPaymentMethod() {
                const activeButton = document.querySelector('.payment-button.border-green-500, .payment-button.border-blue-500, .payment-button.border-purple-500');
                return activeButton ? activeButton.getAttribute('data-payment-method') : null;
            }

            // Function to update checkout button state based on cart items and payment method
            function updateCheckoutButtonState() {
                const checkoutBtn = document.getElementById('checkoutBtn');
                const hasItems = cartItems && cartItems.length > 0;
                const hasPaymentMethod = !!getSelectedPaymentMethod();

                checkoutBtn.disabled = !(hasItems && hasPaymentMethod);
            }

            // Make functions available globally
            window.getSelectedPaymentMethod = getSelectedPaymentMethod;
            window.updateCheckoutButtonState = updateCheckoutButtonState;
        });

        function updateStockDisplay(productId, availableStock) {
            const stockBadge = document.querySelector(`[data-product-id="${productId}"]`)?.closest('.border')?.querySelector('.absolute.top-2.right-2');
            if (stockBadge) {
                const totalStock = availableStock + (stockData[productId]?.in_cart || 0);
                stockBadge.textContent = `Stock: ${availableStock}`;


                if (availableStock === 0) {
                    stockBadge.className = 'absolute top-2 right-2 bg-red-100 text-red-800 text-xs font-semibold px-2 py-1 rounded';
                } else if (availableStock < 5) {
                    stockBadge.className = 'absolute top-2 right-2 bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded';
                } else {
                    stockBadge.className = 'absolute top-2 right-2 bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded';
                }
            }
        }

        function showConfirmModal(title, message, onConfirm) {
            const modal = document.getElementById('confirmModal');
            const titleEl = document.getElementById('confirmTitle');
            const messageEl = document.getElementById('confirmMessage');
            const cancelBtn = document.getElementById('cancelConfirm');
            const okBtn = document.getElementById('okConfirm');

            titleEl.textContent = title;
            messageEl.textContent = message;

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            cancelBtn.onclick = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };

            okBtn.onclick = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                if (typeof onConfirm === "function") onConfirm();
            };
        }


        function addToCart(productId) {
            if (isAddingToCart) {
                return;
            }

            const button = document.querySelector(`button[data-product-id="${productId}"]`);
            if (button) {
                button.disabled = true;
                const originalText = button.innerHTML;
                button.innerHTML = '<span class="animate-pulse">Adding...</span>';
            }

            isAddingToCart = true;

            fetch('{{ route("pos.addToCart") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId })
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Update stock display immediately
                        if (data.available_stock !== undefined) {
                            updateStockDisplay(data.product_id, data.available_stock);
                            if (!stockData[data.product_id]) {
                                stockData[data.product_id] = {};
                            }
                            stockData[data.product_id].in_cart = data.quantity_in_cart;
                        }

                        loadCart();
                        updateCheckoutButtonState();
                    } else {

                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', error.message || 'An error occurred');
                })
                .finally(() => {
                    isAddingToCart = false;
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = `
                                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                                                        </svg>
                                                                                        Add
                                                                                    `;
                    }
                });
        }


        function loadCart() {
            fetch('{{ route("pos.getCart") }}')
                .then(response => {
                    if (!response.ok) throw new Error('Failed to load cart');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        cartItems = data.items;
                        stockData = data.stock_data || {};

                        // Update all stock displays based on cart data
                        Object.keys(stockData).forEach(productId => {
                            updateStockDisplay(productId, stockData[productId].available_stock);
                        });

                        renderCart(data.items, data.total);
                    }
                })
                .catch(error => {
                    console.error('Error loading cart:', error);
                });
        }

        // Render Cart
        function renderCart(items, total) {
            const cartContainer = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');
            const cartCount = document.getElementById('cart-count');
            const checkoutBtn = document.getElementById('checkoutBtn');

            // Update cart count
            cartCount.textContent = items.length;

            if (items.length === 0) {
                cartContainer.innerHTML = `
                                                                <div class="text-center py-8 text-gray-500">
                                                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6H19M7 13l-1.5 6m0 0H19m-11 0a1 1 0 11-2 0 1 1 0 012 0zm12 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                                                    </svg>
                                                                    <p class="text-sm">Cart is empty</p>
                                                                </div>
                                                            `;
                cartTotal.textContent = '₱0.00';
                checkoutBtn.disabled = true;
                updateCartTotals();

            } else {
                cartContainer.innerHTML = items.map(item => `
                                                                <div class="bg-gray-50 rounded-lg p-3 flex items-center gap-3 hover:bg-gray-50 transition shadow-sm hover:shadow-md border border-gray-100">
                                                                    <div class="w-12 h-12 bg-gray-100 rounded flex-shrink-0 overflow-hidden shadow-sm">
                                                                        ${item.image ?
                        `<img src="/storage/${item.image}" alt="${item.name}" class="w-full h-full object-cover">` :
                        `<svg class="w-6 h-6 text-gray-400 m-auto mt-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                            </svg>`
                    }
                                                                    </div>
                                                                    <div class="flex-1 min-w-0">
                                                                        <h4 class="font-semibold text-sm text-gray-900 truncate" title="${item.name}">${item.name}</h4>
                                                                        <p class="text-xs text-gray-600">${item.quantity}</p>
                                                                        <p class="text-sm font-semibold text-green-700">₱${parseFloat(item.subtotal).toFixed(2)}</p>
                                                                    </div>
                                                                    <div class="flex items-center gap-1">
                                                                        <button onclick="updateQuantity(${item.cart_item_id}, ${item.product_id}, ${item.quantity - 1})" 
                                                                                class="w-7 h-7 bg-white border border-gray-300 rounded hover:bg-gray-100 flex items-center justify-center transition shadow-sm hover:shadow"
                                                                                ${item.quantity <= 1 ? 'disabled' : ''}
                                                                                title="Decrease quantity">
                                                                            <svg class="w-3 h-3 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                                            </svg>
                                                                        </button>
                                                                        <input type="number" 
                                                                               value="${item.quantity}" 
                                                                               onchange="updateQuantity(${item.cart_item_id}, ${item.product_id}, this.value)"
                                                                               class="w-14 text-center border text-gray-800 border-gray-300 rounded px-1 py-1 text-sm focus:ring-2 focus:ring-green-500 focus:outline-none shadow-sm"
                                                                               min="1"
                                                                               title="Quantity">
                                                                        <button onclick="updateQuantity(${item.cart_item_id}, ${item.product_id}, ${item.quantity + 1})" 
                                                                                class="w-7 h-7 bg-white border border-gray-300 rounded hover:bg-gray-100 flex items-center justify-center transition shadow-sm hover:shadow"
                                                                                title="Increase quantity">
                                                                            <svg class="w-3 h-3 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                    <button onclick="removeFromCart(${item.cart_item_id}, ${item.product_id})" 
                                                                            class="text-red-500 hover:text-red-700 ml-1 transition p-1 rounded hover:bg-red-50"
                                                                            title="Remove item">
                                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            `).join('');
                cartTotal.textContent = `₱${parseFloat(total).toFixed(2)}`;

                // Update totals with current discount
                updateCartTotals();
                // Use the new function instead of directly setting disabled
                updateCheckoutButtonState();
            }
        }

        // Update Quantity
        function updateQuantity(cartItemId, productId, newQuantity) {
            newQuantity = parseInt(newQuantity);

            if (isNaN(newQuantity) || newQuantity < 1) {
                showToast('error', 'Invalid quantity');
                loadCart(); // Reset to valid value
                return;
            }

            fetch('{{ route("pos.updateCartItem") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    cart_item_id: cartItemId,
                    quantity: newQuantity
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update stock display immediately
                        if (data.available_stock !== undefined) {
                            updateStockDisplay(data.product_id, data.available_stock);
                            if (!stockData[data.product_id]) {
                                stockData[data.product_id] = {};
                            }
                            stockData[data.product_id].in_cart = data.quantity_in_cart;
                        }

                        loadCart();
                    } else {
                        showToast('error', data.message || 'Failed to update quantity');
                        loadCart();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'An error occurred');
                    loadCart();
                });
        }

        // Remove from Cart
        function removeFromCart(cartItemId, productId) {


            fetch('{{ route("pos.removeCartItem") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ cart_item_id: cartItemId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update stock display immediately
                        if (data.available_stock !== undefined && data.product_id) {
                            updateStockDisplay(data.product_id, data.available_stock);
                            if (stockData[data.product_id]) {
                                delete stockData[data.product_id];
                            }
                        }

                        loadCart();
                        updateCheckoutButtonState();
                    } else {
                        showToast('error', data.message || 'Failed to remove item');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'An error occurred');
                });
        }

        // Clear Cart
        function clearCart() {
            if (cartItems.length === 0) {
                showToast('error', 'Cart is already empty');
                return;
            }

            const itemsToRemove = cartItems.map(item => ({
                id: item.cart_item_id,
                product_id: item.product_id
            }));

            Promise.all(itemsToRemove.map(item =>
                fetch('{{ route("pos.removeCartItem") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ cart_item_id: item.id })
                })
            ))
                .then(() => {
                    // Clear stock data
                    stockData = {};
                    loadCart();
                    updateCheckoutButtonState();
                    showToast('success', 'Cart cleared');
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('error', 'Failed to clear cart');
                });
        }

        // Checkout
        function checkout() {
            if (cartItems.length === 0) {
                showToast('error', 'Cart is empty');
                return;
            }

            // Get selected payment method from the button interface
            const selectedPaymentMethod = getSelectedPaymentMethod();

            if (!selectedPaymentMethod) {
                return showToast('error', 'Please select a payment method');
            }

            const paymentMethodDisplay = selectedPaymentMethod === 'credit_card' ? 'Credit/Debit Card' :
                selectedPaymentMethod === 'qr' ? 'QR Code' : 'Cash';

            showConfirmModal(
                "Confirm Checkout",
                `Are you sure you want to proceed with checkout using ${paymentMethodDisplay}?`,
                () => {
                    processCheckout(selectedPaymentMethod); // pass payment method to checkout
                }
            );
        }

        function processCheckout(paymentMethod) {
            const checkoutBtn = document.getElementById('checkoutBtn');
            checkoutBtn.disabled = true;
            checkoutBtn.innerHTML = '<span class="animate-pulse">Processing...</span>';

            // Include the payment method in the request
            fetch('{{ route("pos.checkout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    payment_method: paymentMethod,  // Send the selected payment method
                    discount_percentage: currentDiscount
                })
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast('success', 'Checkout completed successfully!');
                        // Reset discount
                        currentDiscount = 0;
                        document.getElementById('discount-input').value = '';
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showToast('error', data.message || 'Checkout failed');
                        checkoutBtn.disabled = false;
                        checkoutBtn.textContent = 'Checkout';
                    }
                })
                .catch(() => {
                    showToast('error', 'An error occurred during checkout');
                    checkoutBtn.disabled = false;
                    checkoutBtn.textContent = 'Checkout';
                });
        }


        function showToast(type, message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: type,
                    title: message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            } else {
                alert(message);
            }
        }

        // Auto-submit search after typing stops (debounced)
        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('searchForm').submit();
                }, 500);
            });
        }

        function applyDiscount() {
            const discountInput = document.getElementById('discount-input');
            const discount = parseFloat(discountInput.value);

            if (isNaN(discount) || discount < 0 || discount > 100) {
                showToast('error', 'Please enter a valid discount between 0-100%');
                return;
            }

            currentDiscount = discount;
            updateCartTotals();
            showToast('success', `Discount of ${discount}% applied`);
        }

        function clearDiscount() {
            currentDiscount = 0;
            document.getElementById('discount-input').value = '';
            updateCartTotals();
            showToast('info', 'Discount cleared');
        }

        function updateCartTotals() {
            const subtotalAmount = document.getElementById('subtotal-amount');
            const discountAmount = document.getElementById('discount-amount');
            const discountPercentage = document.getElementById('discount-percentage');
            const discountRow = document.getElementById('discount-row');
            const vatAmount = document.getElementById('vat-amount');
            const cartTotal = document.getElementById('cart-total');

            // Calculate base amounts
            const subtotal = cartItems.reduce((sum, item) => sum + item.subtotal, 0);

            // Calculate discount
            const discountValue = (subtotal * currentDiscount) / 100;
            const subtotalAfterDiscount = subtotal - discountValue;

            // Calculate VAT (12% on discounted amount)
            const vat = subtotalAfterDiscount * 0.12;
            const total = subtotalAfterDiscount + vat;

            // Update display
            subtotalAmount.textContent = `₱${subtotal.toFixed(2)}`;

            if (currentDiscount > 0) {
                discountAmount.textContent = `-₱${discountValue.toFixed(2)}`;
                discountPercentage.textContent = `${currentDiscount}% OFF`;
                discountRow.style.display = 'flex';
            } else {
                discountRow.style.display = 'none';
            }

            vatAmount.textContent = `₱${vat.toFixed(2)}`;
            cartTotal.textContent = `₱${total.toFixed(2)}`;
        }
    </script>

@endsection