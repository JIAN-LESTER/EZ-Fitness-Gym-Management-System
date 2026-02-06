@extends('layouts.app')

@section('title', 'Inventory')
@section('header', 'Products Management')

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

/* Image Preview Styles */
.image-preview {
    width: 100%;
    height: 200px;
    border: 2px dashed #D1D5DB;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background-color: #F9FAFB;
}

.image-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
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

        <!-- Header / Add Button -->
        <div class="flex justify-between items-center p-4 sm:p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Products Management</h2>
            <button onclick="openModal('addProductModal')"
                class="flex items-center gap-2 bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-700 shadow-md hover:shadow-lg transition-all duration-300 font-semibold whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Product
            </button>
        </div>

        <!-- Search and Filter Section -->
        <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('products.index') }}" class="space-y-4" role="search">
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
                                placeholder="Search by name or description..."
                                class="pl-10 w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-400 shadow-sm transition-all">
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
                            <span id="filterCount" class="hidden ml-1 px-2 py-0.5 text-xs bg-gray-800 text-white rounded-full">0</span>
                            <svg class="w-4 h-4 transition-transform" id="filterDropdownIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Filter Dropdown Content -->
                        <div id="filterDropdown" class="hidden fixed mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 max-h-[calc(100vh-200px)] overflow-y-auto z-[9999]">
                            <div class="p-4 space-y-4">
                                <!-- Category Filter -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Filter by Category</label>
                                    <div class="space-y-2">
                                        @foreach($categories as $category)
                                            <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                                <input type="checkbox" name="categories[]" value="{{ $category->category_id }}"
                                                    {{ in_array($category->category_id, request('categories', [])) ? 'checked' : '' }} onchange="updateFilterCount()"
                                                    class="w-4 h-4 text-gray-600 rounded focus:ring-2 focus:ring-gray-500">
                                                <span class="ml-3 text-sm font-medium text-gray-700">{{ $category->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="border-t border-gray-200"></div>

                                <!-- Status Filter -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Filter by Status</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="product_status[]" value="available" {{ in_array('available', request('product_status', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Available</span>
                                            <span class="ml-auto px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">Available</span>
                                        </label>

                                        <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="product_status[]" value="unavailable"
                                                {{ in_array('unavailable', request('product_status', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-4 h-4 text-red-600 rounded focus:ring-2 focus:ring-red-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Unavailable</span>
                                            <span class="ml-auto px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">Unavailable</span>
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
                    @if(request('search') || request('categories') || request('product_status'))
                        <div class="w-full sm:w-auto">
                            <a href="{{ route('products.index') }}"
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

        <!-- Products Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Branch</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Price</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Quantity</th>
                        <th class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-3 sm:px-6 py-3 sm:py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $inventory)
                        <tr class="clickable-row hover:bg-gray-50 transition-colors group" 
                            onclick="showProduct('{{ $inventory->product->product_id }}')">

                            <td class="px-3 sm:px-6 py-3 sm:py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center shadow flex-shrink-0">
                                        @if($inventory->product && $inventory->product->image)
                                            <img
                                                src="{{ asset('storage/' . $inventory->product->image) }}"
                                                alt="{{ $inventory->product->name }}"
                                                class="w-full h-full object-cover"
                                            >
                                        @else
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                />
                                            </svg>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        @if($inventory->product)
                                            <p class="font-semibold text-gray-900 truncate max-w-[180px] sm:max-w-[250px]">
                                                {{ $inventory->product->name }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $inventory->product && $inventory->product->category ? $inventory->product->category->name : 'No Category' }}
                                            </p>
                                            <p class="text-gray-500 text-xs sm:text-sm truncate max-w-[200px] sm:max-w-[280px] mt-0.5">
                                                {{ Str::limit($inventory->product->description, 60) }}
                                            </p>
                                        @else
                                            <p class="text-gray-500">No Product</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4 text-gray-700 text-sm">
                                {{ $inventory->product && $inventory->product->branch ? $inventory->product->branch->name : 'N/A' }}
                            </td>

                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-gray-700 font-semibold text-sm whitespace-nowrap">
                                ₱{{ $inventory->product ? number_format($inventory->product->price, 2) : '0.00' }}
                            </td>
                            
                            <td class="px-3 sm:px-6 py-3 sm:py-4 text-gray-700 font-semibold text-sm whitespace-nowrap">
                                {{ number_format($inventory->quantity ?? 0, 0) }}
                            </td>

                            <td class="hidden lg:table-cell px-3 sm:px-6 py-3 sm:py-4">
                                @if($inventory->product)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap
                                        {{ $inventory->product->status === 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($inventory->product->status) }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full whitespace-nowrap bg-gray-100 text-gray-700">N/A</span>
                                @endif
                            </td>
                          
                            <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                                <div class="relative inline-block text-left">
                                    <button onclick="toggleActionsMenu(event, '{{ $inventory->product->product_id }}')"
                                        class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>

                                    <div id="actionsMenu-{{ $inventory->product->product_id }}"
                                        class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                                        <div class="py-1">
                                            <button onclick="editProduct('{{ $inventory->product->product_id }}')"
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.828a2 2 0 01-1.414.586H9v-2a2 2 0 01.586-1.414z" />
                                                </svg>
                                                Edit Product
                                            </button>

                                            <button onclick="openDeleteModal('{{ route('products.destroy', $inventory->product->product_id) }}')"
                                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3" />
                                                </svg>
                                                Delete Product
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 sm:px-6 py-8 sm:py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 sm:w-16 sm:h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-base sm:text-lg font-medium">No products found</p>
                                    <p class="text-xs sm:text-sm mt-1">Try adjusting your search or filter criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->total() > 0)
            <div class="flex flex-col sm:flex-row items-center justify-between p-4 sm:p-6 bg-gray-50 border-t border-gray-200 gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-semibold text-gray-900">{{ $products->firstItem() }}</span> to
                    <span class="font-semibold text-gray-900">{{ $products->lastItem() }}</span> of
                    <span class="font-semibold text-gray-900">{{ $products->total() }}</span> products
                </div>

                <div class="flex gap-2">
                    @if($products->onFirstPage())
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Prev</span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}" class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium">Prev</a>
                    @endif

                    <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium sm:hidden">
                        {{ $products->currentPage() }} / {{ $products->lastPage() }}
                    </span>

                    @if($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}" class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium">Next</a>
                    @else
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0 backdrop-blur bg-opacity-50" onclick="closeModal('addProductModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-5 rounded-t-2xl flex-shrink-0">
                <h2 class="text-xl font-semibold">Add New Product</h2>
            </header>

            <div class="overflow-y-auto flex-1 modal-scrollbar">
                <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8 space-y-6">
                    @csrf

                    <!-- Image Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                        <div class="image-preview" id="addImagePreview">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="file" name="image" id="addImage" accept="image/*" onchange="previewImage(this, 'addImagePreview')"
                            class="mt-2 block w-full text-sm text-gray-800 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-gray-700 cursor-pointer">
                        @error('image')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" placeholder="Name" required
                            class="mt-1 block w-full rounded-lg border-2 text-gray-800 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        @error('name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" required
                            class="mt-1 block w-full rounded-lg border-2 text-gray-800 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="add_branch_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Branch <span class="text-red-500">*</span>
                        </label>
                        @if(auth()->user()->role === 'super_admin')
                            @php
                                $currentBranchId = session('selected_branch_id');
                            @endphp
                            <select name="branch_id" id="add_branch_id" required
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 text-gray-800 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->branch_id }}" {{ $currentBranchId == $branch->branch_id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" value="{{ auth()->user()->branch->name ?? 'N/A' }}" disabled
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-gray-100 px-4 py-2 text-gray-600">
                            <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                        @endif
                        @error('branch_id')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" placeholder="Description"
                            class="mt-1 block w-full rounded-lg border-2 text-gray-800 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"></textarea>
                        @error('description')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Price (₱) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="price" id="price" required min="0" placeholder="₱ ---"
                                class="mt-1 block w-full rounded-lg border-2 text-gray-800 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            @error('price')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity <span class="text-red-500">*</span></label>
                            <input type="number" step="1" name="quantity" id="quantity" required min="0" placeholder="---"
                                class="mt-1 block w-full rounded-lg border-2 text-gray-800 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            @error('quantity')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required
                            class="mt-1 block w-full rounded-lg border-2 text-gray-800 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                        @error('status')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('addProductModal')"
                            class="px-6 py-2 rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200 w-full sm:w-auto transition-colors">Cancel</button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Product Modal -->
    <div id="productShowModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden p-4">
        <div class="absolute inset-0 backdrop-blur bg-opacity-50" onclick="closeModal('productShowModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden">
            <header class="bg-gray-800 text-white p-4 sm:p-5 rounded-t-2xl sticky top-0 z-10 flex justify-between items-center">
                <h2 class="text-lg sm:text-xl font-semibold">Product Details</h2>
                <button onclick="closeModal('productShowModal')" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <div id="productShowContent" class="p-4 sm:p-6 bg-white overflow-y-auto" style="max-height: calc(90vh - 80px);">
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden p-4">
        <div class="absolute inset-0 backdrop-blur bg-opacity-50" onclick="closeModal('editProductModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-4 sm:p-5 rounded-t-2xl flex-shrink-0">
                <h2 class="text-lg sm:text-xl font-semibold">Edit Product</h2>
            </header>

            <div class="overflow-y-auto flex-1 modal-scrollbar">
                <form id="editProductForm" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 md:p-8 space-y-4 sm:space-y-6">
                    @csrf
                    @method('PUT')
                    <!-- Image Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                        <div class="image-preview" id="editImagePreview">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="file" name="image" id="editImage" accept="image/*" onchange="previewImage(this, 'editImagePreview')"
                            class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-800 file:text-white hover:file:bg-gray-700 cursor-pointer">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image</p>
                    </div>

                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit_name" required placeholder="Name"
                            class="mt-1 block w-full rounded-lg border-2 text-gray-800 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    </div>

                    <div>
                        <label for="edit_category_id" class="block text-sm font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="edit_category_id" required
                            class="mt-1 block w-full rounded-lg border-2 text-gray-800 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="edit_branch_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Branch <span class="text-red-500">*</span>
                        </label>
                        @if(auth()->user()->role === 'super_admin')
                            @php
                                $currentBranchId = session('selected_branch_id');
                            @endphp
                            <select name="branch_id" id="edit_branch_id" required
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 text-gray-800 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->branch_id }}" data-branch-id="{{ $branch->branch_id }}">
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" value="{{ auth()->user()->branch->name ?? 'N/A' }}" disabled
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-gray-100 px-4 py-2 text-gray-600">
                            <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                        @endif
                    </div>

                    <div>
                        <label for="edit_description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="edit_description" rows="3" placeholder="Description"
                            class="mt-1 block w-full rounded-lg border-2 text-gray-800 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_price" class="block text-sm font-medium text-gray-700">Price (₱) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="price" id="edit_price" required min="0" placeholder="₱ ---"
                                class="mt-1 block w-full rounded-lg border-2 text-gray-800 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        </div>

                        <div>
                            <label for="edit_quantity" class="block text-sm font-medium text-gray-700">Quantity <span class="text-red-500">*</span></label>
                            <input type="number" step="1" name="quantity" id="edit_quantity" required min="0" placeholder="---"
                                class="mt-1 block w-full rounded-lg border-2 text-gray-800 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        </div>
                    </div>

                    <div>
                        <label for="edit_status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="edit_status" required
                            class="mt-1 block w-full rounded-lg border-2 text-gray-800 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('editProductModal')"
                            class="px-6 py-2 rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200 w-full sm:w-auto transition-colors">Cancel</button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0 backdrop-blur bg-opacity-50" onclick="closeDeleteModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-center text-gray-900 mb-2">
                    Delete Product
                </h3>

                <p class="text-center text-gray-600 mb-6">
                    Are you sure you want to delete <span class="font-bold">{{ $inventory->product->name }}</span>? This action cannot be undone.
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
    // Image Preview Function
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    // Modal Functions
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        const scrollY = window.scrollY;

        document.body.style.position = 'fixed';
        document.body.style.top = `-${scrollY}px`;
        document.body.style.width = '100%';
        document.body.style.overflowY = 'scroll';

        modal.classList.remove('hidden');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const scrollY = document.body.style.top;

        modal.classList.add('hidden');

        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.overflowY = '';

        window.scrollTo(0, parseInt(scrollY || '0') * -1);
    }

    // Delete Modal Functions
    function openDeleteModal(actionUrl) {
        const form = document.getElementById('deleteForm');
        form.action = actionUrl;

        const modal = document.getElementById('deleteModal');
        const scrollY = window.scrollY;

        document.body.style.position = 'fixed';
        document.body.style.top = `-${scrollY}px`;
        document.body.style.width = '100%';
        document.body.style.overflowY = 'scroll';

        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        const scrollY = document.body.style.top;

        modal.classList.add('hidden');

        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.overflowY = '';

        window.scrollTo(0, parseInt(scrollY || '0') * -1);
    }

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

    // Actions Menu Functions
    function toggleActionsMenu(event, productId) {
        event?.stopPropagation();

        // Close all other menus
        document.querySelectorAll('[id^="actionsMenu-"]').forEach(menu => {
            if (menu.id !== `actionsMenu-${productId}`) {
                menu.classList.add('hidden');
            }
        });

        // Toggle current menu
        const menu = document.getElementById(`actionsMenu-${productId}`);
        const button = event?.target?.closest('button');

        if (menu && button) {
            const isHidden = menu.classList.contains('hidden');

            if (isHidden) {
                // Add click handlers to all menu items to close after selection
                const menuItems = menu.querySelectorAll('button');
                menuItems.forEach(item => {
                    if (!item.hasAttribute('data-close-handler')) {
                        item.setAttribute('data-close-handler', 'true');

                        const originalOnClick = item.onclick;

                        item.onclick = function (e) {
                            if (originalOnClick) {
                                originalOnClick.call(this, e);
                            }

                            setTimeout(() => {
                                menu.classList.add('hidden');
                            }, 100);
                        };
                    }
                });

                // Position menu
                const rect = button.getBoundingClientRect();
                menu.style.position = 'fixed';
                menu.style.top = `${rect.bottom + window.scrollY + 8}px`;
                menu.style.left = `${rect.right - 192}px`;
                menu.style.zIndex = '9999';
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        }
    }

    function closeAllActionsMenus() {
        document.querySelectorAll('[id^="actionsMenu-"]').forEach(menu => {
            menu.classList.add('hidden');
        });
    }

    // Edit Product Function
    function editProduct(productId) {
        fetch(`/products/${productId}/edit`)
            .then(res => res.json())
            .then(product => {
                document.getElementById('edit_name').value = product.name;
                document.getElementById('edit_category_id').value = product.category_id;
                document.getElementById('edit_description').value = product.description || '';
                document.getElementById('edit_price').value = product.price;
                document.getElementById('edit_quantity').value = product.inventory ? product.inventory.quantity : 0;
                document.getElementById('edit_status').value = product.status;

                @if(auth()->user()->role === 'super_admin')
                    const editBranchSelect = document.getElementById('edit_branch_id');
                    if (editBranchSelect && product.branch_id) {
                        editBranchSelect.value = product.branch_id;
                        
                        const option = editBranchSelect.querySelector(`option[value="${product.branch_id}"]`);
                        if (option) {
                            option.selected = true;
                        }
                    }
                @endif

                const editImagePreview = document.getElementById('editImagePreview');
                if (product.image) {
                    editImagePreview.innerHTML = `<img src="/storage/${product.image}" alt="Current Image">`;
                } else {
                    editImagePreview.innerHTML = `
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    `;
                }

                document.getElementById('editProductForm').action = `/products/${product.product_id}`;
                openModal('editProductModal');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load product data');
            });
    }

    // Show Product Function
    function showProduct(productId) {
        openModal('productShowModal');

        document.getElementById('productShowContent').innerHTML = `
            <div class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
            </div>
        `;

        fetch(`/products/${productId}`)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(product => {
                renderProductDetails(product);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('productShowContent').innerHTML = `
                    <div class="text-center py-12">
                        <p class="text-red-600">Error loading product details</p>
                    </div>
                `;
            });
    }

    function renderProductDetails(product) {
        const content = document.getElementById('productShowContent');
        const statusColor = product.status === 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';

        let html = `<div class="space-y-5">`;

        // Product Image
        if (product.image) {
            html += `
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <img src="/storage/${product.image}" alt="${product.name}" class="w-full h-64 object-cover rounded-lg">
                </div>
            `;
        }

        // Header with Title and Status
        html += `
            <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800">${product.name}</h3>
                     
                    </div>
                    <span class="px-3 py-1 text-sm rounded-full font-semibold ${statusColor}">
                        ${product.status.charAt(0).toUpperCase() + product.status.slice(1)}
                    </span>
                </div>
            </div>
        `;

        // Basic Information
        html += `
            <div class="bg-white border border-gray-200 rounded-lg">
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-md font-semibold text-gray-800">Basic Information</h4>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Branch</p>
                            <p class="font-semibold text-gray-800">${product.branch ? product.branch.name : 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Category</p>
                            <p class="font-semibold text-gray-800">${product.category ? product.category.name : 'N/A'}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Price</p>
                            <p class="text-xl font-bold text-gray-800">₱${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</p>
                        </div>
                        ${product.inventory ? `
                            <div>
                                <p class="text-gray-500">Quantity</p>
                                <p class="font-semibold text-gray-800">${product.inventory.quantity || 0}</p>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;

        // Description
        if (product.description) {
            html += `
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                        <h4 class="text-md font-semibold text-gray-800">Description</h4>
                    </div>
                    <div class="p-5">
                        <p class="text-gray-600">${product.description}</p>
                    </div>
                </div>
            `;
        }

        // Timestamps
        if (product.created_at) {
            html += `
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                        <h4 class="text-md font-semibold text-gray-800">Additional Information</h4>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Created At</p>
                                <p class="font-medium text-gray-800">${new Date(product.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            </div>
                          
                        </div>
                    </div>
                </div>
            `;
        }

        // Action Button
        html += `
            <div class="border-t border-gray-200 pt-4">
                <button onclick="closeModal('productShowModal'); editProduct('${product.product_id}');"
                    class="w-full px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    Edit Product
                </button>
            </div>
        `;

        html += `</div>`;
        content.innerHTML = html;
    }

    // Initialize on DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
        // Toastr config
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };
        }

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

        // Close action menus when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[id^="actionsMenu-"]') && !event.target.closest('button[onclick*="toggleActionsMenu"]')) {
                closeAllActionsMenus();
            }
        });

        // Handle Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const dropdown = document.getElementById('filterDropdown');
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                    const icon = document.getElementById('filterDropdownIcon');
                    if (icon) icon.classList.remove('rotate-180');
                }

                closeModal('addProductModal');
                closeModal('editProductModal');
                closeModal('productShowModal');
                closeDeleteModal();
                closeAllActionsMenus();
            }
        });
    });
    </script>

@endsection