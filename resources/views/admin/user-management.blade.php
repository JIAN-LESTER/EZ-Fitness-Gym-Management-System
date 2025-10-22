@extends('layouts.app')

@section('title', 'User Management')
@section('header', 'User Management')

@section('content')
    <header class="p-4 flex justify-end items-center">
        <button onclick="openModal('addUserModal')"
            class="flex items-center px-3 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                class="w-5 h-5 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add User
        </button>
    </header>

    <main class="bg-white shadow-md rounded-lg overflow-hidden">
        <section class="p-4 border-t border-b border-gray-200" aria-label="Search users">
            <form method="GET" action="{{ route('admin.user_management') }}" class="flex gap-2 items-center" role="search">
                <label for="search" class="sr-only">Search by name</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name or username..."
                    class="border-gray-200 border rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-gray-500">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.user_management') }}" class="text-gray-500 hover:underline px-2">Clear</a>
                @endif
            </form>
        </section>

        <section class="overflow-x-auto" aria-labelledby="users-table">
            <table class="min-w-full border border-gray-200 text-sm" role="table">
                <caption id="users-table" class="sr-only">List of all users</caption>
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left">Name</th>
                        <th scope="col" class="px-6 py-3 text-left">Username</th>
                        <th scope="col" class="px-6 py-3 text-left">Role</th>
                        <th scope="col" class="px-6 py-3 text-left">Status</th>
                        <th scope="col" class="px-6 py-3 text-left">Membership</th>
                        <th scope="col" class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <button onclick="showUser('{{ $user->user_id }}')" 
                                   class="block hover:text-blue-600 transition-colors text-left w-full">
                                    <p class="font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</p>
                                    <p class="text-gray-500 text-sm">{{ $user->email }}</p>
                                </button>
                            </td>

                            <td class="px-6 py-4">
                                <span class="text-gray-700">{{ $user->username }}</span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : 
                                       ($user->role === 'staff' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-600' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                @if($user->member)
                                    <div class="text-sm">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $user->member->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                            {{ $user->member->plan->name ?? 'No Plan' }}
                                        </span>
                                        @if($user->member->end_date)
                                            <p class="text-xs text-gray-500 mt-1">
                                                Expires: {{ \Carbon\Carbon::parse($user->member->end_date)->format('M d, Y') }}
                                            </p>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">Not a member</span>
                                @endif
                            </td>

                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center space-x-3">
                                    <button onclick="showUser('{{ $user->user_id }}')" 
                                       class="text-green-500 hover:text-green-700" title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    <button onclick="editUser('{{ $user->user_id }}')" 
                                            class="text-blue-500 hover:text-blue-700" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.828a2 2 0 01-1.414.586H9v-2a2 2 0 01.586-1.414z" />
                                        </svg>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($users->total() > 0)
                <div class="flex items-center justify-between p-4 text-sm text-gray-600">
                    <div>
                        Showing 
                        <span class="font-medium">{{ $users->firstItem() }}</span>
                        to 
                        <span class="font-medium">{{ $users->lastItem() }}</span>
                        of 
                        <span class="font-medium">{{ $users->total() }}</span> users
                    </div>

                    <div class="flex space-x-2">
                        @if($users->onFirstPage())
                            <span class="px-3 py-1 rounded-lg bg-gray-200 text-gray-500 cursor-not-allowed">Previous</span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}"
                                class="px-3 py-1 rounded-lg bg-gray-600 text-white hover:bg-gray-700">Previous</a>
                        @endif

                        @if($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}"
                                class="px-3 py-1 rounded-lg bg-gray-600 text-white hover:bg-gray-700">Next</a>
                        @else
                            <span class="px-3 py-1 rounded-lg bg-gray-200 text-gray-500 cursor-not-allowed">Next</span>
                        @endif
                    </div>
                </div>
            @endif
        </section>
    </main>

    <div id="addUserModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('addUserModal')"></div>

        <div class="relative bg-gray-100 dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <header class="bg-gray-600 text-white p-5 rounded-t-2xl sticky top-0 z-10">
                <h2 class="text-xl font-semibold">Add New User</h2>
            </header>

            <form action="{{ route('admin.users-store') }}" method="POST" class="p-6 md:p-8 space-y-6 relative z-10">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                        <input type="text" name="first_name" id="first_name" required
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                        <input type="text" name="last_name" id="last_name" required
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                    </div>
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                    <input type="text" name="username" id="username" required
                        class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" name="email" id="email" required
                        class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input type="password" name="password" id="password" required
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                    </div>
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                    <select name="role" id="role" onchange="toggleMemberFields('add')"
                        class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        <option value="member">Member</option>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

          
                <div id="addMemberFields" class="space-y-4 border-t border-gray-300 dark:border-gray-700 pt-4">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Member Profile (Optional)</h3>
                    
                    <div>
                        <label for="plan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Membership Plan</label>
                        <select name="plan_id" id="plan_id"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            <option value="">Select a plan (optional)</option>
                            @foreach($plans ?? [] as $plan)
                                <option value="{{ $plan->plan_id }}">{{ $plan->name }} - ₱{{ number_format($plan->price, 2) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="sex" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sex</label>
                            <select name="sex" id="sex"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                <option value="">Select...</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div>
                            <label for="birthday" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Birthday</label>
                            <input type="date" name="birthday" id="birthday"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Height (cm)</label>
                            <input type="number" step="0.1" name="height" id="height"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        </div>
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Weight (kg)</label>
                            <input type="number" step="0.1" name="weight" id="weight"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        </div>
                    </div>

                    <div>
                        <label for="mobile_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mobile Number</label>
                        <input type="tel" name="mobile_number" id="mobile_number" placeholder="e.g. 09123456789"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeModal('addUserModal')"
                        class="px-6 py-2 rounded-lg bg-gray-400 text-white hover:bg-gray-500 w-full sm:w-auto">Cancel</button>
                    <button type="submit"
                        class="px-6 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-700 w-full sm:w-auto">Add User</button>
                </div>
            </form>
        </div>
    </div>

 
    <div id="editUserModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('editUserModal')"></div>

        <div class="relative bg-gray-100 dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <header class="bg-gray-600 text-white p-5 rounded-t-2xl sticky top-0 z-10">
                <h2 class="text-xl font-semibold">Edit User</h2>
            </header>

            <form id="editUserForm" method="POST" class="p-6 md:p-8 space-y-6 relative z-10">
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                        <input type="text" name="first_name" id="edit_first_name" required
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                    </div>
                    <div>
                        <label for="edit_last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                        <input type="text" name="last_name" id="edit_last_name" required
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                    </div>
                </div>

                <div>
                    <label for="edit_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                    <input type="text" name="username" id="edit_username" required
                        class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                </div>

                <div>
                    <label for="edit_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" name="email" id="edit_email" required
                        class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password (Optional)</label>
                        <input type="password" name="password" id="edit_password"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                    </div>
                    <div>
                        <label for="edit_role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                        <select name="role" id="edit_role" onchange="toggleMemberFields('edit')"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            <option value="member">Member</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="edit_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="edit_status"
                        class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <!-- Member Fields -->
                <div id="editMemberFields" class="space-y-4 border-t border-gray-300 dark:border-gray-700 pt-4">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Member Profile (Optional)</h3>
                    
                    <div>
                        <label for="edit_plan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Membership Plan</label>
                        <select name="plan_id" id="edit_plan_id"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            <option value="">Select a plan (optional)</option>
                            @foreach($plans ?? [] as $plan)
                                <option value="{{ $plan->plan_id }}">{{ $plan->name }} - ₱{{ number_format($plan->price, 2) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_sex" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sex</label>
                            <select name="sex" id="edit_sex"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                                <option value="">Select...</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_birthday" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Birthday</label>
                            <input type="date" name="birthday" id="edit_birthday"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="edit_height" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Height (cm)</label>
                            <input type="number" step="0.1" name="height" id="edit_height"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        </div>
                        <div>
                            <label for="edit_weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Weight (kg)</label>
                            <input type="number" step="0.1" name="weight" id="edit_weight"
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                        </div>
                    </div>

                    <div>
                        <label for="edit_mobile_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mobile Number</label>
                        <input type="tel" name="mobile_number" id="edit_mobile_number" placeholder="e.g. 09123456789"
                            class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 px-4 py-2 focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeModal('editUserModal')"
                        class="px-6 py-2 rounded-lg bg-gray-400 text-white hover:bg-gray-500 w-full sm:w-auto">Cancel</button>
                    <button type="submit"
                        class="px-6 py-2 rounded-lg bg-gray-600 text-white hover:bg-gray-700 w-full sm:w-auto">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteConfirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg w-full max-w-md mx-4 overflow-hidden">
            <div class="bg-gray-600 text-white px-4 py-3 flex justify-between items-center">
                <h2 class="text-lg font-semibold">Confirm Delete</h2>
                <button type="button" onclick="closeModal('deleteConfirmModal')"
                    class="text-white hover:text-gray-200">✕</button>
            </div>

            <div class="p-6">
                <p class="text-gray-700 dark:text-gray-200">
                    Are you sure you want to delete this user? This action cannot be undone.
                </p>
            </div>

            <div class="flex justify-end gap-2 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeModal('deleteConfirmModal')"
                    class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Cancel
                </button>

                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 focus:ring-2 focus:ring-red-400">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

  
    <div id="userShowModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
        <div class="absolute inset-0" onclick="closeModal('userShowModal')"></div>

        <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
            <header class="bg-gray-600 text-white p-5 rounded-t-2xl sticky top-0 z-10 flex justify-between items-center">
                <h2 class="text-xl font-semibold">User Details</h2>
                <button onclick="closeModal('userShowModal')" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
        function toggleMemberFields(mode) {
            const roleSelect = document.getElementById(mode === 'add' ? 'role' : 'edit_role');
            const memberFields = document.getElementById(mode === 'add' ? 'addMemberFields' : 'editMemberFields');
            
            if (roleSelect.value === 'member') {
                memberFields.style.display = 'block';
            } else {
                memberFields.style.display = 'none';
            }
        }

        function openDeleteModal(actionUrl) {
            document.getElementById('deleteForm').action = actionUrl;
            openModal('deleteConfirmModal');
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function editUser(userId) {
            fetch(`/admin/user_crud/edit/${userId}`)
                .then(res => res.json())
                .then(user => {
                    document.getElementById('editUserId').value = user.user_id;
                    document.getElementById('edit_first_name').value = user.first_name;
                    document.getElementById('edit_last_name').value = user.last_name;
                    document.getElementById('edit_username').value = user.username;
                    document.getElementById('edit_email').value = user.email;
                    document.getElementById('edit_role').value = user.role;
                    document.getElementById('edit_status').value = user.status;

                    // Populate member fields if available
                    if (user.member) {
                        document.getElementById('edit_plan_id').value = user.member.plan_id || '';
                        document.getElementById('edit_sex').value = user.member.sex || '';
                        document.getElementById('edit_birthday').value = user.member.birthday || '';
                        document.getElementById('edit_height').value = user.member.height || '';
                        document.getElementById('edit_weight').value = user.member.weight || '';
                        document.getElementById('edit_mobile_number').value = user.member.mobile_number || '';
                    }

                    toggleMemberFields('edit');

                    document.getElementById('editUserForm').action = `/admin/user_crud/update/${user.user_id}`;
                    openModal('editUserModal');
                });
        }

        function showUser(userId) {
            openModal('userShowModal');
            
            document.getElementById('userShowContent').innerHTML = `
                <div class="flex justify-center items-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-600"></div>
                </div>
            `;

            fetch(`/admin/user_crud/show/${userId}`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return res.json();
                })
                .then(user => {
                    renderUserDetails(user);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('userShowContent').innerHTML = `
                        <div class="text-center py-12">
                            <p class="text-red-600">Error loading user details</p>
                        </div>
                    `;
                });
        }

        function renderUserDetails(user) {
            const content = document.getElementById('userShowContent');
            
            const avatarInitial = user.first_name.charAt(0).toUpperCase() + user.last_name.charAt(0).toUpperCase();
            
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
                                    `<div class="w-32 h-32 rounded-full bg-gray-400 flex items-center justify-center text-4xl font-bold text-white">
                                        ${avatarInitial}
                                    </div>`
                                }
                                
                                <h2 class="text-2xl font-bold mt-4 text-gray-800 dark:text-gray-200">
                                    ${user.first_name} ${user.last_name}
                                </h2>
                                <p class="text-gray-500 dark:text-gray-400">@${user.username}</p>
                                
                                <div class="flex gap-2 mt-3">
                                    <span class="px-3 py-1 text-xs rounded-full ${roleColor}">
                                        ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                                    </span>
                                    <span class="px-3 py-1 text-xs rounded-full ${statusColor}">
                                        ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                                    </span>
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

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4 space-y-2">
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
                                <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    ${user.member.plan ? user.member.plan.name : 'No Plan Assigned'}
                                </p>
                                ${user.member.plan ? `
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        ₱${parseFloat(user.member.plan.price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} / ${user.member.plan.duration_days} days
                                    </p>
                                ` : ''}
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Membership Status</p>
                                <span class="inline-block px-3 py-1 text-sm rounded-full ${memberStatusColor}">
                                    ${user.member.status.charAt(0).toUpperCase() + user.member.status.slice(1)}
                                </span>
                            </div>

                            ${user.member.start_date ? `
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Start Date</p>
                                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                        ${new Date(user.member.start_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}
                                    </p>
                                </div>
                            ` : ''}

                            ${user.member.end_date ? `
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">End Date</p>
                                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                        ${new Date(user.member.end_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}
                                    </p>
                                    ${daysRemainingHTML}
                                </div>
                            ` : ''}
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Personal Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                ${user.member.sex ? `
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Sex</p>
                                        <p class="text-gray-800 dark:text-gray-200 font-medium capitalize">${user.member.sex}</p>
                                    </div>
                                ` : ''}

                                ${user.member.birthday ? `
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Birthday</p>
                                        <p class="text-gray-800 dark:text-gray-200 font-medium">${new Date(user.member.birthday).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                                    </div>
                                ` : ''}

                                ${user.member.height ? `
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Height</p>
                                        <p class="text-gray-800 dark:text-gray-200 font-medium">${user.member.height} cm</p>
                                    </div>
                                ` : ''}

                                ${user.member.weight ? `
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Weight</p>
                                        <p class="text-gray-800 dark:text-gray-200 font-medium">${user.member.weight} kg</p>
                                    </div>
                                ` : ''}

                                ${user.member.mobile_number ? `
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Mobile Number</p>
                                        <p class="text-gray-800 dark:text-gray-200 font-medium">${user.member.mobile_number}</p>
                                    </div>
                                ` : ''}

                                ${user.member.qr_code ? `
                                    <div class="md:col-span-2">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">QR Code</p>
                                        <img src="/storage/${user.member.qr_code}" alt="QR Code" class="w-32 h-32 border border-gray-200 rounded">
                                    </div>
                                ` : ''}
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

            html += `
                    </div>
                </div>
            `;

            content.innerHTML = html;
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleMemberFields('add');
        });
    </script>

@endsection