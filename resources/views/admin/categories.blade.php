@extends('layouts.app')

@section('title', 'Categories Management')
@section('header', 'Categories Management')

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
    input[type="email"],
    input[type="password"],
    input[type="tel"],
    input[type="number"],
    input[type="date"],
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

        <!-- Header / Add Button -->
        <div class="flex justify-between items-center p-4 sm:p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Categories Management</h2>
            <button onclick="openModal('addCategoryModal')"
                class="flex items-center gap-2 bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-700 shadow-md hover:shadow-lg transition-all duration-300 font-semibold whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Category
            </button>
        </div>

        <!-- Search Section -->
        <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('categories.index') }}" class="space-y-4" role="search">
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
                                placeholder="Search by category name..."
                                class="pl-10 w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-400 shadow-sm transition-all">
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

                    <!-- Clear Search -->
                    @if(request('search'))
                        <div class="w-full sm:w-auto">
                            <a href="{{ route('categories.index') }}"
                                class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 transition-all duration-300 font-semibold shadow-md whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Clear Search
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <!-- Categories Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Category Name</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4">
                                <button onclick="showCategory('{{ $category->category_id }}')" class="hover:text-gray-900 transition-colors text-left w-full">
                                    <p class="font-semibold text-gray-900">{{ $category->name }}</p>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <button onclick="showCategory('{{ $category->category_id }}')" class="text-gray-500 hover:text-gray-700" title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    <button onclick="editCategory('{{ $category->category_id }}')" class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.828a2 2 0 01-1.414.586H9v-2a2 2 0 01.586-1.414z" />
                                        </svg>
                                    </button>

                                    <button onclick="openDeleteModal('{{ route('categories.destroy', $category->category_id) }}')" class="text-red-500 hover:text-red-700" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">No categories found</p>
                                    <p class="text-sm mt-1">Try adjusting your search criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($categories->total() > 0)
            <div class="flex flex-col sm:flex-row items-center justify-between p-4 sm:p-6 bg-gray-50 border-t border-gray-200 gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-semibold text-gray-900">{{ $categories->firstItem() }}</span> to
                    <span class="font-semibold text-gray-900">{{ $categories->lastItem() }}</span> of
                    <span class="font-semibold text-gray-900">{{ $categories->total() }}</span> categories
                </div>

                <div class="flex gap-2">
                    @if($categories->onFirstPage())
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Prev</span>
                    @else
                        <a href="{{ $categories->previousPageUrl() }}" class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium">Prev</a>
                    @endif

                    <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium sm:hidden">
                        {{ $categories->currentPage() }} / {{ $categories->lastPage() }}
                    </span>

                    @if($categories->hasMorePages())
                        <a href="{{ $categories->nextPageUrl() }}" class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium">Next</a>
                    @else
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('addCategoryModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-5 rounded-t-2xl flex-shrink-0">
                <h2 class="text-xl font-semibold">Add New Category</h2>
            </header>

            <div class="overflow-y-auto flex-1 modal-scrollbar">
                <form action="{{ route('categories.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Category Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        @error('name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('addCategoryModal')"
                            class="px-6 py-2 rounded-lg border-2 border-gray-300 bg-transparent text-gray-700 hover:bg-gray-50 w-full sm:w-auto transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">
                            Add Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editCategoryModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('editCategoryModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-5 rounded-t-2xl flex-shrink-0">
                <h2 class="text-xl font-semibold">Edit Category</h2>
            </header>

            <div class="overflow-y-auto flex-1 modal-scrollbar">
                <form id="editCategoryForm" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700">Category Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit_name" required
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('editCategoryModal')"
                            class="px-6 py-2 rounded-lg border-2 border-gray-300 bg-transparent text-gray-700 hover:bg-gray-50 w-full sm:w-auto transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">
                            Update Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Category Modal -->
    <div id="categoryShowModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('categoryShowModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-5 rounded-t-2xl flex-shrink-0 flex justify-between items-center">
                <h2 class="text-xl font-semibold">Category Details</h2>
                <button onclick="closeModal('categoryShowModal')" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <div id="categoryShowContent" class="overflow-y-auto flex-1 modal-scrollbar p-6">
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
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

    // Edit Category
    function editCategory(categoryId) {
        fetch(`/categories/${categoryId}/edit`)
            .then(res => {
                if (!res.ok) throw new Error('Failed to fetch category data');
                return res.json();
            })
            .then(category => {
                document.getElementById('edit_name').value = category.name;
                document.getElementById('editCategoryForm').action = `/categories/${category.category_id}`;
                openModal('editCategoryModal');
            })
            .catch(error => {
                console.error('Error:', error);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to load category data');
                } else {
                    alert('Failed to load category data');
                }
            });
    }

    // Show Category
    function showCategory(categoryId) {
        openModal('categoryShowModal');

        document.getElementById('categoryShowContent').innerHTML = `
            <div class="flex justify-center items-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
            </div>
        `;

        fetch(`/categories/${categoryId}`)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(category => {
                renderCategoryDetails(category);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('categoryShowContent').innerHTML = `
                    <div class="text-center py-12">
                        <p class="text-red-600">Error loading category details</p>
                    </div>
                `;
                if (typeof toastr !== 'undefined') {
                    toastr.error('Failed to load category details');
                }
            });
    }

    function renderCategoryDetails(category) {
        const content = document.getElementById('categoryShowContent');

        let html = `
            <div class="space-y-6">
                <div class="bg-gray-50 rounded-lg p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">${category.name}</h3>
                            <p class="text-sm text-gray-500 mt-1">Category ID: #${category.category_id}</p>
                        </div>
                    </div>

                    ${category.created_at ? `
                        <div class="border-t border-gray-200 pt-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Created At</p>
                                    <p class="text-gray-800 font-medium">${new Date(category.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                                </div>
                                ${category.updated_at ? `
                                    <div>
                                        <p class="text-sm text-gray-500">Last Updated</p>
                                        <p class="text-gray-800 font-medium">${new Date(category.updated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    ` : ''}

                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <button onclick="closeModal('categoryShowModal'); editCategory('${category.category_id}');"
                                class="w-full px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Edit Category
                        </button>
                    </div>
                </div>
            </div>
        `;

        content.innerHTML = html;
    }

    // Delete Modal with SweetAlert
    function openDeleteModal(actionUrl) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'swal-custom-popup',
                    confirmButton: 'swal-confirm-btn',
                    cancelButton: 'swal-cancel-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    submitDeleteForm(actionUrl);
                }
            });
        } else {
            if (confirm('Are you sure you want to delete this category?')) {
                submitDeleteForm(actionUrl);
            }
        }
    }

    function submitDeleteForm(actionUrl) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = actionUrl;

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
    document.addEventListener('DOMContentLoaded', function() {
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
    </script>

@endsection
