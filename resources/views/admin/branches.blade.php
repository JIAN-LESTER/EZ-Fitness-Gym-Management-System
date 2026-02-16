@extends('layouts.app')
@section('title', 'Branches | EZ Fitness')
@section('header', 'Branches')

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
    textarea,
    select {
        color: #111827 !important;
    }

    input::placeholder,
    textarea::placeholder {
        color: #9CA3AF !important;
    }

    /* Smooth transition for enabled/disabled state */
    select:not(:disabled),
    input[type="text"]:not(:disabled) {
        background-color: #ffffff !important;
        transition: background-color 0.2s ease, opacity 0.2s ease;
    }

    select:disabled,
    input[type="text"]:disabled {
        background-color: #F3F4F6 !important;
        cursor: not-allowed;
        opacity: 0.6;
        transition: background-color 0.2s ease, opacity 0.2s ease;
    }
</style>

@section('content')
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

        <!-- Header & Add Button -->
        <div class="flex justify-between items-center p-4 sm:p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Branch Management</h2>
            <button onclick="openModal('addBranchModal')"
                class="flex items-center gap-2 bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-700 shadow-md hover:shadow-lg transition-all duration-300 font-semibold whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Branch
            </button>
        </div>

        <!-- Search Section -->
        <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('admin.branch_management') }}"
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
                            placeholder="Search by branch name or address..."
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
                    <a href="{{ route('admin.branch_management') }}"
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
            @forelse($branches as $branch)
                @if($loop->first)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @endif

                <div class="bg-white border-2 border-gray-200 rounded-2xl p-6 hover:shadow-lg hover:border-gray-300 transition-all duration-200">
                    <!-- Branch Header -->
                    <div class="mb-4">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $branch->name }}</h3>
                        <p class="text-sm text-gray-600">
                            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $branch->address }}
                        </p>
                    </div>

                    <!-- Branch Stats -->
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <span class="text-sm text-gray-600 font-medium">Members</span>
                            <span class="text-sm font-semibold text-gray-700">{{ $branch->users_count }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600 font-medium">Subscriptions</span>
                            <span class="text-sm font-semibold text-gray-700">{{ $branch->subscriptions_count }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-2">
                        <button onclick='editBranch({{ $branch->branch_id }})'
                            class="flex-1 px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-white font-semibold transition-all text-sm shadow-md">
                            Edit
                        </button>
                        <button onclick='openDeleteModal("{{ route('branches.destroy', $branch->branch_id) }}", "{{ $branch->name }}")'
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
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <p class="text-lg font-medium text-gray-500">No branches found</p>
                    <p class="text-sm mt-1 text-gray-400">Try adjusting your search criteria</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($branches->hasPages())
            <div class="flex flex-col sm:flex-row items-center justify-between p-4 sm:p-6 bg-gray-50 border-t border-gray-200 gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-semibold text-gray-900">{{ $branches->firstItem() }}</span> to
                    <span class="font-semibold text-gray-900">{{ $branches->lastItem() }}</span> of
                    <span class="font-semibold text-gray-900">{{ $branches->total() }}</span> branches
                </div>

                <div class="flex gap-2">
                    @if($branches->onFirstPage())
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Prev</span>
                    @else
                        <a href="{{ $branches->previousPageUrl() }}"
                            class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium">Prev</a>
                    @endif

                    <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium sm:hidden">
                        {{ $branches->currentPage() }} / {{ $branches->lastPage() }}
                    </span>

                    @if($branches->hasMorePages())
                        <a href="{{ $branches->nextPageUrl() }}"
                            class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium">Next</a>
                    @else
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Add Branch Modal -->
    <div id="addBranchModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('addBranchModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-5 rounded-t-2xl flex-shrink-0">
                <h2 class="text-xl font-semibold">Add New Branch</h2>
            </header>

            <div class="overflow-y-auto flex-1 modal-scrollbar">
                <form method="POST" action="{{ route('branches.store') }}" class="p-6 md:p-8 space-y-6">
                    @csrf
                    
                    <!-- Branch Name -->
                    <div>
                        <label for="add_name" class="block text-sm font-medium text-gray-700 mb-2">Branch Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="add_name" required
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            placeholder="e.g., Downtown Branch">
                    </div>

                    <!-- Address Section Header -->
                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Address Details</h3>
                    </div>

                    <!-- Country -->
                    <div>
                        <label for="add_country" class="block text-sm font-medium text-gray-700 mb-2">Country <span class="text-red-500">*</span></label>
                        <input type="text" name="country" id="add_country" required value="Philippines" readonly
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-gray-100 px-4 py-2">
                    </div>

                    <!-- Region & Province -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="add_region" class="block text-sm font-medium text-gray-700 mb-2">Region <span class="text-red-500">*</span></label>
                            <select name="region" id="add_region" required onchange="loadProvinces('add')"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="">Select Region</option>
                            </select>
                        </div>
                        <div>
                            <label for="add_province" class="block text-sm font-medium text-gray-700 mb-2">Province <span class="text-red-500">*</span></label>
                            <select name="province" id="add_province" required onchange="loadCities('add')" disabled
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="">Select Province</option>
                            </select>
                        </div>
                    </div>

                    <!-- City/Municipality -->
                    <div>
                        <label for="add_city" class="block text-sm font-medium text-gray-700 mb-2">City/Municipality <span class="text-red-500">*</span></label>
                        <select name="city" id="add_city" required disabled onchange="onCityChange('add')"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            <option value="">Select City/Municipality</option>
                        </select>
                    </div>

                    <!-- Street Name -->
                    <div>
                        <label for="add_street" class="block text-sm font-medium text-gray-700 mb-2">Street Name/Barangay <span class="text-red-500">*</span></label>
                        <input type="text" name="street" id="add_street" required disabled
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            placeholder="e.g., Barangay San Antonio, Main Street"
                            oninput="onStreetInput('add')">
                    </div>

                    <!-- Building Name/Number -->
                    <div>
                        <label for="add_building" class="block text-sm font-medium text-gray-700 mb-2">Building Name/Number</label>
                        <input type="text" name="building" id="add_building" disabled
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            placeholder="e.g., Fitness Center Building, Unit 123">
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('addBranchModal')"
                            class="px-6 py-2 rounded-lg border-2 border-gray-300 bg-transparent text-gray-700 hover:bg-gray-50 w-full sm:w-auto transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">
                            Add Branch
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Branch Modal -->
    <div id="editBranchModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('editBranchModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-5 rounded-t-2xl flex-shrink-0">
                <h2 class="text-xl font-semibold">Edit Branch</h2>
            </header>

            <div class="overflow-y-auto flex-1 modal-scrollbar">
                <form id="editBranchForm" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Branch Name -->
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-2">Branch Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit_name" required
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    </div>

                    <!-- Address Section Header -->
                    <div class="pt-4 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Address Details</h3>
                    </div>

                    <!-- Country -->
                    <div>
                        <label for="edit_country" class="block text-sm font-medium text-gray-700 mb-2">Country <span class="text-red-500">*</span></label>
                        <input type="text" name="country" id="edit_country" required value="Philippines" readonly
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-gray-100 px-4 py-2">
                    </div>

                    <!-- Region & Province -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_region" class="block text-sm font-medium text-gray-700 mb-2">Region <span class="text-red-500">*</span></label>
                            <select name="region" id="edit_region" required onchange="loadProvinces('edit')"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="">Select Region</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_province" class="block text-sm font-medium text-gray-700 mb-2">Province <span class="text-red-500">*</span></label>
                            <select name="province" id="edit_province" required onchange="loadCities('edit')" disabled
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="">Select Province</option>
                            </select>
                        </div>
                    </div>

                    <!-- City/Municipality -->
                    <div>
                        <label for="edit_city" class="block text-sm font-medium text-gray-700 mb-2">City/Municipality <span class="text-red-500">*</span></label>
                        <select name="city" id="edit_city" required disabled onchange="onCityChange('edit')"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            <option value="">Select City/Municipality</option>
                        </select>
                    </div>

                    <!-- Street Name -->
                    <div>
                        <label for="edit_street" class="block text-sm font-medium text-gray-700 mb-2">Street Name/Barangay <span class="text-red-500">*</span></label>
                        <input type="text" name="street" id="edit_street" required disabled
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            oninput="onStreetInput('edit')">
                    </div>

                    <!-- Building Name/Number -->
                    <div>
                        <label for="edit_building" class="block text-sm font-medium text-gray-700 mb-2">Building Name/Number</label>
                        <input type="text" name="building" id="edit_building" disabled
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('editBranchModal')"
                            class="px-6 py-2 rounded-lg border-2 border-gray-300 bg-transparent text-gray-700 hover:bg-gray-50 w-full sm:w-auto transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">
                            Update Branch
                        </button>
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
                    Delete Branch
                </h3>

                <p class="text-center text-gray-600 mb-6">
                    Are you sure you want to delete <span id="deleteBranchName" class="font-bold text-red-600"></span>? This action cannot be undone.
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
let regionsData = [];
let provincesData = [];
let citiesData = [];

// ─── Cascading Enable/Disable Helpers ────────────────────────────────────────

/**
 * Enable a field and update its visual state.
 */
function enableField(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.disabled = false;
}

/**
 * Disable a field, reset its value, and update visual state.
 */
function disableField(id, defaultOption = null) {
    const el = document.getElementById(id);
    if (!el) return;
    el.disabled = true;

    // Reset selects to their placeholder, inputs to empty
    if (el.tagName === 'SELECT') {
        el.innerHTML = defaultOption || '<option value="">Select...</option>';
    } else {
        el.value = '';
    }
}

/**
 * Called when City/Municipality selection changes.
 * Enables Street if a city is selected; disables Street + Building otherwise.
 */
function onCityChange(mode) {
    const citySelect = document.getElementById(`${mode}_city`);
    const streetInput = document.getElementById(`${mode}_street`);
    const buildingInput = document.getElementById(`${mode}_building`);

    if (citySelect.value) {
        enableField(`${mode}_street`);
        // Building state follows Street — keep disabled until street has a value
        if (!streetInput.value.trim()) {
            buildingInput.disabled = true;
        }
    } else {
        disableField(`${mode}_street`);
        disableField(`${mode}_building`);
    }
}

/**
 * Called on every keystroke in Street Name.
 * Enables Building once Street has any content; disables it when cleared.
 */
function onStreetInput(mode) {
    const streetInput = document.getElementById(`${mode}_street`);
    const buildingInput = document.getElementById(`${mode}_building`);

    if (streetInput.value.trim()) {
        enableField(`${mode}_building`);
    } else {
        buildingInput.disabled = true;
        buildingInput.value = '';
    }
}

// ─── PSGC API Loaders ─────────────────────────────────────────────────────────

async function loadRegions() {
    try {
        const response = await fetch('https://psgc.gitlab.io/api/regions/');
        regionsData = await response.json();
        
        populateRegions('add');
        populateRegions('edit');
    } catch (error) {
        console.error('Error loading regions:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error('Failed to load regions data');
        }
    }
}

function populateRegions(mode) {
    const select = document.getElementById(`${mode}_region`);
    if (!select) return;

    select.innerHTML = '<option value="">Select Region</option>';
    
    regionsData.forEach(region => {
        const option = document.createElement('option');
        option.value = region.name;
        option.textContent = region.name;
        select.appendChild(option);
    });
}

async function loadProvinces(mode) {
    const regionSelect = document.getElementById(`${mode}_region`);
    const provinceSelect = document.getElementById(`${mode}_province`);

    // Reset downstream fields
    disableField(`${mode}_province`, '<option value="">Select Province</option>');
    disableField(`${mode}_city`, '<option value="">Select City/Municipality</option>');
    disableField(`${mode}_street`);
    disableField(`${mode}_building`);

    if (!regionSelect.value) return;

    const region = regionsData.find(r => r.name === regionSelect.value);
    if (!region) return;

    try {
        const response = await fetch(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`);
        provincesData = await response.json();
        
        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        
        provincesData.forEach(province => {
            const option = document.createElement('option');
            option.value = province.name;
            option.textContent = province.name;
            option.setAttribute('data-code', province.code);
            provinceSelect.appendChild(option);
        });

        // Enable province now that it has options
        enableField(`${mode}_province`);
    } catch (error) {
        console.error('Error loading provinces:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error('Failed to load provinces data');
        }
    }
}

async function loadCities(mode) {
    const provinceSelect = document.getElementById(`${mode}_province`);
    const citySelect = document.getElementById(`${mode}_city`);

    // Reset downstream fields
    disableField(`${mode}_city`, '<option value="">Select City/Municipality</option>');
    disableField(`${mode}_street`);
    disableField(`${mode}_building`);

    if (!provinceSelect.value) return;

    const selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
    const provinceCode = selectedOption.getAttribute('data-code');
    if (!provinceCode) return;

    try {
        const [citiesResponse, municipalitiesResponse] = await Promise.all([
            fetch(`https://psgc.gitlab.io/api/provinces/${provinceCode}/cities/`),
            fetch(`https://psgc.gitlab.io/api/provinces/${provinceCode}/municipalities/`)
        ]);
        
        const cities = await citiesResponse.json();
        const municipalities = await municipalitiesResponse.json();
        
        citiesData = [...cities, ...municipalities].sort((a, b) => a.name.localeCompare(b.name));
        
        citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
        
        citiesData.forEach(city => {
            const option = document.createElement('option');
            option.value = city.name;
            option.textContent = city.name;
            citySelect.appendChild(option);
        });

        // Enable city now that it has options
        enableField(`${mode}_city`);
    } catch (error) {
        console.error('Error loading cities:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error('Failed to load cities data');
        }
    }
}

// ─── Address Parser ───────────────────────────────────────────────────────────

function parseAddress(address) {
    // Format: "Building, Street, City, Province, Region" or "Street, City, Province, Region"
    const parts = address.split(',').map(p => p.trim());
    
    if (parts.length === 5) {
        return {
            building: parts[0],
            street: parts[1],
            city: parts[2],
            province: parts[3],
            region: parts[4]
        };
    } else if (parts.length === 4) {
        return {
            building: '',
            street: parts[0],
            city: parts[1],
            province: parts[2],
            region: parts[3]
        };
    }
    
    return { building: '', street: '', city: '', province: '', region: '' };
}

// ─── Modal Helpers ────────────────────────────────────────────────────────────

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
        form.reset();

        // Re-disable and reset all cascading fields
        const mode = modalId === 'addBranchModal' ? 'add' : 'edit';
        document.getElementById(`${mode}_province`).innerHTML = '<option value="">Select Province</option>';
        document.getElementById(`${mode}_province`).disabled = true;
        document.getElementById(`${mode}_city`).innerHTML = '<option value="">Select City/Municipality</option>';
        document.getElementById(`${mode}_city`).disabled = true;
        document.getElementById(`${mode}_street`).disabled = true;
        document.getElementById(`${mode}_building`).disabled = true;
    }
}

// ─── Edit Branch ──────────────────────────────────────────────────────────────

async function editBranch(branchId) {
    console.log('Editing branch:', branchId);
    
    try {
        const response = await fetch(`/admin/branches/${branchId}/edit`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const branch = await response.json();
        console.log('Branch data:', branch);
        
        const addressParts = parseAddress(branch.address);
        
        document.getElementById('edit_name').value = branch.name || '';
        document.getElementById('editBranchForm').action = `/admin/branches/${branch.branch_id}`;
        
        if (regionsData.length === 0) await loadRegions();
        
        // Set region
        const regionSelect = document.getElementById('edit_region');
        regionSelect.value = addressParts.region;
        
        if (addressParts.region) {
            await loadProvinces('edit'); // this enables province & resets downstream

            // Set province
            const provinceSelect = document.getElementById('edit_province');
            for (let i = 0; i < provinceSelect.options.length; i++) {
                if (provinceSelect.options[i].value === addressParts.province) {
                    provinceSelect.selectedIndex = i;
                    break;
                }
            }

            if (addressParts.province) {
                await loadCities('edit'); // this enables city & resets downstream

                // Set city
                const citySelect = document.getElementById('edit_city');
                citySelect.value = addressParts.city;

                if (addressParts.city) {
                    // Enable street
                    enableField('edit_street');
                    document.getElementById('edit_street').value = addressParts.street || '';

                    if (addressParts.street) {
                        // Enable building
                        enableField('edit_building');
                        document.getElementById('edit_building').value = addressParts.building || '';
                    }
                }
            }
        }
        
        openModal('editBranchModal');
        
    } catch (error) {
        console.error('Error loading branch:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error('Failed to load branch data. Please try again.');
        } else {
            alert('Failed to load branch data. Please try again.');
        }
    }
}

// ─── Delete Modal ─────────────────────────────────────────────────────────────

function openDeleteModal(actionUrl, branchName) {
    const form = document.getElementById('deleteForm');
    const nameSpan = document.getElementById('deleteBranchName');
    
    form.action = actionUrl;
    nameSpan.textContent = branchName;

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

// ─── Init ─────────────────────────────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function() {
    loadRegions();
    
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modals = ['addBranchModal', 'editBranchModal', 'deleteModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains('hidden')) {
                    if (modalId === 'deleteModal') {
                        closeDeleteModal();
                    } else {
                        closeModal(modalId);
                    }
                }
            });
        }
    });
    
    console.log('Branch management page initialized');
});
    </script>

@endsection