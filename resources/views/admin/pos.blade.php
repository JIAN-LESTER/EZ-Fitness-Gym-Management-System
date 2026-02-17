@extends('layouts.app')
@section('title', 'Point of Sale | EZ Fitness')
@section('header', 'Point of Sale')

@section('content')

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        .tab-pill {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            font-weight: 600;
            transition: all .2s;
            cursor: pointer;
            user-select: none;
        }
        .tab-pill.active { background: #1f2937; color: #fff; }
        .tab-pill:not(.active) { background: transparent; color: #1f2937; }

        /* ── Mobile-only ── */
        @media (max-width: 1023px) {
            html, body { height: 100%; overflow: hidden; }

            #mobile-cart-sheet {
                position: fixed;
                bottom: 0;
                left: 60px;
                right: 0;
                z-index: 40;
                background: #fff;
                border-radius: 20px 20px 0 0;
                box-shadow: 0 -8px 30px rgba(0,0,0,.15);
                transition: transform .35s cubic-bezier(.4,0,.2,1);
                transform: translateY(calc(100% - 64px));
                max-height: 85vh;
                display: flex;
                flex-direction: column;
            }
            #mobile-cart-sheet.expanded { transform: translateY(0); }

            #cart-drag-handle {
                width: 40px; height: 5px;
                background: #d1d5db;
                border-radius: 9999px;
                margin: 10px auto 0;
                flex-shrink: 0;
            }

            #cart-sheet-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 8px 16px 10px;
                flex-shrink: 0;
                cursor: pointer;
            }

            #cart-sheet-body {
                flex: 1;
                overflow-y: auto;
                padding: 0 12px 16px;
                overscroll-behavior: contain;
            }

            #products-area {
                overflow-y: auto;
                padding-bottom: 80px;
            }

            .mobile-pos-wrapper {
                overflow-x: hidden;
                width: 100%;
            }
        }

        /* ── Product card base ── */
        .product-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
            transition: box-shadow .2s, transform .15s;
            cursor: pointer;
            overflow: hidden;
        }
        .product-card:active { transform: scale(.98); }

        #products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(145px, 165px));
            gap: 10px;
            align-content: start;
            justify-content: start;
        }

        /* ── Mobile product card: comfortable height, add btn inside ── */
        .mobile-product-card {
            display: flex;
            align-items: stretch;
            width: 100%;
            max-width: 440px;   /* prevents ugly stretch on large phones/tablets */
            min-height: 76px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            transition: box-shadow .2s, transform .15s;
        }
        .mobile-product-card:active { transform: scale(.98); }

        /* Thumbnail */
        .mpc-thumb {
            width: 68px;
            min-width: 68px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        /* Body */
        .mpc-body {
            flex: 1;
            min-width: 0;
            padding: 10px 10px 10px 12px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
        }
        .mpc-name {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.3;
        }
        .mpc-price {
            font-size: 12px;
            font-weight: 700;
            color: #15803d;
            line-height: 1;
        }
        /* Add button lives right below price, tightly grouped */
        .mpc-add-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 6px;
            padding: 4px 10px 4px 8px;
            background: #1f2937;
            color: #fff;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: background .15s;
            align-self: flex-start;
        }
        .mpc-add-btn:active { background: #374151; }

        /* Stock badge on right side of body */
        .mpc-stock {
            align-self: flex-start;
            flex-shrink: 0;
            margin-top: 0;
            padding: 2px 7px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 700;
            white-space: nowrap;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            min-width: 36px;
        }

        /* Right column: stock + add stacked */
        .mpc-right {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            padding: 10px 12px 10px 0;
            gap: 6px;
            flex-shrink: 0;
        }

        /* ── Category "see more" ── */
        #cat-extra-pills { display: none; }
        #cat-extra-pills.visible { display: contents; }

        .payment-button.selected-cash   { border-color: #374151; background: #f9fafb; }
        .payment-button.selected-gcash  { border-color: #7c3aed; background: #f5f3ff; }

        .qty-btn {
            width: 28px; height: 28px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: background .15s;
            flex-shrink: 0;
        }
        .qty-btn:active { background: #f3f4f6; }

        #searchInput { -webkit-appearance: none; border-radius: 10px; }

        .modal-backdrop {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.45);
            backdrop-filter: blur(2px);
            display: none;
            align-items: flex-end;
            justify-content: center;
            z-index: 50;
            padding: 0;
        }
        @media (max-width: 1023px) { .modal-backdrop { left: 60px; } }
        @media (min-width: 640px) { .modal-backdrop { align-items: center; padding: 16px; } }
        .modal-backdrop.open { display: flex; }

        .modal-card {
            background: #fff;
            border-radius: 20px 20px 0 0;
            width: 100%;
            padding: 24px 20px 32px;
            max-height: 90vh;
            overflow-y: auto;
        }
        @media (min-width: 640px) {
            .modal-card { border-radius: 16px; max-width: 420px; padding: 28px 24px; max-height: none; }
        }
    </style>

    {{-- ═══════════════════════════════════════════
         DESKTOP LAYOUT  (lg and above)
    ═══════════════════════════════════════════ --}}
    <div class="w-full h-full hidden lg:flex gap-3">

        <div class="flex-1 min-w-0 bg-white shadow rounded-lg p-3 flex flex-col" style="max-height: calc(100vh - 120px);">
            <h2 class="text-lg font-semibold mb-3 text-gray-800">Products</h2>

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
                    <a href="{{ route('pos.index', ['search' => request('search')]) }}" class="px-4 py-1.5 rounded border transition-all duration-200 text-xs font-medium {{ !request('category') ? 'bg-gray-800 text-white' : 'bg-white text-gray-800 hover:bg-gray-50 border-gray-300' }}">All</a>
                    @foreach($categories as $category)
                        <a href="{{ route('pos.index', ['category' => $category->category_id, 'search' => request('search')]) }}"
                            class="px-4 py-1.5 rounded border text-xs font-medium transition-all duration-200 whitespace-nowrap {{ request('category') == $category->category_id ? 'bg-gray-800 text-white' : 'bg-white text-gray-800 hover:bg-gray-50 border-gray-300' }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div class="grid grid-cols-3 lg:grid-cols-5 gap-3 mb-3">
                    @forelse ($products as $item)
                        <div class="border rounded-lg border-gray-200 p-2 hover:shadow-md cursor-pointer transition flex flex-col justify-between relative bg-white">
                            <div class="h-28 bg-white rounded mb-2 flex items-center justify-center overflow-hidden p-2">
                                @if($item->product && $item->product->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-contain">
                                @else
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                @endif
                            </div>
                            <span class="absolute top-1 right-1
                                @if($item->quantity == 0) bg-red-100 text-red-800
                                @elseif($item->quantity < 5) bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800 @endif
                                text-xs font-semibold px-1.5 py-0.5 rounded stock-badge"
                                data-product-id="{{ $item->product->product_id }}" data-initial-stock="{{ $item->quantity }}">
                                Stock: {{ $item->quantity }}
                            </span>
                            <h3 class="font-bold text-sm text-gray-900 truncate" title="{{ $item->product->name }}">{{ $item->product->name }}</h3>
                            <p class="text-xs text-gray-500 line-clamp-2 mb-3" title="{{ $item->product->description }}">{{ $item->product->description ?? 'No description' }}</p>
                            <div class="mt-auto flex justify-between items-center">
                                <p class="font-bold text-green-700 text-xs">₱{{ number_format($item->product->price, 2) }}</p>
                                <button onclick="addToCart({{ $item->product->product_id }})"
                                    class="bg-gray-800 hover:bg-gray-700 text-white px-2 py-1 rounded text-xs flex items-center gap-1 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                    title="Add to Cart" data-product-id="{{ $item->product->product_id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-gray-500 text-sm font-medium">No products available</p>
                            <p class="text-gray-400 text-xs mt-1">Try adjusting your search or filter</p>
                        </div>
                    @endforelse
                </div>
                @if($products->hasPages())
                    <div class="mt-3 pb-1">{{ $products->links() }}</div>
                @endif
            </div>
        </div>

        {{-- Cart panel --}}
        <div class="w-80 xl:w-96 flex-shrink-0 bg-white shadow rounded-xl p-4 flex flex-col" style="max-height: calc(100vh - 120px);">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-lg font-bold text-gray-800">Cart</h2>
                <span id="cart-count-desktop" class="bg-gray-800 text-white text-xs font-bold px-2.5 py-1 rounded-full">0</span>
            </div>
            <div id="cart-items-desktop" class="flex-1 overflow-y-auto space-y-2 border-b pb-3">
                <div class="text-center py-10 text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6H19M7 13l-1.5 6m0 0H19m-11 0a1 1 0 11-2 0 1 1 0 012 0zm12 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                    </svg>
                    <p class="text-sm">Cart is empty</p>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Payment Method</p>
                <div class="flex gap-2">
                    <button type="button" data-payment-method="cash"
                        class="payment-button flex-1 flex items-center justify-center gap-2 p-2.5 border-2 border-gray-200 rounded-xl transition-all text-sm font-semibold text-gray-700 hover:border-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Cash
                    </button>
                    <button type="button" data-payment-method="gcash"
                        class="payment-button flex-1 flex items-center justify-center gap-2 p-2.5 border-2 border-gray-200 rounded-xl transition-all text-sm font-semibold text-gray-700 hover:border-purple-400">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        GCash
                    </button>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t space-y-1">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500"><span id="items-count-desktop">0</span> items · <span id="total-items-count-desktop">0</span> qty</span>
                    <span id="cart-total-desktop" class="text-xl font-bold text-gray-800">₱0.00</span>
                </div>
            </div>
            <div class="mt-3 flex gap-2">
                <button id="checkoutBtnDesktop" onclick="checkout()"
                    class="flex-1 bg-gray-800 text-white py-2.5 rounded-xl font-semibold text-sm transition hover:bg-gray-700 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed"
                    disabled>Checkout</button>
                <button onclick="clearCart()"
                    class="flex-1 bg-red-50 text-red-600 border border-red-200 py-2.5 rounded-xl font-semibold text-sm transition hover:bg-red-100">Clear</button>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════
         MOBILE LAYOUT  (below lg)
    ═══════════════════════════════════════════ --}}
    <div class="lg:hidden mobile-pos-wrapper flex flex-col" style="height: calc(100vh - 56px); overflow: hidden; width: 100%; max-width: 100%; box-sizing: border-box;">

        {{-- Top bar: search + categories --}}
        <div class="bg-white px-3 pt-2 pb-0 shadow-sm z-10 flex-shrink-0" style="max-width: 100%; overflow: hidden;">
            <form method="GET" id="searchFormMobile" class="mb-2">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" id="searchInputMobile"
                        value="{{ request('search') }}"
                        placeholder="Search products…"
                        class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-800 focus:ring-2 focus:ring-gray-400 focus:outline-none bg-gray-50">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                </div>
            </form>

            {{-- Category pills: All + first 3 visible, rest toggled --}}
            <div class="pb-2">
                <div id="cat-pills-row" class="flex flex-wrap gap-1.5">
                    {{-- "All" pill always visible --}}
                    <a href="{{ route('pos.index', ['search' => request('search')]) }}"
                       class="tab-pill px-3 py-1 text-xs border {{ !request('category') ? 'active border-gray-800' : 'border-gray-200' }}">
                        All
                    </a>

                    {{-- First 3 categories always visible --}}
                    @foreach($categories as $index => $category)
                        @if($index < 3)
                            <a href="{{ route('pos.index', ['category' => $category->category_id, 'search' => request('search')]) }}"
                               class="tab-pill px-3 py-1 text-xs border {{ request('category') == $category->category_id ? 'active border-gray-800' : 'border-gray-200' }}">
                                {{ $category->name }}
                            </a>
                        @endif
                    @endforeach

                    {{-- Hidden categories (index >= 3) --}}
                    @if($categories->count() > 3)
                        <div id="cat-extra-pills" class="flex flex-wrap gap-1.5 contents" style="display:none;">
                            @foreach($categories as $index => $category)
                                @if($index >= 3)
                                    <a href="{{ route('pos.index', ['category' => $category->category_id, 'search' => request('search')]) }}"
                                       class="tab-pill px-3 py-1 text-xs border {{ request('category') == $category->category_id ? 'active border-gray-800' : 'border-gray-200' }}">
                                        {{ $category->name }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                        <button id="cat-see-more-btn" onclick="toggleMobileCategories()"
                            class="tab-pill px-3 py-1 text-xs border border-dashed border-gray-300 text-gray-500 gap-1">
                            <svg id="cat-see-more-icon" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                            <span id="cat-see-more-label">More</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Products scroll area --}}
        <div id="products-area" class="flex-1 overflow-y-auto overflow-x-hidden bg-gray-50 min-h-0"
             style="padding: 8px 10px 0; width: 100%; box-sizing: border-box;">

            <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 6px; width: 100%; padding-bottom: 96px;">
                @forelse($products as $item)
                    {{-- New-style mobile product card --}}
                    <div class="mobile-product-card">
                        {{-- Thumbnail --}}
                        <div class="mpc-thumb">
                            @if($item->product && $item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Name, price, add button --}}
                        <div class="mpc-body">
                            <span class="mpc-name" title="{{ $item->product->name }}">{{ $item->product->name }}</span>
                            <span class="mpc-price">₱{{ number_format($item->product->price, 2) }}</span>
                            <button onclick="addToCart({{ $item->product->product_id }})"
                                    data-product-id="{{ $item->product->product_id }}"
                                    class="mpc-add-btn">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add
                            </button>
                        </div>

                        {{-- Stock badge --}}
                        <div class="mpc-right">
                            <span class="stock-badge mpc-stock
                                @if($item->quantity == 0) bg-red-100 text-red-700
                                @elseif($item->quantity < 5) bg-amber-100 text-amber-700
                                @else bg-sky-100 text-sky-700 @endif"
                                data-product-id="{{ $item->product->product_id }}"
                                data-initial-stock="{{ $item->quantity }}">
                                {{ $item->quantity }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16" style="width: 100%;">
                        <svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-gray-400 text-sm">No products found</p>
                    </div>
                @endforelse
            </div>

            @if($products->hasPages())
                <div class="pb-4">{{ $products->links() }}</div>
            @endif
        </div>

        {{-- Bottom Sheet Cart --}}
        <div id="mobile-cart-sheet">
            <div id="cart-drag-handle"></div>

            <div id="cart-sheet-header" onclick="toggleCartSheet()">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6H19m-11 0a1 1 0 11-2 0 1 1 0 012 0zm12 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                    </svg>
                    <span class="font-bold text-gray-800 text-sm">Cart</span>
                    <span id="cart-count-mobile" class="bg-gray-800 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">0</span>
                </div>
                <div class="flex items-center gap-3">
                    <span id="cart-total-mobile" class="font-bold text-gray-800 text-base">₱0.00</span>
                    <svg id="cart-chevron" class="w-5 h-5 text-gray-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                    </svg>
                </div>
            </div>

            <div id="cart-sheet-body">
                <div id="cart-items-mobile" class="space-y-2 mb-3 min-h-[60px]">
                    <div class="text-center py-6 text-gray-400"><p class="text-sm">Cart is empty</p></div>
                </div>

                <div class="mb-3">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Payment Method</p>
                    <div class="flex gap-2">
                        <button type="button" data-payment-method="cash"
                            class="payment-button flex-1 flex items-center justify-center gap-1.5 py-2.5 border-2 border-gray-200 rounded-xl text-sm font-semibold text-gray-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Cash
                        </button>
                        <button type="button" data-payment-method="gcash"
                            class="payment-button flex-1 flex items-center justify-center gap-1.5 py-2.5 border-2 border-gray-200 rounded-xl text-sm font-semibold text-gray-700 transition">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            GCash
                        </button>
                    </div>
                </div>

                <div class="border-t pt-3 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">
                            <span id="items-count-mobile">0</span> items ·
                            <span id="total-items-count-mobile">0</span> qty
                        </span>
                        <span class="text-xs text-gray-400">Total</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-black text-gray-800" id="cart-total-mobile-big">₱0.00</span>
                    </div>
                    <div class="flex gap-2">
                        <button id="checkoutBtnMobile" onclick="checkout()"
                            class="flex-1 bg-gray-800 text-white py-3 rounded-xl font-bold text-sm transition hover:bg-gray-700 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed"
                            disabled>Checkout</button>
                        <button onclick="clearCart()"
                            class="bg-red-50 text-red-600 border border-red-200 px-4 py-3 rounded-xl font-semibold text-sm transition hover:bg-red-100">Clear</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════
         MODALS (shared)
    ═══════════════════════════════════════════ --}}

    <div id="confirmModal" class="modal-backdrop">
        <div class="modal-card">
            <h2 class="text-base font-bold text-gray-800 mb-1.5" id="confirmTitle">Confirm Action</h2>
            <p class="text-gray-500 mb-5 text-sm" id="confirmMessage">Are you sure?</p>
            <div class="flex gap-3">
                <button id="cancelConfirm" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">Cancel</button>
                <button id="okConfirm" class="flex-1 py-2.5 bg-gray-800 text-white rounded-xl text-sm font-semibold hover:bg-gray-700 transition">Confirm</button>
            </div>
        </div>
    </div>

    <div id="gcashModal" class="modal-backdrop">
        <div class="modal-card">
            <div class="flex items-center gap-2 mb-1.5">
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <h2 class="text-base font-bold text-gray-800">GCash Payment</h2>
            </div>
            <p class="text-gray-500 mb-4 text-sm">Enter the GCash reference code to proceed.</p>
            <input type="text" id="gcashReferenceCode" placeholder="Reference code"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-semibold text-gray-800 focus:ring-2 focus:ring-purple-400 focus:outline-none mb-1">
            <p class="text-xs text-gray-400 mb-5">Enter the transaction reference code from GCash</p>
            <div class="flex gap-3">
                <button id="cancelGcash" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">Cancel</button>
                <button id="confirmGcash" class="flex-1 py-2.5 bg-purple-600 text-white rounded-xl text-sm font-bold hover:bg-purple-700 transition">Confirm Payment</button>
            </div>
        </div>
    </div>

    <div id="cashModal" class="modal-backdrop">
        <div class="modal-card">
            <h2 class="text-base font-bold text-gray-800 mb-3">Cash Payment</h2>
            <div class="mb-3 p-3 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-500 mb-0.5">Total Amount Due</p>
                <p id="cashTotalAmount" class="text-2xl font-black text-gray-800">₱0.00</p>
            </div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Amount Received</label>
            <input type="number" id="cashAmount" placeholder="0.00" step="0.01" min="0"
                class="w-full px-4 py-3 border border-gray-200 rounded-xl text-base font-semibold text-gray-800 focus:ring-2 focus:ring-gray-400 focus:outline-none mb-1"
                oninput="calculateChange()">
            <p class="text-xs text-gray-400 mb-3">Enter the amount received from customer</p>
            <div id="changeSection" class="hidden mb-3 p-3 bg-emerald-50 rounded-xl border border-emerald-100">
                <p class="text-xs text-emerald-600 font-medium mb-0.5">Change</p>
                <p id="changeAmount" class="text-xl font-black text-emerald-700">₱0.00</p>
            </div>
            <div id="insufficientWarning" class="hidden mb-3 p-3 bg-red-50 rounded-xl border border-red-100">
                <p class="text-xs text-red-600 font-semibold mb-0.5">Insufficient payment</p>
                <p id="remainingAmount" class="text-sm text-red-700 font-bold">₱0.00 short</p>
            </div>
            <div class="flex gap-3">
                <button id="cancelCash" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">Cancel</button>
                <button id="confirmCash" class="flex-1 py-2.5 bg-gray-800 text-white rounded-xl text-sm font-bold hover:bg-gray-700 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed transition" disabled>Confirm Payment</button>
            </div>
        </div>
    </div>


    <script>
/* ── Sidebar offset ── */
(function() {
    function applySidebarOffset() {
        if (window.innerWidth >= 1024) return;
        const sidebar = document.querySelector('aside, nav.sidebar, [data-sidebar], .sidebar');
        const offset  = sidebar ? sidebar.getBoundingClientRect().right : 60;
        const sheet   = document.getElementById('mobile-cart-sheet');
        if (sheet) sheet.style.left = offset + 'px';
        document.querySelectorAll('.modal-backdrop').forEach(m => m.style.left = offset + 'px');
    }
    document.addEventListener('DOMContentLoaded', applySidebarOffset);
    window.addEventListener('resize', applySidebarOffset);
})();

/* ── Mobile category toggle ── */
function toggleMobileCategories() {
    const extra = document.getElementById('cat-extra-pills');
    const icon  = document.getElementById('cat-see-more-icon');
    const label = document.getElementById('cat-see-more-label');
    const open  = extra.style.display !== 'none' && extra.style.display !== '';
    if (open) {
        extra.style.display = 'none';
        icon.style.transform = '';
        label.textContent = 'More';
    } else {
        extra.style.display = 'contents';
        icon.style.transform = 'rotate(180deg)';
        label.textContent = 'Less';
    }
}

let cartItems        = [];
let pendingUpdates   = new Map();
let stockAdjustments = new Map();
let isAddingToCart   = false;

const isMobile = () => window.innerWidth < 1024;

let cartSheetExpanded = false;
function toggleCartSheet() {
    const sheet   = document.getElementById('mobile-cart-sheet');
    const chevron = document.getElementById('cart-chevron');
    cartSheetExpanded = !cartSheetExpanded;
    sheet.classList.toggle('expanded', cartSheetExpanded);
    chevron.style.transform = cartSheetExpanded ? 'rotate(180deg)' : '';
}

document.addEventListener('DOMContentLoaded', function () {
    initPaymentButtons();
    loadCart();

    const mobileSearch = document.getElementById('searchInputMobile');
    if (mobileSearch) {
        let t;
        mobileSearch.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(() => document.getElementById('searchFormMobile').submit(), 500);
        });
    }
    const desktopSearch = document.getElementById('searchInputDesktop');
    if (desktopSearch) {
        let t;
        desktopSearch.addEventListener('input', () => {
            clearTimeout(t);
            t = setTimeout(() => document.getElementById('searchFormDesktop').submit(), 500);
        });
    }
});

function initPaymentButtons() {
    document.querySelectorAll('.payment-button').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.payment-button').forEach(b => b.classList.remove('selected-cash', 'selected-gcash'));
            const m = this.dataset.paymentMethod;
            if (m === 'cash')  this.classList.add('selected-cash');
            if (m === 'gcash') this.classList.add('selected-gcash');
            updateCheckoutButtonState();
        });
    });
    document.querySelectorAll('[data-payment-method="cash"]').forEach(b => b.classList.add('selected-cash'));
}

window.getSelectedPaymentMethod = function () {
    const active = document.querySelector('.payment-button.selected-cash, .payment-button.selected-gcash');
    return active ? active.dataset.paymentMethod : null;
};

window.updateCheckoutButtonState = function () {
    const enabled = cartItems.length > 0 && !!getSelectedPaymentMethod();
    ['checkoutBtnDesktop','checkoutBtnMobile'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.disabled = !enabled;
    });
};

function updateStockDisplay(productId, deltaQty) {
    document.querySelectorAll(`.stock-badge[data-product-id="${productId}"]`).forEach(badge => {
        const initial = parseInt(badge.dataset.initialStock, 10) || 0;
        const prev    = stockAdjustments.get(productId) || 0;
        const next    = prev + deltaQty;
        stockAdjustments.set(productId, next);
        const shown   = Math.max(0, initial - next);
        badge.textContent = shown;
        badge.className = badge.className.replace(/bg-\w+-\d+|text-\w+-\d+/g, '');
        if (shown === 0)    badge.classList.add('bg-red-100',   'text-red-700');
        else if (shown < 5) badge.classList.add('bg-amber-100', 'text-amber-700');
        else                badge.classList.add('bg-sky-100',   'text-sky-700');
    });
}

function getDisplayedStock(productId) {
    const badge = document.querySelector(`.stock-badge[data-product-id="${productId}"]`);
    if (!badge) return 0;
    return Math.max(0, (parseInt(badge.dataset.initialStock,10)||0) - (stockAdjustments.get(productId)||0));
}

function showConfirmModal(title, message, onConfirm) {
    const modal = document.getElementById('confirmModal');
    document.getElementById('confirmTitle').textContent   = title;
    document.getElementById('confirmMessage').innerHTML   = message;
    modal.classList.add('open');
    document.getElementById('cancelConfirm').onclick = () => modal.classList.remove('open');
    document.getElementById('okConfirm').onclick = () => {
        modal.classList.remove('open');
        if (typeof onConfirm === 'function') onConfirm();
    };
}

function addToCart(productId) {
    if (isAddingToCart) return;
    if (getDisplayedStock(productId) <= 0) { showToast('error','Out of stock'); return; }
    document.querySelectorAll(`button[data-product-id="${productId}"]`).forEach(b => b.disabled = true);
    isAddingToCart = true;
    updateStockDisplay(productId, 1);
    fetch('/pos/add-to-cart', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ product_id: productId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            loadCart();
            if (!cartSheetExpanded && isMobile()) toggleCartSheet();
        } else {
            updateStockDisplay(productId, -1);
            showToast('error', data.message || 'Failed to add');
        }
    })
    .catch(() => { updateStockDisplay(productId, -1); showToast('error','Error'); })
    .finally(() => {
        isAddingToCart = false;
        document.querySelectorAll(`button[data-product-id="${productId}"]`).forEach(b => b.disabled = false);
    });
}

function loadCart() {
    fetch('/pos/get-cart')
        .then(r => { if (!r.ok) throw new Error(); return r.json(); })
        .then(data => {
            if (!data.success) return;
            cartItems = data.items;
            stockAdjustments.clear();
            data.items.forEach(item => stockAdjustments.set(item.product_id, (stockAdjustments.get(item.product_id)||0) + item.quantity));
            document.querySelectorAll('.stock-badge').forEach(badge => {
                const pid  = parseInt(badge.dataset.productId);
                const init = parseInt(badge.dataset.initialStock, 10) || 0;
                const shown = Math.max(0, init - (stockAdjustments.get(pid)||0));
                badge.textContent = shown;
                badge.classList.remove('bg-red-100','text-red-700','bg-amber-100','text-amber-700','bg-sky-100','text-sky-700','bg-blue-100','text-blue-700');
                if (shown === 0)    badge.classList.add('bg-red-100','text-red-700');
                else if (shown < 5) badge.classList.add('bg-amber-100','text-amber-700');
                else                badge.classList.add('bg-sky-100','text-sky-700');
            });
            renderCart(data.items, data.total);
        })
        .catch(e => console.error('loadCart:', e));
}

function cartItemHTML(item) {
    const unitPrice = (item.subtotal / item.quantity).toFixed(2);
    return `
    <div class="flex items-center gap-2 bg-gray-50 rounded-xl p-2 border border-gray-100"
         data-cart-item-id="${item.cart_item_id}" data-product-id="${item.product_id}">
        <div class="w-10 h-10 rounded-lg bg-gray-100 flex-shrink-0 overflow-hidden">
            ${item.image
                ? `<img src="/storage/${item.image}" alt="${item.name}" class="w-full h-full object-cover">`
                : `<svg class="w-5 h-5 text-gray-300 m-auto mt-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-gray-800 truncate">${item.name}</p>
            <p class="text-[10px] text-gray-400">₱${unitPrice} each</p>
            <p class="text-sm font-bold text-green-700 item-subtotal">₱${parseFloat(item.subtotal).toFixed(2)}</p>
        </div>
        <div class="flex items-center gap-1 flex-shrink-0">
            <button class="qty-btn" onclick="decreaseQuantity(${item.cart_item_id}, ${item.product_id})">
                <svg class="w-3 h-3 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
            </button>
            <input type="number" value="${item.quantity}" min="1"
                   onchange="handleQuantityInput(${item.cart_item_id}, ${item.product_id}, this.value, ${item.quantity})"
                   class="w-10 text-center border border-gray-200 rounded-lg py-1 text-xs font-bold text-gray-800 focus:ring-1 focus:ring-gray-400 focus:outline-none item-quantity-input">
            <button class="qty-btn" onclick="increaseQuantity(${item.cart_item_id}, ${item.product_id})">
                <svg class="w-3 h-3 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            </button>
        </div>
        <button onclick="removeFromCart(${item.cart_item_id}, ${item.product_id})"
                class="ml-1 text-red-400 hover:text-red-600 p-1 rounded-lg hover:bg-red-50 transition flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
    </div>`;
}

const emptyCartHTML = `
    <div class="text-center py-8 text-gray-300">
        <svg class="w-10 h-10 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6H19m-11 0a1 1 0 11-2 0 1 1 0 012 0zm12 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
        <p class="text-sm text-gray-400">Cart is empty</p>
    </div>`;

function renderCart(items, total) {
    const html     = items.length ? items.map(cartItemHTML).join('') : emptyCartHTML;
    const totalStr = `₱${parseFloat(total).toFixed(2)}`;
    const count    = items.length;
    const totalQty = items.reduce((s,i) => s + i.quantity, 0);
    const dc = document.getElementById('cart-items-desktop');
    if (dc) dc.innerHTML = html;
    setText('cart-count-desktop', count);
    setText('cart-total-desktop', totalStr);
    setText('items-count-desktop', count);
    setText('total-items-count-desktop', totalQty);
    const mc = document.getElementById('cart-items-mobile');
    if (mc) mc.innerHTML = html;
    setText('cart-count-mobile', count);
    setText('cart-total-mobile', totalStr);
    setText('cart-total-mobile-big', totalStr);
    setText('items-count-mobile', count);
    setText('total-items-count-mobile', totalQty);
    updateCheckoutButtonState();
}

function setText(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
}

function increaseQuantity(cartItemId, productId) {
    const item = cartItems.find(i => i.cart_item_id === cartItemId);
    if (!item) return;
    if (getDisplayedStock(productId) < 1) { showToast('error','Not enough stock'); return; }
    applyQuantityChange(cartItemId, productId, item.quantity + 1);
}

function decreaseQuantity(cartItemId, productId) {
    const item = cartItems.find(i => i.cart_item_id === cartItemId);
    if (!item) return;
    if (item.quantity <= 1) { removeFromCart(cartItemId, productId); return; }
    applyQuantityChange(cartItemId, productId, item.quantity - 1);
}

function handleQuantityInput(cartItemId, productId, rawValue, oldQty) {
    const newQty = parseInt(rawValue, 10);
    const input  = document.querySelector(`[data-cart-item-id="${cartItemId}"] .item-quantity-input`);
    if (isNaN(newQty) || newQty < 1) { showToast('error','Invalid quantity'); if (input) input.value = oldQty; return; }
    if (newQty === oldQty) return;
    const delta = newQty - oldQty;
    if (delta > 0 && getDisplayedStock(productId) < delta) {
        showToast('error',`Only ${getDisplayedStock(productId)} in stock`);
        if (input) input.value = oldQty;
        return;
    }
    applyQuantityChange(cartItemId, productId, newQty);
}

function applyQuantityChange(cartItemId, productId, newQty) {
    const item = cartItems.find(i => i.cart_item_id === cartItemId);
    if (!item) return;
    const oldQty = item.quantity, delta = newQty - oldQty, unitPrice = item.subtotal / oldQty;
    item.quantity = newQty; item.subtotal = unitPrice * newQty;
    document.querySelectorAll(`[data-cart-item-id="${cartItemId}"]`).forEach(row => {
        const qi = row.querySelector('.item-quantity-input'), si = row.querySelector('.item-subtotal');
        if (qi) qi.value = newQty;
        if (si) si.textContent = `₱${item.subtotal.toFixed(2)}`;
    });
    updateStockDisplay(productId, delta);
    refreshTotalsDisplay();
    if (pendingUpdates.has(cartItemId)) clearTimeout(pendingUpdates.get(cartItemId));
    const tid = setTimeout(() => { pendingUpdates.delete(cartItemId); syncQtyWithServer(cartItemId, productId, newQty, oldQty, delta, unitPrice); }, 200);
    pendingUpdates.set(cartItemId, tid);
}

function syncQtyWithServer(cartItemId, productId, newQty, oldQty, delta, unitPrice) {
    fetch('/pos/update-cart-item', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ cart_item_id: cartItemId, quantity: newQty })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const localTotal = cartItems.reduce((s,i) => s + i.subtotal, 0);
            if (Math.abs(data.total - localTotal) > 0.01) loadCart();
        } else { revertQuantityChange(cartItemId, productId, oldQty, delta, unitPrice); showToast('error', data.message || 'Failed to update'); }
    })
    .catch(() => { revertQuantityChange(cartItemId, productId, oldQty, delta, unitPrice); showToast('error','Network error — reverted'); });
}

function revertQuantityChange(cartItemId, productId, oldQty, delta, unitPrice) {
    const item = cartItems.find(i => i.cart_item_id === cartItemId);
    if (!item) return;
    item.quantity = oldQty; item.subtotal = unitPrice * oldQty;
    document.querySelectorAll(`[data-cart-item-id="${cartItemId}"]`).forEach(row => {
        const qi = row.querySelector('.item-quantity-input'), si = row.querySelector('.item-subtotal');
        if (qi) qi.value = oldQty;
        if (si) si.textContent = `₱${item.subtotal.toFixed(2)}`;
    });
    updateStockDisplay(productId, -delta);
    refreshTotalsDisplay();
}

function refreshTotalsDisplay() {
    const total    = cartItems.reduce((s,i) => s + i.subtotal, 0);
    const totalQty = cartItems.reduce((s,i) => s + i.quantity, 0);
    const count = cartItems.length, str = `₱${total.toFixed(2)}`;
    setText('cart-total-desktop', str); setText('cart-total-mobile', str); setText('cart-total-mobile-big', str);
    setText('items-count-desktop', count); setText('items-count-mobile', count);
    setText('total-items-count-desktop', totalQty); setText('total-items-count-mobile', totalQty);
    setText('cart-count-desktop', count); setText('cart-count-mobile', count);
}

function removeFromCart(cartItemId, productId) {
    const item = cartItems.find(i => i.cart_item_id === cartItemId);
    if (!item) return;
    const qty = item.quantity;
    document.querySelectorAll(`[data-cart-item-id="${cartItemId}"]`).forEach(r => r.style.opacity = '0.4');
    updateStockDisplay(productId, -qty);
    fetch('/pos/remove-cart-item', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ cart_item_id: cartItemId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            cartItems = cartItems.filter(i => i.cart_item_id !== cartItemId);
            renderCart(cartItems, cartItems.reduce((s,i) => s + i.subtotal, 0));
        } else {
            document.querySelectorAll(`[data-cart-item-id="${cartItemId}"]`).forEach(r => r.style.opacity = '1');
            updateStockDisplay(productId, qty);
            showToast('error', data.message || 'Failed to remove');
        }
    })
    .catch(() => {
        document.querySelectorAll(`[data-cart-item-id="${cartItemId}"]`).forEach(r => r.style.opacity = '1');
        updateStockDisplay(productId, qty);
        showToast('error','Error');
    });
}

function clearCart() {
    if (!cartItems.length) { showToast('error','Cart is already empty'); return; }
    showConfirmModal('Clear Cart','Remove all items from the cart?', () => {
        const snap = [...cartItems];
        snap.forEach(i => updateStockDisplay(i.product_id, -i.quantity));
        Promise.all(snap.map(item => fetch('/pos/remove-cart-item', {
            method: 'POST',
            headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ cart_item_id: item.cart_item_id })
        })))
        .then(() => { cartItems = []; renderCart([],0); showToast('success','Cart cleared'); })
        .catch(() => { showToast('error','Failed to clear cart'); loadCart(); });
    });
}

function checkout() {
    if (!cartItems.length) { showToast('error','Cart is empty'); return; }
    const method = getSelectedPaymentMethod();
    if (!method) { showToast('error','Select a payment method'); return; }
    if (method === 'gcash') showGcashModal();
    else showCashModal();
}

function showGcashModal() {
    const modal = document.getElementById('gcashModal'), input = document.getElementById('gcashReferenceCode'), confirm = document.getElementById('confirmGcash');
    input.value = ''; modal.classList.add('open');
    document.getElementById('cancelGcash').onclick = () => modal.classList.remove('open');
    confirm.onclick = () => {
        const code = input.value.trim();
        if (!code) { showToast('error','Enter reference code'); return; }
        if (code.length < 6) { showToast('error','Code must be ≥ 6 characters'); return; }
        modal.classList.remove('open');
        showConfirmModal('Confirm GCash', `Proceed with GCash payment?<br><strong>Ref: ${code}</strong>`, () => processCheckout('gcash', code));
    };
    input.onkeypress = e => { if (e.key === 'Enter') confirm.click(); };
    setTimeout(() => input.focus(), 150);
}

function showCashModal() {
    const modal = document.getElementById('cashModal'), cashInput = document.getElementById('cashAmount'),
          totalLabel = document.getElementById('cashTotalAmount'), confirmBtn = document.getElementById('confirmCash');
    const totalDue = cartItems.reduce((s,i) => s + i.subtotal, 0);
    totalLabel.textContent = `₱${totalDue.toFixed(2)}`; cashInput.value = ''; cashInput.min = totalDue.toFixed(2);
    document.getElementById('changeSection').classList.add('hidden');
    document.getElementById('insufficientWarning').classList.add('hidden');
    confirmBtn.disabled = true; modal.classList.add('open');
    document.getElementById('cancelCash').onclick = () => modal.classList.remove('open');
    confirmBtn.onclick = () => {
        const paid = parseFloat(cashInput.value), change = paid - totalDue;
        if (!paid || paid < totalDue) { showToast('error','Insufficient payment'); return; }
        modal.classList.remove('open');
        showConfirmModal('Confirm Cash Payment',
            `<strong>Total: ₱${totalDue.toFixed(2)}</strong><br>Paid: ₱${paid.toFixed(2)}<br>Change: ₱${change.toFixed(2)}`,
            () => processCheckout('cash', null, paid, change));
    };
    setTimeout(() => cashInput.focus(), 150);
}

function calculateChange() {
    const paid = parseFloat(document.getElementById('cashAmount').value) || 0;
    const totalDue = parseFloat(document.getElementById('cashTotalAmount').textContent.replace('₱','')) || 0;
    const changeBox = document.getElementById('changeSection'), warnBox = document.getElementById('insufficientWarning'), confirmBtn = document.getElementById('confirmCash');
    if (paid >= totalDue && paid > 0) {
        document.getElementById('changeAmount').textContent = `₱${(paid-totalDue).toFixed(2)}`;
        changeBox.classList.remove('hidden'); warnBox.classList.add('hidden'); confirmBtn.disabled = false;
    } else if (paid > 0) {
        document.getElementById('remainingAmount').textContent = `₱${(totalDue-paid).toFixed(2)} short`;
        changeBox.classList.add('hidden'); warnBox.classList.remove('hidden'); confirmBtn.disabled = true;
    } else {
        changeBox.classList.add('hidden'); warnBox.classList.add('hidden'); confirmBtn.disabled = true;
    }
}

function processCheckout(method, referenceCode=null, cashAmount=null, change=null) {
    ['checkoutBtnDesktop','checkoutBtnMobile'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.disabled = true; el.innerHTML = '<span class="animate-pulse">Processing…</span>'; }
    });
    const payload = { payment_method: method };
    if (method === 'gcash' && referenceCode) payload.reference_code = referenceCode;
    if (method === 'cash' && cashAmount !== null) { payload.cash_amount = cashAmount; payload.change = change; }
    fetch('/pos/checkout', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const msg = method === 'gcash' ? `GCash completed! Ref: ${referenceCode}` : method === 'cash' ? `Cash payment done! Change: ₱${change.toFixed(2)}` : 'Checkout completed!';
            showToast('success', msg);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showToast('error', data.message || 'Checkout failed');
            ['checkoutBtnDesktop','checkoutBtnMobile'].forEach(id => { const el = document.getElementById(id); if (el) { el.disabled = false; el.textContent = 'Checkout'; } });
        }
    })
    .catch(() => {
        showToast('error','Error during checkout');
        ['checkoutBtnDesktop','checkoutBtnMobile'].forEach(id => { const el = document.getElementById(id); if (el) { el.disabled = false; el.textContent = 'Checkout'; } });
    });
}

function showToast(type, message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: type, title: message, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
    } else { alert(message); }
}
    </script>

@endsection