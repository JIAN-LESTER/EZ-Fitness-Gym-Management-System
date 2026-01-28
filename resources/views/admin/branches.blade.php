@extends('layouts.app')
@section('title', 'Branches')
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
                        <button onclick='editBranch(@json($branch))'
                            class="flex-1 px-4 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 text-white font-semibold transition-all text-sm shadow-md">
                            Edit
                        </button>
                        <button onclick='openDeleteModal(@json($branch))'
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
                            <select name="province" id="add_province" required onchange="loadCities('add')"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="">Select Province</option>
                            </select>
                        </div>
                    </div>

                    <!-- City/Municipality -->
                    <div>
                        <label for="add_city" class="block text-sm font-medium text-gray-700 mb-2">City/Municipality <span class="text-red-500">*</span></label>
                        <select name="city" id="add_city" required
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            <option value="">Select City/Municipality</option>
                        </select>
                    </div>

                    <!-- Street Name -->
                    <div>
                        <label for="add_street" class="block text-sm font-medium text-gray-700 mb-2">Street Name/Barangay <span class="text-red-500">*</span></label>
                        <input type="text" name="street" id="add_street" required
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            placeholder="e.g., Barangay San Antonio, Main Street">
                    </div>

                    <!-- Building Name/Number -->
                    <div>
                        <label for="add_building" class="block text-sm font-medium text-gray-700 mb-2">Building Name/Number</label>
                        <input type="text" name="building" id="add_building"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800"
                            placeholder="e.g., Fitness Center Building, Unit 123">
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('addBranchModal')"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">
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
                            <select name="province" id="edit_province" required onchange="loadCities('edit')"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="">Select Province</option>
                            </select>
                        </div>
                    </div>

                    <!-- City/Municipality -->
                    <div>
                        <label for="edit_city" class="block text-sm font-medium text-gray-700 mb-2">City/Municipality <span class="text-red-500">*</span></label>
                        <select name="city" id="edit_city" required
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            <option value="">Select City/Municipality</option>
                        </select>
                    </div>

                    <!-- Street Name -->
                    <div>
                        <label for="edit_street" class="block text-sm font-medium text-gray-700 mb-2">Street Name/Barangay <span class="text-red-500">*</span></label>
                        <input type="text" name="street" id="edit_street" required
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    </div>

                    <!-- Building Name/Number -->
                    <div>
                        <label for="edit_building" class="block text-sm font-medium text-gray-700 mb-2">Building Name/Number</label>
                        <input type="text" name="building" id="edit_building"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('editBranchModal')"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">
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


    <script>
       let regionsData = [];
let provincesData = [];
let citiesData = [];

// Load regions on page load
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
    const citySelect = document.getElementById(`${mode}_city`);
    
    if (!regionSelect.value) {
        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
        return;
    }

    // Find region code
    const region = regionsData.find(r => r.name === regionSelect.value);
    if (!region) return;

    try {
        const response = await fetch(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`);
        provincesData = await response.json();
        
        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
        
        provincesData.forEach(province => {
            const option = document.createElement('option');
            option.value = province.name;
            option.textContent = province.name;
            option.setAttribute('data-code', province.code);
            provinceSelect.appendChild(option);
        });
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
    
    if (!provinceSelect.value) {
        citySelect.innerHTML = '<option value="">Select City/Municipality</option>';
        return;
    }

    // Find province code
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
    } catch (error) {
        console.error('Error loading cities:', error);
        if (typeof toastr !== 'undefined') {
            toastr.error('Failed to load cities data');
        }
    }
}

// Helper function to parse address
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
    
    return {
        building: '',
        street: '',
        city: '',
        province: '',
        region: ''
    };
}

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
        
        // Reset selects to default
        const selects = form.querySelectorAll('select');
        selects.forEach(select => {
            if (select.id !== `${modalId === 'addBranchModal' ? 'add' : 'edit'}_country`) {
                select.innerHTML = '<option value="">Select...</option>';
            }
        });
    }
}

function validateBranchForm(form) {
    let valid = true;
    clearAllErrors(form);

    const name = form.querySelector('[name="name"]');
    const region = form.querySelector('[name="region"]');
    const province = form.querySelector('[name="province"]');
    const city = form.querySelector('[name="city"]');
    const street = form.querySelector('[name="street"]');

    if (!name.value.trim()) {
        showError(name, 'Branch name is required');
        valid = false;
    } else if (name.value.trim().length < 3) {
        showError(name, 'Branch name must be at least 3 characters');
        valid = false;
    }

    if (!region.value) {
        showError(region, 'Region is required');
        valid = false;
    }

    if (!province.value) {
        showError(province, 'Province is required');
        valid = false;
    }

    if (!city.value) {
        showError(city, 'City/Municipality is required');
        valid = false;
    }

    if (!street.value.trim()) {
        showError(street, 'Street name is required');
        valid = false;
    }

    return valid;
}

async function editBranch(branch) {
    // Parse the address
    const addressParts = parseAddress(branch.address);
    
    // Set branch name
    document.getElementById('edit_name').value = branch.name;
    
    // Set form action
    document.getElementById('editBranchForm').action = `/admin/branches/${branch.branch_id}`;
    
    // Wait for regions to load if not already loaded
    if (regionsData.length === 0) {
        await loadRegions();
    }
    
    // Set region
    const regionSelect = document.getElementById('edit_region');
    regionSelect.value = addressParts.region;
    
    // Load and set provinces
    if (addressParts.region) {
        await loadProvinces('edit');
        setTimeout(() => {
            const provinceSelect = document.getElementById('edit_province');
            
            // Find and select the province
            for (let i = 0; i < provinceSelect.options.length; i++) {
                if (provinceSelect.options[i].value === addressParts.province) {
                    provinceSelect.selectedIndex = i;
                    break;
                }
            }
            
            // Load and set cities
            if (addressParts.province) {
                loadCities('edit').then(() => {
                    setTimeout(() => {
                        const citySelect = document.getElementById('edit_city');
                        citySelect.value = addressParts.city;
                    }, 100);
                });
            }
        }, 200);
    }
    
    // Set street and building
    document.getElementById('edit_street').value = addressParts.street;
    document.getElementById('edit_building').value = addressParts.building;
    
    // Open modal
    openModal('editBranchModal');
}

function openDeleteModal(branch) {
    const hasUsers = branch.users_count > 0;
    const hasSubscriptions = branch.subscriptions_count > 0;

    if (hasUsers || hasSubscriptions) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Cannot Delete Branch',
                html: `
                    <div class="text-left space-y-2">
                        <p class="text-gray-700">This branch cannot be deleted because it has:</p>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            ${hasUsers ? `<p class="text-sm text-gray-600">• ${branch.users_count} user(s)</p>` : ''}
                            ${hasSubscriptions ? `<p class="text-sm text-gray-600">• ${branch.subscriptions_count} subscription(s)</p>` : ''}
                        </div>
                        <p class="text-gray-700 mt-4">Please reassign or remove associated data before deleting this branch.</p>
                    </div>
                `,
                icon: 'error',
                confirmButtonColor: '#6b7280',
                confirmButtonText: 'Understood'
            });
        } else {
            alert('Cannot delete branch with associated users or subscriptions.');
        }
        return;
    }

    if (typeof Swal === 'undefined') {
        if (confirm('Are you sure you want to delete this branch?')) {
            submitDeleteForm(branch.branch_id);
        }
        return;
    }

    Swal.fire({
        title: 'Delete Branch?',
        html: `
            <div class="text-left space-y-2">
                <p class="text-gray-700">You are about to delete:</p>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="font-semibold text-gray-900">${branch.name}</p>
                    <p class="text-sm text-gray-600">${branch.address}</p>
                </div>
                <p class="text-red-600 font-medium mt-4">This action cannot be undone!</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        width: '600px'
    }).then((result) => {
        if (result.isConfirmed) {
            submitDeleteForm(branch.branch_id);
        }
    });
}

function submitDeleteForm(branchId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/branches/${branchId}`;

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

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load regions data
    loadRegions();
    
    // Form validation
    const addForm = document.querySelector('#addBranchModal form');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            if (!validateBranchForm(this)) {
                e.preventDefault();
            }
        });
    }

    const editForm = document.querySelector('#editBranchForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            if (!validateBranchForm(this)) {
                e.preventDefault();
            }
        });
    }
    
    // Close modals with Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                if (!modal.classList.contains('hidden')) {
                    closeModal(modal.id);
                }
            });
        }
    });
});
    </script>

@endsection