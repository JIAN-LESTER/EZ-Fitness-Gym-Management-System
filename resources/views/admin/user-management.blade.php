@extends('layouts.app')

@section('title', 'User Management')
@section('header', 'User Management')

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
</style>

@section('content')
    @php
        $isStaff = auth()->user()->role === 'staff';
    @endphp

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">

        <!-- Header / Add Button -->
        <div
            class="flex justify-between items-center p-4 sm:p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800">User Management</h2>
            <button onclick="openModal('addUserModal')"
                class="flex items-center gap-2 bg-gradient-to-r from-gray-600 to-gray-700 text-white px-6 py-3 rounded-xl hover:from-gray-700 hover:to-gray-800 shadow-md hover:shadow-lg transition-all duration-300 font-semibold whitespace-nowrap">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add {{ $isStaff ? 'Member' : 'User' }}
            </button>
        </div>

        <!-- Search & Filters -->
        <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-200">
            <form method="GET" action="{{ route('admin.user_management') }}" class="space-y-4" role="search">

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
                            class="w-full sm:w-auto flex items-center justify-between gap-2 bg-white border-2 border-gray-200 text-gray-700 px-6 py-3 rounded-xl hover:border-gray-300 shadow-sm transition-all duration-300 font-semibold whitespace-nowrap">
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
                                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 rounded-lg transition-colors">
                                        Apply Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full sm:w-auto">
                        <button type="submit"
                            class="w-full sm:w-auto flex items-center justify-center gap-2 bg-gradient-to-r from-gray-600 to-gray-700 text-white px-6 py-3 rounded-xl hover:from-gray-700 hover:to-gray-800 shadow-md hover:shadow-lg transition-all duration-300 font-semibold whitespace-nowrap">
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
                            Membership</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        {{-- Skip non-members if user is staff --}}
                        @if($isStaff && $user->role !== 'member')
                            @continue
                        @endif

                        @php
                            $pendingApproval = $user->member
                                && $user->member->isApproved == false
                                && $user->member->isDisabled == false
                                && $user->member->plan_id !== null;

                            $isDenied = $user->member
                                && $user->member->isDisabled == true
                                && $user->member->isApproved == false;

                            $incompleteProfile = $user->member
                                && ($user->member->plan_id === null
                                    || $user->member->sex === null
                                    || $user->member->birthday === null
                                    || $user->member->mobile_number === null);

                            $isSuspended = $user->member && $user->member->status === 'expired';
                            $isRenewalPending = $user->member && $user->member->renewal_pending;
                        @endphp

                        @if($incompleteProfile)
                            @continue
                        @endif

                        <tr class="clickable-row hover:bg-gray-50 transition-colors group"
                            onclick="showUser('{{ $user->user_id }}')">

                            @if($pendingApproval || $isRenewalPending)
                                {{-- PENDING APPROVAL OR RENEWAL --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-yellow-200 flex items-center justify-center text-gray-800 font-bold text-sm shadow-md">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 truncate">{{ $user->first_name }}
                                                {{ $user->last_name }}
                                            </p>
                                            <p class="text-gray-500 text-sm truncate">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td colspan="4" class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-yellow-600 font-medium">
                                            {{ $isRenewalPending ? 'Renewal pending' : 'Awaiting approval' }} -
                                            {{ $user->member->plan->name ?? 'No Plan' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-center gap-3">
                                        <button onclick="approveMember('{{ $user->member->member_id }}')"
                                            class="flex items-center gap-1 px-3 py-1.5 bg-green-500 text-white rounded-lg hover:bg-green-600 font-semibold text-sm transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ $isRenewalPending ? 'Approve Renewal' : 'Approve' }}
                                        </button>

                                        <button onclick="denyMember('{{ $user->member->member_id }}')"
                                            class="flex items-center gap-1 px-3 py-1.5 bg-red-500 text-white rounded-lg hover:bg-red-600 font-semibold text-sm transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Deny
                                        </button>
                                    </div>
                                </td>

                            @elseif($isDenied)
                                {{-- DENIED --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-red-200 flex items-center justify-center text-gray-800 font-bold text-sm shadow-md">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 truncate">{{ $user->first_name }}
                                                {{ $user->last_name }}
                                            </p>
                                            <p class="text-gray-500 text-sm truncate">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td colspan="4" class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        <span class="text-red-600 font-medium">Membership Disabled</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                                    <div class="flex items-center justify-center gap-3">
                                        <button onclick="approveMember('{{ $user->member->member_id }}')"
                                            class="flex items-center gap-1 px-3 py-1.5 bg-green-500 text-white rounded-lg hover:bg-green-600 font-semibold text-sm transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Enable
                                        </button>

                                        <button onclick="openDeleteModal('{{ route('admin.users-destroy', $user->user_id) }}')"
                                            class="text-red-500 hover:text-red-700" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>

                            @else
                                {{-- ACTIVE/NORMAL USERS --}}
                                @if($user->role !== 'member' || ($user->member && $user->member->status !== 'inactive' && $user->member->isApproved == true))
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-800 font-bold text-sm shadow-md">
                                                    {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? 'N', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-900 truncate">{{ $user->first_name }}
                                                        {{ $user->last_name }}
                                                    </p>
                                                    <p class="text-gray-500 text-sm truncate">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 text-gray-700 font-medium">{{ $user->username }}</td>

                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full 
                                                                    {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' :
                                    ($user->role === 'staff' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2 py-1 text-xs font-semibold rounded-full 
                                                                    {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                                {{ ucfirst($user->status) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-gray-700">
                                            @if($user->member)
                                                    <div class="text-sm">
                                                        <span
                                                            class="px-2 py-1 text-xs font-semibold rounded-full 
                                                                                            {{ $user->member->status === 'active' ? 'bg-green-100 text-green-700' :
                                                ($user->member->status === 'expired' ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700') }}">
                                                            {{ $user->member->plan->name ?? 'No Plan' }}
                                                        </span>
                                                        @if($user->member->status === 'expired')
                                                            <p class="text-xs text-red-600 mt-1 font-semibold">
                                                                {{ $isSuspended && $user->member->suspended_at ? 'Suspended' : 'Expired' }}
                                                            </p>
                                                        @elseif($user->member->end_date)
                                                            @php
                                                                $now = now();
                                                                $end = $user->member->end_date;
                                                                $days = (int) $now->diffInDays($end, false);
                                                                $hours = (int) $now->diffInHours($end, false);
                                                            @endphp
                                                            <p class="text-xs text-gray-500 mt-1">
                                                                @if ($days >= 1)
                                                                    {{ $days }} {{ $days == 1 ? 'day' : 'days' }} left
                                                                @elseif ($hours > 0)
                                                                    {{ $hours }} {{ $hours == 1 ? 'hour' : 'hours' }} left
                                                                @elseif ($hours === 0)
                                                                    Expires today
                                                                @else
                                                                    Expired
                                                                @endif
                                                            </p>
                                                        @endif
                                                    </div>
                                            @else
                                                <span class="text-gray-400 text-xs">Not a member</span>
                                            @endif
                                        </td>

                                        <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                                            <div class="relative inline-block text-left">
                                                <button onclick="toggleActionsMenu(event, '{{ $user->user_id }}')"
                                                    class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                                                    title="Actions">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                    </svg>
                                                </button>

                                                <div id="actionsMenu-{{ $user->user_id }}"
                                                    class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                                                    <div class="py-1">
                                                        <button onclick="editUser('{{ $user->user_id }}')"
                                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor" class="w-4 h-4">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.828a2 2 0 01-1.414.586H9v-2a2 2 0 01.586-1.414z" />
                                                            </svg>
                                                            Edit User
                                                        </button>

                                                        @if($user->role === 'member' && $user->member)
                                                            @if($user->member->status === 'expired')
                                                                <button onclick="reactivateMember('{{ $user->member->member_id }}')"
                                                                    class="w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-gray-100 flex items-center gap-2">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                        stroke="currentColor" class="w-4 h-4">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    Reactivate
                                                                </button>
                                                            @else
                                                                <button onclick="suspendMember('{{ $user->member->member_id }}')"
                                                                    class="w-full text-left px-4 py-2 text-sm text-orange-600 hover:bg-gray-100 flex items-center gap-2">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                        stroke="currentColor" class="w-4 h-4">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                            d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                    </svg>
                                                                    Suspend
                                                                </button>
                                                            @endif
                                                        @endif

                                                        <button
                                                            onclick="openDeleteModal('{{ route('admin.users-destroy', $user->user_id) }}')"
                                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor" class="w-4 h-4">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3" />
                                                            </svg>
                                                            Delete User
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                @endif
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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
                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-gray-600 to-gray-700 text-white hover:from-gray-700 hover:to-gray-800 shadow-md transition-all duration-200 font-medium">Prev</a>
                    @endif

                    <span class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium sm:hidden">
                        {{ $users->currentPage() }} / {{ $users->lastPage() }}
                    </span>

                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}"
                            class="px-4 py-2 rounded-xl bg-gradient-to-r from-gray-600 to-gray-700 text-white hover:from-gray-700 hover:to-gray-800 shadow-md transition-all duration-200 font-medium">Next</a>
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

        <div
            class="relative bg-gray-100 dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-600 text-white p-5 rounded-t-2xl flex-shrink-0">
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
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">First
                                Name</label>
                            <input type="text" name="first_name" id="first_name"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            @error('first_name')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last
                                Name</label>
                            <input type="text" name="last_name" id="last_name"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            @error('last_name')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="username"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                        <input type="text" name="username" id="username"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        @error('username')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" id="email"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                            <input type="password" name="password" id="password"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            @error('password')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        </div>
                    </div>

                    {{-- Only show role selector for non-staff --}}
                    @if(!$isStaff)
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                            <select name="role" id="role" onchange="toggleMemberFields('add')"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                <option value="member">Member</option>
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    @endif

                    {{-- Member fields - always visible for staff --}}
                    <div id="addMemberFields" class="space-y-4 border-t border-gray-300 dark:border-gray-700 pt-4"
                        style="{{ $isStaff ? 'display: block;' : '' }}">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Member Profile
                            {{ $isStaff ? '(Required)' : '(Optional)' }}
                        </h3>

                        <div>
                            <label for="plan_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Membership Plan
                                {{ $isStaff ? '<span class="text-red-500">*</span>' : '' }}</label>
                            <select name="plan_id" id="plan_id" {{ $isStaff ? 'required' : '' }}
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                <option value="">Select a plan{{ $isStaff ? '' : ' (optional)' }}</option>
                                @foreach($plans ?? [] as $plan)
                                    <option value="{{ $plan->plan_id }}">{{ $plan->name }} -
                                        ₱{{ number_format($plan->price, 2) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="sex" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sex
                                    {{ $isStaff ? '<span class="text-red-500">*</span>' : '' }}</label>
                                <select name="sex" id="sex" {{ $isStaff ? 'required' : '' }}
                                    class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                    <option value="">Select...</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div>
                                <label for="birthday"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Birthday
                                    {{ $isStaff ? '<span class="text-red-500">*</span>' : '' }}</label>
                                <input type="date" name="birthday" id="birthday" {{ $isStaff ? 'required' : '' }}
                                    class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="height"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Height (cm)</label>
                                <input type="number" step="0.1" name="height" id="height"
                                    class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            </div>
                            <div>
                                <label for="weight"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Weight (kg)</label>
                                <input type="number" step="0.1" name="weight" id="weight"
                                    class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            </div>
                        </div>

                        <div>
                            <label for="mobile_number"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mobile Number
                                {{ $isStaff ? '<span class="text-red-500">*</span>' : '' }}</label>
                            <input type="tel" name="mobile_number" id="mobile_number" placeholder="e.g. 09123456789" {{ $isStaff ? 'required' : '' }}
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        </div>
                    </div>

                    <div
                        class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700 sticky bottom-0 bg-gray-100 dark:bg-gray-900 pb-2">
                        <button type="button" onclick="closeModal('addUserModal')"
                            class="px-6 py-2 rounded-lg bg-gray-400 text-white hover:bg-gray-500 w-full sm:w-auto">Cancel</button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-700 w-full sm:w-auto">Add
                            {{ $isStaff ? 'Member' : 'User' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modified Edit User Modal - Similar changes --}}
    <div id="editUserModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('editUserModal')"></div>

        <div
            class="relative bg-gray-100 dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
            <header class="bg-gray-600 text-white p-5 rounded-t-2xl flex-shrink-0">
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_first_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                            <input type="text" name="first_name" id="edit_first_name"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            @error('first_name')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="edit_last_name"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                            <input type="text" name="last_name" id="edit_last_name"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            @error('last_name')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="edit_username"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                        <input type="text" name="username" id="edit_username"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        @error('username')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="edit_email"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" name="email" id="edit_email"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password
                                (Optional)</label>
                            <input type="password" name="password" id="edit_password"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            @error('password')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="edit_password_confirmation"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="edit_password_confirmation"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        </div>
                    </div>

                    @if(!$isStaff)
                        <div>
                            <label for="edit_role"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                            <select name="role" id="edit_role" onchange="toggleMemberFields('edit')"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                <option value="member">Member</option>
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div>
                            <label for="edit_status"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select name="status" id="edit_status"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    @endif

                    {{-- Member fields - always visible for staff --}}
                    <div id="editMemberFields" class="space-y-4 border-t border-gray-300 dark:border-gray-700 pt-4"
                        style="{{ $isStaff ? 'display: block;' : '' }}">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Member Profile
                            {{ $isStaff ? '(Required)' : '(Optional)' }}
                        </h3>

                        <div>
                            <label for="edit_plan_id"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Membership Plan</label>
                            <select name="plan_id" id="edit_plan_id"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                <option value="">Select a plan (optional)</option>
                                @foreach($plans ?? [] as $plan)
                                    <option value="{{ $plan->plan_id }}">{{ $plan->name }} -
                                        ₱{{ number_format($plan->price, 2) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="edit_sex"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sex</label>
                                <select name="sex" id="edit_sex"
                                    class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                    <option value="">Select...</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div>
                                <label for="edit_birthday"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Birthday</label>
                                <input type="date" name="birthday" id="edit_birthday"
                                    class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="edit_height"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Height (cm)</label>
                                <input type="number" step="0.1" name="height" id="edit_height"
                                    class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            </div>
                            <div>
                                <label for="edit_weight"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Weight (kg)</label>
                                <input type="number" step="0.1" name="weight" id="edit_weight"
                                    class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            </div>
                        </div>

                        <div>
                            <label for="edit_mobile_number"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mobile Number</label>
                            <input type="tel" name="mobile_number" id="edit_mobile_number" placeholder="e.g. 09123456789"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        </div>
                    </div>

                    <div
                        class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700 sticky bottom-0 bg-gray-100 dark:bg-gray-900 pb-2">
                        <button type="button" onclick="closeModal('editUserModal')"
                            class="px-6 py-2 rounded-lg bg-gray-400 text-white hover:bg-gray-500 w-full sm:w-auto">Cancel</button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-700 w-full sm:w-auto">Update
                            {{ $isStaff ? 'Member' : 'User' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Details Modal -->
    <div id="userShowModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('userShowModal')"></div>

        <div
            class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
            <header class="bg-gray-600 text-white p-5 rounded-t-2xl sticky top-0 z-10 flex justify-between items-center">
                <h2 class="text-xl font-semibold">User Details</h2>
                <button onclick="closeModal('userShowModal')" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <div id="userShowContent" class="p-6">
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
                </div>
            </div>
        </div>
    </div>

    <script>

        const isStaff = {{ $isStaff ? 'true' : 'false' }};

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
            if (typeof Swal === 'undefined') {
                if (confirm('Are you sure you want to delete this user?')) {
                    submitDeleteForm(actionUrl);
                }
                return;
            }

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

        function renderUserDetails(user) {
            const content = document.getElementById('userShowContent');
            if (!content) return;

            const avatarInitial = `${user.first_name?.charAt(0) || 'U'}${user.last_name?.charAt(0) || 'N'}`.toUpperCase();

            let roleColor = 'bg-blue-100 text-blue-700';
            if (user.role === 'admin') roleColor = 'bg-red-100 text-red-700';
            if (user.role === 'staff') roleColor = 'bg-purple-100 text-purple-700';

            const statusColor = user.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600';

            let html = `
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-1">
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                            <div class="flex flex-col items-center mb-6">
                                ${user.avatar ?
                    `<img src="/storage/${user.avatar}" alt="${user.first_name}" class="w-32 h-32 rounded-full object-cover border-4 border-gray-200">` :
                    `<div class="w-32 h-32 rounded-full bg-gray-400 flex items-center justify-center text-4xl font-bold text-white">${avatarInitial}</div>`
                }

                                <h2 class="text-2xl font-bold mt-4 text-gray-800 dark:text-gray-200">${user.first_name} ${user.last_name}</h2>
                                <p class="text-gray-500 dark:text-gray-400">@${user.username}</p>

                                <div class="flex gap-2 mt-3">
                                    <span class="px-3 py-1 text-xs rounded-full ${roleColor}">${user.role.charAt(0).toUpperCase() + user.role.slice(1)}</span>
                                    <span class="px-3 py-1 text-xs rounded-full ${statusColor}">${user.status.charAt(0).toUpperCase() + user.status.slice(1)}</span>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                                    <p class="text-gray-800 dark:text-gray-200 font-medium break-all">${user.email}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">User ID</p>
                                    <p class="text-gray-800 dark:text-gray-200 font-medium">#${user.user_id}</p>
                                </div>
                                ${user.created_at ? `
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Member Since</p>
                                        <p class="text-gray-800 dark:text-gray-200 font-medium">${new Date(user.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                                    </div>
                                ` : ''}
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                                <button onclick="closeModal('userShowModal'); editUser('${user.user_id}');" 
                                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Edit User
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2 space-y-6">
            `;

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
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            Membership Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Plan</p>
                                <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">${user.member.plan ? user.member.plan.name : 'No Plan Assigned'}</p>
                                ${user.member.plan ? `<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">₱${parseFloat(user.member.plan.price).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} / ${user.member.plan.duration_days} days</p>` : ''}
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Membership Status</p>
                                <span class="inline-block px-3 py-1 text-sm rounded-full ${memberStatusColor}">${user.member.status.charAt(0).toUpperCase() + user.member.status.slice(1)}</span>
                            </div>

                            ${user.member.start_date ? `
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Start Date</p>
                                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">${new Date(user.member.start_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</p>
                                </div>
                            ` : ''}

                            ${user.member.end_date ? `
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">End Date</p>
                                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">${new Date(user.member.end_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</p>
                                    ${daysRemainingHTML}
                                </div>
                            ` : ''}
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Personal Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                ${user.member.sex ? `<div><p class="text-sm text-gray-500 dark:text-gray-400">Sex</p><p class="text-gray-800 dark:text-gray-200 font-medium capitalize">${user.member.sex}</p></div>` : ''}
                                ${user.member.birthday ? `<div><p class="text-sm text-gray-500 dark:text-gray-400">Birthday</p><p class="text-gray-800 dark:text-gray-200 font-medium">${new Date(user.member.birthday).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p></div>` : ''}
                                ${user.member.height ? `<div><p class="text-sm text-gray-500 dark:text-gray-400">Height</p><p class="text-gray-800 dark:text-gray-200 font-medium">${user.member.height} cm</p></div>` : ''}
                                ${user.member.weight ? `<div><p class="text-sm text-gray-500 dark:text-gray-400">Weight</p><p class="text-gray-800 dark:text-gray-200 font-medium">${user.member.weight} kg</p></div>` : ''}
                                ${user.member.mobile_number ? `<div><p class="text-sm text-gray-500 dark:text-gray-400">Mobile Number</p><p class="text-gray-800 dark:text-gray-200 font-medium">${user.member.mobile_number}</p></div>` : ''}
                                ${user.member.qr_code ? `<div class="md:col-span-2"><p class="text-sm text-gray-500 dark:text-gray-400 mb-2">QR Code</p><img src="/storage/${user.member.qr_code}" alt="QR Code" class="w-32 h-32 border border-gray-200 rounded"></div>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <div class="text-center py-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Not a Member</h3>
                            <p class="text-gray-600 dark:text-gray-400">This user doesn't have a membership profile yet.</p>
                        </div>
                    </div>
                `;
            }

            html += '</div></div>';
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

        function approveMember(memberId) {
            if (typeof Swal === 'undefined') {
                if (confirm('Approve this member?')) {
                    const payment = prompt('Enter payment method (cash/gcash):');
                    if (payment) {
                        if (payment.toLowerCase() === 'gcash') {
                            const reference = prompt('Enter GCash reference code:');
                            if (reference) {
                                window.location.href = `/admin/user_crud/approve/${memberId}?payment=${payment}&reference=${encodeURIComponent(reference)}`;
                            } else {
                                alert('GCash reference code is required');
                            }
                        } else {
                            window.location.href = `/admin/user_crud/approve/${memberId}?payment=${payment}`;
                        }
                    }
                }
                return;
            }

            Swal.fire({
                title: 'Approve Member?',
                html: `
                <div class="mb-4">
                    <p class="text-gray-700 mb-4">Please select a payment method for this membership:</p>

                    <input type="hidden" id="selected-payment" value="">

                    <div class="space-y-3">
                        <!-- Cash Option -->
                        <div class="payment-option border-2 border-gray-200 rounded-xl p-4 cursor-pointer transition-all hover:border-green-400" 
                             data-payment="cash"
                             style="transition: all 0.3s ease;">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
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

                        <!-- GCash Option -->
                        <div class="payment-option border-2 border-gray-200 rounded-xl p-4 cursor-pointer transition-all hover:border-blue-400" 
                             data-payment="gcash"
                             style="transition: all 0.3s ease;">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="text-left">
                                        <div class="font-semibold text-gray-800">GCash Payment</div>
                                        <div class="text-sm text-gray-500">Mobile wallet payment</div>
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
                confirmButtonText: 'Confirm Payment',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'payment-modal-popup'
                },
                didOpen: () => {
                    // Add hover and click effects
                    const options = document.querySelectorAll('.payment-option');
                    const hiddenInput = document.getElementById('selected-payment');

                    options.forEach(option => {
                        // Hover effect
                        option.addEventListener('mouseenter', function () {
                            if (!this.classList.contains('selected')) {
                                this.style.borderColor = '#cbd5e1';
                                this.style.transform = 'translateY(-4px)';
                                this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.1)';
                            }
                        });

                        option.addEventListener('mouseleave', function () {
                            if (!this.classList.contains('selected')) {
                                this.style.borderColor = '#e5e7eb';
                                this.style.transform = 'translateY(0)';
                                this.style.boxShadow = 'none';
                            }
                        });

                        // Click effect
                        option.addEventListener('click', function () {
                            // Remove selection from all options
                            options.forEach(opt => {
                                opt.classList.remove('selected');
                                opt.style.borderColor = '#e5e7eb';
                                opt.style.background = 'white';
                                opt.style.transform = 'translateY(0)';
                                opt.style.boxShadow = 'none';
                                opt.querySelector('.checkmark').style.display = 'none';
                            });

                            // Add selection to clicked option
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

                            this.style.transform = 'translateY(-4px)';
                            this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.15)';
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

                    // If GCash, ask for reference number
                    if (payment === 'gcash') {
                        Swal.fire({
                            title: 'GCash Reference',
                            html: `
                            <div class="text-left">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Enter GCash Reference Number:
                                </label>
                                <input type="text" 
                                       id="gcash-reference" 
                                       class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       placeholder="e.g., 1234567890123">
                                <p class="text-xs text-gray-500 mt-2">This is required for GCash payments</p>
                            </div>
                        `,
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonColor: '#3b82f6',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Submit',
                            cancelButtonText: 'Back',
                            preConfirm: () => {
                                const reference = document.getElementById('gcash-reference').value;
                                if (!reference || reference.trim() === '') {
                                    Swal.showValidationMessage('Reference number is required');
                                    return false;
                                }
                                return reference;
                            }
                        }).then((refResult) => {
                            if (refResult.isConfirmed && refResult.value) {
                                // Show loading and redirect
                                Swal.fire({
                                    title: 'Processing...',
                                    text: 'Approving member with GCash payment',
                                    icon: 'info',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                window.location.href = `/admin/user_crud/approve/${memberId}?payment=gcash&reference=${encodeURIComponent(refResult.value)}`;
                            }
                        });
                    } else {
                        // Cash payment - redirect directly
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Approving member with cash payment',
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        window.location.href = `/admin/user_crud/approve/${memberId}?payment=cash`;
                    }
                }
            });
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