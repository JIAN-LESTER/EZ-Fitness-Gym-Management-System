@extends('layouts.app')
@section('title', 'Membership Plans')
@section('header', 'Membership Plans')

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
    input[type="number"],
    select,
    textarea {
        color: #111827 !important;
    }

    input::placeholder,
    textarea::placeholder {
        color: #9CA3AF !important;
    }
</style>

@section('content')
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

        <!-- Header & Add Button -->
        <div
            class="flex justify-between items-center p-4 sm:p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Membership Plans Management</h2>
            <button onclick="openModal('addPlanModal')"
                class="flex items-center gap-2 bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-700 shadow-md hover:shadow-lg transition-all duration-300 font-semibold whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Plan
            </button>
        </div>

        <!-- Search Section -->
        <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('admin.plan_management') }}"
                class="flex flex-wrap lg:flex-nowrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ $search ?? '' }}"
                            placeholder="Search by plan name or price..."
                            class="pl-10 w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-400 shadow-sm transition-all">
                    </div>
                </div>
                <button type="submit"
                    class="w-full sm:w-auto flex items-center justify-center gap-2 bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-700 shadow-md hover:shadow-lg transition-all duration-300 font-semibold whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Search
                </button>
                @if(request()->query())
                    <a href="{{ route('admin.plan_management') }}"
                        class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 transition-all duration-300 font-semibold shadow-md whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear Filters
                    </a>
                @endif
            </form>
        </div>

        <!-- Cards Grid -->
        <div class="p-6">
            @forelse($plans as $plan)
                @if($loop->first)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @endif

                    <div
                        class="bg-white border-2 border-gray-200 rounded-2xl p-6 hover:shadow-lg hover:border-gray-300 transition-all duration-200">
                        <!-- Plan Header -->
           
                          <div class="mb-4">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full mb-2">
                            {{ $plan->branch->name ?? 'N/A' }}
                        </span>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ $plan->details }}</p>
                    </div>

                        <!-- Plan Details -->
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600 font-medium">Price</span>
                                <span class="text-lg font-bold text-gray-900">₱{{ number_format($plan->price) }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600 font-medium">Duration</span>
                                <span class="text-sm font-semibold text-gray-700">{{ $plan->duration_days }} days</span>
                            </div>
                            <!-- <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-600 font-medium">Branch</span>
                                <span class="text-sm font-semibold text-gray-700">{{ $plan->branch->name ?? 'N/A' }}</span>
                            </div> -->
                            <div class="flex items-center justify-between py-2">
                                <span class="text-sm text-gray-600 font-medium">Members</span>
                                <span class="text-sm font-semibold text-gray-700">{{ $plan->members_count }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <button onclick='editPlan(@json($plan))'
                                class="flex-1 px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-white font-semibold transition-all text-sm shadow-md">
                                Edit
                            </button>
                            <button onclick='openDeleteModal("{{ route('plans.destroy', $plan->plan_id) }}", "{{ $plan->name }}")'
                                class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold transition-all text-sm shadow-md">
                                Delete
                            </button>
                        </div>
                    </div>

                    @if($loop->last)
                        </div>
                    @endif
            @empty
                <div class="flex flex-col items-center justify-center py-16">
                    <svg class="w-20 h-20 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-lg font-medium text-gray-500">No plans found</p>
                    <p class="text-sm mt-1 text-gray-400">Try adjusting your search criteria</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($plans->hasPages())
            <div
                class="flex flex-col sm:flex-row items-center justify-between p-4 sm:p-6 bg-gray-50 border-t border-gray-200 gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-semibold text-gray-900">{{ $plans->firstItem() }}</span> to
                    <span class="font-semibold text-gray-900">{{ $plans->lastItem() }}</span> of
                    <span class="font-semibold text-gray-900">{{ $plans->total() }}</span> plans
                </div>

                <div class="flex gap-2">
                    @if($plans->onFirstPage())
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Prev</span>
                    @else
                        <a href="{{ $plans->previousPageUrl() }}"
                            class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium">Prev</a>
                    @endif

                    <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium sm:hidden">
                        {{ $plans->currentPage() }} / {{ $plans->lastPage() }}
                    </span>

                    @if($plans->hasMorePages())
                        <a href="{{ $plans->nextPageUrl() }}"
                            class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium">Next</a>
                    @else
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Add Plan Modal -->
    <div id="addPlanModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('addPlanModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-5 rounded-t-2xl flex-shrink-0">
                <h2 class="text-xl font-semibold">Add New Plan</h2>
            </header>

            <div class="overflow-y-auto flex-1 modal-scrollbar">
                <form method="POST" action="{{ route('plans.store') }}" class="p-6 md:p-8 space-y-6">
                    @csrf
                    <div>
                        <label for="add_name" class="block text-sm font-medium text-gray-700 mb-2">Plan Name</label>
                        <input type="text" name="name" id="add_name"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            placeholder="e.g., Basic Plan">
                        @error('add_name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="add_branch" class="block text-sm font-medium text-gray-700 mb-2">
                            Branch <span class="text-red-500">*</span>
                        </label>
                        @if(auth()->user()->role === 'super_admin')
                            <select name="branch_id" id="add_branch" required
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 text-gray-800 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->branch_id }}">{{ $branch->name }}</option>
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
                        <label for="add_details" class="block text-sm font-medium text-gray-700 mb-2">Details</label>
                        <input type="text" name="details" id="add_details"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            placeholder="e.g., This plan is good for 1 year">
                    </div>
                    <div>
                        <label for="add_price" class="block text-sm font-medium text-gray-700 mb-2">Price (₱)</label>
                        <input type="number" name="price" id="add_price" step="0.01" min="0"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            placeholder="0.00">
                        @error('add_price')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="add_duration" class="block text-sm font-medium text-gray-700 mb-2">Duration
                            (Days)</label>
                        <input type="number" name="duration_days" id="add_duration" min="1"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            placeholder="30">
                        @error('add_duration')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div
                        class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('addPlanModal')"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">
                            Add Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Plan Modal -->
    <div id="editPlanModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('editPlanModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-5 rounded-t-2xl flex-shrink-0">
                <h2 class="text-xl font-semibold">Edit Plan</h2>
            </header>

            <div class="overflow-y-auto flex-1 modal-scrollbar">
                <form id="editPlanForm" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-2">Plan Name</label>
                        <input type="text" name="name" id="edit_name"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        @error('edit_name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="edit_branch" class="block text-sm font-medium text-gray-700 mb-2">
                            Branch <span class="text-red-500">*</span>
                        </label>
                        @if(auth()->user()->role === 'super_admin')
                            <select name="branch_id" id="edit_branch" required
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 text-gray-800 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->branch_id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" value="{{ auth()->user()->branch->name ?? 'N/A' }}" disabled
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-gray-100 px-4 py-2 text-gray-600">
                            <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                        @endif
                    </div>

                    <div>
                        <label for="edit_details" class="block text-sm font-medium text-gray-700 mb-2">Details</label>
                        <input type="text" name="details" id="edit_details"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    </div>
                    <div>
                        <label for="edit_price" class="block text-sm font-medium text-gray-700 mb-2">Price (₱)</label>
                        <input type="number" name="price" id="edit_price" step="0.01" min="0"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        @error('edit_price')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="edit_duration" class="block text-sm font-medium text-gray-700 mb-2">Duration
                            (Days)</label>
                        <input type="number" name="duration_days" id="edit_duration" min="1"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        @error('edit_duration')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div
                        class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('editPlanModal')"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">
                            Update Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

     <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0 backdrop-blur bg-opacity-50" onclick="closeDeleteModal()"></div>

        @php
           
        @endphp

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-center text-gray-900 mb-2">
                    Delete Membership Plan
                </h3>

                <p class="text-center text-gray-600 mb-6">
                    Are you sure you want to delete <span id="deletePlanName" class="font-bold text-red-600"></span>? This action cannot be undone.
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
        // Validation Utility Functions
        const showError = (element, message) => {
            element.classList.remove('border-gray-300');
            element.classList.add('border-red-500');

            const container = element.closest('div');
            let errorSpan = container.querySelector('.error-message');
            if (!errorSpan) {
                errorSpan = document.createElement('p');
                errorSpan.className = 'error-message text-red-600 text-xs mt-1 block';
                container.appendChild(errorSpan);
            }
            errorSpan.textContent = message;
        };

        const clearError = (element) => {
            element.classList.remove('border-red-500');
            element.classList.add('border-gray-300');

            const container = element.closest('div');
            const errorSpan = container.querySelector('.error-message');
            if (errorSpan) errorSpan.remove();
        };

        const clearAllErrors = (form) => {
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            form.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
                el.classList.add('border-gray-300');
            });
        };

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

            const form = modal.querySelector('form');
            if (form) {
                clearAllErrors(form);
                form.reset();
            }
        }

        function validatePlanForm(form) {
            let valid = true;
            clearAllErrors(form);

            const name = form.querySelector('[name="name"]');
            const price = form.querySelector('[name="price"]');
            const duration = form.querySelector('[name="duration_days"]');

            // Validate Plan Name
            if (!name.value.trim()) {
                showError(name, 'Plan name is required');
                valid = false;
            } else if (name.value.trim().length < 3) {
                showError(name, 'Plan name must be at least 3 characters');
                valid = false;
            } else if (name.value.trim().length > 100) {
                showError(name, 'Plan name must not exceed 100 characters');
                valid = false;
            }

            // Validate Price
            if (!price.value || price.value === '') {
                showError(price, 'Price is required');
                valid = false;
            } else if (parseFloat(price.value) < 0) {
                showError(price, 'Price cannot be negative');
                valid = false;
            } else if (parseFloat(price.value) === 0) {
                showError(price, 'Price must be greater than 0');
                valid = false;
            } else if (parseFloat(price.value) > 999999.99) {
                showError(price, 'Price is too large');
                valid = false;
            }

            // Validate Duration
            if (!duration.value || duration.value === '') {
                showError(duration, 'Duration is required');
                valid = false;
            } else if (parseInt(duration.value) < 1) {
                showError(duration, 'Duration must be at least 1 day');
                valid = false;
            } else if (parseInt(duration.value) > 3650) {
                showError(duration, 'Duration cannot exceed 3650 days (10 years)');
                valid = false;
            }

            if (!valid && typeof toastr !== 'undefined') {
                toastr.error('Please fix the errors in the form');
            }

            return valid;
        }

        function setupLiveValidation(form) {
            const name = form.querySelector('[name="name"]');
            const price = form.querySelector('[name="price"]');
            const duration = form.querySelector('[name="duration_days"]');

            // Name validation
            name.addEventListener('blur', function () {
                if (this.value.trim() && this.value.trim().length >= 3 && this.value.trim().length <= 100) {
                    clearError(this);
                }
            });

            name.addEventListener('input', function () {
                if (this.value.trim() && this.value.trim().length >= 3) {
                    clearError(this);
                }
            });

            // Price validation
            price.addEventListener('blur', function () {
                if (this.value && parseFloat(this.value) > 0 && parseFloat(this.value) <= 999999.99) {
                    clearError(this);
                }
            });

            price.addEventListener('input', function () {
                if (this.value && parseFloat(this.value) > 0) {
                    clearError(this);
                }
            });

            // Duration validation
            duration.addEventListener('blur', function () {
                if (this.value && parseInt(this.value) >= 1 && parseInt(this.value) <= 3650) {
                    clearError(this);
                }
            });

            duration.addEventListener('input', function () {
                if (this.value && parseInt(this.value) >= 1) {
                    clearError(this);
                }
            });
        }

        function editPlan(plan) {
            document.getElementById('edit_name').value = plan.name;
            document.getElementById('edit_details').value = plan.details || '';
            document.getElementById('edit_price').value = plan.price;
            document.getElementById('edit_duration').value = plan.duration_days;

            // Set branch value if super_admin
            @if(auth()->user()->role === 'super_admin')
                document.getElementById('edit_branch').value = plan.branch_id;
            @endif

            document.getElementById('editPlanForm').action = `/admin/plans/${plan.plan_id}`;
            openModal('editPlanModal');
        }

         function openDeleteModal(actionUrl, planName) {
    const form = document.getElementById('deleteForm');
    const nameSpan = document.getElementById('deletePlanName');
    form.action = actionUrl;
    nameSpan.textContent = planName;

    const modal = document.getElementById('deleteModal');
    const scrollY = window.scrollY;

    // Prevent body scroll
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

    // Restore body scroll
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.width = '';
    document.body.style.overflowY = '';

    window.scrollTo(0, parseInt(scrollY || '0') * -1);
}

        function submitDeleteForm(planId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/plans/${planId}`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
            }

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }

        // Initialize on DOM Ready
        document.addEventListener('DOMContentLoaded', function () {
            // Setup Add Plan Form Validation
            const addPlanForm = document.querySelector('#addPlanModal form');
            if (addPlanForm) {
                addPlanForm.addEventListener('submit', function (e) {
                    if (!validatePlanForm(this)) {
                        e.preventDefault();
                    }
                });
                setupLiveValidation(addPlanForm);
            }

            // Setup Edit Plan Form Validation
            const editPlanForm = document.querySelector('#editPlanForm');
            if (editPlanForm) {
                editPlanForm.addEventListener('submit', function (e) {
                    if (!validatePlanForm(this)) {
                        e.preventDefault();
                    }
                });
                setupLiveValidation(editPlanForm);
            }

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
        });

        // Close modals on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        closeModal(modal.id);
                    }
                });
            }
        });
    </script>

@endsection