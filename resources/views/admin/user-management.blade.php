@extends('layouts.app')

@section('title', 'Account Management')
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
                                        <label
                                            class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="roles[]" value="member" {{ in_array('member', request('roles', [])) ? 'checked' : '' }} onchange="updateFilterCount()"
                                                class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Member</span>
                                            <span
                                                class="ml-auto px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded-full">Member</span>
                                        </label>

                                        <label
                                            class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="roles[]" value="staff" {{ in_array('staff', request('roles', [])) ? 'checked' : '' }} onchange="updateFilterCount()"
                                                class="w-4 h-4 text-purple-600 rounded focus:ring-2 focus:ring-purple-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Staff</span>
                                            <span
                                                class="ml-auto px-2 py-0.5 text-xs bg-purple-100 text-purple-700 rounded-full">Staff</span>
                                        </label>

                                        <label
                                            class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="roles[]" value="admin" {{ in_array('admin', request('roles', [])) ? 'checked' : '' }} onchange="updateFilterCount()"
                                                class="w-4 h-4 text-red-600 rounded focus:ring-2 focus:ring-red-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Admin</span>
                                            <span
                                                class="ml-auto px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">Admin</span>
                                        </label>

                                        @if($isSuperAdmin)
                                            <label
                                                class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                                <input type="checkbox" name="roles[]" value="super_admin" {{ in_array('super_admin', request('roles', [])) ? 'checked' : '' }}
                                                    onchange="updateFilterCount()"
                                                    class="w-4 h-4 text-red-600 rounded focus:ring-2 focus:ring-red-500">
                                                <span class="ml-3 text-sm font-medium text-gray-700">Super Admin</span>
                                                <span
                                                    class="ml-auto px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">Super
                                                    Admin</span>
                                            </label>
                                        @endif
                                    </div>
                                </div>

                                <div class="border-t border-gray-200"></div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Filter by Status</label>
                                    <div class="space-y-2">
                                        <label
                                            class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="user_status[]" value="active" {{ in_array('active', request('user_status', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-4 h-4 text-green-600 rounded focus:ring-2 focus:ring-green-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Active</span>
                                            <span
                                                class="ml-auto px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">Active</span>
                                        </label>

                                        <label
                                            class="flex items-center px-3 py-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                            <input type="checkbox" name="user_status[]" value="inactive" {{ in_array('inactive', request('user_status', [])) ? 'checked' : '' }}
                                                onchange="updateFilterCount()"
                                                class="w-4 h-4 text-gray-600 rounded focus:ring-2 focus:ring-gray-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Inactive</span>
                                            <span
                                                class="ml-auto px-2 py-0.5 text-xs bg-gray-200 text-gray-600 rounded-full">Inactive</span>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">User
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Username</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Plan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Date Created</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
         <tbody class="divide-y divide-gray-100">
    {{-- REPLACE the @forelse($users as $user) section in your user-management.blade.php --}}

@forelse($users as $user)
    {{-- Skip non-members if user is staff --}}
    @if($isStaff && $user->role !== 'member')
        @continue
    @endif

    @php
        // Determine member status
        $needsApproval = $user->member && $user->member->needsApproval();
        $isDenied = $user->member && ($user->member->isDisabled || $user->member->isDisabledForSubscription);
        $isSuspended = $user->member && $user->member->subscription_status === 'suspended';
        $isCancelled = $user->member && ($user->member->subscription_status === 'cancelled' || $user->member->status === 'cancelled');
        $isRenewalPending = $user->member && $user->member->renewal_pending;

        // Check if fully approved
        $isFullyApproved = $user->role === 'member'
            && $user->member
            && $user->member->isFullyApproved();

        // Determine user status for display
        $displayStatus = $user->status; // Default to user table status
        
        // For members, override based on approval/subscription status
        if ($user->role === 'member' && $user->member) {
            if ($isCancelled) {
                $displayStatus = 'cancelled';
            } elseif ($isSuspended) {
                $displayStatus = 'suspended';
            } elseif ($user->member->isApproved && $user->member->isApprovedForSubscription) {
                $displayStatus = 'active';
            } else {
                $displayStatus = 'inactive';
            }
        }

        // Determine plan display
        $planDisplay = 'Not a member';
        if ($user->role === 'member' && $user->member) {
            if ($isCancelled) {
                $planDisplay = 'cancelled';
            } elseif ($user->member->isApproved && $user->member->isApprovedForSubscription && $user->member->subscription) {
                $planDisplay = 'active_subscription';
            } elseif (!$user->member->isApproved || !$user->member->isApprovedForSubscription) {
                $planDisplay = 'incomplete';
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
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                    Member
                </span>
            </td>

            <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                    Pending Approval
                </span>
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
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">
                    Member
                </span>
            </td>

            <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                    Denied
                </span>
            </td>

            <td class="px-6 py-4">
                <span class="text-red-600 text-sm font-medium">Membership Disabled</span>
            </td>

            <td class="px-6 py-4 text-sm text-gray-500">
                {{ $user->created_at->format('M d, Y') }}
            </td>

            {{-- ONLY DELETE BUTTON --}}
            <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                <button onclick="openDeleteModal('{{ route('admin.users-destroy', $user->user_id) }}')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3" />
                    </svg>
                    <span class="text-sm font-medium">Delete</span>
                </button>
            </td>

        @else
            {{-- NORMAL/ACTIVE/INACTIVE/SUSPENDED/CANCELLED USERS --}}
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-800 font-bold text-sm shadow-md">
                        {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? 'N', 0, 1)) }}
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 truncate">{{ $user->first_name }} {{ $user->last_name }}</p>
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
                <span class="px-2 py-1 text-xs font-semibold rounded-full
                    {{ $displayStatus === 'active' ? 'bg-green-100 text-green-700' : 
                       ($displayStatus === 'suspended' ? 'bg-orange-100 text-orange-700' :
                       ($displayStatus === 'cancelled' ? 'bg-purple-100 text-purple-700' : 'bg-gray-200 text-gray-600')) }}">
                    {{ ucfirst($displayStatus) }}
                </span>
            </td>
<td class="px-6 py-4 text-gray-700">
    @if($user->role === 'member' && $user->member)
        @if($planDisplay === 'cancelled')
            <span class="text-purple-600 text-sm font-medium">Plan Cancelled</span>
        @elseif($user->member->status === 'suspended')
            <div class="text-sm">
                <p class="font-medium text-orange-600">Suspended</p>
                <p class="text-xs text-gray-500">
                    {{ $user->member->plan_days_remaining_before_suspend ?? 0 }} days paused
                </p>
                <p class="text-xs text-blue-600 mt-1">Plan: {{ $user->member->plan->name ?? 'N/A' }}</p>
            </div>
                    @elseif($planDisplay === 'active_subscription')
                        <div class="text-sm">
                            <p class="font-medium text-gray-900">{{ $user->member->subscription->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">
                                @if($user->member->end_date_for_subscription)
                                    @php
                                        $now = now();
                                        $end = $user->member->end_date_for_subscription;
                                        $days = (int) $now->diffInDays($end, false);
                                    @endphp
                                    @if($isSuspended)
                                        <span class="text-orange-600">Suspended ({{ $user->member->days_remaining_before_suspend ?? 0 }} days paused)</span>
                                    @elseif ($days >= 1)
                                        {{ $days }} {{ $days == 1 ? 'day' : 'days' }} left
                                    @elseif ($days === 0)
                                        Expires today
                                    @else
                                        Expired
                                    @endif
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
                                    // Determine if member can be suspended/cancelled
                                    $isActive = $user->member->isApproved 
                                        && $user->member->isApprovedForSubscription 
                                        && $user->member->subscription_status === 'active';
                                @endphp

                                @if($isSuspended)
                                    <!-- Resume Button (only shown when suspended) -->
                                    <button onclick="resumeMember('{{ $user->member->member_id }}')"
                                        class="w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-gray-100 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Resume
                                    </button>
                                @elseif($isActive)
                                    <!-- Suspend Button (only shown when active) -->
                                    <button onclick="suspendMember('{{ $user->member->member_id }}')"
                                        class="w-full text-left px-4 py-2 text-sm text-orange-600 hover:bg-gray-100 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Suspend
                                    </button>

                                    <!-- Cancel Plan Button (only shown when active) -->
                                    <button onclick="cancelPlan('{{ $user->member->member_id }}')"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Cancel Plan
                                    </button>
                                @endif
                            @endif

                            <!-- Delete Button -->
                            <button onclick="openDeleteModal('{{ route('admin.users-destroy', $user->user_id) }}')"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3" />
                                </svg>
                                Delete User
                            </button>
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
            <div
                class="flex flex-col sm:flex-row items-center justify-between p-4 sm:p-6 bg-gray-50 border-t border-gray-200 gap-4">
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
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First
                                Name</label>
                            <input type="text" name="first_name" id="first_name"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                            @error('first_name')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last
                                Name</label>
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
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm
                                Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        </div>
                    </div>

                    {{-- Only show role selector for non-staff --}}
                    @if(!$isStaff)
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                            <select name="role" id="role" onchange="toggleMemberFields('add')"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="member">Member</option>
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                                @if($isSuperAdmin)
                                    <option value="super_admin">Super Admin</option>
                                @endif
                            </select>
                        </div>
                    @endif


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



                    {{-- Member fields - always visible for staff --}}
                    <div id="addMemberFields" class="space-y-4 pt-4" style="{{ $isStaff ? 'display: block;' : '' }}">
                        <h3 class="text-sm font-semibold text-gray-700">
                            Member Profile {{ $isStaff ? '(Required)' : '(Optional)' }}
                        </h3>

                        <div>
                            <label for="plan_id" class="block text-sm font-medium text-gray-700">
                                Membership Plan
                                @if($isStaff)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <select name="plan_id" id="plan_id" {{ $isStaff ? 'required' : '' }}
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="">Select a plan{{ $isStaff ? '' : ' (optional)' }}</option>
                                @foreach($plans ?? [] as $plan)
                                    <option value="{{ $plan->plan_id }}">{{ $plan->name }} -
                                        ₱{{ number_format($plan->price, 2) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="sex" class="block text-sm font-medium text-gray-700">
                                    Sex
                                    @if($isStaff)
                                        <span class="text-red-500">*</span>
                                    @endif
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
                                    @if($isStaff)
                                        <span class="text-red-500">*</span>
                                    @endif
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
                                @if($isStaff)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <input type="tel" name="mobile_number" id="mobile_number" placeholder="e.g. 09123456789" {{ $isStaff ? 'required' : '' }}
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                        </div>
                    </div>
                    <div>
                        <label for="subscription_id" class="block text-sm font-medium text-gray-700">
                            Subscription
                            @if($isStaff)
                                <span class="text-red-500">*</span>
                            @endif
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

                    {{-- Payment Method Selection (only shown if both plan and subscription are selected) --}}
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
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-blue-800">Payment will be recorded and QR code will be generated
                                    automatically.</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 sticky bottom-0 bg-white pb-2">
                        <button type="button" onclick="closeModal('addUserModal')"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto"
                            to">Cancel</button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 w-full sm:w-auto">Add
                            {{ $isStaff ? 'Member' : 'User' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modified Edit User Modal - Similar changes --}}
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
                            <label for="edit_role" class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                            <select name="role" id="edit_role" onchange="toggleMemberFields('edit')"
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                <option value="member">Member</option>
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                                @if($isSuperAdmin)
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
                    @endif

                    <div>
                        <label for="edit_branch" class="block text-sm font-medium text-gray-700 mb-2">Branch <span class="text-red-500">*</span></label>
                        @if(auth()->user()->role === 'super_admin')
                            <select name="branch_id" id="edit_branch" required
                                class="mt-1 block w-full rounded-lg border-2 border-gray-300 bg-white px-4 py-2 text-gray-800 focus:ring-2 focus:ring-gray-800 focus:border-gray-800">
                                
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

                        {{-- Payment Section (only shown if both plan and subscription are selected) --}}
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
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
                    Delete User
                </h3>

                <p class="text-center text-gray-600 mb-6">
                    Are you sure you want to delete <span class="font-bold">{{ $user->name }}</span>? This action cannot be undone.
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

    
<div id="cancelPlanModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
    <div class="absolute inset-0 backdrop-blur bg-opacity-50" onclick="closeCancelPlanModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>

            <h3 class="text-xl font-bold text-center text-gray-900 mb-2">
                Cancel Plan
            </h3>

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

            <h3 class="text-xl font-bold text-center text-gray-900 mb-2">
                Suspend Member
            </h3>

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

            <h3 class="text-xl font-bold text-center text-gray-900 mb-2">
                Resume Member
            </h3>

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
        const isAdmin = {{ $isAdmin ? 'true' : 'false' }}

                                    const showError = (element, message) => {
            if (!element) return;

            element.classList.remove('border-2 border-gray-300');
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
            element.classList.add('border-2 border-gray-300');

            const container = element.closest('div');
            const errorSpan = container.querySelector('.error-message');
            if (errorSpan) errorSpan.remove();
        };

        const clearAllErrors = (form) => {
            if (!form) return;

            form.querySelectorAll('.error-message').forEach(el => el.remove());
            form.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
                el.classList.add('border-2 border-gray-300');
            });
        };

        // ===========================
        // MODAL FUNCTIONS
        // ===========================

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            // Save current scroll position
            const scrollY = window.scrollY;

            // Prevent body scroll
            document.body.style.position = 'fixed';
            document.body.style.top = `-${scrollY}px`;
            document.body.style.width = '100%';
            document.body.style.overflowY = 'scroll'; // Prevent layout shift

            // Show modal
            modal.classList.remove('hidden');

            // Add custom scrollbar styles to modal content
            const modalContent = modal.querySelector('.overflow-y-auto');
            if (modalContent) {
                modalContent.style.scrollbarWidth = 'thin';
                modalContent.style.scrollbarColor = '#9CA3AF #F3F4F6';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;

            // Get the scroll position that was saved
            const scrollY = document.body.style.top;

            // Hide modal
            modal.classList.add('hidden');

            // Restore body scroll
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            document.body.style.overflowY = '';

            // Restore scroll position
            window.scrollTo(0, parseInt(scrollY || '0') * -1);

            // Clear form errors when closing
            const form = modal.querySelector('form');
            if (form) {
                clearAllErrors(form);
            }
        }

        // ===========================
        // TOGGLE MEMBER FIELDS
        // ===========================

        function toggleMemberFields(mode) {
            // If staff, always show member fields
            if (isStaff) {
                const memberFields = document.getElementById(mode === 'add' ? 'addMemberFields' : 'editMemberFields');
                if (memberFields) {
                    memberFields.style.display = 'block';
                }
                return;
            }

            // Original logic for non-staff
            const roleSelect = document.getElementById(mode === 'add' ? 'role' : 'edit_role');
            const memberFields = document.getElementById(mode === 'add' ? 'addMemberFields' : 'editMemberFields');

            if (!roleSelect || !memberFields) return;

            if (roleSelect.value === 'member') {
                memberFields.style.display = 'block';
            } else {
                memberFields.style.display = 'none';
            }
        }

        // ===========================
        // DELETE MODAL
        // ===========================


        function openDeleteModal(actionUrl) {
    const form = document.getElementById('deleteForm');
    form.action = actionUrl;

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

        function removeFilter(name, value) {
            const form = document.querySelector('form[role="search"]');
            if (!form) return;

            const checkbox = form.querySelector(`input[name="${name}"][value="${value}"]`);
            if (checkbox) {
                checkbox.checked = false;
                form.submit();
            }
        }

        // ===========================
        // ACTIONS MENU
        // ===========================
        // ===========================
        // ACTIONS MENU - AUTO CLOSE AFTER SELECTION
        // ===========================

        function toggleActionsMenu(event, userId) {
            event?.stopPropagation();

            // Close all other menus
            document.querySelectorAll('[id^="actionsMenu-"]').forEach(menu => {
                if (menu.id !== `actionsMenu-${userId}`) {
                    menu.classList.add('hidden');
                }
            });

            // Toggle current menu
            const menu = document.getElementById(`actionsMenu-${userId}`);
            const button = event?.target?.closest('button');

            if (menu && button) {
                const isHidden = menu.classList.contains('hidden');

                if (isHidden) {
                    // Add close button if it doesn't exist
                    if (!menu.querySelector('.menu-close-btn')) {
                        const closeBtn = document.createElement('button');
                        closeBtn.className = 'menu-close-btn absolute top-2 right-2 text-gray-400 hover:text-gray-600 p-1';
                        closeBtn.innerHTML = `
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            `;
                        closeBtn.onclick = (e) => {
                            e.stopPropagation();
                            menu.classList.add('hidden');
                        };
                        menu.insertBefore(closeBtn, menu.firstChild);
                    }

                    // Add click handlers to all menu items to close after selection
                    const menuItems = menu.querySelectorAll('button');
                    menuItems.forEach(item => {
                        // Remove existing auto-close listeners to avoid duplicates
                        if (!item.hasAttribute('data-close-handler')) {
                            item.setAttribute('data-close-handler', 'true');

                            // Store original onclick
                            const originalOnClick = item.onclick;

                            item.onclick = function (e) {
                                // Call original function
                                if (originalOnClick) {
                                    originalOnClick.call(this, e);
                                }

                                // Close menu after a short delay
                                setTimeout(() => {
                                    menu.classList.add('hidden');
                                }, 100);
                            };
                        }
                    });

                    // Position menu relative to button
                    const rect = button.getBoundingClientRect();
                    menu.style.position = 'fixed';
                    menu.style.top = `${rect.bottom + window.scrollY + 8}px`;
                    menu.style.left = `${rect.right - 192}px`; // 192px = w-48 (12rem)
                    menu.style.zIndex = '9999';
                    menu.classList.remove('hidden');
                } else {
                    menu.classList.add('hidden');
                }
            }
        }

        // Helper function to close specific menu
        function closeActionsMenu(userId) {
            const menu = document.getElementById(`actionsMenu-${userId}`);
            if (menu) {
                menu.classList.add('hidden');
            }
        }

        // Helper function to close all menus
        function closeAllActionsMenus() {
            document.querySelectorAll('[id^="actionsMenu-"]').forEach(menu => {
                menu.classList.add('hidden');
            });
        }

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
        // EDIT USER
        // ===========================

        function editUser(userId) {
            fetch(`/admin/user_crud/edit/${userId}`)
                .then(res => {
                    if (!res.ok) throw new Error('Failed to fetch user data');
                    return res.json();
                })
                .then(user => {
                    // Check if staff is trying to edit non-member
                    if (isStaff && user.role !== 'member') {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('You can only edit members');
                        } else {
                            alert('You can only edit members');
                        }
                        return;
                    }

                    populateEditForm(user);
                    openModal('editUserModal');
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to load user data');
                    } else {
                        alert('Failed to load user data');
                    }
                });
        }

        function populateEditForm(user) {
            const setValueById = (id, value) => {
                const element = document.getElementById(id);
                if (element) element.value = value || '';
            };

            setValueById('editUserId', user.user_id);
            setValueById('edit_first_name', user.first_name);
            setValueById('edit_last_name', user.last_name);
            setValueById('edit_username', user.username);
            setValueById('edit_email', user.email);
            setValueById('edit_role', user.role);
            setValueById('edit_branch_id', user.branch_id);
            setValueById('edit_status', user.status);

            if (user.member) {
                setValueById('edit_plan_id', user.member.plan_id);
                setValueById('edit_sex', user.member.sex);
                setValueById('edit_birthday', user.member.birthday);
                setValueById('edit_height', user.member.height);
                setValueById('edit_weight', user.member.weight);
                setValueById('edit_mobile_number', user.member.mobile_number);
            } else {
                setValueById('edit_plan_id', '');
                setValueById('edit_sex', '');
                setValueById('edit_birthday', '');
                setValueById('edit_height', '');
                setValueById('edit_weight', '');
                setValueById('edit_mobile_number', '');
            }

            toggleMemberFields('edit');

            const form = document.getElementById('editUserForm');
            if (form) {
                form.action = `/admin/user_crud/update/${user.user_id}`;
            }
        }

        // ===========================
        // SHOW USER
        // ===========================

        function showUser(userId) {
            openModal('userShowModal');

            const content = document.getElementById('userShowContent');
            if (content) {
                content.innerHTML = `
                                                        <div class="flex justify-center items-center py-12">
                                                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
                                                        </div>
                                                    `;
            }

            fetch(`/admin/user_crud/show/${userId}`)
                .then(res => {
                    if (!res.ok) throw new Error('Failed to fetch user details');
                    return res.json();
                })
                .then(user => {
                    // Check if staff is trying to view non-member
                    if (isStaff && user.role !== 'member') {
                        closeModal('userShowModal');
                        if (typeof toastr !== 'undefined') {
                            toastr.error('You can only view member details');
                        } else {
                            alert('You can only view member details');
                        }
                        return;
                    }

                    renderUserDetails(user);
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (content) {
                        content.innerHTML = `
                                                                <div class="text-center py-12">
                                                                    <p class="text-red-600">Error loading user details</p>
                                                                </div>
                                                            `;
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to load user details');
                    }
                });
        }

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

// Toggle reference code for edit modal
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
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    };

    let html = `<div class="space-y-5">`;

    // HEADER SECTION
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
                        ${user.role === 'super_admin' ? 'Super Admin' : user.role.charAt(0).toUpperCase() + user.role.slice(1).replace('_', ' ')}
                    </span>
                    <span class="px-3 py-1 text-xs rounded-full font-semibold ${statusColor}">
                        ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                    </span>
                </div>
            </div>
        </div>
    `;

    // USER INFORMATION SECTION
    html += `
        <div class="bg-white border border-gray-200 rounded-lg">
            <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                <h4 class="text-md font-semibold text-gray-800">User Information</h4>
            </div>

            <div class="p-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                    <div>
                        <p class="text-gray-500">User ID</p>
                        <p class="font-semibold text-gray-800">#${user.user_id}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Username</p>
                        <p class="font-medium text-gray-800">@${user.username}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Email Address</p>
                        <p class="font-medium text-gray-800 break-all">${user.email}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Account Created</p>
                        <p class="font-medium text-gray-800">${user.created_at ? formatDate(user.created_at) : 'N/A'}</p>
                    </div>

                    ${user.branch ? `
                        <div>
                            <p class="text-gray-500">Branch</p>
                            <p class="font-medium text-gray-800">${user.branch.name}</p>
                            ${user.branch.address ? `<p class="text-xs text-gray-500 mt-1">${user.branch.address}</p>` : ''}
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;

    // MEMBERSHIP SECTION
    if (user.member) {
        const memberStatusColor = user.member.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700';

        let daysRemainingHTML = '';
        if (user.member.end_date) {
            const endDate = new Date(user.member.end_date);
            const today = new Date();
            const diffTime = endDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays > 0) {
                daysRemainingHTML = `<p class="text-sm text-green-600 mt-1">${diffDays} days remaining</p>`;
            } else if (diffDays < 0) {
                daysRemainingHTML = `<p class="text-sm text-red-600 mt-1">Expired ${Math.abs(diffDays)} days ago</p>`;
            } else {
                daysRemainingHTML = `<p class="text-sm text-orange-600 mt-1">Expires today</p>`;
            }
        }

        html += `
            <div class="bg-white border border-gray-200 rounded-lg">
                <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-md font-semibold text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                        Membership Information
                    </h4>
                </div>

                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                        <div>
                            <p class="text-gray-500">Member ID</p>
                            <p class="font-semibold text-gray-800">#${user.member.member_id}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 mb-2">Membership Status</p>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold ${memberStatusColor}">
                                ${user.member.status.charAt(0).toUpperCase() + user.member.status.slice(1)}
                            </span>
                        </div>

                        ${user.member.plan ? `
                            <div>
                                <p class="text-gray-500">Membership Plan</p>
                                <p class="font-semibold text-gray-800">${user.member.plan.name}</p>
                                <p class="text-xs text-gray-600 mt-1">₱${parseFloat(user.member.plan.price).toFixed(2)} / ${user.member.plan.duration_days} days</p>
                            </div>
                        ` : ''}

                        ${user.member.subscription ? `
                            <div>
                                <p class="text-gray-500">Subscription</p>
                                <p class="font-semibold text-gray-800">${user.member.subscription.name}</p>
                                ${user.member.subscription.duration_days ? `<p class="text-xs text-gray-500 mt-1">${user.member.subscription.duration_days} days</p>` : ''}
                                ${user.member.subscription.price ? `<p class="text-xs text-gray-600">₱${parseFloat(user.member.subscription.price).toFixed(2)}</p>` : ''}
                            </div>
                        ` : ''}

                        ${user.member.start_date ? `
                            <div>
                                <p class="text-gray-500">Start Date</p>
                                <p class="font-medium text-gray-800">${formatDate(user.member.start_date)}</p>
                            </div>
                        ` : ''}

                        ${user.member.end_date ? `
                            <div>
                                <p class="text-gray-500">End Date</p>
                                <p class="font-medium text-gray-800">${formatDate(user.member.end_date)}</p>
                                ${daysRemainingHTML}
                            </div>
                        ` : ''}
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <h5 class="font-semibold text-gray-800 mb-3">Personal Information</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            ${user.member.sex ? `
                                <div>
                                    <p class="text-gray-500">Sex</p>
                                    <p class="font-medium text-gray-800 capitalize">${user.member.sex}</p>
                                </div>
                            ` : ''}

                            ${user.member.birthday ? `
                                <div>
                                    <p class="text-gray-500">Birthday</p>
                                    <p class="font-medium text-gray-800">${formatDate(user.member.birthday)}</p>
                                </div>
                            ` : ''}

                            ${user.member.height ? `
                                <div>
                                    <p class="text-gray-500">Height</p>
                                    <p class="font-medium text-gray-800">${user.member.height} cm</p>
                                </div>
                            ` : ''}

                            ${user.member.weight ? `
                                <div>
                                    <p class="text-gray-500">Weight</p>
                                    <p class="font-medium text-gray-800">${user.member.weight} kg</p>
                                </div>
                            ` : ''}

                            ${user.member.mobile_number ? `
                                <div>
                                    <p class="text-gray-500">Mobile Number</p>
                                    <p class="font-medium text-gray-800">${user.member.mobile_number}</p>
                                </div>
                            ` : ''}
                        </div>

                        ${user.member.qr_code ? `
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-500 mb-2">QR Code</p>
                                <img src="/storage/${user.member.qr_code}" alt="QR Code" class="w-32 h-32 border border-gray-200 rounded-lg">
                            </div>
                        ` : ''}
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Not a Member</h3>
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
                    const valid = validateForm(this, 'add');
                    if (!valid) {
                        e.preventDefault();
                        scrollToFirstError(this);
                    }
                });

            }

            const editUserForm = document.querySelector('#editUserForm');
            if (editUserForm) {
                editUserForm.addEventListener('submit', function (e) {
                    const valid = validateForm(this, 'edit');
                    if (!valid) {
                        e.preventDefault();
                        scrollToFirstError(this);
                    }
                });

            }
        }

        function validateForm(form, mode) {
            let valid = true;
            clearAllErrors(form);

            const prefix = mode === 'add' ? '' : 'edit_';
            const fields = {
                firstName: form.querySelector(`#${prefix}first_name`),
                lastName: form.querySelector(`#${prefix}last_name`),
                username: form.querySelector(`#${prefix}username`),
                email: form.querySelector(`#${prefix}email`),
                password: form.querySelector(`#${prefix}password`),
                passwordConfirmation: form.querySelector(`#${prefix === '' ? 'password_confirmation' : 'password'}`),
                role: form.querySelector(`#${prefix}role`)
            };

            // First Name
            if (fields.firstName && !fields.firstName.value.trim()) {
                showError(fields.firstName, 'First name is required');
                valid = false;
            }

            // Last Name
            if (fields.lastName && !fields.lastName.value.trim()) {
                showError(fields.lastName, 'Last name is required');
                valid = false;
            }

            // Username
            if (fields.username) {
                if (!fields.username.value.trim()) {
                    showError(fields.username, 'Username is required');
                    valid = false;
                } else if (fields.username.value.length < 3) {
                    showError(fields.username, 'Username must be at least 3 characters');
                    valid = false;
                }
            }

            // Email
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (fields.email) {
                if (!fields.email.value.trim()) {
                    showError(fields.email, 'Email is required');
                    valid = false;
                } else if (!emailPattern.test(fields.email.value)) {
                    showError(fields.email, 'Please enter a valid email address');
                    valid = false;
                }
            }

            // Password (only for add mode or if password is provided in edit mode)
            if (mode === 'add' && fields.password) {
                if (!fields.password.value) {
                    showError(fields.password, 'Password is required');
                    valid = false;
                } else if (fields.password.value.length < 6) {
                    showError(fields.password, 'Password must be at least 6 characters');
                    valid = false;
                }

                if (fields.passwordConfirmation) {
                    if (!fields.passwordConfirmation.value) {
                        showError(fields.passwordConfirmation, 'Please confirm your password');
                        valid = false;
                    } else if (fields.password.value !== fields.passwordConfirmation.value) {
                        showError(fields.passwordConfirmation, 'Passwords do not match');
                        valid = false;
                    }
                }
            } else if (mode === 'edit' && fields.password && fields.password.value && fields.password.value.length < 6) {
                showError(fields.password, 'Password must be at least 6 characters');
                valid = false;
            }

            // Member fields validation
            if (fields.role && fields.role.value === 'member') {
                const memberFields = {
                    height: form.querySelector(`#${prefix}height`),
                    weight: form.querySelector(`#${prefix}weight`),
                    mobile: form.querySelector(`#${prefix}mobile_number`)
                };

                if (memberFields.height && memberFields.height.value && (memberFields.height.value <= 0 || memberFields.height.value > 300)) {
                    showError(memberFields.height, 'Please enter a valid height (1-300 cm)');
                    valid = false;
                }

                if (memberFields.weight && memberFields.weight.value && (memberFields.weight.value <= 0 || memberFields.weight.value > 500)) {
                    showError(memberFields.weight, 'Please enter a valid weight (1-500 kg)');
                    valid = false;
                }

                if (memberFields.mobile && memberFields.mobile.value) {
                    const mobilePattern = /^(09|\+639)\d{9}$/;
                    if (!mobilePattern.test(memberFields.mobile.value)) {
                        showError(memberFields.mobile, 'Enter a valid mobile number (e.g., 09123456789)');
                        valid = false;
                    }
                }
            }

            if (!valid && typeof toastr !== 'undefined') {
                toastr.error('Please fix the errors in the form');
            }

            return valid;
        }

        function setupLiveValidation(form, mode) {
            const prefix = mode === 'add' ? '' : 'edit_';

            const fields = {
                firstName: form.querySelector(`#${prefix}first_name`),
                lastName: form.querySelector(`#${prefix}last_name`),
                username: form.querySelector(`#${prefix}username`),
                email: form.querySelector(`#${prefix}email`),
                password: form.querySelector(`#${prefix}password`),
                passwordConfirmation: form.querySelector(`#${prefix === '' ? 'password_confirmation' : 'password'}`),
                height: form.querySelector(`#${prefix}height`),
                weight: form.querySelector(`#${prefix}weight`),
                mobile: form.querySelector(`#${prefix}mobile_number`)
            };

            // Helper function to add validation
            const addValidation = (field, validator) => {
                if (!field) return;

                field.addEventListener('blur', () => validator(field));
                field.addEventListener('input', () => {
                    if (field.value.trim()) validator(field);
                });
            };

            // First Name & Last Name
            addValidation(fields.firstName, (el) => {
                if (el.value.trim()) clearError(el);
            });

            addValidation(fields.lastName, (el) => {
                if (el.value.trim()) clearError(el);
            });

            // Username
            addValidation(fields.username, (el) => {
                if (el.value.trim() && el.value.length >= 3) {
                    clearError(el);
                } else if (el.value.trim() && el.value.length < 3) {
                    showError(el, 'Username must be at least 3 characters');
                }
            });

            // Email
            addValidation(fields.email, (el) => {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (el.value.trim() && emailPattern.test(el.value)) {
                    clearError(el);
                } else if (el.value.trim()) {
                    showError(el, 'Please enter a valid email address');
                }
            });

            // Password
            addValidation(fields.password, (el) => {
                if (el.value && el.value.length >= 6) {
                    clearError(el);
                } else if (el.value && el.value.length < 6) {
                    showError(el, 'Password must be at least 6 characters');
                }
            });

            // Password Confirmation
            if (fields.password && fields.passwordConfirmation) {
                addValidation(fields.passwordConfirmation, (el) => {
                    if (el.value && fields.password.value === el.value) {
                        clearError(el);
                    } else if (el.value) {
                        showError(el, 'Passwords do not match');
                    }
                });
            }

            // Height
            addValidation(fields.height, (el) => {
                if (el.value && (el.value <= 0 || el.value > 300)) {
                    showError(el, 'Please enter a valid height (1-300 cm)');
                } else if (el.value) {
                    clearError(el);
                }
            });

            // Weight
            addValidation(fields.weight, (el) => {
                if (el.value && (el.value <= 0 || el.value > 500)) {
                    showError(el, 'Please enter a valid weight (1-500 kg)');
                } else if (el.value) {
                    clearError(el);
                }
            });

            // Mobile
            addValidation(fields.mobile, (el) => {
                const mobilePattern = /^(09|\+639)\d{9}$/;
                if (el.value && !mobilePattern.test(el.value)) {
                    showError(el, 'Enter a valid mobile number (e.g., 09123456789)');
                } else if (el.value) {
                    clearError(el);
                }
            });
        }

        function scrollToFirstError(form) {
            const firstError = form.querySelector('.error-message');
            if (!firstError) return;

            const scrollContainer = form.closest('.modal-scrollbar') || form.closest('.overflow-y-auto');
            if (scrollContainer) {
                const errorElement = firstError.closest('div').querySelector('input, select, textarea') || firstError.closest('div');
                const containerTop = scrollContainer.scrollTop;
                const errorTop = errorElement.offsetTop;
                const offset = 100;

                scrollContainer.scrollTo({
                    top: errorTop - offset,
                    behavior: 'smooth'
                });
            } else {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // ===========================
        // MEMBER ACTIONS
        // ===========================

        function approveProfile(memberId) {
            if (typeof Swal === 'undefined') {
                const payment = prompt('Enter payment method (cash/gcash):');
                if (payment) {
                    if (payment.toLowerCase() === 'gcash') {
                        const reference = prompt('Enter GCash reference code:');
                        if (reference) {
                            submitProfileApprovalForm(memberId, payment, reference);
                        }
                    } else {
                        submitProfileApprovalForm(memberId, payment, null);
                    }
                }
                return;
            }
            Swal.fire({
                title: 'Approve Profile & Process Payment?',
                html: `
                        <div class="mb-4">
                            <p class="text-gray-700 mb-4">Process membership plan payment:</p>
                            <input type="hidden" id="selected-payment" value="">

                            <div class="space-y-3">
                                <div class="payment-option border-2 border-gray-200 rounded-xl p-4 cursor-pointer" data-payment="cash">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </div>
                                            <div class="text-left">
                                                <div class="font-semibold text-gray-800">Cash Payment</div>
                                                <div class="text-sm text-gray-500">Direct cash payment</div>
                                            </div>
                                        </div>
                                        <div class="checkmark hidden w-6 h-6 bg-green-500 rounded-full items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="payment-option border-2 border-gray-200 rounded-xl p-4 cursor-pointer" data-payment="gcash">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div class="text-left">
                                                <div class="font-semibold text-gray-800">GCash Payment</div>
                                                <div class="text-sm text-gray-500">Mobile wallet</div>
                                            </div>
                                        </div>
                                        <div class="checkmark hidden w-6 h-6 bg-blue-500 rounded-full items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Process Payment',
                cancelButtonText: 'Cancel',
                didOpen: () => {
                    const options = document.querySelectorAll('.payment-option');
                    const hiddenInput = document.getElementById('selected-payment');

                    options.forEach(option => {
                        option.addEventListener('click', function () {
                            options.forEach(opt => {
                                opt.classList.remove('selected');
                                opt.style.borderColor = '#e5e7eb';
                                opt.style.background = 'white';
                                opt.querySelector('.checkmark').style.display = 'none';
                            });

                            this.classList.add('selected');
                            const payment = this.getAttribute('data-payment');
                            hiddenInput.value = payment;

                            if (payment === 'cash') {
                                this.style.borderColor = '#10b981';
                                this.style.background = 'linear-gradient(to bottom, #f0fdf4, white)';
                            } else {
                                this.style.borderColor = '#3b82f6';
                                this.style.background = 'linear-gradient(to bottom, #eff6ff, white)';
                            }

                            this.querySelector('.checkmark').style.display = 'flex';
                        });
                    });
                },
                preConfirm: () => {
                    const selectedPayment = document.getElementById('selected-payment').value;
                    if (!selectedPayment) {
                        Swal.showValidationMessage('Please select a payment method');
                        return false;
                    }
                    return selectedPayment;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const payment = result.value;

                    if (payment === 'gcash') {
                        Swal.fire({
                            title: 'GCash Reference',
                            html: `
                                    <div class="text-left">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Enter GCash Reference Number:</label>
                                        <input type="text" id="gcash-reference" class="w-full px-4 py-2 border-2 rounded-lg" placeholder="e.g., 1234567890123">
                                    </div>
                                `,
                            showCancelButton: true,
                            confirmButtonColor: '#3b82f6',
                            preConfirm: () => {
                                const reference = document.getElementById('gcash-reference').value;
                                if (!reference) {
                                    Swal.showValidationMessage('Reference number is required');
                                    return false;
                                }
                                return reference;
                            }
                        }).then((refResult) => {
                            if (refResult.isConfirmed) {
                                submitProfileApprovalForm(memberId, 'gcash', refResult.value);
                            }
                        });
                    } else {
                        submitProfileApprovalForm(memberId, 'cash', null);
                    }
                }
            });
        }
        // Helper function to submit the profile approval form
        function submitProfileApprovalForm(memberId, paymentMethod, referenceCode) {
            const form = document.createElement('form');
            form.method = 'GET';
            let url = `/admin/user_crud/approve-profile/${memberId}?payment_method=${paymentMethod}`;
            if (referenceCode) {
                url += `&reference_code=${encodeURIComponent(referenceCode)}`;
            }

            form.action = url;

            document.body.appendChild(form);
            form.submit();
        }

        function approveSubscription(memberId) {
            if (typeof Swal === 'undefined') {
                const payment = prompt('Enter payment method (cash/gcash):');
                if (payment) {
                    if (payment.toLowerCase() === 'gcash') {
                        const reference = prompt('Enter GCash reference code:');
                        if (reference) {
                            submitSubscriptionApprovalForm(memberId, payment, reference);
                        }
                    } else {
                        submitSubscriptionApprovalForm(memberId, payment, null);
                    }
                }
                return;
            }

            Swal.fire({
                title: 'Approve & Process Payment?',
                html: `
                <div class="mb-4">
                    <p class="text-gray-700 mb-4">Process subscription payment:</p>
                    <input type="hidden" id="selected-payment" value="">

                    <div class="space-y-3">
                        <div class="payment-option border-2 border-gray-200 rounded-xl p-4 cursor-pointer" data-payment="cash">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="font-semibold text-gray-800">Cash Payment</div>
                                        <div class="text-sm text-gray-500">Direct cash payment</div>
                                    </div>
                                </div>
                                <div class="checkmark hidden w-6 h-6 bg-green-500 rounded-full items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="payment-option border-2 border-gray-200 rounded-xl p-4 cursor-pointer" data-payment="gcash">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="font-semibold text-gray-800">GCash Payment</div>
                                        <div class="text-sm text-gray-500">Mobile wallet</div>
                                    </div>
                                </div>
                                <div class="checkmark hidden w-6 h-6 bg-blue-500 rounded-full items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Process Payment',
                cancelButtonText: 'Cancel',
                didOpen: () => {
                    const options = document.querySelectorAll('.payment-option');
                    const hiddenInput = document.getElementById('selected-payment');

                    options.forEach(option => {
                        option.addEventListener('click', function () {
                            options.forEach(opt => {
                                opt.classList.remove('selected');
                                opt.style.borderColor = '#e5e7eb';
                                opt.style.background = 'white';
                                opt.querySelector('.checkmark').style.display = 'none';
                            });

                            this.classList.add('selected');
                            const payment = this.getAttribute('data-payment');
                            hiddenInput.value = payment;

                            if (payment === 'cash') {
                                this.style.borderColor = '#10b981';
                                this.style.background = 'linear-gradient(to bottom, #f0fdf4, white)';
                            } else {
                                this.style.borderColor = '#3b82f6';
                                this.style.background = 'linear-gradient(to bottom, #eff6ff, white)';
                            }

                            this.querySelector('.checkmark').style.display = 'flex';
                        });
                    });
                },
                preConfirm: () => {
                    const selectedPayment = document.getElementById('selected-payment').value;
                    if (!selectedPayment) {
                        Swal.showValidationMessage('Please select a payment method');
                        return false;
                    }
                    return selectedPayment;
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    const payment = result.value;

                    if (payment === 'gcash') {
                        Swal.fire({
                            title: 'GCash Reference',
                            html: `
                            <div class="text-left">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Enter GCash Reference Number:</label>
                                <input type="text" id="gcash-reference" class="w-full px-4 py-2 border-2 rounded-lg" placeholder="e.g., 1234567890123">
                            </div>
                        `,
                            showCancelButton: true,
                            confirmButtonColor: '#3b82f6',
                            preConfirm: () => {
                                const reference = document.getElementById('gcash-reference').value;
                                if (!reference) {
                                    Swal.showValidationMessage('Reference number is required');
                                    return false;
                                }
                                return reference;
                            }
                        }).then((refResult) => {
                            if (refResult.isConfirmed) {
                                submitSubscriptionApprovalForm(memberId, 'gcash', refResult.value);
                            }
                        });
                    } else {
                        submitSubscriptionApprovalForm(memberId, 'cash', null);
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
    const editPlanSelect = document.getElementById('edit_plan_id');
    const editSubscriptionSelect = document.getElementById('edit_subscription_id');
    
    if (editPlanSelect) {
        editPlanSelect.addEventListener('change', updateEditPaymentSection);
    }
    
    if (editSubscriptionSelect) {
        editSubscriptionSelect.addEventListener('change', updateEditPaymentSection);
    }
});

        function submitSubscriptionApprovalForm(memberId, paymentMethod, referenceCode) {
            const form = document.createElement('form');
            form.method = 'GET';
            let url = `/admin/user_crud/approve-subscription/${memberId}?payment_method=${paymentMethod}`;
            if (referenceCode) {
                url += `&reference_code=${encodeURIComponent(referenceCode)}`;
            }

            form.action = url;
            document.body.appendChild(form);
            form.submit();
        }


        function denyMember(memberId) {
            if (typeof Swal === 'undefined') {
                if (confirm('Deny this member?')) {
                    window.location.href = `/admin/user_crud/deny/${memberId}`;
                }
                return;
            }

            Swal.fire({
                title: 'Deny Member?',
                text: "This member will not be able to access the system.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, deny',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/admin/user_crud/deny/${memberId}`;
                }
            });
        }

        function suspendMember(memberId) {
            if (typeof Swal === 'undefined') {
                if (confirm('Suspend this member? This will immediately expire their subscription.')) {
                    window.location.href = `/admin/user_crud/suspend/${memberId}`;
                }
                return;
            }

            Swal.fire({
                title: 'Suspend Member?',
                html: `
                                                        <p class="text-gray-700 mb-2">This will immediately expire the member's subscription.</p>
                                                        <p class="text-sm text-gray-500">The remaining days will be saved and can be restored if reactivated.</p>
                                                    `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f97316',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, suspend',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/admin/user_crud/suspend/${memberId}`;
                }
            });
        }

        function reactivateMember(memberId) {
            if (typeof Swal === 'undefined') {
                if (confirm('Reactivate this member? Their remaining days will be restored.')) {
                    window.location.href = `/admin/user_crud/reactivate/${memberId}`;
                }
                return;
            }

            Swal.fire({
                title: 'Reactivate Member?',
                html: `
                                                        <p class="text-gray-700 mb-2">This will restore the member's subscription.</p>
                                                        <p class="text-sm text-gray-500">Their remaining days will be restored if available.</p>
                                                    `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, reactivate',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/admin/user_crud/reactivate/${memberId}`;
                }
            });
        }

        function openCancelPlanModal(memberId) {
    const modal = document.getElementById('cancelPlanModal');
    const form = document.getElementById('cancelPlanForm');
    form.action = `/admin/user_crud/cancel-plan/${memberId}`;

    const scrollY = window.scrollY;

    // Prevent body scroll
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = '100%';
    document.body.style.overflowY = 'scroll';

    modal.classList.remove('hidden');
}

function closeCancelPlanModal() {
    const modal = document.getElementById('cancelPlanModal');
    const scrollY = document.body.style.top;

    modal.classList.add('hidden');

    // Restore body scroll
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.width = '';
    document.body.style.overflowY = '';

    window.scrollTo(0, parseInt(scrollY || '0') * -1);
}

// ===========================
// RESUME MEMBER MODAL
// ===========================

function openResumeModal(memberId) {
    const modal = document.getElementById('resumeModal');
    const form = document.getElementById('resumeForm');
    form.action = `/admin/user_crud/resume/${memberId}`;

    const scrollY = window.scrollY;

    // Prevent body scroll
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = '100%';
    document.body.style.overflowY = 'scroll';

    modal.classList.remove('hidden');
}

function closeResumeModal() {
    const modal = document.getElementById('resumeModal');
    const scrollY = document.body.style.top;

    modal.classList.add('hidden');

    // Restore body scroll
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.width = '';
    document.body.style.overflowY = '';

    window.scrollTo(0, parseInt(scrollY || '0') * -1);
}

// ===========================
// UPDATED SUSPEND MEMBER MODAL
// ===========================

function openSuspendModal(memberId) {
    const modal = document.getElementById('suspendModal');
    const form = document.getElementById('suspendForm');
    form.action = `/admin/user_crud/suspend/${memberId}`;

    const scrollY = window.scrollY;

    // Prevent body scroll
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = '100%';
    document.body.style.overflowY = 'scroll';

    modal.classList.remove('hidden');
}

function closeSuspendModal() {
    const modal = document.getElementById('suspendModal');
    const scrollY = document.body.style.top;

    modal.classList.add('hidden');

    // Restore body scroll
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.width = '';
    document.body.style.overflowY = '';

    window.scrollTo(0, parseInt(scrollY || '0') * -1);
}

// Updated suspend member function
function suspendMember(memberId) {
    closeAllActionsMenus();
    openSuspendModal(memberId);
}

// Updated reactivate function (now called resume)
function resumeMember(memberId) {
    closeAllActionsMenus();
    openResumeModal(memberId);
}

// New cancel plan function
function cancelPlan(memberId) {
    closeAllActionsMenus();
    openCancelPlanModal(memberId);
}

        // ===========================
        // INITIALIZATION
        // ===========================

        function addCustomScrollbarStyles() {
            if (document.getElementById('customScrollbarStyles')) return;

            const style = document.createElement('style');
            style.id = 'customScrollbarStyles';
            style.textContent = `
                                                    /* Custom Scrollbar for Modals */
                                                    .overflow-y-auto::-webkit-scrollbar {
                                                        width: 8px;
                                                    }

                                                    .overflow-y-auto::-webkit-scrollbar-track {
                                                        background: #F3F4F6;
                                                        border-radius: 10px;
                                                    }

                                                    .overflow-y-auto::-webkit-scrollbar-thumb {
                                                        background: #9CA3AF;
                                                        border-radius: 10px;
                                                    }

                                                    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                                                        background: #6B7280;
                                                    }

                                                    /* Firefox */
                                                    .overflow-y-auto {
                                                        scrollbar-width: thin;
                                                        scrollbar-color: #9CA3AF #F3F4F6;
                                                        scroll-behavior: smooth;
                                                    }

                                                    /* SweetAlert Custom Styles */
                                                    .swal-custom-popup {
                                                        border-radius: 1rem !important;
                                                    }

                                                    .swal-confirm-btn,
                                                    .swal-cancel-btn {
                                                        border-radius: 0.5rem !important;
                                                        padding: 0.5rem 1.5rem !important;
                                                    }

                                                    /* Rotate icon */
                                                    .rotate-180 {
                                                        transform: rotate(180deg);
                                                    }
                                                `;
            document.head.appendChild(style);
        }

        // Initialize everything when DOM is ready
        document.addEventListener('DOMContentLoaded', function () {
            // Add custom styles
            addCustomScrollbarStyles();

            // Initialize member fields visibility
            toggleMemberFields('add');

            // Configure toastr if available
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

            // Setup form validation
            setupFormValidation();

            // Initialize filter count
            updateFilterCount();

            // Close dropdown when clicking outside
            document.addEventListener('click', function (event) {
                const dropdown = document.getElementById('filterDropdown');
                const button = event.target.closest('button[onclick*="toggleFilterDropdown"]');

                if (!button && dropdown && !dropdown.contains(event.target) && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                    const icon = document.getElementById('filterDropdownIcon');
                    if (icon) icon.classList.remove('rotate-180');
                }

                // Close action menus when clicking outside
                const actionsMenus = document.querySelectorAll('[id^="actionsMenu-"]');
                const isClickInsideMenu = event.target.closest('[id^="actionsMenu-"]');
                const isClickOnToggleButton = event.target.closest('[onclick*="toggleActionsMenu"]');

                if (!isClickInsideMenu && !isClickOnToggleButton) {
                    actionsMenus.forEach(menu => {
                        menu.classList.add('hidden');
                    });
                }
            });

            // Handle Escape key to close modals

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    // Close all action menus
                    closeAllActionsMenus();

                    // Close modals
                    const openModals = document.querySelectorAll('.backdrop-blur-sm:not(.hidden)');
                    openModals.forEach(modal => {
                        if (modal.id) closeModal(modal.id);
                    });
                }
            });
        });

    </script>



@endsection