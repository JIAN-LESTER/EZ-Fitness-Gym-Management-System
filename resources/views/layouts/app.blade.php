<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
    x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))"
    :class="darkMode ? 'dark' : ''">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo_image/ez_fitness_gym_logo.png') }}">

    <style>
        [x-cloak] {
            display: none !important;
        }
        link[rel="icon"] {
            border-radius: 10px !important;
        }
    </style>
</head>

<body x-data="{ sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false' }"
    x-init="$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))"
    class="flex h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

<?php 
$user = Auth::user();
$member = null;

// Only get member data if user is actually a member
if ($user->role === 'member') {
    $member = $user->member;
}

// Count pending member approvals (only for admins)
$pendingApprovalsCount = 0;
if ($user->role === 'admin') {
    $pendingApprovalsCount = \App\Models\MemberProfile::where('isApproved', false)
        ->where('isDisabled', false)
        ->count();
}
?>

    <!-- Sidebar -->
    <aside
    x-cloak
    class="bg-gray-800 text-white dark:bg-gray-800 dark:text-white shadow-md flex flex-col"
    :class="sidebarOpen ? 'w-60' : 'w-16'">

    <!-- Logo Section -->
    <div class="flex justify-center items-center p-4 border-b border-gray-700">
        <!-- Full logo when sidebar is expanded -->
        <div x-show="sidebarOpen" x-cloak class="transition-all duration-300 flex items-center justify-center">
            <img src="{{ asset('logo_image/ez_fitness_gym_logo.png') }}" 
                alt="EZ Fitness" 
                class="h-30 w-auto max-w-[160px] rounded-2xl object-contain hover:scale-105 transition-transform shadow-lg shadow-gray-900/50 hover:shadow-xl hover:shadow-gray-900/40">
        </div>
        
        <!-- Icon/compact logo when sidebar is collapsed -->
        <div x-show="!sidebarOpen" x-cloak class="transition-all duration-300 flex items-center justify-center">
            <img src="{{ asset('logo_image/ez_fitness_gym_logo.png') }}" 
                alt="EZ Fitness" 
                class="h-8 w-8 rounded-lg object-cover hover:scale-110 transition-transform">
        </div>
    </div>
        <nav class="flex-1 px-2 space-y-2">
            @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" @click="profileOpen = false"
                               class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4 0h8" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Dashboard</span>
                            </a>

                       <a href="{{ route('admin.user_management') }}" @click="profileOpen = false"  
   class="flex items-center justify-between space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('admin.user_management') ? 'bg-white/20 text-white' : '' }}">
    <div class="flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="transition-opacity">Users</span>
    </div>
    
    <!-- Notification Badge -->
    @if($pendingApprovalsCount > 0)
        <span x-show="sidebarOpen" x-cloak 
            class="flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse">
            {{ $pendingApprovalsCount }}
        </span>
        <!-- Dot indicator when sidebar is collapsed -->
        <span x-show="!sidebarOpen" x-cloak 
            class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse border-2 border-gray-800">
        </span>
    @endif
</a>


                            <a href="{{ route('attendance.scanner') }}" @click="profileOpen = false"  class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('attendance.scanner') ? 'bg-white/20 text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">QR Scanner</span>
                            </a>

                            <a href="{{ route('admin.plan_management') }}" @click="profileOpen = false"  class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('admin.plan_management') ? 'bg-white/20 text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Membership Plans</span>
                            </a>
                                <a href="{{ route('products.index') }}" @click="profileOpen = false"
                                class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('products.index') ? 'bg-white/20 text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Inventory</span>
                            </a>
                <a href="{{ route('pos.index') }}" @click="profileOpen = false"
                    class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('pos.index') ? 'bg-white/20 text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">POS</span>
                </a>
                <a href="{{ route('categories.index') }}" @click="profileOpen = false"
                    class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('categories.index') ? 'bg-white/20 text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6h.008v.008H6V6z" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">Categories</span>
                </a>
                <a href="{{ route('sales.index') }}" @click="profileOpen = false"
                    class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('sales.index') ? 'bg-white/20 text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">Sales</span>
                </a>
                            <a href="{{ route('transactions.index') }}" @click="profileOpen = false"
                                class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('transactions.index') ? 'bg-white/20 text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Transactions</span>
                            </a>
                            <a href="#" @click="profileOpen = false"
                                class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('logs.show') ? 'bg-white/20 text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Attendance Logs</span>
                            </a>
                            <a href="{{ route('logs.show') }}" @click="profileOpen = false"
                                class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('logs.show') ? 'bg-white/20 text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Logs</span>
                            </a>

            @elseif(auth()->user()->role === 'staff')
                <!-- Staff Menu Items -->
                <a href="{{ route('staff.dashboard') }}" @click="profileOpen = false"
                    class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('staff.dashboard') ? 'bg-white/20 text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4 0h8" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">Dashboard</span>
                </a>

                <a href="{{ route('attendance.scanner') }}" @click="profileOpen = false"  
                class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('attendance.scanner') ? 'bg-white/20 text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">QR Scanner</span>
                </a>

                <a href="{{ route('products.index') }}" @click="profileOpen = false"
                class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('products.index') ? 'bg-white/20 text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">Inventory</span>
                </a>

                <a href="{{ route('pos.index') }}" @click="profileOpen = false"
                class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('pos.index') ? 'bg-white/20 text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">POS</span>
                </a>

                <a href="{{ route('sales.index') }}" @click="profileOpen = false"
                class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('sales.index') ? 'bg-white/20 text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">Sales</span>
                </a>

                <a href="{{ route('transactions.index') }}" @click="profileOpen = false"
                class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('transactions.index') ? 'bg-white/20 text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">Transactions</span>
                </a>
            @elseif(auth()->user()->role === 'member')
                <a href="{{ route('member.dashboard') }}" @click="profileOpen = false"
                    class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('logs.show') ? 'bg-white/20 text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4 0h8" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">Dashboard</span>
                </a>

            @elseif(auth()->user()->role === 'staff')
            <a href="{{ route('admin.dashboard') }}" @click="profileOpen = false"
                               class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4 0h8" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Dashboard</span>
                            </a>

                                  <a href="{{ route('pos.index') }}" @click="profileOpen = false"
                    class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('pos.index') ? 'bg-white/20 text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">POS</span>
                </a>


          
            @endif
        </nav>
    </aside>

    <div class="flex-1 @yield('fullscreen', 'flex flex-col')">
        <!-- Header - conditionally positioned for fullscreen pages -->
        <header
            class="@yield('header-class', 'relative') h-16 bg-gray-800 text-white p-4 flex justify-between items-center shadow-sm dark:bg-white dark:text-gray-800 z-30">
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="p-2 rounded hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white focus:outline-none transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h1 class="text-lg font-semibold">@yield('header', 'Page')</h1>
            </div>

            <div x-data="{ profileOpen: false }" class="relative">
                <button @click="profileOpen = !profileOpen" class="flex items-center space-x-2 focus:outline-none px-3 py-2 rounded transition-colors hover:bg-gray-200 hover:text-gray-800">
                    <div class="flex items-center space-x-2">
                        <div class="relative">
                            <div
                                class="w-8 h-8 bg-gray-800 text-white dark:bg-white dark:text-gray-800 rounded-full flex items-center justify-center font-semibold transition-colors duration-200">
                                {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                            </div>
                        </div>
                        <span class="hidden md:block">{{ $user->first_name ?? $user->username }}</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform"
                        :class="profileOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="profileOpen" x-cloak @click.away="profileOpen = false"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="translate-y-4 absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-lg z-50 border border-gray-200">

                    <div class="px-4 py-3 border-b border-gray-200">
                        <p class="text-sm font-medium text-gray-900">
                            {{ $user->first_name }} {{ $user->last_name }}
                        </p>
                        <p class="text-sm text-gray-500 truncate">
                            {{ $user->email }}
                        </p>
                        @if($user->role === 'member' && $member && $member->status === 'inactive')
                            <p class="text-xs text-red-600 mt-1">
                                Profile incomplete
                            </p>
                        @endif
                    </div>

                    <div class="py-1">
                    @if($user->role === 'member' && $member && $member->status === 'inactive' && $member->isApproved == true)
    <button
        onclick="opencompleteMembershipModal(); document.querySelector('[x-data]').__x.$data.profileOpen = false"
        class="flex items-center w-full px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        Complete Profile
    </button>
@endif

                        <button
                            onclick="openProfileModal(); document.querySelector('[x-data]').__x.$data.profileOpen = false"
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            View Profile
                        </button>

                        <button
                            onclick="openEditProfileModal(); document.querySelector('[x-data]').__x.$data.profileOpen = false"
                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Profile
                        </button>

                        <div class="border-t border-gray-200 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>


        <main class="@yield('main-class', 'flex-1 overflow-y-auto p-4 dark:bg-gray-200')">
            @yield('content')
        </main>
    </div>


@if($user->role === 'member' && $member)
   
    {{-- STATE 1: Profile incomplete - needs to fill out form --}}
    @if(!$member->plan_id || !$member->sex || !$member->birthday || !$member->mobile_number)
        {{-- Complete Membership Profile Modal - First time setup --}}
        <div id="completeMembershipModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/50">
            <div class="relative bg-white text-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
                <div class="bg-gray-600 text-white p-5 rounded-t-2xl">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <h2 class="text-xl font-semibold">Complete Your Membership Profile</h2>
                    </div>
                    <p class="text-gray-100 text-sm mt-1">Please complete your membership information to continue.</p>
                </div>

                <form id="completeProfileForm" action="{{ route('profile.complete-member-profile') }}" method="POST" class="p-6 md:p-8 space-y-6 relative z-10">
                    @csrf
                    @method('PUT')

                    <div x-data="{ open: false, selected: '', selectedId: '' }" class="relative">
                        <label for="plan_id" class="block text-sm font-medium text-gray-800">
                            Membership Plan <span class="text-red-500">*</span>
                        </label>

                        <input type="hidden" name="plan_id" x-model="selectedId">

                        <button type="button" @click="open = !open"
                            class="mt-2 w-full flex justify-between items-center rounded-xl border border-gray-800 bg-gray-200 px-4 py-3">
                            <span x-text="selected || 'Select a plan'"></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false"
                             class="absolute mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-md z-50">
                            @foreach($plans as $plan)
                                <div @click="
                                        selected = '{{ $plan->name }} — ₱{{ number_format($plan->price, 2) }}';
                                        selectedId = '{{ $plan->plan_id }}';
                                        open = false
                                    "
                                    class="flex justify-between px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                    <span>{{ $plan->name }}</span>
                                    <span>₱{{ number_format($plan->price, 2) }}</span>
                                </div>
                            @endforeach
                        </div>

                        @error('plan_id')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">
                            Sex <span class="text-red-500">*</span>
                        </span>

                        <div class="flex items-center space-x-6">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="sex" value="male" {{ old('sex')=='male' ? 'checked' : '' }} class="peer sr-only">
                                <div class="w-16 h-16 rounded-full border-2 border-gray-300 peer-checked:border-gray-800
                                            flex items-center justify-center transition duration-200 bg-gray-100 hover:bg-gray-200">
                                    <img src="https://cdn-icons-png.flaticon.com/512/921/921106.png"
                                         alt="Male avatar" class="w-10 h-10 object-contain opacity-90">
                                </div>
                                <span class="block text-center mt-1 text-gray-800 text-sm font-medium">Male</span>
                            </label>

                            <label class="relative cursor-pointer">
                                <input type="radio" name="sex" value="female" {{ old('sex')=='female' ? 'checked' : '' }} class="peer sr-only">
                                <div class="w-16 h-16 rounded-full border-2 border-gray-300 peer-checked:border-gray-800
                                            flex items-center justify-center transition duration-200 bg-gray-100 hover:bg-gray-200">
                                    <img src="https://cdn-icons-png.flaticon.com/512/921/921124.png"
                                         alt="Female avatar" class="w-10 h-10 object-contain opacity-90">
                                </div>
                                <span class="block text-center mt-1 text-gray-800 text-sm font-medium">Female</span>
                            </label>
                        </div>

                        <div class="mt-1">
                            @error('sex')
                                <span class="text-red-500 text-xs block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="birthday" class="block text-sm font-medium text-gray-700 dark:text-gray-800">
                            Birthday <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="birthday" id="birthday"
                            class="mt-2 block w-full rounded-xl border-gray-800 bg-gray-200 dark:border-gray-600 px-4 py-3 focus:ring-gray-500 focus:border-gray-500">
                        @error('birthday')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-800">
                                Height (cm)
                            </label>
                            <input type="number" step="0.1" name="height" id="height"
                                class="mt-2 block w-full rounded-xl border-gray-800 bg-gray-200 dark:border-gray-600 px-4 py-3 focus:ring-gray-500 focus:border-gray-500">
                            @error('height')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-800">
                                Weight (kg)
                            </label>
                            <input type="number" step="0.1" name="weight" id="weight"
                                class="mt-2 block w-full rounded-xl border-gray-800 bg-gray-200 dark:border-gray-600 px-4 py-3 focus:ring-gray-500 focus:border-gray-500">
                            @error('weight')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="mobile_number" class="block text-sm font-medium text-gray-700 dark:text-gray-800">
                            Mobile Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="mobile_number" id="mobile_number" placeholder="e.g. 09123456789"
                            pattern="[0-9]{11}"
                            class="mt-2 block w-full rounded-xl border-gray-800 bg-gray-200 dark:border-gray-600 px-4 py-3 focus:ring-gray-500 focus:border-gray-500">
                        @error('mobile_number')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit"
                            class="px-6 py-3 rounded-xl bg-gray-600 text-white hover:bg-gray-700 font-medium">
                            Complete Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

    {{-- STATE 2: Profile complete but waiting for admin approval --}}
    @elseif($member->isApproved == false && $member->isDisabled == false && $member->status === 'inactive')
        {{-- Waiting for Approval Modal with Retry --}}
        <div id="waitingApprovalModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/50">
            <div class="relative bg-white text-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div class="bg-yellow-500 text-white p-5 rounded-t-2xl">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-xl font-semibold">Pending Approval</h2>
                    </div>
                </div>

                <div class="p-8 text-center">
                    <div class="mb-6">
                        <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Account Under Review</h3>
                        <p id="approvalStatusMessage" class="text-gray-600 text-lg mb-4">
                            Your profile has been submitted and is awaiting admin approval.
                        </p>
                        <p class="text-gray-500 text-sm">You'll receive an email with your QR code once approved.</p>
                        
                        @if($member->plan)
                            <div class="mt-4 p-3 bg-gray-100 rounded-lg">
                                <p class="text-sm text-gray-600">Selected Plan:</p>
                                <p class="font-bold text-gray-800">{{ $member->plan->name }}</p>
                                <p class="text-sm text-gray-500">₱{{ number_format($member->plan->price, 2) }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <button onclick="checkApprovalStatus()" 
                            class="w-full px-6 py-3 rounded-xl bg-yellow-500 text-white hover:bg-yellow-600 font-medium transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Check Status
                        </button>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                class="w-full px-6 py-3 rounded-xl bg-gray-600 text-white hover:bg-gray-700 font-medium transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- QR Code Approved Modal (hidden by default, shown via JavaScript) --}}
        <div id="qrApprovedModal" class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/50">
            <div class="relative bg-white text-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div class="bg-green-500 text-white p-5 rounded-t-2xl">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-xl font-semibold">Membership Approved!</h2>
                    </div>
                </div>

                <div class="p-8 text-center">
                    <div class="mb-6">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Welcome to EZ Fitness!</h3>
                        <p class="text-gray-600 text-lg mb-4">Your membership has been activated.</p>
                        <p class="text-gray-500 text-sm mb-4">Your QR code has been sent to your email.</p>
                        
                        <div id="qrCodeDisplay" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
                            <img id="qrCodeImage" src="" alt="QR Code" class="mx-auto w-48 h-48">
                        </div>
                    </div>

                    <button onclick="closeQRApprovedModal()" 
                        class="w-full px-6 py-3 rounded-xl bg-green-500 text-white hover:bg-green-600 font-medium transition-colors">
                        Continue to Dashboard
                    </button>
                </div>
            </div>
        </div>

    {{-- STATE 3: Account has been rejected/disabled --}}
    @elseif($member->isDisabled == true)
        {{-- Account Rejected Modal --}}
        <div id="accountRejectedModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/50">
            <div class="relative bg-white text-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div class="bg-red-500 text-white p-5 rounded-t-2xl">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-xl font-semibold">Account Not Approved</h2>
                    </div>
                </div>

                <div class="p-8 text-center">
                    <div class="mb-6">
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Access Denied</h3>
                        <p class="text-gray-600 text-lg mb-4">Your account was not approved.</p>
                        <p class="text-gray-500 text-sm">Please contact the administrator for more information.</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3 rounded-xl bg-gray-600 text-white hover:bg-gray-700 font-medium transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endif



    {{-- VIEW PROFILE MODAL --}}
    <div id="profileModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/40">
        <div
            class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden border border-gray-200 dark:border-gray-700">

            <div class="flex justify-between items-center p-6 bg-gray-200 text-gray-800 dark:bg-gray-900/50">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">My Profile</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">View your account information</p>
                </div>
                <button onclick="closeProfileModal()"
                    class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-6 max-h-[calc(100vh-16rem)] overflow-y-auto">

                {{-- Personal Information Section --}}
                <div class="space-y-4">
                    <h3
                        class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Personal Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">First Name</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $user->first_name }}</p>
                        </div>
                        <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Last Name</p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $user->last_name }}</p>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Username</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $user->username }}</p>
                    </div>

                    <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Email Address</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $user->email }}</p>
                    </div>

                    <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Role</p>
                        <span
                            class="inline-block px-3 py-1 text-sm rounded-full
                        {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' :
    ($user->role === 'staff' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                </div>

                {{-- MEMBER PROFILE DETAILS - Only show if user is a member --}}
                @if($user->role === 'member')
                    @if($member)
                        <div class="space-y-4">
                            <h3
                                class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Membership Details
                            </h3>

                            <div
                                class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/20 rounded-xl border border-gray-200 dark:border-gray-800">
                                <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Current Plan</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-800">
                                    {{ $member->plan->name ?? 'N/A' }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                @if($member->sex)
                                    <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sex</p>
                                        <p class="text-base font-semibold text-gray-900 dark:text-white capitalize">
                                            {{ $member->sex }}</p>
                                    </div>
                                @endif
                                @if($member->birthday)
                                    <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Birthday</p>
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($member->birthday)->format('M d, Y') }}</p>
                                    </div>
                                @endif
                            </div>

                            @if($member->height || $member->weight || $member->mobile_number)
                                <div class="space-y-4">
                                    <h4
                                        class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                        </svg>
                                        Physical Information
                                    </h4>

                                    <div class="grid grid-cols-2 gap-4">
                                        @if($member->height)
                                            <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Height</p>
                                                <p class="text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ $member->height }} <span class="text-sm font-normal">cm</span></p>
                                            </div>
                                        @endif
                                        @if($member->weight)
                                            <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Weight</p>
                                                <p class="text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ $member->weight }} <span class="text-sm font-normal">kg</span></p>
                                            </div>
                                        @endif
                                    </div>

                                    @if($member->mobile_number)
                                        <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Mobile Number</p>
                                            <p class="text-base font-semibold text-gray-900 dark:text-white">
                                                {{ $member->mobile_number }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <div
                            class="p-6 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-amber-900 dark:text-amber-800">Profile Incomplete</p>
                                    <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">Please complete your membership
                                        profile to access all features.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            <div
                class="flex justify-end gap-3 p-6 bg-gray-200 text-gray-800 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeProfileModal()"
                    class="px-6 py-2.5 rounded-xl bg-gray-200 text-gray-700 hover:bg-gray-800 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 font-medium transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- EDIT PROFILE MODAL - Updated for non-members --}}
    <div id="editProfileModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/40">
        <div
            class="bg-white  rounded-3xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden border ">

            <div class="flex justify-between items-center p-6 bg-gray-800">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Profile</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update your account information</p>
                </div>
                <button onclick="closeEditProfileModal()"
                    class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('profile.update') }}" method="POST"
                class="max-h-[calc(100vh-12rem)] overflow-y-auto">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">
                    {{-- Personal Information Section --}}
                    <div class="space-y-4">
                        <h3
                            class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Personal Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="first_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">First
                                    Name</label>
                                <input type="text" name="first_name" id="first_name" value="{{ $user->first_name }}"
                                    required
                                    class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label for="last_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Last
                                    Name</label>
                                <input type="text" name="last_name" id="last_name" value="{{ $user->last_name }}"
                                    required
                                    class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                            </div>
                        </div>

                        <div>
                            <label for="username"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Username</label>
                            <input type="text" name="username" id="username" value="{{ $user->username }}" required
                                class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Email
                                Address</label>
                            <input type="email" name="email" id="email" value="{{ $user->email }}" required
                                class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                        </div>
                    </div>

                    {{-- Membership Information Section - Only for members --}}
                    @if($user->role === 'member' && $member)
                        <div class="space-y-4">
                            <h3
                                class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Membership Details
                            </h3>

                            <div>
                                <label for="plan_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Membership
                                    Plan</label>
                                <select name="plan_id" id="plan_id" disabled
                                    class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                                    <option value="">Keep current plan</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->plan_id }}" {{ $member->plan_id == $plan->plan_id ? 'selected' : '' }}>
                                            {{ $plan->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="sex"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Sex</label>
                                    <select name="sex" id="sex"
                                        class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                                        <option value="male" {{ $member->sex == 'male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="female" {{ $member->sex == 'female' ? 'selected' : '' }}>Female
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="birthday"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Birthday</label>
                                    <input type="date" name="birthday" id="birthday" value="{{ $member->birthday }}"
                                        class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="height"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Height
                                        (cm)</label>
                                    <input type="number" step="0.1" name="height" id="height"
                                        value="{{ $member->height }}"
                                        class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                                </div>
                                <div>
                                    <label for="weight"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Weight
                                        (kg)</label>
                                    <input type="number" step="0.1" name="weight" id="weight"
                                        value="{{ $member->weight }}"
                                        class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                                </div>
                            </div>

                            <div>
                                <label for="mobile_number"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Mobile
                                    Number</label>
                                <input type="tel" name="mobile_number" id="mobile_number"
                                    value="{{ $member->mobile_number }}"
                                    class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                            </div>
                        </div>
                    @endif

                    {{-- Security Section --}}
                    <div class="space-y-4">
                        <h3
                            class="text-sm font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Change Password
                            <span class="text-xs font-normal text-gray-800">(Optional)</span>
                        </h3>

                        <div>
                            <label for="old_password"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Current
                                Password</label>
                            <input type="password" name="old_password" id="old_password"
                                class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="new_password"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">New
                                    Password</label>
                                <input type="password" name="new_password" id="new_password"
                                    class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label for="new_password_confirmation"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Confirm New
                                    Password</label>
                                <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                    class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="flex justify-end gap-3 p-6  text-gray-800 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeEditProfileModal()"
                        class="px-6 py-2.5 rounded-xl bg-gray-200 text-gray-700 hover:bg-gray-800 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-gray-600 text-white hover:bg-gray-700 font-medium transition-colors shadow-lg shadow-gray-500/30">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('membership_expired') && $user->role === 'member' && $member)
<div id="renewalModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/50">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
        <div class="bg-orange-500 text-white p-5">
            <div class="flex items-center space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-xl font-semibold">Membership Expired</h2>
            </div>
        </div>

        <div class="p-8">
            <p class="text-gray-700 text-lg mb-6">
                Your membership plan <strong>{{ $member->plan->name }}</strong> has expired. 
                Would you like to renew your membership?
            </p>

            <form action="{{ route('member.request-renewal') }}" method="POST" class="space-y-6">
                @csrf

                <div x-data="{ open: false, selected: '{{ $member->plan->name }} — ₱{{ number_format($member->plan->price, 2) }}', selectedId: '{{ $member->plan_id }}' }">
                    <label class="block text-sm font-medium text-gray-800 mb-2">
                        Select Membership Plan <span class="text-red-500">*</span>
                    </label>

                    <input type="hidden" name="plan_id" x-model="selectedId">

                    <button type="button" @click="open = !open"
                        class="w-full flex justify-between items-center rounded-xl border-2 border-gray-300 bg-gray-50 px-4 py-3">
                        <span x-text="selected"></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                         class="absolute mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-md z-50 max-h-60 overflow-y-auto">
                        @foreach($plans as $plan)
                            <div @click="
                                    selected = '{{ $plan->name }} — ₱{{ number_format($plan->price, 2) }}';
                                    selectedId = '{{ $plan->plan_id }}';
                                    open = false
                                "
                                class="flex justify-between px-4 py-2 hover:bg-gray-100 cursor-pointer">
                                <span>{{ $plan->name }}</span>
                                <span>₱{{ number_format($plan->price, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" name="action" value="renew"
                        class="flex-1 px-6 py-3 rounded-xl bg-orange-500 text-white hover:bg-orange-600 font-medium transition-colors">
                        Request Renewal
                    </button>
                    <button type="submit" name="action" value="skip"
                        class="flex-1 px-6 py-3 rounded-xl bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium transition-colors">
                        Skip for Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif




    <script src="//unpkg.com/alpinejs" defer></script>

<script>
function openProfileModal() {
    const modal = document.getElementById('profileModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeProfileModal() {
    const modal = document.getElementById('profileModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function openEditProfileModal() {
    const modal = document.getElementById('editProfileModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeEditProfileModal() {
    const modal = document.getElementById('editProfileModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function opencompleteMembershipModal() {
    const modal = document.getElementById('completeMembershipModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

// Validation Utility Functions
const showError = (element, message) => {
    element.classList.remove('border-gray-800');
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
    element.classList.add('border-gray-800');

    const container = element.closest('div');
    const errorSpan = container.querySelector('.error-message');
    if (errorSpan) errorSpan.remove();
};

const clearAllErrors = (form) => {
    form.querySelectorAll('.error-message').forEach(el => el.remove());
    form.querySelectorAll('.border-red-500').forEach(el => {
        el.classList.remove('border-red-500');
        el.classList.add('border-gray-800');
    });
};

// Complete Profile Form Validation
document.addEventListener('DOMContentLoaded', function () {
    const completeProfileForm = document.getElementById('completeProfileForm');

    if (completeProfileForm) {
        completeProfileForm.addEventListener('submit', function (e) {
            let valid = true;
            clearAllErrors(this);

            // Get Alpine.js data for plan selection
            const planContainer = this.querySelector('[x-data]');
            const planButton = planContainer?.querySelector('button');
            const hiddenPlanInput = this.querySelector('input[name="plan_id"]');
            
            // Plan validation
            if (!hiddenPlanInput || !hiddenPlanInput.value) {
                if (planButton) {
                    planButton.classList.add('border-red-500');
                    const errorDiv = document.createElement('p');
                    errorDiv.className = 'error-message text-red-600 text-xs mt-1 block';
                    errorDiv.textContent = 'Please select a membership plan';
                    planContainer.appendChild(errorDiv);
                }
                valid = false;
            }

            // Sex validation
            const sex = this.querySelector('input[name="sex"]:checked');
            if (!sex) {
                const sexContainer = this.querySelector('.mb-4');
                const errorDiv = sexContainer.querySelector('.mt-1');
                if (errorDiv) {
                    let error = errorDiv.querySelector('.error-message');
                    if (!error) {
                        error = document.createElement('p');
                        error.className = 'error-message text-red-600 text-xs block';
                        errorDiv.appendChild(error);
                    }
                    error.textContent = 'Please select your sex';
                }
                valid = false;
            }

            // Birthday validation
            const birthday = this.querySelector('input[name="birthday"]');
            if (!birthday.value) {
                showError(birthday, 'Birthday is required');
                valid = false;
            }

            // Height validation
            const height = this.querySelector('input[name="height"]');
            if (height.value && (height.value <= 0 || height.value > 300)) {
                showError(height, 'Please enter a valid height (1-300 cm)');
                valid = false;
            }

            // Weight validation
            const weight = this.querySelector('input[name="weight"]');
            if (weight.value && (weight.value <= 0 || weight.value > 500)) {
                showError(weight, 'Please enter a valid weight (1-500 kg)');
                valid = false;
            }

            // Mobile number validation
            const mobile = this.querySelector('input[name="mobile_number"]');
            const mobilePattern = /^(09|\+639)\d{9}$/;
            if (!mobile.value) {
                showError(mobile, 'Mobile number is required');
                valid = false;
            } else if (!mobilePattern.test(mobile.value)) {
                showError(mobile, 'Enter a valid mobile number (e.g., 09123456789)');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
                const firstError = this.querySelector('.error-message');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        // Live validation for complete profile form
        const birthday = completeProfileForm.querySelector('input[name="birthday"]');
        const height = completeProfileForm.querySelector('input[name="height"]');
        const weight = completeProfileForm.querySelector('input[name="weight"]');
        const mobile = completeProfileForm.querySelector('input[name="mobile_number"]');

        if (birthday) {
            birthday.addEventListener('blur', function() {
                if (this.value) clearError(this);
            });
        }

        if (height) {
            height.addEventListener('blur', function() {
                if (this.value && (this.value <= 0 || this.value > 300)) {
                    showError(this, 'Please enter a valid height (1-300 cm)');
                } else if (this.value) {
                    clearError(this);
                }
            });
        }

        if (weight) {
            weight.addEventListener('blur', function() {
                if (this.value && (this.value <= 0 || this.value > 500)) {
                    showError(this, 'Please enter a valid weight (1-500 kg)');
                } else if (this.value) {
                    clearError(this);
                }
            });
        }

        if (mobile) {
            mobile.addEventListener('blur', function() {
                const mobilePattern = /^(09|\+639)\d{9}$/;
                if (this.value && !mobilePattern.test(this.value)) {
                    showError(this, 'Enter a valid mobile number (e.g., 09123456789)');
                } else if (this.value) {
                    clearError(this);
                }
            });
        }
    }

    // Edit Profile Form Validation
    const editProfileForm = document.querySelector('#editProfileModal form');

    if (editProfileForm) {
        editProfileForm.addEventListener('submit', function (e) {
            let valid = true;
            clearAllErrors(this);

            // Personal Information validation
            const firstName = this.querySelector('input[name="first_name"]');
            const lastName = this.querySelector('input[name="last_name"]');
            const username = this.querySelector('input[name="username"]');
            const email = this.querySelector('input[name="email"]');

            if (!firstName.value.trim()) {
                showError(firstName, 'First name is required');
                valid = false;
            }

            if (!lastName.value.trim()) {
                showError(lastName, 'Last name is required');
                valid = false;
            }

            if (!username.value.trim()) {
                showError(username, 'Username is required');
                valid = false;
            } else if (username.value.length < 3) {
                showError(username, 'Username must be at least 3 characters');
                valid = false;
            }

            // Email validation
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.value.trim()) {
                showError(email, 'Email is required');
                valid = false;
            } else if (!emailPattern.test(email.value)) {
                showError(email, 'Please enter a valid email address');
                valid = false;
            }

            // Member-specific validation
            const sex = this.querySelector('select[name="sex"]');
            const birthday = this.querySelector('input[name="birthday"]');
            const height = this.querySelector('input[name="height"]');
            const weight = this.querySelector('input[name="weight"]');
            const mobile = this.querySelector('input[name="mobile_number"]');

            if (height && height.value && (height.value <= 0 || height.value > 300)) {
                showError(height, 'Please enter a valid height (1-300 cm)');
                valid = false;
            }

            if (weight && weight.value && (weight.value <= 0 || weight.value > 500)) {
                showError(weight, 'Please enter a valid weight (1-500 kg)');
                valid = false;
            }

            if (mobile && mobile.value) {
                const mobilePattern = /^(09|\+639)\d{9}$/;
                if (!mobilePattern.test(mobile.value)) {
                    showError(mobile, 'Enter a valid mobile number (e.g., 09123456789)');
                    valid = false;
                }
            }

            // Password validation (only if user is trying to change password)
            const oldPassword = this.querySelector('input[name="old_password"]');
            const newPassword = this.querySelector('input[name="new_password"]');
            const confirmPassword = this.querySelector('input[name="new_password_confirmation"]');

            if (oldPassword.value || newPassword.value || confirmPassword.value) {
                if (!oldPassword.value) {
                    showError(oldPassword, 'Current password is required to change password');
                    valid = false;
                }

                if (!newPassword.value) {
                    showError(newPassword, 'New password is required');
                    valid = false;
                } else if (newPassword.value.length < 8) {
                    showError(newPassword, 'Password must be at least 8 characters');
                    valid = false;
                }

                if (!confirmPassword.value) {
                    showError(confirmPassword, 'Please confirm your new password');
                    valid = false;
                } else if (newPassword.value !== confirmPassword.value) {
                    showError(confirmPassword, 'Passwords do not match');
                    valid = false;
                }
            }

            if (!valid) {
                e.preventDefault();
                const firstError = this.querySelector('.error-message');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });

        // Live validation for edit profile form
        const firstName = editProfileForm.querySelector('input[name="first_name"]');
        const lastName = editProfileForm.querySelector('input[name="last_name"]');
        const username = editProfileForm.querySelector('input[name="username"]');
        const email = editProfileForm.querySelector('input[name="email"]');
        const birthday = editProfileForm.querySelector('input[name="birthday"]');
        const height = editProfileForm.querySelector('input[name="height"]');
        const weight = editProfileForm.querySelector('input[name="weight"]');
        const mobile = editProfileForm.querySelector('input[name="mobile_number"]');
        const newPassword = editProfileForm.querySelector('input[name="new_password"]');
        const confirmPassword = editProfileForm.querySelector('input[name="new_password_confirmation"]');

        if (firstName) {
            firstName.addEventListener('blur', function() {
                if (this.value.trim()) clearError(this);
            });
        }

        if (lastName) {
            lastName.addEventListener('blur', function() {
                if (this.value.trim()) clearError(this);
            });
        }

        if (username) {
            username.addEventListener('blur', function() {
                if (this.value.trim() && this.value.length >= 3) {
                    clearError(this);
                } else if (this.value.trim() && this.value.length < 3) {
                    showError(this, 'Username must be at least 3 characters');
                }
            });
        }

        if (email) {
            email.addEventListener('blur', function() {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (this.value.trim() && emailPattern.test(this.value)) {
                    clearError(this);
                } else if (this.value.trim()) {
                    showError(this, 'Please enter a valid email address');
                }
            });
        }

        if (birthday) {
            birthday.addEventListener('blur', function() {
                if (this.value) {
                    clearError(this);
                }
            });
        }

        if (height) {
            height.addEventListener('blur', function() {
                if (this.value && (this.value <= 0 || this.value > 300)) {
                    showError(this, 'Please enter a valid height (1-300 cm)');
                } else if (this.value) {
                    clearError(this);
                }
            });
        }

        if (weight) {
            weight.addEventListener('blur', function() {
                if (this.value && (this.value <= 0 || this.value > 500)) {
                    showError(this, 'Please enter a valid weight (1-500 kg)');
                } else if (this.value) {
                    clearError(this);
                }
            });
        }

        if (mobile) {
            mobile.addEventListener('blur', function() {
                const mobilePattern = /^(09|\+639)\d{9}$/;
                if (this.value && !mobilePattern.test(this.value)) {
                    showError(this, 'Enter a valid mobile number (e.g., 09123456789)');
                } else if (this.value) {
                    clearError(this);
                }
            });
        }

        if (newPassword) {
            newPassword.addEventListener('blur', function() {
                if (this.value && this.value.length < 8) {
                    showError(this, 'Password must be at least 8 characters');
                } else if (this.value) {
                    clearError(this);
                }
            });
        }

        if (confirmPassword) {
            confirmPassword.addEventListener('blur', function() {
                if (this.value && newPassword.value !== this.value) {
                    showError(this, 'Passwords do not match');
                } else if (this.value) {
                    clearError(this);
                }
            });
        }
    }
});


function checkApprovalStatus() {
    const button = event.target;
    const originalContent = button.innerHTML;
    
    // Show loading state
    button.disabled = true;
    button.innerHTML = `
        <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    `;

    fetch('{{ route("member.check-approval") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'approved') {
            // Hide waiting modal
            document.getElementById('waitingApprovalModal').classList.add('hidden');
            
            // Show approved modal
            const approvedModal = document.getElementById('qrApprovedModal');
            approvedModal.classList.remove('hidden');
            
            // Show QR code if available
            if (data.qr_code_url) {
                const qrDisplay = document.getElementById('qrCodeDisplay');
                const qrImage = document.getElementById('qrCodeImage');
                
                // Add timestamp to prevent caching
                qrImage.src = data.qr_code_url + '?t=' + new Date().getTime();
                
                // Show the QR code container
                qrDisplay.classList.remove('hidden');
                
                // Handle image load errors
                qrImage.onerror = function() {
                    console.error('Failed to load QR code image:', data.qr_code_url);
                    qrDisplay.innerHTML = `
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-600">QR code will be sent to your email</p>
                        </div>
                    `;
                };
                
                // Log successful load
                qrImage.onload = function() {
                    console.log('QR code loaded successfully');
                };
            } else {
                // If no QR code URL, show message
                const qrDisplay = document.getElementById('qrCodeDisplay');
                qrDisplay.innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-600">QR code has been sent to your email</p>
                    </div>
                `;
                qrDisplay.classList.remove('hidden');
            }
            
            Toastify({
                text: data.message,
                duration: 5000,
                gravity: "top",
                position: "right",
                backgroundColor: "linear-gradient(to right, #10b981, #059669)",
                stopOnFocus: true,
            }).showToast();
        } else if (data.status === 'rejected') {
            document.getElementById('approvalStatusMessage').innerHTML = 
                '<span class="text-red-600 font-semibold">Your application was not approved. Please contact support for more information.</span>';
            
            Toastify({
                text: data.message,
                duration: 5000,
                gravity: "top",
                position: "right",
                backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
                stopOnFocus: true,
            }).showToast();
        } else {
            Toastify({
                text: 'Still pending approval. Please check back later.',
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "linear-gradient(to right, #f59e0b, #d97706)",
                stopOnFocus: true,
            }).showToast();
        }
    })
    .catch(error => {
        console.error('Error checking approval status:', error);
        Toastify({
            text: 'Error checking status. Please try again.',
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
            stopOnFocus: true,
        }).showToast();
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalContent;
    });
}

function closeQRApprovedModal() {
    document.getElementById('qrApprovedModal').classList.add('hidden');
    // Reload to show updated dashboard
    window.location.reload();
}
</script>

    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Toastify({
                    text: "{{ session('success') }}",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #10b981, #059669)",
                    stopOnFocus: true,
                }).showToast();
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Toastify({
                    text: "{{ session('error') }}",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
                    stopOnFocus: true,
                }).showToast();
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Toastify({
                    text: "{{ $errors->first() }}",
                    duration: 4000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #ef4444, #dc2626)",
                    stopOnFocus: true,
                }).showToast();
            });
            
        </script>
    @endif




</body>

</html>
