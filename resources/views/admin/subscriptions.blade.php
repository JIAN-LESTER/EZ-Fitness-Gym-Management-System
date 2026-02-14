@extends('layouts.app')
@section('title', 'Subscriptions | EZ Fitness')
@section('header', 'Subscriptions')

<style>
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
        <div class="flex justify-between items-center p-4 sm:p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Subscription Management</h2>
            <button onclick="openModal('addSubscriptionModal')"
                class="flex items-center gap-2 bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-700 shadow-md hover:shadow-lg transition-all duration-300 font-semibold whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Subscription
            </button>
        </div>

        <!-- Search Section -->
        <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('admin.subscription_management') }}"
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
                            placeholder="Search by subscription name or price..."
                            class="pl-10 w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-400 shadow-sm transition-all">
                    </div>
                </div>
                <div class="w-full sm:w-auto min-w-[200px]">
                    <select name="branch_id" id="branch_filter"
                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 shadow-sm transition-all">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->branch_id }}" {{ ($branch_filter ?? '') == $branch->branch_id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
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
                    <a href="{{ route('admin.subscription_management') }}"
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
            @forelse($subscriptions as $subscription)
                @if($loop->first)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @endif

                <div class="bg-white border-2 border-gray-200 rounded-2xl p-6 hover:shadow-lg hover:border-gray-300 transition-all duration-200">
                    <!-- Subscription Header -->
                    <div class="mb-4">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $subscription->name }}</h3>
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full mb-2">
                            {{ $subscription->branch->name ?? 'N/A' }}
                        </span>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ $subscription->details }}</p>
                    </div>

                    <!-- Subscription Details -->
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600 font-medium">Price</span>
                            <span class="text-lg font-bold text-gray-900">₱{{ number_format($subscription->price) }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600 font-medium">Duration</span>
                            <span class="text-sm font-semibold text-gray-700">{{ $subscription->duration_days }} days</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600 font-medium">Members</span>
                            <span class="text-sm font-semibold text-gray-700">{{ $subscription->members_count ?? 0 }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button onclick='editSubscription(@json($subscription))'
                            class="flex-1 px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-white font-semibold transition-all text-sm shadow-md">
                            Edit
                        </button>
                        <button onclick="openDeleteModal('{{ route('subscriptions.destroy', $subscription->subscription_id) }}', '{{ $subscription->name }}')"
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
                    <p class="text-lg font-medium text-gray-500">No subscriptions found</p>
                    <p class="text-sm mt-1 text-gray-400">Try adjusting your search criteria</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($subscriptions->hasPages())
            <div class="flex flex-col sm:flex-row items-center justify-between p-4 sm:p-6 bg-gray-50 border-t border-gray-200 gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-semibold text-gray-900">{{ $subscriptions->firstItem() }}</span> to
                    <span class="font-semibold text-gray-900">{{ $subscriptions->lastItem() }}</span> of
                    <span class="font-semibold text-gray-900">{{ $subscriptions->total() }}</span> subscriptions
                </div>

                <div class="flex gap-2">
                    @if($subscriptions->onFirstPage())
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Prev</span>
                    @else
                        <a href="{{ $subscriptions->previousPageUrl() }}"
                            class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium">Prev</a>
                    @endif

                    <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium sm:hidden">
                        {{ $subscriptions->currentPage() }} / {{ $subscriptions->lastPage() }}
                    </span>

                    @if($subscriptions->hasMorePages())
                        <a href="{{ $subscriptions->nextPageUrl() }}"
                            class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium">Next</a>
                    @else
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Add Subscription Modal -->
    <div id="addSubscriptionModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('addSubscriptionModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-5 rounded-t-2xl flex-shrink-0">
                <h2 class="text-xl font-semibold">Add New Subscription</h2>
            </header>

            <div class="overflow-y-auto flex-1 modal-scrollbar">
                <form method="POST" action="{{ route('subscriptions.store') }}" class="p-6 md:p-8 space-y-6">
                    @csrf
                    <div>
                        <label for="add_name" class="block text-sm font-medium text-gray-700 mb-2">Subscription Name</label>
                        <input type="text" name="name" id="add_name"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            placeholder="e.g., Basic Subscription">
                    </div>
       <div>
                        <label for="add_branch" class="block text-sm font-medium text-gray-700 mb-2">
                            Branch
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
                            placeholder="e.g., This subscription is good for 1 year">
                    </div>
                    <div>
                        <label for="add_price" class="block text-sm font-medium text-gray-700 mb-2">Price (₱)</label>
                        <input type="number" name="price" id="add_price" step="0.01" min="0"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            placeholder="0.00">
                    </div>
                    <div>
                        <label for="add_duration" class="block text-sm font-medium text-gray-700 mb-2">Duration (Days)</label>
                        <input type="number" name="duration_days" id="add_duration" min="1"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            placeholder="30">
                    </div>
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('addSubscriptionModal')"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">
                            Add Subscription
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Subscription Modal -->
    <div id="editSubscriptionModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('editSubscriptionModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-5 rounded-t-2xl flex-shrink-0">
                <h2 class="text-xl font-semibold">Edit Subscription</h2>
            </header>

            <div class="overflow-y-auto flex-1 modal-scrollbar">
                <form id="editSubscriptionForm" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-2">Subscription Name</label>
                        <input type="text" name="name" id="edit_name"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
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
                    </div>
                    <div>
                        <label for="edit_duration" class="block text-sm font-medium text-gray-700 mb-2">Duration (Days)</label>
                        <input type="number" name="duration_days" id="edit_duration" min="1"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    </div>
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('editSubscriptionModal')"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">
                            Update Subscription
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                    Delete Subscription
                </h3>

                <p class="text-center text-gray-600 mb-6">
                    Are you sure you want to delete <span id="deleteSubscriptionName" class="font-bold text-red-600"></span>? This action cannot be undone.
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

        function validateSubscriptionForm(form) {
            let valid = true;
            clearAllErrors(form);

            const name = form.querySelector('[name="name"]');
            const branch = form.querySelector('[name="branch_id"]');
            const price = form.querySelector('[name="price"]');
            const duration = form.querySelector('[name="duration_days"]');

            if (!name.value.trim()) {
                showError(name, 'Subscription name is required');
                valid = false;
            } else if (name.value.trim().length < 3) {
                showError(name, 'Subscription name must be at least 3 characters');
                valid = false;
            }

            if (!branch.value) {
                showError(branch, 'Branch is required');
                valid = false;
            }

            if (!price.value || price.value === '') {
                showError(price, 'Price is required');
                valid = false;
            } else if (parseFloat(price.value) <= 0) {
                showError(price, 'Price must be greater than 0');
                valid = false;
            }

            if (!duration.value || duration.value === '') {
                showError(duration, 'Duration is required');
                valid = false;
            } else if (parseInt(duration.value) < 1) {
                showError(duration, 'Duration must be at least 1 day');
                valid = false;
            }

            return valid;
        }

        function editSubscription(subscription) {
            document.getElementById('edit_name').value = subscription.name;
            document.getElementById('edit_branch').value = subscription.branch_id;
            document.getElementById('edit_details').value = subscription.details || '';
            document.getElementById('edit_price').value = subscription.price;
            document.getElementById('edit_duration').value = subscription.duration_days;
            document.getElementById('editSubscriptionForm').action = `/admin/subscriptions/${subscription.subscription_id}`;
            openModal('editSubscriptionModal');
        }

         function openDeleteModal(actionUrl, subscriptionName) {
    const form = document.getElementById('deleteForm');
    const nameSpan = document.getElementById('deleteSubscriptionName');
    form.action = actionUrl;

        nameSpan.textContent = subscriptionName;

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

        function submitDeleteForm(subscriptionId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/subscriptions/${subscriptionId}`;

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

        document.addEventListener('DOMContentLoaded', function() {
            const addForm = document.querySelector('#addSubscriptionModal form');
            if (addForm) {
                addForm.addEventListener('submit', function(e) {
                    if (!validateSubscriptionForm(this)) {
                        e.preventDefault();
                    }
                });
            }

            const editForm = document.querySelector('#editSubscriptionForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    if (!validateSubscriptionForm(this)) {
                        e.preventDefault();
                    }
                });
            }
        });

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