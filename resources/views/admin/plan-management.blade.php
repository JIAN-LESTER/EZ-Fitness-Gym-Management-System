@extends('layouts.app')

@section('content')
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

        <!-- Header & Add Button -->
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-2xl font-bold text-gray-800">Membership Plans</h2>
            <button onclick="openModal('addPlanModal')"
                class="flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-xl transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Plan
            </button>
        </div>

        <!-- Search Section -->
        <div class="p-4 sm:p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
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
                            class="pl-10 w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-gray-500 bg-white text-gray-900 placeholder-gray-400 shadow-sm transition-all">
                    </div>
                </div>
                <button type="submit"
                    class="flex items-center gap-2 bg-gray-700 hover:bg-gray-800 text-white px-6 py-3 rounded-xl shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Search
                </button>
                @if(request()->query())
                    <a href="{{ route('admin.plan_management') }}"
                        class="flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-xl shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Plan
                            Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Details
                                </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Price
                            </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Duration (Days)</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total
                            Members</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($plans as $plan)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-900 font-medium">{{ $plan->name }}</td>
                            <td class="px-6 py-4 text-gray-900 font-medium">{{ $plan->details }}</td>
                            <td class="px-6 py-4 text-gray-700 font-medium">₱{{ number_format($plan->price) }}</td>
                            <td class="px-6 py-4 text-gray-700 font-medium">{{ $plan->duration_days }}</td>
                            <td class="px-6 py-4 text-gray-700 font-medium">{{ $plan->members_count }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button onclick='editPlan(@json($plan))'
                                        class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold transition-all">
                                        Edit
                                    </button>
                                    <button onclick='openDeleteModal(@json($plan))'
                                        class="px-4 py-2 rounded-xl bg-red-100 hover:bg-red-200 text-red-700 font-semibold transition-all">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-lg font-medium">No plans found</p>
                                    <p class="text-sm mt-1">Try adjusting your search criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($plans->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $plans->links() }}
            </div>
        @endif
    </div>

    <!-- Add Plan Modal -->
    <div id="addPlanModal"
        class="hidden fixed inset-0 backdrop-blur z-50 flex items-center justify-center text-gray-800 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-800">Add New Plan</h3>
                <button onclick="closeModal('addPlanModal')" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('plans.store') }}" class="p-6 space-y-4">
                @csrf
                <div>
                    <label for="add_name" class="block text-sm font-semibold text-gray-700 mb-2">Plan Name</label>
                    <input type="text" name="name" id="add_name" required
                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all"
                        placeholder="e.g., Basic Plan">
                </div>
                <div>
                    <label for="add_details" class="block text-sm font-semibold text-gray-700 mb-2">Details</label>
                    <input type="text" name="details" id="add_details" required
                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all"
                        placeholder="e.g., This plan is good for 1 year">
                </div>
                <div>
                    <label for="add_price" class="block text-sm font-semibold text-gray-700 mb-2">Price (₱)</label>
                    <input type="number" name="price" id="add_price" step="0.01" min="0" required
                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all"
                        placeholder="0.00">
                </div>
                <div>
                    <label for="add_duration" class="block text-sm font-semibold text-gray-700 mb-2">Duration (Days)</label>
                    <input type="number" name="duration_days" id="add_duration" min="1" required
                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all"
                        placeholder="30">
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal('addPlanModal')"
                        class="flex-1 px-4 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold transition-all">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-3 rounded-xl bg-gray-700 hover:bg-gray-800 text-white font-semibold transition-all">
                        Add Plan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Plan Modal -->
    <div id="editPlanModal"
        class="hidden fixed inset-0 backdrop-blur z-50 flex items-center justify-center text-gray-800 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-800">Edit Plan</h3>
                <button onclick="closeModal('editPlanModal')" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editPlanForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_name" class="block text-sm font-semibold text-gray-700 mb-2">Plan Name</label>
                    <input type="text" name="name" id="edit_name" required
                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all">
                </div>
                <div>
                    <label for="edit_details" class="block text-sm font-semibold text-gray-700 mb-2">Details</label>
                    <input type="text" name="details" id="edit_details" required
                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all">
                </div>
                <div>
                    <label for="edit_price" class="block text-sm font-semibold text-gray-700 mb-2">Price (₱)</label>
                    <input type="number" name="price" id="edit_price" step="0.01" min="0" required
                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all">
                </div>
                <div>
                    <label for="edit_duration" class="block text-sm font-semibold text-gray-700 mb-2">Duration
                        (Days)</label>
                    <input type="number" name="duration_days" id="edit_duration" min="1" required
                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all">
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeModal('editPlanModal')"
                        class="flex-1 px-4 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold transition-all">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-3 rounded-xl bg-gray-700 hover:bg-gray-800 text-white font-semibold transition-all">
                        Update Plan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deletePlanModal"
        class="hidden fixed inset-0 backdrop-blur z-50 text-gray-800 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-800">Confirm Deletion</h3>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex-shrink-0">
                        <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-gray-900 font-medium">Are you sure you want to delete this plan?</p>
                        <p class="text-sm text-gray-600 mt-1" id="deletePlanName"></p>
                        <p class="text-sm text-red-600 mt-2">This action cannot be undone.</p>
                    </div>
                </div>
                <form id="deletePlanForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex gap-3">
                        <button type="button" onclick="closeModal('deletePlanModal')"
                            class="flex-1 px-4 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold transition-all">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold transition-all">
                            Delete Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function editPlan(plan) {
            document.getElementById('edit_name').value = plan.name;
            document.getElementById('edit_details').value = plan.details;
            document.getElementById('edit_price').value = plan.price;
            document.getElementById('edit_duration').value = plan.duration_days;
            document.getElementById('editPlanForm').action = `/admin/plans/${plan.plan_id}`;
            openModal('editPlanModal');
        }

        function openDeleteModal(plan) {
            document.getElementById('deletePlanName').textContent = `Plan: ${plan.name} (₱${parseFloat(plan.price).toFixed(2)})`;
            document.getElementById('deletePlanForm').action = `/xadmin/plans/${plan.plan_id}`;
            openModal('deletePlanModal');
        }

   

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