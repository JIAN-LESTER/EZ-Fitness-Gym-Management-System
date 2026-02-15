@extends('layouts.app')

@section('title', 'Accounts | EZ Fitness')
@section('header', 'Account Management')

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

    /* Clickable Row */
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .clickable-row:hover {
        background-color: #f9fafb;
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
    @php
        $isStaff = auth()->user()->role === 'staff';
        $isSuperAdmin = auth()->user()->role === 'super_admin';
        $isAdmin = auth()->user()->role === 'admin';
        $currentAuthId = auth()->id();
    @endphp

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

        <!-- Header / Add Button -->
        <div
            class="flex justify-between items-center p-4 sm:p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">Account Management</h2>
            <button onclick="openModal('addUserModal')"
                class="flex items-center gap-2 bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-700 shadow-md hover:shadow-lg transition-all duration-300 font-semibold whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add {{ $isStaff ? 'Member' : 'User' }}
            </button>
        </div>

        <!-- Search & Filters -->
        <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-200">
            @if ($isStaff)
                <form method="GET" action="{{ route('staff.user_management') }}" class="space-y-4" role="search"></form>
            @else
                <form method="GET" action="{{ route('admin.user_management') }}" class="space-y-4" role="search">
            @endif

                <div class="flex flex-wrap lg:flex-nowrap items-center gap-3">

                    <div class="flex-1 min-w-[200px]">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                placeholder="Search by name or username..."
                                class="pl-10 w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent bg-white text-gray-900 placeholder-gray-400 shadow-sm transition-all">
                        </div>
                    </div>

                    <!-- Filter Dropdown -->
                    <div class="relative w-full sm:w-auto">
                        <button type="button" onclick="toggleFilterDropdown()"
                            class="w-full sm:w-auto flex items-center justify-between gap-2 bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-700 shadow-sm transition-all duration-300 font-semibold whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filters
                            <span id="filterCount"
                                class="hidden ml-1 px-2 py-0.5 text-xs bg-blue-600 text-white rounded-full">0</span>
                            <svg class="w-4 h-4 transition-transform" id="filterDropdownIcon" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div id="filterDropdown"
                            class="hidden fixed mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-200 max-h-[calc(100vh-200px)] overflow-y-auto z-[9999]">
                            <div class="p-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Filter by Role</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="roles[]" value="member" {{ in_array('member', request('roles', [])) ? 'checked' : '' }} onchange="updateFilterCount()"
                                                class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Member</span>
                                            <span class="ml-auto px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded-full">Member</span>
                                        </label>

                                        <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="roles[]" value="staff" {{ in_array('staff', request('roles', [])) ? 'checked' : '' }} onchange="updateFilterCount()"
                                                class="w-4 h-4 text-purple-600 rounded focus:ring-2 focus:ring-purple-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Staff</span>
                                            <span class="ml-auto px-2 py-0.5 text-xs bg-purple-100 text-purple-700 rounded-full">Staff</span>
                                        </label>

                                        <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="roles[]" value="admin" {{ in_array('admin', request('roles', [])) ? 'checked' : '' }} onchange="updateFilterCount()"
                                                class="w-4 h-4 text-red-600 rounded focus:ring-2 focus:ring-red-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Admin</span>
                                            <span class="ml-auto px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">Admin</span>
                                        </label>

                                        @if($isSuperAdmin)
                                            <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                                <input type="checkbox" name="roles[]" value="super_admin" {{ in_array('super_admin', request('roles', [])) ? 'checked' : '' }}
                                                    onchange="updateFilterCount()"
                                                    class="w-4 h-4 text-red-600 rounded focus:ring-2 focus:ring-red-500">
                                                <span class="ml-3 text-sm font-medium text-gray-700">Super Admin</span>
                                                <span class="ml-auto px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">Super Admin</span>
                                            </label>
                                        @endif
                                    </div>
                                </div>

                                <div class="border-t border-gray-200"></div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Filter by Status</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="user_status[]" value="active" {{ in_array('active', request('user_status', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Active</span>
                                            <span class="ml-auto px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">Active</span>
                                        </label>

                                        <label class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="user_status[]" value="inactive" {{ in_array('inactive', request('user_status', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-4 h-4 text-gray-600 rounded focus:ring-2 focus:ring-gray-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Inactive</span>
                                            <span class="ml-auto px-2 py-0.5 text-xs bg-gray-200 text-gray-600 rounded-full">Inactive</span>
                                        </label>
                                    </div>
                                </div>

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

                    <div class="w-full sm:w-auto">
                        <button type="submit"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 bg-gray-800 text-white px-6 py-3 rounded-xl hover:bg-gray-700 shadow-md hover:shadow-lg transition-all duration-300 font-semibold whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Search
                        </button>
                    </div>

                    @if(request('search') || request('roles') || request('user_status'))
                        <div class="w-full sm:w-auto">
                            <a href="{{ route('admin.user_management') }}"
                                class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 transition-all duration-300 font-semibold shadow-md whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear Filters
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Plan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date Created</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">

@forelse($users as $user)
    {{-- Skip non-members if user is staff --}}
    @if($isStaff && $user->role !== 'member')
        @continue
    @endif

   @php
    $isOwnAccount = ($user->user_id == $currentAuthId);

    // Determine member status
    $needsApproval = $user->member && (
        $user->member->needsApproval()
        || (!$user->member->isApproved && !$user->member->isDisabled && !$user->member->isDisabledForSubscription)
    );
    $isDenied = $user->member && ($user->member->isDisabled || $user->member->isDisabledForSubscription);
    $isSuspended = $user->member && $user->member->subscription_status === 'suspended';
    $isCancelled = $user->member && ($user->member->subscription_status === 'cancelled' || $user->member->status === 'cancelled');
    $isRenewalPending = $user->member && $user->member->renewal_pending;

    // Per-item expiry checks
    $planExpired = false;
    $subExpired  = false;
    $planDaysAgo = 0;
    $subDaysAgo  = 0;

    if ($user->role === 'member' && $user->member
        && $user->member->isApproved && $user->member->isApprovedForSubscription
        && !$isCancelled && !$isSuspended) {

        if ($user->member->end_date && now()->gt($user->member->end_date)) {
            $planExpired = true;
            $planDaysAgo = (int) now()->diffInDays($user->member->end_date);
        }
        if ($user->member->end_date_for_subscription && now()->gt($user->member->end_date_for_subscription)) {
            $subExpired = true;
            $subDaysAgo = (int) now()->diffInDays($user->member->end_date_for_subscription);
        }
    }

    $bothExpired = $planExpired && $subExpired;

    // Check if fully approved
    $isFullyApproved = $user->role === 'member'
        && $user->member
        && $user->member->isFullyApproved();

    // Determine user status for display
    $displayStatus = $user->status;
    if ($user->role === 'member' && $user->member) {
        if ($isCancelled) {
            $displayStatus = 'cancelled';
        } elseif ($isSuspended) {
            $displayStatus = 'suspended';
        } elseif ($user->member->isApproved && $user->member->isApprovedForSubscription) {
            if ($bothExpired) {
                $displayStatus = 'both_expired';
            } elseif ($planExpired) {
                $displayStatus = 'plan_expired';
            } elseif ($subExpired) {
                $displayStatus = 'sub_expired';
            } else {
                $displayStatus = 'active';
            }
        } else {
            $displayStatus = 'inactive';
        }
    }

    // Determine plan display
    $planDisplay = 'Not a member';
    if ($user->role === 'member' && $user->member) {
        if ($isCancelled) {
            $planDisplay = 'cancelled';
        } elseif ($bothExpired) {
            $planDisplay = 'both_expired';
        } elseif ($planExpired) {
            $planDisplay = 'plan_expired';
        } elseif ($subExpired) {
            $planDisplay = 'sub_expired';
        } elseif ($user->member->isApproved && $user->member->isApprovedForSubscription && $user->member->subscription) {
            $planDisplay = 'active_subscription';
        } elseif (!$user->member->isApproved || !$user->member->isApprovedForSubscription) {
            $planDisplay = 'incomplete';
        }
    }

    // canDelete logic unchanged
    $canDelete = false;
    if (!$isOwnAccount && $user->role !== 'super_admin') {
        if ($isSuperAdmin) {
            $canDelete = true;
        } elseif ($isAdmin) {
            $canDelete = in_array($user->role, ['member', 'staff']);
        } elseif ($isStaff) {
            $canDelete = $user->role === 'member';
        }
    }
@endphp

    <tr class="clickable-row hover:bg-gray-50 transition-colors group"
        onclick="showUser('{{ $user->user_id }}')">

        @if($needsApproval)
            {{-- PENDING APPROVAL ROW --}}
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-yellow-200 flex items-center justify-center text-gray-800 font-bold text-sm shadow-md">
                        {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
                        <p class="text-gray-500 text-sm truncate">{{ $user->email }}</p>
                    </div>
                </div>
            </td>

            <td class="px-6 py-4 text-gray-700 font-medium">{{ $user->username }}</td>

            <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Member</span>
            </td>

            <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Pending Approval</span>
            </td>

            <td class="px-6 py-4">
                <div class="text-sm">
                    <p class="font-medium text-gray-900">{{ $user->member->plan->name ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-500">₱{{ number_format($user->member->plan->price ?? 0, 2) }}</p>
                    @if($user->member->subscription)
                        <p class="text-xs text-blue-600 mt-1">+ {{ $user->member->subscription->name }}</p>
                    @endif
                </div>
            </td>

            <td class="px-6 py-4 text-sm text-gray-500">
                {{ $user->created_at->format('M d, Y') }}
            </td>

            {{-- APPROVE/DENY BUTTONS --}}
            <td class="px-6 py-4" onclick="event.stopPropagation()">
                <div class="flex items-center justify-center gap-2">
                    <button onclick="approveSubscription('{{ $user->member->member_id }}')"
                        class="group flex items-center gap-1.5 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 font-semibold text-sm transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Approve</span>
                    </button>

                    <button onclick="denyMember('{{ $user->member->member_id }}')"
                        class="group flex items-center gap-1.5 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 font-semibold text-sm transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Deny</span>
                    </button>
                </div>
            </td>

        @elseif($isDenied)
            {{-- DENIED STATUS ROW --}}
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-200 flex items-center justify-center text-gray-800 font-bold text-sm shadow-md">
                        {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
                        <p class="text-gray-500 text-sm truncate">{{ $user->email }}</p>
                    </div>
                </div>
            </td>

            <td class="px-6 py-4 text-gray-700 font-medium">{{ $user->username }}</td>

            <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Member</span>
            </td>

            <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Denied</span>
            </td>

            <td class="px-6 py-4">
                <span class="text-red-600 text-sm font-medium">Membership Disabled</span>
            </td>

            <td class="px-6 py-4 text-sm text-gray-500">
                {{ $user->created_at->format('M d, Y') }}
            </td>

            {{-- DELETE BUTTON (only if permitted) --}}
            <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                @if($canDelete)
                    <button onclick="openDeleteModal('{{ route('admin.users-destroy', $user->user_id) }}', '{{ $user->first_name }} {{ $user->last_name }}')"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3" />
                        </svg>
                        <span class="text-sm font-medium">Delete</span>
                    </button>
                @else
                    <span class="text-gray-300 text-xs">—</span>
                @endif
            </td>

        @else
            {{-- NORMAL/ACTIVE/INACTIVE/SUSPENDED/CANCELLED USERS --}}
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full {{ $isOwnAccount ? 'bg-blue-200' : 'bg-gray-200' }} flex items-center justify-center text-gray-800 font-bold text-sm shadow-md">
                        {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? 'N', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 truncate">
                            {{ $user->first_name }} {{ $user->last_name }}
                            @if($isOwnAccount)
                                <span class="ml-1 text-xs text-blue-600 font-medium">(You)</span>
                            @endif
                        </p>
                        <p class="text-gray-500 text-sm truncate">{{ $user->email }}</p>
                    </div>
                </div>
            </td>

            <td class="px-6 py-4 text-gray-700 font-medium">{{ $user->username }}</td>

            <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full
                    {{ $user->role === 'super_admin' ? 'bg-red-100 text-red-700' :
                        ($user->role === 'admin' ? 'bg-orange-100 text-orange-700' :
                            ($user->role === 'staff' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700')) }}">
                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                </span>
            </td>

  <td class="px-6 py-4">
    @if($displayStatus === 'active')
        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Active</span>
    @elseif($displayStatus === 'suspended')
        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">Suspended</span>
    @elseif($displayStatus === 'cancelled')
        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">Cancelled</span>
    @elseif($displayStatus === 'both_expired')
        <div class="flex flex-col gap-0.5">
            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700 w-fit">Expired</span>
            <span class="text-xs text-red-500 font-medium">Plan &amp; Sub expired</span>
        </div>
    @elseif($displayStatus === 'plan_expired')
        <div class="flex flex-col gap-0.5">
            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700 w-fit">Expired</span>
            <span class="text-xs text-red-500 font-medium">Plan expired</span>
        </div>
    @elseif($displayStatus === 'sub_expired')
        <div class="flex flex-col gap-0.5">
            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700 w-fit">Expired</span>
            <span class="text-xs text-amber-600 font-medium">Sub expired</span>
        </div>
    @else
        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-600">{{ ucfirst($displayStatus) }}</span>
    @endif
</td>

            <td class="px-6 py-4 text-gray-700">
    @if($user->role === 'member' && $user->member)
        @if($planDisplay === 'cancelled')
            <span class="text-purple-600 text-sm font-medium">Plan Cancelled</span>

        @elseif($planDisplay === 'both_expired')
            <div class="text-sm space-y-1">
                <div>
                    <p class="font-medium text-red-600">{{ $user->member->plan->name ?? 'Plan' }}</p>
                    <p class="text-xs text-red-400">Expired {{ $planDaysAgo }} {{ $planDaysAgo == 1 ? 'day' : 'days' }} ago</p>
                </div>
                <div class="border-t border-gray-100 pt-1">
                    <p class="font-medium text-red-600">{{ $user->member->subscription->name ?? 'Sub' }}</p>
                    <p class="text-xs text-red-400">Expired {{ $subDaysAgo }} {{ $subDaysAgo == 1 ? 'day' : 'days' }} ago</p>
                </div>
            </div>

        @elseif($planDisplay === 'plan_expired')
            <div class="text-sm space-y-1">
                <div>
                    <p class="font-medium text-red-600">{{ $user->member->plan->name ?? 'Plan' }}</p>
                    <p class="text-xs text-red-400">Expired {{ $planDaysAgo }} {{ $planDaysAgo == 1 ? 'day' : 'days' }} ago</p>
                </div>
                @if($user->member->subscription)
                    <div class="border-t border-gray-100 pt-1">
                        <p class="text-xs text-gray-500">{{ $user->member->subscription->name }}</p>
                        <p class="text-xs text-green-600">Sub still active</p>
                    </div>
                @endif
            </div>

        @elseif($planDisplay === 'sub_expired')
            <div class="text-sm space-y-1">
                @if($user->member->plan)
                    <div>
                        <p class="font-medium text-gray-800">{{ $user->member->plan->name }}</p>
                        <p class="text-xs text-green-600">Plan still active</p>
                    </div>
                @endif
                <div class="border-t border-gray-100 pt-1">
                    <p class="font-medium text-amber-600">{{ $user->member->subscription->name ?? 'Subscription' }}</p>
                    <p class="text-xs text-amber-500">Expired {{ $subDaysAgo }} {{ $subDaysAgo == 1 ? 'day' : 'days' }} ago</p>
                </div>
            </div>

        @elseif($user->member->status === 'suspended')
            <div class="text-sm">
                <p class="font-medium text-orange-600">Suspended</p>
                <p class="text-xs text-gray-500">{{ $user->member->plan_days_remaining_before_suspend ?? 0 }} days paused</p>
                <p class="text-xs text-blue-600 mt-1">Plan: {{ $user->member->plan->name ?? 'N/A' }}</p>
            </div>

        @elseif($planDisplay === 'active_subscription')
            <div class="text-sm">
                <p class="font-medium text-gray-900">{{ $user->member->subscription->name ?? 'N/A' }}</p>
                <p class="text-xs text-gray-500">
                    @php
                        $now = now();
                        $end = $user->member->end_date_for_subscription;
                        $days = $end ? (int) $now->diffInDays($end, false) : null;
                    @endphp
                    @if($isSuspended)
                        <span class="text-orange-600">Suspended ({{ $user->member->days_remaining_before_suspend ?? 0 }} days paused)</span>
                    @elseif($days !== null && $days >= 1)
                        {{ $days }} {{ $days == 1 ? 'day' : 'days' }} left
                    @elseif($days === 0)
                        Expires today
                    @else
                        Expired
                    @endif
                </p>
                <p class="text-xs text-blue-600 mt-1">w/ {{ $user->member->plan->name ?? 'Plan' }}</p>
            </div>

        @elseif($planDisplay === 'incomplete')
            <span class="text-orange-600 text-sm font-medium">Incomplete Profile</span>

        @else
            <span class="text-gray-400 text-xs">No Plan</span>
        @endif
    @else
        <span class="text-gray-400 text-xs">Not a member</span>
    @endif
</td>

            <td class="px-6 py-4">
                <div class="flex flex-col">
                    <span class="text-gray-900 font-medium">{{ $user->created_at->format('M d, Y') }}</span>
                    <span class="text-gray-500 text-sm">{{ $user->created_at->format('h:i A') }}</span>
                </div>
            </td>

            {{-- 3-DOT MENU WITH DYNAMIC OPTIONS --}}
            <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                <div class="relative inline-block text-left">
                    <button onclick="toggleActionsMenu(event, '{{ $user->user_id }}')"
                        class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                    </button>

                    <div id="actionsMenu-{{ $user->user_id }}"
                        class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                        <div class="py-1">
                            <!-- Edit Button -->
                            <button onclick="editUser('{{ $user->user_id }}')"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.828a2 2 0 01-1.414.586H9v-2a2 2 0 01.586-1.414z" />
                                </svg>
                                Edit User
                            </button>

                            @if($user->role === 'member' && $user->member)
                                @php
                                    $isActive = $user->member->isApproved
                                        && $user->member->isApprovedForSubscription
                                        && in_array($user->member->subscription_status, ['active', 'subscribed']);
                                @endphp

                                @if($isSuspended)
                                    <button onclick="resumeMember('{{ $user->member->member_id }}')"
                                        class="w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-gray-100 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Resume
                                    </button>
                                @elseif($isActive)
                                    <button onclick="suspendMember('{{ $user->member->member_id }}')"
                                        class="w-full text-left px-4 py-2 text-sm text-orange-600 hover:bg-gray-100 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Suspend
                                    </button>

                                    <button onclick="cancelPlan('{{ $user->member->member_id }}')"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Cancel Plan
                                    </button>
                                @endif
                            @endif

                            <!-- Delete Button (only if permitted) -->
                            @if($canDelete)
                                <button onclick="openDeleteModal('{{ route('admin.users-destroy', $user->user_id) }}', '{{ $user->first_name }} {{ $user->last_name }}')"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3" />
                                    </svg>
                                    Delete User
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </td>
        @endif
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
            <div class="flex flex-col items-center justify-center">
                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-lg font-medium">No {{ $isStaff ? 'members' : 'users' }} found</p>
                <p class="text-sm mt-1">Try adjusting your search or filter criteria</p>
            </div>
        </td>
    </tr>
@endforelse

                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->total() > 0)
            <div class="flex flex-col sm:flex-row items-center justify-between p-4 sm:p-6 bg-gray-50 border-t border-gray-200 gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-semibold text-gray-900">{{ $users->firstItem() }}</span> to
                    <span class="font-semibold text-gray-900">{{ $users->lastItem() }}</span> of
                    <span class="font-semibold text-gray-900">{{ $users->total() }}</span> users
                </div>

                <div class="flex gap-2">
                    @if($users->onFirstPage())
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Prev</span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}"
                            class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium">Prev</a>
                    @endif

                    <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium sm:hidden">
                        {{ $users->currentPage() }} / {{ $users->lastPage() }}
                    </span>

                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}"
                            class="px-4 py-2 rounded-xl bg-gray-800 text-white hover:bg-gray-700 shadow-md transition-all duration-200 font-medium">Next</a>
                    @else
                        <span class="px-4 py-2 rounded-xl bg-gray-200 text-gray-400 cursor-not-allowed font-medium">Next</span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('addUserModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-5 rounded-t-2xl flex-shrink-0">
                <h2 class="text-xl font-semibold">Add New {{ $isStaff ? 'Member' : 'User' }}</h2>
            </header>

            <div class="overflow-y-auto flex-1 modal-scrollbar">
                <form action="{{ route('admin.users-store') }}" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf

                    {{-- Hidden role field for staff --}}
                    @if($isStaff)
                        <input type="hidden" name="role" value="member">
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" name="first_name" id="first_name"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            @error('first_name')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" name="last_name" id="last_name"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            @error('last_name')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" id="username"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        @error('username')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email"
                            class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" id="password"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            @error('password')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        </div>
                    </div>

                    {{-- Only show role selector for non-staff --}}
                    @if(!$isStaff)
                        <div>
                            <label for="add_branch" class="block text-sm font-medium text-gray-700 mb-2">Branch <span class="text-red-500">*</span></label>
                            @if(auth()->user()->role === 'super_admin')
                                <select name="branch_id" id="add_branch_id" required onchange="onAddBranchChange()"
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 text-gray-800 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->branch_id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" value="{{ auth()->user()->branch->name ?? 'N/A' }}" disabled
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-gray-100 px-4 py-2 text-gray-600">
                                <input type="hidden" name="branch_id" id="add_branch_id" value="{{ auth()->user()->branch_id }}">
                            @endif
                            @error('branch_id')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                            <select name="role" id="role" onchange="toggleMemberFields('add')"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="member">Member</option>
                                <option value="staff">Staff</option>
                                @if($isSuperAdmin)
                                    <option value="admin">Admin</option>
                                    <option value="super_admin">Super Admin</option>
                                @endif
                            </select>
                        </div>
                    @else
                        {{-- Staff: branch display only, role hidden --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Branch</label>
                            <input type="text" value="{{ auth()->user()->branch->name ?? 'N/A' }}" disabled
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-gray-100 px-4 py-2 text-gray-600">
                            <input type="hidden" name="branch_id" id="add_branch_id" value="{{ auth()->user()->branch_id }}">
                        </div>
                    @endif

                    {{-- Member fields: shown only when role=member AND branch is selected --}}
                    <div id="addMemberFields" class="space-y-4 pt-4 border-t-2 border-gray-100" style="{{ $isStaff ? 'display: block;' : 'display: none;' }}">
                        <h3 class="text-sm font-semibold text-gray-700">
                            Member Profile {{ $isStaff ? '(Required)' : '(Optional)' }}
                        </h3>

                        <div>
                            <label for="plan_id" class="block text-sm font-medium text-gray-700">
                                Membership Plan
                                @if($isStaff)<span class="text-red-500">*</span>@endif
                            </label>
                            <select name="plan_id" id="plan_id" {{ $isStaff ? 'required' : '' }}
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="">Select a plan{{ $isStaff ? '' : ' (optional)' }}</option>
                                @foreach($plans ?? [] as $plan)
                                    <option value="{{ $plan->plan_id }}">{{ $plan->name }} - ₱{{ number_format($plan->price, 2) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="subscription_id" class="block text-sm font-medium text-gray-700">
                                Subscription
                                @if($isStaff)<span class="text-red-500">*</span>@endif
                            </label>
                            <select name="subscription_id" id="subscription_id" {{ $isStaff ? 'required' : '' }}
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="">Select a subscription{{ $isStaff ? '' : ' (optional)' }}</option>
                                @php
                                    $subscriptions = \App\Models\Subscriptions::all();
                                @endphp
                                @foreach($subscriptions ?? [] as $subscription)
                                    <option value="{{ $subscription->subscription_id }}">{{ $subscription->name }} -
                                        ₱{{ number_format($subscription->price, 2) }} / {{ $subscription->duration_days }} days
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="sex" class="block text-sm font-medium text-gray-700">
                                    Sex
                                    @if($isStaff)<span class="text-red-500">*</span>@endif
                                </label>
                                <select name="sex" id="sex" {{ $isStaff ? 'required' : '' }}
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                    <option value="">Select...</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div>
                                <label for="birthday" class="block text-sm font-medium text-gray-700">
                                    Birthday
                                    @if($isStaff)<span class="text-red-500">*</span>@endif
                                </label>
                                <input type="date" name="birthday" id="birthday" {{ $isStaff ? 'required' : '' }}
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700">Height (cm)</label>
                                <input type="number" step="0.1" name="height" id="height"
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            </div>
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                                <input type="number" step="0.1" name="weight" id="weight"
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            </div>
                        </div>

                        <div>
                            <label for="mobile_number" class="block text-sm font-medium text-gray-700">
                                Mobile Number
                                @if($isStaff)<span class="text-red-500">*</span>@endif
                            </label>
                            <input type="tel" name="mobile_number" id="mobile_number" placeholder="e.g. 09123456789" {{ $isStaff ? 'required' : '' }}
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        </div>
                    </div>

                    {{-- Payment Method Selection --}}
                    <div id="paymentSection" class="hidden space-y-4 pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700">Payment Details</h3>

                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <select name="payment_method" id="payment_method"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="cash">Cash</option>
                                <option value="gcash">GCash</option>
                            </select>
                        </div>

                        <div id="referenceCodeDiv" class="hidden">
                            <label for="reference_code" class="block text-sm font-medium text-gray-700">
                                GCash Reference Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="reference_code" id="reference_code" placeholder="e.g., 1234567890123"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-blue-800">Payment will be recorded and QR code will be generated automatically.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('addUserModal')"
                            class="px-6 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 w-full sm:w-auto">Cancel</button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">Add {{ $isStaff ? 'Member' : 'User' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit User Modal --}}
    <div id="editUserModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('editUserModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-5 rounded-t-2xl flex-shrink-0">
                <h2 class="text-xl font-semibold">Edit {{ $isStaff ? 'Member' : 'User' }}</h2>
            </header>

            <div class="overflow-y-auto flex-1 modal-scrollbar">
                <form id="editUserForm" method="POST" class="p-6 md:p-8 space-y-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editUserId">

                    {{-- Hidden role field for staff --}}
                    @if($isStaff)
                        <input type="hidden" name="role" value="member">
                    @endif

                    <!-- Basic Information Section -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" id="edit_first_name" required
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            </div>
                            <div>
                                <label for="edit_last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" id="edit_last_name" required
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            </div>
                        </div>

                        <div>
                            <label for="edit_username" class="block text-sm font-medium text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="username" id="edit_username" required
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        </div>

                        <div>
                            <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="edit_email" required
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_password" class="block text-sm font-medium text-gray-700 mb-2">New Password (Optional)</label>
                                <input type="password" name="password" id="edit_password"
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            </div>
                            <div>
                                <label for="edit_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="edit_password_confirmation"
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            </div>
                        </div>

                        @if(!$isStaff)
                            <div>
                                <label for="edit_branch_id" class="block text-sm font-medium text-gray-700 mb-2">Branch <span class="text-red-500">*</span></label>
                                @if(auth()->user()->role === 'super_admin')
                                    <select name="branch_id" id="edit_branch_id" required onchange="onEditBranchChange()"
                                        class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 text-gray-800 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->branch_id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" value="{{ auth()->user()->branch->name ?? 'N/A' }}" disabled
                                        class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-gray-100 px-4 py-2 text-gray-600">
                                    <input type="hidden" name="branch_id" id="edit_branch_id" value="{{ auth()->user()->branch_id }}">
                                @endif
                            </div>

                            <div>
                                <label for="edit_role" class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                                <select name="role" id="edit_role" onchange="toggleMemberFields('edit')"
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                    <option value="member">Member</option>
                                    <option value="staff">Staff</option>
                                    @if($isSuperAdmin)
                                        <option value="admin">Admin</option>
                                        <option value="super_admin">Super Admin</option>
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label for="edit_status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                                <select name="status" id="edit_status"
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        @else
                            {{-- Staff: branch display only --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Branch</label>
                                <input type="text" value="{{ auth()->user()->branch->name ?? 'N/A' }}" disabled
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-gray-100 px-4 py-2 text-gray-600">
                                <input type="hidden" name="branch_id" id="edit_branch_id" value="{{ auth()->user()->branch_id }}">
                            </div>
                        @endif
                    </div>

                    {{-- COLLAPSIBLE MEMBER FIELDS SECTION --}}
                    <div id="editMemberFieldsContainer" class="border-t-2 border-gray-200 pt-4" style="{{ $isStaff ? 'display: block;' : '' }}">
                        <!-- Collapsible Header -->
                        <button type="button" onclick="toggleEditMemberSection()"
                            class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <div class="text-left">
                                    <h3 class="text-sm font-semibold text-gray-800">Member Profile {{ $isStaff ? '(Required)' : '(Optional)' }}</h3>
                                    <p class="text-xs text-gray-500">Add membership plan and personal details</p>
                                </div>
                            </div>
                            <svg id="editMemberSectionIcon" class="w-5 h-5 text-gray-600 transform transition-transform {{ $isStaff ? 'rotate-180' : '' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Collapsible Content -->
                        <div id="editMemberFieldsContent" class="space-y-4 mt-4 {{ $isStaff ? '' : 'hidden' }}">
                            <div>
                                <label for="edit_plan_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Membership Plan
                                    @if($isStaff)<span class="text-red-500">*</span>@endif
                                </label>
                                <select name="plan_id" id="edit_plan_id" {{ $isStaff ? 'required' : '' }}
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                    <option value="">Select a plan{{ $isStaff ? '' : ' (optional)' }}</option>
                                    @foreach($plans ?? [] as $plan)
                                        <option value="{{ $plan->plan_id }}">{{ $plan->name }} - ₱{{ number_format($plan->price, 2) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="edit_sex" class="block text-sm font-medium text-gray-700 mb-2">
                                        Sex
                                        @if($isStaff)<span class="text-red-500">*</span>@endif
                                    </label>
                                    <select name="sex" id="edit_sex" {{ $isStaff ? 'required' : '' }}
                                        class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                        <option value="">Select...</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="edit_birthday" class="block text-sm font-medium text-gray-700 mb-2">
                                        Birthday
                                        @if($isStaff)<span class="text-red-500">*</span>@endif
                                    </label>
                                    <input type="date" name="birthday" id="edit_birthday" {{ $isStaff ? 'required' : '' }}
                                        class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="edit_height" class="block text-sm font-medium text-gray-700 mb-2">Height (cm)</label>
                                    <input type="number" step="0.1" name="height" id="edit_height"
                                        class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                </div>
                                <div>
                                    <label for="edit_weight" class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                                    <input type="number" step="0.1" name="weight" id="edit_weight"
                                        class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                </div>
                            </div>

                            <div>
                                <label for="edit_mobile_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Mobile Number
                                    @if($isStaff)<span class="text-red-500">*</span>@endif
                                </label>
                                <input type="tel" name="mobile_number" id="edit_mobile_number" placeholder="e.g. 09123456789" {{ $isStaff ? 'required' : '' }}
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            </div>

                            <div>
                                <label for="edit_subscription_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Subscription
                                    @if($isStaff)<span class="text-red-500">*</span>@endif
                                </label>
                                <select name="subscription_id" id="edit_subscription_id" {{ $isStaff ? 'required' : '' }} onchange="updateEditPaymentSection()"
                                    class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                    <option value="">Select a subscription{{ $isStaff ? '' : ' (optional)' }}</option>
                                    @php
                                        $subscriptions = \App\Models\Subscriptions::all();
                                    @endphp
                                    @foreach($subscriptions ?? [] as $subscription)
                                        <option value="{{ $subscription->subscription_id }}">{{ $subscription->name }} - ₱{{ number_format($subscription->price, 2) }} / {{ $subscription->duration_days }} days</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Payment Section --}}
                            <div id="editPaymentSection" class="hidden space-y-4 pt-4 border-t border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-700">Payment Details</h3>

                                <div>
                                    <label for="edit_payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                                        Payment Method <span class="text-red-500">*</span>
                                    </label>
                                    <select name="payment_method" id="edit_payment_method" onchange="toggleEditReferenceCode()"
                                        class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                        <option value="cash">Cash</option>
                                        <option value="gcash">GCash</option>
                                    </select>
                                </div>

                                <div id="editReferenceCodeDiv" class="hidden">
                                    <label for="edit_reference_code" class="block text-sm font-medium text-gray-700 mb-2">
                                        GCash Reference Code <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="reference_code" id="edit_reference_code" placeholder="e.g., 1234567890123"
                                        class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                </div>

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-sm text-blue-800">Payment will be recorded and QR code will be generated automatically.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('editUserModal')"
                            class="px-6 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 w-full sm:w-auto transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto transition-colors">
                            Update {{ $isStaff ? 'Member' : 'User' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Details Modal -->
    <div id="userShowModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('userShowModal')"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-800 text-white p-5 rounded-t-2xl flex-shrink-0 flex justify-between items-center">
                <h2 class="text-xl font-semibold">User Details</h2>
                <button onclick="closeModal('userShowModal')" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <div id="userShowContent" class="overflow-y-auto flex-1 modal-scrollbar p-6 bg-white">
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
                </div>
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

                <h3 class="text-xl font-bold text-center text-gray-900 mb-2">Delete User</h3>

                <p class="text-center text-gray-600 mb-6">
                    Are you sure you want to delete <span id="deleteUserName" class="font-bold text-red-600"></span>? This action cannot be undone.
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

    <!-- Cancel Plan Modal -->
    <div id="cancelPlanModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0 backdrop-blur bg-opacity-50" onclick="closeCancelPlanModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-center text-gray-900 mb-2">Cancel Plan</h3>

                <p class="text-center text-gray-600 mb-6">
                    Are you sure you want to cancel this member's plan? <strong>This action cannot be undone.</strong>
                </p>

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-6">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="text-sm text-amber-800">
                            <p class="font-semibold mb-1">What happens when you cancel:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Membership and subscription expire immediately</li>
                                <li>Member must renew to regain access</li>
                                <li>All remaining days will be lost</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form id="cancelPlanForm" method="GET" action="">
                    <div class="flex gap-3">
                        <button type="button" onclick="closeCancelPlanModal()"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-lg font-medium transition-colors">
                            Cancel Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Suspend Modal -->
    <div id="suspendModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0 backdrop-blur bg-opacity-50" onclick="closeSuspendModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-center text-gray-900 mb-2">Suspend Member</h3>

                <p class="text-center text-gray-600 mb-6">
                    Are you sure you want to suspend this member's subscription?
                </p>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-6">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">What happens when you suspend:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Member's remaining days are <strong>paused</strong></li>
                                <li>Subscription status set to "Suspended"</li>
                                <li>Days can be <strong>restored</strong> by resuming later</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form id="suspendForm" method="GET" action="">
                    <div class="flex gap-3">
                        <button type="button" onclick="closeSuspendModal()"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 text-white bg-orange-600 hover:bg-orange-700 rounded-lg font-medium transition-colors">
                            Suspend
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Resume Modal -->
    <div id="resumeModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0 backdrop-blur bg-opacity-50" onclick="closeResumeModal()"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-center text-gray-900 mb-2">Resume Member</h3>

                <p class="text-center text-gray-600 mb-6">
                    Resume this member's subscription and restore their remaining days?
                </p>

                <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-6">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-green-800">
                            <p class="font-semibold mb-1">What happens when you resume:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Subscription status set to "Active"</li>
                                <li>Paused days are <strong>restored</strong></li>
                                <li>Member regains full access</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form id="resumeForm" method="GET" action="">
                    <div class="flex gap-3">
                        <button type="button" onclick="closeResumeModal()"
                            class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 text-white bg-green-600 hover:bg-green-700 rounded-lg font-medium transition-colors">
                            Resume
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const isStaff = {{ $isStaff ? 'true' : 'false' }};
        const isSuperAdmin = {{ $isSuperAdmin ? 'true' : 'false' }};
        const isAdmin = {{ $isAdmin ? 'true' : 'false' }};

        // ===========================
        // ERROR HELPERS
        // ===========================

        const showError = (element, message) => {
            if (!element) return;
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
            if (!element) return;
            element.classList.remove('border-red-500');
            element.classList.add('border-gray-300');
            const container = element.closest('div');
            const errorSpan = container.querySelector('.error-message');
            if (errorSpan) errorSpan.remove();
        };

        const clearAllErrors = (form) => {
            if (!form) return;
            form.querySelectorAll('.error-message').forEach(el => el.remove());
            form.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
                el.classList.add('border-gray-300');
            });
        };

        // ===========================
        // MODAL FUNCTIONS
        // ===========================

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            const scrollY = window.scrollY;
            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollY}px`;
            document.body.style.width = '100%';
            document.body.style.overflowY = 'scroll';
            modal.classList.remove('hidden');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
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
                // Reset all text/email/password/number/date/tel inputs
                form.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="date"], input[type="tel"]').forEach(el => {
                    // Don't wipe hidden inputs that hold fixed values (branch_id for non-super_admin)
                    if (el.type !== 'hidden') el.value = '';
                });
                // Reset all selects to first option
                form.querySelectorAll('select').forEach(sel => { sel.selectedIndex = 0; });
            }

            // Extra reset for edit modal specifically
            if (modalId === 'editUserModal') {
                // Collapse member section
                const content = document.getElementById('editMemberFieldsContent');
                const icon = document.getElementById('editMemberSectionIcon');
                if (content && !isStaff) {
                    content.classList.add('hidden');
                    if (icon) icon.classList.remove('rotate-180');
                }
                // Hide payment section
                const paySection = document.getElementById('editPaymentSection');
                if (paySection) paySection.classList.add('hidden');
                // Hide reference code div
                const refDiv = document.getElementById('editReferenceCodeDiv');
                if (refDiv) refDiv.classList.add('hidden');
                // Reset member container visibility to default (shown for staff, hidden for others until role chosen)
                const memberContainer = document.getElementById('editMemberFieldsContainer');
                if (memberContainer && !isStaff) memberContainer.style.display = 'none';
                // Reset plan/subscription selects to blank
                const planSel = document.getElementById('edit_plan_id');
                if (planSel) planSel.innerHTML = '<option value="">Select a plan (optional)</option>';
                const subSel = document.getElementById('edit_subscription_id');
                if (subSel) subSel.innerHTML = '<option value="">Select a subscription (optional)</option>';
            }
        }

        // ===========================
        // TOGGLE MEMBER FIELDS
        // ===========================

        function toggleMemberFields(mode) {
            if (isStaff) return; // Staff always show member fields

            if (mode === 'add') {
                const roleSelect = document.getElementById('role');
                const branchSelect = document.getElementById('add_branch_id');
                const memberFields = document.getElementById('addMemberFields');
                if (!memberFields) return;

                const branchVal = branchSelect ? branchSelect.value : '{{ auth()->user()->branch_id }}';
                const isAdminOrSuperAdmin = !isSuperAdmin && !isAdmin; // non-super context
                const roleVal = roleSelect ? roleSelect.value : 'member';

                // Show member fields only when role is member AND branch is selected
                if (roleVal === 'member' && branchVal) {
                    memberFields.style.display = 'block';
                } else {
                    memberFields.style.display = 'none';
                }
            }

            if (mode === 'edit') {
                const roleSelect = document.getElementById('edit_role');
                const container = document.getElementById('editMemberFieldsContainer');
                if (!roleSelect || !container) return;
                container.style.display = roleSelect.value === 'member' ? 'block' : 'none';
            }
        }

        // Called when branch changes in ADD modal (super_admin only)
        function onAddBranchChange() {
            const branchSelect = document.getElementById('add_branch_id');
            const branchId = branchSelect ? branchSelect.value : null;

            // Reload plans and subscriptions for this branch
            if (branchId) {
                loadPlansByBranch(branchId, 'plan_id', 'subscription_id');
            } else {
                resetPlanSelect('plan_id');
                resetPlanSelect('subscription_id');
            }

            // Re-evaluate member fields visibility
            toggleMemberFields('add');
        }

        // Called when branch changes in EDIT modal (super_admin only)
        function onEditBranchChange() {
            const branchSelect = document.getElementById('edit_branch_id');
            const branchId = branchSelect ? branchSelect.value : null;
            if (branchId) {
                loadPlansByBranch(branchId, 'edit_plan_id', 'edit_subscription_id');
            }
        }

        function loadPlansByBranch(branchId, planSelectId, subSelectId) {
            fetch(`/admin/user_crud/plans-by-branch?branch_id=${branchId}`)
                .then(res => res.json())
                .then(data => {
                    // Populate plan select
                    const planSel = document.getElementById(planSelectId);
                    if (planSel) {
                        const curPlan = planSel.value;
                        planSel.innerHTML = '<option value="">Select a plan (optional)</option>';
                        (data.plans || []).forEach(p => {
                            const opt = document.createElement('option');
                            opt.value = p.plan_id;
                            opt.textContent = `${p.name} - ₱${parseFloat(p.price).toLocaleString('en-PH', {minimumFractionDigits: 2})}`;
                            if (String(p.plan_id) === String(curPlan)) opt.selected = true;
                            planSel.appendChild(opt);
                        });
                    }
                    // Populate subscription select
                    const subSel = document.getElementById(subSelectId);
                    if (subSel) {
                        const curSub = subSel.value;
                        subSel.innerHTML = '<option value="">Select a subscription (optional)</option>';
                        (data.subscriptions || []).forEach(s => {
                            const opt = document.createElement('option');
                            opt.value = s.subscription_id;
                            opt.textContent = `${s.name} - ₱${parseFloat(s.price).toLocaleString('en-PH', {minimumFractionDigits: 2})} / ${s.duration_days} days`;
                            if (String(s.subscription_id) === String(curSub)) opt.selected = true;
                            subSel.appendChild(opt);
                        });
                    }
                })
                .catch(err => console.error('Failed to load plans by branch:', err));
        }

        function resetPlanSelect(selectId) {
            const sel = document.getElementById(selectId);
            if (sel) sel.innerHTML = '<option value="">Select a plan (optional)</option>';
        }

        // ===========================
        // DELETE MODAL
        // ===========================

        function openDeleteModal(actionUrl, userName) {
            const form = document.getElementById('deleteForm');
            const nameSpan = document.getElementById('deleteUserName');
            form.action = actionUrl;
            nameSpan.textContent = userName;

            const scrollY = window.scrollY;
            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollY}px`;
            document.body.style.width = '100%';
            document.body.style.overflowY = 'scroll';

            document.getElementById('deleteModal').classList.remove('hidden');
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

        // ===========================
        // FILTER DROPDOWN
        // ===========================

        function toggleFilterDropdown() {
            const dropdown = document.getElementById('filterDropdown');
            const button = event?.target?.closest('button');
            const icon = document.getElementById('filterDropdownIcon');
            if (!dropdown || !button) return;
            const rect = button.getBoundingClientRect();
            dropdown.style.position = 'fixed';
            dropdown.style.left = rect.left + 'px';
            dropdown.style.top = (rect.bottom + 8) + 'px';
            dropdown.classList.toggle('hidden');
            if (icon) icon.classList.toggle('rotate-180');
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

        // ===========================
        // ACTIONS MENU
        // ===========================

        function toggleActionsMenu(event, userId) {
            event?.stopPropagation();

            document.querySelectorAll('[id^="actionsMenu-"]').forEach(menu => {
                if (menu.id !== `actionsMenu-${userId}`) menu.classList.add('hidden');
            });

            const menu = document.getElementById(`actionsMenu-${userId}`);
            const button = event?.target?.closest('button');

            if (menu && button) {
                const isHidden = menu.classList.contains('hidden');
                if (isHidden) {
                    const rect = button.getBoundingClientRect();
                    menu.style.position = 'fixed';
                    menu.style.top = `${rect.bottom + 8}px`;
                    menu.style.left = `${rect.right - 192}px`;
                    menu.style.zIndex = '9999';
                    menu.classList.remove('hidden');
                } else {
                    menu.classList.add('hidden');
                }
            }
        }

        function closeAllActionsMenus() {
            document.querySelectorAll('[id^="actionsMenu-"]').forEach(menu => menu.classList.add('hidden'));
        }

        // ===========================
        // ADD MODAL - PAYMENT SECTION
        // ===========================

        document.addEventListener('DOMContentLoaded', function () {
            const planSelect = document.getElementById('plan_id');
            const subscriptionSelect = document.getElementById('subscription_id');
            const paymentSection = document.getElementById('paymentSection');
            const paymentMethodSelect = document.getElementById('payment_method');
            const referenceCodeDiv = document.getElementById('referenceCodeDiv');

            function updatePaymentSection() {
                if (planSelect && subscriptionSelect && paymentSection) {
                    if (planSelect.value && subscriptionSelect.value) {
                        paymentSection.classList.remove('hidden');
                    } else {
                        paymentSection.classList.add('hidden');
                    }
                }
            }

            if (planSelect) planSelect.addEventListener('change', updatePaymentSection);
            if (subscriptionSelect) subscriptionSelect.addEventListener('change', updatePaymentSection);

            if (paymentMethodSelect) {
                paymentMethodSelect.addEventListener('change', function () {
                    if (this.value === 'gcash') {
                        referenceCodeDiv.classList.remove('hidden');
                        document.getElementById('reference_code').required = true;
                    } else {
                        referenceCodeDiv.classList.add('hidden');
                        document.getElementById('reference_code').required = false;
                    }
                });
            }
        });

        // ===========================
        // EDIT MODAL - MEMBER SECTION TOGGLE
        // ===========================

        function toggleEditMemberSection() {
            const content = document.getElementById('editMemberFieldsContent');
            const icon = document.getElementById('editMemberSectionIcon');
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        function updateEditPaymentSection() {
            const planSelect = document.getElementById('edit_plan_id');
            const subscriptionSelect = document.getElementById('edit_subscription_id');
            const paymentSection = document.getElementById('editPaymentSection');
            if (planSelect && subscriptionSelect && paymentSection) {
                if (planSelect.value && subscriptionSelect.value) {
                    paymentSection.classList.remove('hidden');
                } else {
                    paymentSection.classList.add('hidden');
                }
            }
        }

        function toggleEditReferenceCode() {
            const paymentMethod = document.getElementById('edit_payment_method');
            const referenceCodeDiv = document.getElementById('editReferenceCodeDiv');
            const referenceCodeInput = document.getElementById('edit_reference_code');
            if (paymentMethod && referenceCodeDiv && referenceCodeInput) {
                if (paymentMethod.value === 'gcash') {
                    referenceCodeDiv.classList.remove('hidden');
                    referenceCodeInput.required = true;
                } else {
                    referenceCodeDiv.classList.add('hidden');
                    referenceCodeInput.required = false;
                }
            }
        }

        // ===========================
        // EDIT USER
        // ===========================

        function editUser(userId) {
            fetch(`/admin/user_crud/edit/${userId}`)
                .then(res => {
                    if (!res.ok) throw new Error('Failed to fetch user data');
                    return res.json();
                })
                .then(user => {
                    if (isStaff && user.role !== 'member') {
                        alert('You can only edit members');
                        return;
                    }
                    populateEditForm(user);
                    openModal('editUserModal');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load user data');
                });
        }

        function populateEditForm(user) {
            const setVal = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.value = (value !== null && value !== undefined) ? value : '';
            };

            setVal('editUserId', user.user_id);
            setVal('edit_first_name', user.first_name);
            setVal('edit_last_name', user.last_name);
            setVal('edit_username', user.username);
            setVal('edit_email', user.email);
            setVal('edit_role', user.role);
            setVal('edit_branch_id', user.branch_id);
            setVal('edit_status', user.status);

            const pendingPlanId = user.member ? user.member.plan_id : null;
            const pendingSubId  = user.member ? user.member.subscription_id : null;

            function fillSelect(selId, items, valueKey, labelFn, pendingValue) {
                const sel = document.getElementById(selId);
                if (!sel) return;
                sel.innerHTML = '<option value="">Select (optional)</option>';
                items.forEach(function(item) {
                    const opt = document.createElement('option');
                    opt.value = item[valueKey];
                    opt.textContent = labelFn(item);
                    sel.appendChild(opt);
                });
                if (pendingValue) sel.value = String(pendingValue);
            }

            if (user.branch_id) {
                const planSel = document.getElementById('edit_plan_id');
                const subSel  = document.getElementById('edit_subscription_id');
                if (planSel) planSel.innerHTML = '<option value="">Loading\u2026</option>';
                if (subSel)  subSel.innerHTML  = '<option value="">Loading\u2026</option>';

                fetch('/admin/user_crud/plans-by-branch?branch_id=' + user.branch_id)
                    .then(function(res) {
                        if (!res.ok) throw new Error('fetch failed');
                        return res.json();
                    })
                    .then(function(data) {
                        fillSelect(
                            'edit_plan_id',
                            data.plans || [],
                            'plan_id',
                            function(p) {
                                return p.name + ' - \u20b1' + parseFloat(p.price).toLocaleString('en-PH', {minimumFractionDigits: 2});
                            },
                            pendingPlanId
                        );
                        fillSelect(
                            'edit_subscription_id',
                            data.subscriptions || [],
                            'subscription_id',
                            function(s) {
                                return s.name + ' - \u20b1' + parseFloat(s.price).toLocaleString('en-PH', {minimumFractionDigits: 2}) + ' / ' + s.duration_days + ' days';
                            },
                            pendingSubId
                        );
                    })
                    .catch(function() {
                        // Route not available yet — restore static options and select the saved values
                        if (planSel) planSel.innerHTML = '<option value="">Select a plan (optional)</option>';
                        if (subSel)  subSel.innerHTML  = '<option value="">Select a subscription (optional)</option>';
                        if (pendingPlanId) setVal('edit_plan_id', pendingPlanId);
                        if (pendingSubId)  setVal('edit_subscription_id', pendingSubId);
                    });
            }

            if (user.member) {
                setVal('edit_sex', user.member.sex);
                setVal('edit_birthday', user.member.birthday);
                setVal('edit_height', user.member.height);
                setVal('edit_weight', user.member.weight);
                setVal('edit_mobile_number', user.member.mobile_number);

                const content = document.getElementById('editMemberFieldsContent');
                const icon = document.getElementById('editMemberSectionIcon');
                if (content && content.classList.contains('hidden')) {
                    content.classList.remove('hidden');
                    if (icon) icon.classList.add('rotate-180');
                }
            } else {
                setVal('edit_sex', '');
                setVal('edit_birthday', '');
                setVal('edit_height', '');
                setVal('edit_weight', '');
                setVal('edit_mobile_number', '');
            }

            toggleMemberFields('edit');

            setVal('edit_password', '');
            setVal('edit_password_confirmation', '');

            const form = document.getElementById('editUserForm');
            if (form) form.action = '/admin/user_crud/update/' + user.user_id;
        }
        // ===========================
        // SHOW USER
        // ===========================

        function showUser(userId) {
            openModal('userShowModal');
            const content = document.getElementById('userShowContent');
            if (content) {
                content.innerHTML = `<div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
                </div>`;
            }

            fetch(`/admin/user_crud/show/${userId}`)
                .then(res => {
                    if (!res.ok) throw new Error('Failed to fetch user details');
                    return res.json();
                })
                .then(user => {
                    if (isStaff && user.role !== 'member') {
                        closeModal('userShowModal');
                        alert('You can only view member details');
                        return;
                    }
                    renderUserDetails(user);
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (content) {
                        content.innerHTML = `<div class="text-center py-12"><p class="text-red-600">Error loading user details</p></div>`;
                    }
                });
        }

        function renderUserDetails(user) {
            const content = document.getElementById('userShowContent');
            if (!content) return;

            const avatarInitial = `${user.first_name?.charAt(0) || 'U'}${user.last_name?.charAt(0) || 'N'}`.toUpperCase();

            let roleColor = 'bg-blue-100 text-blue-700';
            if (user.role === 'super_admin') roleColor = 'bg-red-100 text-red-700';
            if (user.role === 'admin') roleColor = 'bg-orange-100 text-orange-700';
            if (user.role === 'staff') roleColor = 'bg-purple-100 text-purple-700';

            const statusColor = user.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600';

            const formatDate = (dateStr) => {
                const date = new Date(dateStr);
                return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            };

            let html = `<div class="space-y-5">`;

            html += `
                <div class="bg-gray-50 p-5 rounded-lg">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-4">
                            ${user.avatar ?
                                `<img src="/storage/${user.avatar}" alt="${user.first_name}" class="w-20 h-20 rounded-full object-cover border-4 border-gray-200">` :
                                `<div class="w-20 h-20 rounded-full bg-gray-400 flex items-center justify-center text-2xl font-bold text-white">${avatarInitial}</div>`
                            }
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800">${user.first_name} ${user.last_name}</h3>
                                <p class="text-sm text-gray-500">@${user.username}</p>
                                <p class="text-sm text-gray-600 mt-1">${user.email}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <span class="px-3 py-1 text-xs rounded-full font-semibold ${roleColor}">
                                ${user.role === 'super_admin' ? 'Super Admin' : user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                            </span>
                            <span class="px-3 py-1 text-xs rounded-full font-semibold ${statusColor}">
                                ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                            </span>
                        </div>
                    </div>
                </div>
            `;

            html += `
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                        <h4 class="text-md font-semibold text-gray-800">User Information</h4>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                            <div><p class="text-gray-500">User ID</p><p class="font-semibold text-gray-800">#${user.user_id}</p></div>
                            <div><p class="text-gray-500">Username</p><p class="font-medium text-gray-800">@${user.username}</p></div>
                            <div><p class="text-gray-500">Email Address</p><p class="font-medium text-gray-800 break-all">${user.email}</p></div>
                            <div><p class="text-gray-500">Account Created</p><p class="font-medium text-gray-800">${user.created_at ? formatDate(user.created_at) : 'N/A'}</p></div>
                            ${user.branch ? `<div><p class="text-gray-500">Branch</p><p class="font-medium text-gray-800">${user.branch.name}</p></div>` : ''}
                        </div>
                    </div>
                </div>
            `;

    if (user.member) {
    const now = new Date();

    // ── Plan dates ──
    const planEndRaw   = user.member.end_date;
    const planStartRaw = user.member.start_date;
    const planEnd      = planEndRaw   ? new Date(planEndRaw)   : null;
    const planStart    = planStartRaw ? new Date(planStartRaw) : null;
    const planExpired  = planEnd && now > planEnd;

    // ── Subscription dates ──
    const subEndRaw   = user.member.end_date_for_subscription;
    const subStartRaw = user.member.start_date_for_subscription;
    const subEnd      = subEndRaw   ? new Date(subEndRaw)   : null;
    const subStart    = subStartRaw ? new Date(subStartRaw) : null;
    const subExpired  = subEnd && now > subEnd;

    // ── Overall approval flags ──
    const subStatus     = (user.member.subscription_status || '').toLowerCase();
    const isCancelled   = subStatus === 'cancelled' || (user.member.status || '').toLowerCase() === 'cancelled';
    const isSuspended   = subStatus === 'suspended';
    const isApproved    = !!user.member.isApproved;
    const isApprovedSub = !!user.member.isApprovedForSubscription;

    // ── Overall status badge ──
    let computedStatus, statusColor;
    if (isCancelled) {
        computedStatus = 'Cancelled'; statusColor = 'bg-purple-100 text-purple-700';
    } else if (isSuspended) {
        computedStatus = 'Suspended'; statusColor = 'bg-orange-100 text-orange-700';
    } else if (isApproved && isApprovedSub && planExpired && subExpired) {
        computedStatus = 'Both Expired'; statusColor = 'bg-red-100 text-red-700';
    } else if (isApproved && isApprovedSub && planExpired) {
        computedStatus = 'Plan Expired'; statusColor = 'bg-red-100 text-red-700';
    } else if (isApproved && isApprovedSub && subExpired) {
        computedStatus = 'Sub Expired'; statusColor = 'bg-amber-100 text-amber-700';
    } else if (isApproved && isApprovedSub) {
        computedStatus = 'Active'; statusColor = 'bg-green-100 text-green-700';
    } else {
        computedStatus = 'Inactive'; statusColor = 'bg-gray-200 text-gray-600';
    }

    // ── Helper: build "X days remaining / expired N days ago" line ──
    function expiryLine(endDate, expired) {
        if (!endDate) return '';
        const diffMs   = endDate - now;
        const diffDays = Math.ceil(Math.abs(diffMs) / (1000 * 60 * 60 * 24));
        if (expired) {
            return `<p class="text-xs text-red-500 mt-1 font-medium">Expired ${diffDays} ${diffDays === 1 ? 'day' : 'days'} ago</p>`;
        }
        const remaining = Math.ceil(diffMs / (1000 * 60 * 60 * 24));
        if (remaining === 0) return `<p class="text-xs text-orange-500 mt-1 font-medium">Expires today</p>`;
        if (remaining <= 7) return `<p class="text-xs text-orange-500 mt-1 font-medium">${remaining} ${remaining === 1 ? 'day' : 'days'} left</p>`;
        return `<p class="text-xs text-green-600 mt-1">${remaining} days remaining</p>`;
    }

    // ── Plan card ──
    const planCardHTML = user.member.plan ? `
        <div class="bg-white border ${planExpired ? 'border-red-200' : 'border-gray-200'} rounded-lg overflow-hidden">
            <div class="px-5 py-3 border-b ${planExpired ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-gray-50'} flex items-center justify-between">
                <h5 class="text-sm font-semibold ${planExpired ? 'text-red-700' : 'text-gray-800'}">Membership Plan</h5>
                ${planExpired
                    ? `<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">Expired</span>`
                    : `<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Active</span>`
                }
            </div>
            <div class="p-4 grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 text-xs mb-1">Plan Name</p>
                    <p class="font-semibold ${planExpired ? 'text-red-700' : 'text-gray-800'}">${user.member.plan.name}</p>
                    <p class="text-xs text-gray-500 mt-0.5">₱${parseFloat(user.member.plan.price).toFixed(2)}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs mb-1">Duration</p>
                    <p class="font-medium text-gray-800">${user.member.plan.duration_days || '—'} days</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs mb-1">Start Date</p>
                    <p class="font-medium text-gray-800">${planStart ? formatDate(planStartRaw) : '—'}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs mb-1">End Date</p>
                    <p class="font-medium ${planExpired ? 'text-red-600' : 'text-gray-800'}">${planEnd ? formatDate(planEndRaw) : '—'}</p>
                    ${expiryLine(planEnd, planExpired)}
                </div>
            </div>
        </div>` : '';

    // ── Subscription card ──
    const subCardHTML = user.member.subscription ? `
        <div class="bg-white border ${subExpired ? 'border-amber-200' : 'border-gray-200'} rounded-lg overflow-hidden">
            <div class="px-5 py-3 border-b ${subExpired ? 'border-amber-200 bg-amber-50' : 'border-gray-200 bg-gray-50'} flex items-center justify-between">
                <h5 class="text-sm font-semibold ${subExpired ? 'text-amber-700' : 'text-gray-800'}">Subscription</h5>
                ${subExpired
                    ? `<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Expired</span>`
                    : `<span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">Active</span>`
                }
            </div>
            <div class="p-4 grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 text-xs mb-1">Subscription Name</p>
                    <p class="font-semibold ${subExpired ? 'text-amber-700' : 'text-gray-800'}">${user.member.subscription.name}</p>
                    <p class="text-xs text-gray-500 mt-0.5">₱${parseFloat(user.member.subscription.price).toFixed(2)}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs mb-1">Duration</p>
                    <p class="font-medium text-gray-800">${user.member.subscription.duration_days || '—'} days</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs mb-1">Start Date</p>
                    <p class="font-medium text-gray-800">${subStart ? formatDate(subStartRaw) : '—'}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs mb-1">End Date</p>
                    <p class="font-medium ${subExpired ? 'text-amber-600' : 'text-gray-800'}">${subEnd ? formatDate(subEndRaw) : '—'}</p>
                    ${expiryLine(subEnd, subExpired)}
                </div>
            </div>
        </div>` : '';

    html += `
        <div class="bg-white border border-gray-200 rounded-lg">
            <div class="px-5 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <h4 class="text-md font-semibold text-gray-800">Membership Information</h4>
                <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusColor}">${computedStatus}</span>
            </div>
            <div class="p-5 space-y-4">

                <!-- Member meta row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-500">Member ID</p><p class="font-semibold text-gray-800">#${user.member.member_id}</p></div>
                    ${isSuspended && user.member.suspended_at ? `
                    <div>
                        <p class="text-gray-500">Suspended At</p>
                        <p class="font-medium text-orange-700">${formatDate(user.member.suspended_at)}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Plan: ${user.member.plan_days_remaining_before_suspend ?? 0} days paused &nbsp;|&nbsp;
                            Sub: ${user.member.days_remaining_before_suspend ?? 0} days paused
                        </p>
                    </div>` : ''}
                </div>

                <!-- Plan card -->
                ${planCardHTML}

                <!-- Subscription card -->
                ${subCardHTML}

                <!-- Personal info -->
                <div class="border-t border-gray-200 pt-4">
                    <h5 class="font-semibold text-gray-800 mb-3">Personal Information</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        ${user.member.sex        ? `<div><p class="text-gray-500">Sex</p><p class="font-medium text-gray-800 capitalize">${user.member.sex}</p></div>` : ''}
                        ${user.member.birthday   ? `<div><p class="text-gray-500">Birthday</p><p class="font-medium text-gray-800">${formatDate(user.member.birthday)}</p></div>` : ''}
                        ${user.member.height     ? `<div><p class="text-gray-500">Height</p><p class="font-medium text-gray-800">${user.member.height} cm</p></div>` : ''}
                        ${user.member.weight     ? `<div><p class="text-gray-500">Weight</p><p class="font-medium text-gray-800">${user.member.weight} kg</p></div>` : ''}
                        ${user.member.mobile_number ? `<div><p class="text-gray-500">Mobile</p><p class="font-medium text-gray-800">${user.member.mobile_number}</p></div>` : ''}
                    </div>
                    ${user.member.qr_code ? `
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-500 mb-2">QR Code</p>
                        <img src="/storage/${user.member.qr_code}" alt="QR Code" class="w-32 h-32 border border-gray-200 rounded-lg">
                    </div>` : ''}
                </div>

            </div>
        </div>
    `;
} else {
    html += `
        <div class="bg-white border border-gray-200 rounded-lg">
            <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                <h4 class="text-md font-semibold text-gray-800">Membership Information</h4>
            </div>
            <div class="p-8 text-center">
                <p class="text-gray-600">This user doesn't have a membership profile yet.</p>
            </div>
        </div>
    `;
}

            html += '</div>';
            content.innerHTML = html;
        }

        // ===========================
        // FORM VALIDATION
        // ===========================

        function setupFormValidation() {
            const addUserForm = document.querySelector('#addUserModal form');
            if (addUserForm) {
                addUserForm.addEventListener('submit', function (e) {
                    if (!validateForm(this, 'add')) {
                        e.preventDefault();
                        scrollToFirstError(this);
                    }
                });
            }

            const editUserForm = document.querySelector('#editUserForm');
            if (editUserForm) {
                editUserForm.addEventListener('submit', function (e) {
                    if (!validateForm(this, 'edit')) {
                        e.preventDefault();
                        scrollToFirstError(this);
                    }
                });
            }
        }

        function validateForm(form, mode) {
            let valid = true;
            clearAllErrors(form);

            // FIX: Use explicit element IDs rather than fragile prefix logic
            const isEdit = mode === 'edit';

            const firstName     = form.querySelector(isEdit ? '#edit_first_name'           : '#first_name');
            const lastName      = form.querySelector(isEdit ? '#edit_last_name'            : '#last_name');
            const username      = form.querySelector(isEdit ? '#edit_username'             : '#username');
            const email         = form.querySelector(isEdit ? '#edit_email'                : '#email');
            const password      = form.querySelector(isEdit ? '#edit_password'             : '#password');
            const passwordConf  = form.querySelector(isEdit ? '#edit_password_confirmation' : '#password_confirmation');
            const height        = form.querySelector(isEdit ? '#edit_height'               : '#height');
            const weight        = form.querySelector(isEdit ? '#edit_weight'               : '#weight');
            const mobile        = form.querySelector(isEdit ? '#edit_mobile_number'        : '#mobile_number');

            // First Name
            if (firstName && !firstName.value.trim()) {
                showError(firstName, 'First name is required');
                valid = false;
            }

            // Last Name
            if (lastName && !lastName.value.trim()) {
                showError(lastName, 'Last name is required');
                valid = false;
            }

            // Username
            if (username) {
                if (!username.value.trim()) {
                    showError(username, 'Username is required');
                    valid = false;
                } else if (username.value.trim().length < 3) {
                    showError(username, 'Username must be at least 3 characters');
                    valid = false;
                }
            }

            // Email
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email) {
                if (!email.value.trim()) {
                    showError(email, 'Email is required');
                    valid = false;
                } else if (!emailPattern.test(email.value.trim())) {
                    showError(email, 'Please enter a valid email address');
                    valid = false;
                }
            }

            // Password: required on add; optional on edit (validate only if filled)
            if (!isEdit) {
                if (password && !password.value) {
                    showError(password, 'Password is required');
                    valid = false;
                } else if (password && password.value.length < 6) {
                    showError(password, 'Password must be at least 6 characters');
                    valid = false;
                }
                // Confirm password (add mode)
                if (password && password.value && passwordConf) {
                    if (!passwordConf.value) {
                        showError(passwordConf, 'Please confirm your password');
                        valid = false;
                    } else if (password.value !== passwordConf.value) {
                        showError(passwordConf, 'Passwords do not match');
                        valid = false;
                    }
                }
            } else {
                // Edit mode: only validate if a new password is being set
                if (password && password.value) {
                    if (password.value.length < 6) {
                        showError(password, 'Password must be at least 6 characters');
                        valid = false;
                    } else if (passwordConf && password.value !== passwordConf.value) {
                        showError(passwordConf, 'Passwords do not match');
                        valid = false;
                    }
                }
            }

            // Height
            if (height && height.value) {
                const h = parseFloat(height.value);
                if (h <= 0 || h > 300) {
                    showError(height, 'Please enter a valid height (1–300 cm)');
                    valid = false;
                }
            }

            // Weight
            if (weight && weight.value) {
                const w = parseFloat(weight.value);
                if (w <= 0 || w > 500) {
                    showError(weight, 'Please enter a valid weight (1–500 kg)');
                    valid = false;
                }
            }

            // Mobile
            if (mobile && mobile.value) {
                const mobilePattern = /^(09|\+639)\d{9}$/;
                if (!mobilePattern.test(mobile.value.trim())) {
                    showError(mobile, 'Enter a valid mobile number (e.g., 09123456789)');
                    valid = false;
                }
            }

            return valid;
        }

        function setupLiveValidation() {
            // Add modal live validation
            setupLiveValidationForForm(false);
            // Edit modal live validation
            setupLiveValidationForForm(true);
        }

        function setupLiveValidationForForm(isEdit) {
            const getEl = (id) => document.getElementById(isEdit ? `edit_${id}` : id);
            const passwordConf = document.getElementById(isEdit ? 'edit_password_confirmation' : 'password_confirmation');

            const addV = (el, fn) => {
                if (!el) return;
                el.addEventListener('blur', () => fn(el));
                el.addEventListener('input', () => { if (el.value.trim()) fn(el); });
            };

            addV(getEl('first_name'), (el) => { if (el.value.trim()) clearError(el); });
            addV(getEl('last_name'), (el) => { if (el.value.trim()) clearError(el); });

            addV(getEl('username'), (el) => {
                if (el.value.trim().length >= 3) clearError(el);
                else if (el.value.trim()) showError(el, 'Username must be at least 3 characters');
            });

            addV(getEl('email'), (el) => {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (el.value.trim() && re.test(el.value)) clearError(el);
                else if (el.value.trim()) showError(el, 'Please enter a valid email address');
            });

            const passwordEl = getEl('password');
            addV(passwordEl, (el) => {
                if (el.value && el.value.length >= 6) clearError(el);
                else if (el.value) showError(el, 'Password must be at least 6 characters');
            });

            if (passwordEl && passwordConf) {
                addV(passwordConf, (el) => {
                    if (el.value && passwordEl.value === el.value) clearError(el);
                    else if (el.value) showError(el, 'Passwords do not match');
                });
            }

            addV(getEl('height'), (el) => {
                const h = parseFloat(el.value);
                if (el.value && (h <= 0 || h > 300)) showError(el, 'Please enter a valid height (1–300 cm)');
                else if (el.value) clearError(el);
            });

            addV(getEl('weight'), (el) => {
                const w = parseFloat(el.value);
                if (el.value && (w <= 0 || w > 500)) showError(el, 'Please enter a valid weight (1–500 kg)');
                else if (el.value) clearError(el);
            });

            addV(getEl('mobile_number'), (el) => {
                const re = /^(09|\+639)\d{9}$/;
                if (el.value && !re.test(el.value.trim())) showError(el, 'Enter a valid mobile number (e.g., 09123456789)');
                else if (el.value) clearError(el);
            });
        }

        function scrollToFirstError(form) {
            const firstError = form.querySelector('.error-message');
            if (!firstError) return;
            const scrollContainer = form.closest('.modal-scrollbar') || form.closest('.overflow-y-auto');
            if (scrollContainer) {
                const errorElement = firstError.closest('div');
                scrollContainer.scrollTo({ top: errorElement.offsetTop - 100, behavior: 'smooth' });
            } else {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // ===========================
        // MEMBER ACTIONS - APPROVAL MODALS
        // ===========================

  function approveProfile(memberId) {
    openPaymentApprovalModal(memberId, 'profile');
}

function approveSubscription(memberId) {
    openPaymentApprovalModal(memberId, 'subscription');
}

function openPaymentApprovalModal(memberId, type) {
    // Remove any existing modal first
    const existing = document.getElementById('paymentApprovalModal');
    if (existing) existing.remove();

    const modalHTML = `
        <div id="paymentApprovalModal" class="fixed inset-0 z-[9999] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-green-700 text-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold">Approve ${type === 'profile' ? 'Profile' : 'Subscription'}</h2>
                            <p class="text-green-100 text-sm mt-1">Select payment method</p>
                        </div>
                        <button type="button" id="closePaymentApprovalBtn"
                            class="text-white/80 hover:text-white p-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 mb-6 text-center">Choose how the customer will pay:</p>
                    <div class="space-y-3 mb-6">
                        <button type="button" id="cashPaymentBtn"
                            class="w-full p-5 border-2 border-gray-200 rounded-xl hover:border-green-500 hover:bg-green-50 transition-all text-left group">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-200 transition-colors flex-shrink-0">
                                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-800 mb-1">Cash Payment</h3>
                                    <p class="text-sm text-gray-600">Accept direct cash payment</p>
                                </div>
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </button>
                        <button type="button" id="gcashPaymentBtn"
                            class="w-full p-5 border-2 border-gray-200 rounded-xl hover:border-purple-500 hover:bg-purple-50 transition-all text-left group">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center group-hover:bg-purple-200 transition-colors flex-shrink-0">
                                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-800 mb-1">GCash Payment</h3>
                                    <p class="text-sm text-gray-600">Mobile wallet payment</p>
                                </div>
                                <svg class="w-6 h-6 text-gray-400 group-hover:text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </button>
                    </div>
                    <button type="button" id="cancelPaymentApprovalBtn"
                        class="w-full px-6 py-3 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 font-semibold transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>`;

    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Lock scroll
    const scrollY = window.scrollY;
    document.body.dataset.scrollY = scrollY;
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = '100%';

    // Attach events via addEventListener (no inline onclick = no bubbling issues)
    document.getElementById('closePaymentApprovalBtn').addEventListener('click', closePaymentApprovalModal);
    document.getElementById('cancelPaymentApprovalBtn').addEventListener('click', closePaymentApprovalModal);

    document.getElementById('cashPaymentBtn').addEventListener('click', function(e) {
        e.stopPropagation();
        closePaymentApprovalModal();
        if (type === 'profile') submitApprovalForm(memberId, 'profile', 'cash', null);
        else submitApprovalForm(memberId, 'subscription', 'cash', null);
    });

    document.getElementById('gcashPaymentBtn').addEventListener('click', function(e) {
        e.stopPropagation();
        closePaymentApprovalModal();
        showGcashReferenceModal(memberId, type);
    });
}

function closePaymentApprovalModal() {
    const modal = document.getElementById('paymentApprovalModal');
    if (!modal) return;
    modal.remove();
    unlockScroll();
}

function unlockScroll() {
    const scrollY = parseInt(document.body.dataset.scrollY || '0');
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.width = '';
    delete document.body.dataset.scrollY;
    window.scrollTo(0, scrollY);
}

function showGcashReferenceModal(memberId, type) {
    const existing = document.getElementById('gcashReferenceModal');
    if (existing) existing.remove();

    const modalHTML = `
        <div id="gcashReferenceModal" class="fixed inset-0 z-[9999] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold">GCash Payment</h2>
                                <p class="text-purple-100 text-sm">Enter reference code</p>
                            </div>
                        </div>
                        <button type="button" id="closeGcashModalBtn" class="text-white/80 hover:text-white p-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <label for="gcashRefCode" class="block text-sm font-semibold text-gray-700 mb-2">
                            GCash Reference Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="gcashRefCode" placeholder="Enter reference code" maxlength="30"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-gray-800 font-mono text-base transition-all">
                        <p class="text-xs text-gray-500 mt-2">Enter the transaction reference code from GCash</p>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" id="cancelGcashBtn"
                            class="flex-1 px-6 py-3 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 font-semibold transition-colors">
                            Cancel
                        </button>
                        <button type="button" id="confirmGcashBtn" disabled
                            class="flex-1 px-6 py-3 rounded-xl bg-gradient-to-r from-purple-600 to-purple-700 text-white hover:from-purple-700 hover:to-purple-800 font-semibold transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>
        </div>`;

    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Re-lock scroll (closePaymentApprovalModal already unlocked it)
    const scrollY = window.scrollY;
    document.body.dataset.scrollY = scrollY;
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = '100%';

    const input = document.getElementById('gcashRefCode');
    const confirmBtn = document.getElementById('confirmGcashBtn');

    input.addEventListener('input', function() {
        const val = this.value.trim();
        confirmBtn.disabled = val.length < 6;
        if (val.length > 0 && val.length < 6) {
            this.classList.add('border-red-500');
            this.classList.remove('border-gray-300', 'border-green-500');
        } else if (val.length >= 6) {
            this.classList.remove('border-red-500', 'border-gray-300');
            this.classList.add('border-green-500');
        } else {
            this.classList.remove('border-red-500', 'border-green-500');
            this.classList.add('border-gray-300');
        }
    });

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !confirmBtn.disabled) confirmBtn.click();
    });

    document.getElementById('closeGcashModalBtn').addEventListener('click', closeGcashReferenceModal);
    document.getElementById('cancelGcashBtn').addEventListener('click', closeGcashReferenceModal);

    confirmBtn.addEventListener('click', function() {
        const refCode = input.value.trim();
        if (!refCode || refCode.length < 6) return;
        closeGcashReferenceModal();
        submitApprovalForm(memberId, type, 'gcash', refCode);
    });

    setTimeout(() => input.focus(), 100);
}

function closeGcashReferenceModal() {
    const modal = document.getElementById('gcashReferenceModal');
    if (!modal) return;
    modal.remove();
    unlockScroll();
}

function submitApprovalForm(memberId, type, paymentMethod, referenceCode) {
    const route = type === 'profile'
        ? `/admin/user_crud/approve-profile/${memberId}`
        : `/admin/user_crud/approve-subscription/${memberId}`;

    const params = new URLSearchParams({ payment_method: paymentMethod });
    if (referenceCode) params.set('reference_code', referenceCode);

    window.location.href = `${route}?${params.toString()}`;
}

        function submitSubscriptionApprovalForm(memberId, paymentMethod, referenceCode) {
            const form = document.createElement('form');
            form.method = 'GET';
            let url = `/admin/user_crud/approve-subscription/${memberId}?payment_method=${paymentMethod}`;
            if (referenceCode) url += `&reference_code=${encodeURIComponent(referenceCode)}`;
            form.action = url;
            document.body.appendChild(form);
            form.submit();
        }

        function denyMember(memberId) {
            if (typeof Swal === 'undefined') {
                if (confirm('Deny this member?')) window.location.href = `/admin/user_crud/deny/${memberId}`;
                return;
            }
            Swal.fire({
                title: 'Deny Member?',
                text: 'This member will not be able to access the system.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, deny',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = `/admin/user_crud/deny/${memberId}`;
            });
        }

        // ===========================
        // SUSPEND / RESUME / CANCEL PLAN MODALS
        // ===========================

        function openSuspendModal(memberId) {
            document.getElementById('suspendForm').action = `/admin/user_crud/suspend/${memberId}`;
            const scrollY = window.scrollY;
            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollY}px`;
            document.body.style.width = '100%';
            document.body.style.overflowY = 'scroll';
            document.getElementById('suspendModal').classList.remove('hidden');
        }

        function closeSuspendModal() {
            const scrollY = document.body.style.top;
            document.getElementById('suspendModal').classList.add('hidden');
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            document.body.style.overflowY = '';
            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        }

        function openResumeModal(memberId) {
            document.getElementById('resumeForm').action = `/admin/user_crud/resume/${memberId}`;
            const scrollY = window.scrollY;
            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollY}px`;
            document.body.style.width = '100%';
            document.body.style.overflowY = 'scroll';
            document.getElementById('resumeModal').classList.remove('hidden');
        }

        function closeResumeModal() {
            const scrollY = document.body.style.top;
            document.getElementById('resumeModal').classList.add('hidden');
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            document.body.style.overflowY = '';
            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        }

        function openCancelPlanModal(memberId) {
            document.getElementById('cancelPlanForm').action = `/admin/user_crud/cancel-plan/${memberId}`;
            const scrollY = window.scrollY;
            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollY}px`;
            document.body.style.width = '100%';
            document.body.style.overflowY = 'scroll';
            document.getElementById('cancelPlanModal').classList.remove('hidden');
        }

        function closeCancelPlanModal() {
            const scrollY = document.body.style.top;
            document.getElementById('cancelPlanModal').classList.add('hidden');
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            document.body.style.overflowY = '';
            window.scrollTo(0, parseInt(scrollY || '0') * -1);
        }

        function suspendMember(memberId) {
            closeAllActionsMenus();
            openSuspendModal(memberId);
        }

        function resumeMember(memberId) {
            closeAllActionsMenus();
            openResumeModal(memberId);
        }

        function cancelPlan(memberId) {
            closeAllActionsMenus();
            openCancelPlanModal(memberId);
        }

        // ===========================
        // INITIALIZATION
        // ===========================

        document.addEventListener('DOMContentLoaded', function () {
            // For staff: member fields always visible, load plans for their branch
            if (isStaff) {
                const staffBranchId = document.getElementById('add_branch_id')?.value;
                if (staffBranchId) {
                    loadPlansByBranch(staffBranchId, 'plan_id', 'subscription_id');
                }
            } else {
                // Initialize member fields visibility on add modal
                toggleMemberFields('add');

                // For admin (non-super-admin): pre-load plans for their fixed branch
                if (isAdmin) {
                    const adminBranchId = document.getElementById('add_branch_id')?.value;
                    if (adminBranchId) {
                        loadPlansByBranch(adminBranchId, 'plan_id', 'subscription_id');
                    }
                }
            }

            // Setup form validation
            setupFormValidation();

            // Setup live validation
            setupLiveValidation();

            // Initialize filter count
            updateFilterCount();

            // Close dropdowns/menus when clicking outside
            document.addEventListener('click', function (event) {
                const dropdown = document.getElementById('filterDropdown');
                const filterBtn = event.target.closest('button[onclick*="toggleFilterDropdown"]');
                if (!filterBtn && dropdown && !dropdown.contains(event.target) && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                    const icon = document.getElementById('filterDropdownIcon');
                    if (icon) icon.classList.remove('rotate-180');
                }

                const isClickInsideMenu = event.target.closest('[id^="actionsMenu-"]');
                const isClickOnToggleButton = event.target.closest('[onclick*="toggleActionsMenu"]');
                if (!isClickInsideMenu && !isClickOnToggleButton) closeAllActionsMenus();
            });

            // Escape key closes modals
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeAllActionsMenus();
                    closeGcashReferenceModal();
                    closePaymentApprovalModal();
                    ['addUserModal', 'editUserModal', 'userShowModal'].forEach(id => {
                        const modal = document.getElementById(id);
                        if (modal && !modal.classList.contains('hidden')) closeModal(id);
                    });
                }
                // Enter key in GCash reference input
                if (e.key === 'Enter') {
                    const gcashInput = document.getElementById('gcashRefCode');
                    const confirmBtn = document.getElementById('confirmGcashRef');
                    if (gcashInput && document.activeElement === gcashInput && confirmBtn && !confirmBtn.disabled) {
                        confirmBtn.click();
                    }
                }
            });

            // Add custom scrollbar style
            const style = document.createElement('style');
            style.textContent = `.rotate-180 { transform: rotate(180deg); }`;
            document.head.appendChild(style);
        });
    </script>

@endsection