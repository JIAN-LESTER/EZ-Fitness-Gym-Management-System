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
        /* CRITICAL: Prevent any layout shift or flashing */
        [x-cloak] {
            display: none !important;
        }
        
        /* Base sidebar styles - NO transitions on initial load */
        .sidebar-container {
            transition: none !important;
            will-change: width;
        }
        
        /* Only enable transitions after page load */
        .sidebar-loaded .sidebar-container {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-container.sidebar-open {
            width: 15rem;
        }
        
        .sidebar-container:not(.sidebar-open) {
            width: 4rem;
        }
        
        /* Content visibility transitions */
        .sidebar-content {
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
            display: none;
        }
        
        .sidebar-open .sidebar-content {
            opacity: 1;
            display: block;
            transition-delay: 0.1s;
        }
        
        .sidebar-collapsed-content {
            opacity: 1;
            transition: opacity 0.2s ease-in-out;
            display: block;
        }
        
        .sidebar-open .sidebar-collapsed-content {
            opacity: 0;
            display: none;
        }
        
        /* Prevent horizontal scrollbar during transition */
        body {
            overflow-x: hidden;
        }
        
        /* Critical CSS for immediate render */
        .sidebar-init-open .sidebar-container {
            width: 15rem !important;
        }
        
        .sidebar-init-closed .sidebar-container {
            width: 4rem !important;
        }
        
        .sidebar-init-closed .sidebar-content {
            display: none !important;
            opacity: 0 !important;
        }
        
        .sidebar-init-open .sidebar-collapsed-content {
            display: none !important;
            opacity: 0 !important;
        }
        
        .sidebar-init-open .sidebar-content {
            display: block !important;
            opacity: 1 !important;
        }
        
        .sidebar-init-closed .sidebar-collapsed-content {
            display: block !important;
            opacity: 1 !important;
        }
    </style>
    
    <script>
        // CRITICAL: Run BEFORE ANY rendering happens
        (function() {
            'use strict';
            
            // Get saved state (default to open if not set)
            const sidebarOpen = localStorage.getItem('sidebarOpen') !== 'false';
            
            // Apply class to HTML element immediately
            const htmlElement = document.documentElement;
            if (sidebarOpen) {
                htmlElement.classList.add('sidebar-init-open');
                htmlElement.classList.remove('sidebar-init-closed');
            } else {
                htmlElement.classList.add('sidebar-init-closed');
                htmlElement.classList.remove('sidebar-init-open');
            }
        })();

        // Modal functions - defined early so they're available everywhere
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

        function openLogoutModal() {
            const modal = document.getElementById('logoutModal');
            if (modal) {
                const scrollY = window.scrollY;
                document.body.style.position = 'fixed';
                document.body.style.top = `-${scrollY}px`;
                document.body.style.width = '100%';
                document.body.style.overflowY = 'scroll';
                modal.classList.remove('hidden');
            }
        }

        function closeLogoutModal() {
            const modal = document.getElementById('logoutModal');
            if (modal) {
                const scrollY = document.body.style.top;
                modal.classList.add('hidden');
                document.body.style.position = '';
                document.body.style.top = '';
                document.body.style.width = '';
                document.body.style.overflowY = '';
                window.scrollTo(0, parseInt(scrollY || '0') * -1);
            }
        }

        function opencompleteMembershipModal() {
            const modal = document.getElementById('completeMembershipModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeQRApprovedModal() {
            const modal = document.getElementById('qrApprovedModal');
            if (modal) {
                modal.classList.add('hidden');
            }
            window.location.reload();
        }
    </script>
</head>


<body x-data="{ 
        sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false'
    }"
    x-init="
    setTimeout(() => {
        document.body.classList.add('sidebar-loaded');
        document.documentElement.classList.remove('sidebar-init-open', 'sidebar-init-closed');
    }, 100);
    
    // Auto-collapse on mobile on initial load
    if (window.innerWidth < 768) sidebarOpen = false;
    
    // Watch resize to auto-collapse when shrinking to mobile
    window.addEventListener('resize', () => {
        if (window.innerWidth < 768) sidebarOpen = false;
    });

    $watch('sidebarOpen', val => {
        localStorage.setItem('sidebarOpen', val);
        if (!val) {
            document.documentElement.classList.add('sidebar-init-closed');
            document.documentElement.classList.remove('sidebar-init-open');
            requestAnimationFrame(() => requestAnimationFrame(() => {
                document.documentElement.classList.remove('sidebar-init-closed');
            }));
        }
    });
"
    class="flex h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

<?php
$user = \App\Models\User::with(['member.plan', 'member.subscription', 'branch'])
    ->find(Auth::id());
$member = null;
$plan   = null;
$plans  = collect();

if ($user->role === 'member') {
    // Force direct DB query — bypass ORM identity map
    $member = \App\Models\MemberProfile::with(['plan', 'subscription'])
        ->where('user_id', $user->user_id)
        ->first();

    // $plan is the member's current membership plan
    $plan = $member?->plan ?? null;

    // $plans is the full list for the branch (needed by renewal modal)
    if ($user->branch_id) {
        $plans = \App\Models\MembershipPlan::where('branch_id', $user->branch_id)
            ->orderBy('price')
            ->get();
    }
}

$selectedBranchId = null;

if ($user->role === 'super_admin') {
    $selectedBranchId = session('selected_branch_id');
} elseif ($user->branch_id) {
    $selectedBranchId = $user->branch_id;
}

$pendingApprovalsCount = 0;

if (in_array($user->role, ['admin', 'super_admin', 'staff'])) {
    $query = \App\Models\MemberProfile::where('isApproved', false)
        ->where('isDisabled', false);

    if ($selectedBranchId) {
        $query->whereHas('user', function ($q) use ($selectedBranchId) {
            $q->where('branch_id', $selectedBranchId);
        });
    }

    $pendingApprovalsCount = $query->count();
}

$lowStockCount = 0;

if (in_array($user->role, ['admin', 'staff', 'super_admin'])) {
    $lowStockThreshold = 10;

    $query = \App\Models\Inventory::where('quantity', '<=', $lowStockThreshold)
        ->where('quantity', '>', 0);

    if ($selectedBranchId) {
        $query->where('branch_id', $selectedBranchId);
    }

    $lowStockCount = $query->count();
}
?>

    <!-- Sidebar -->
    <aside
        class="sidebar-container bg-gray-800 text-white dark:bg-gray-800 dark:text-white shadow-md flex flex-col"
        :class="sidebarOpen ? 'sidebar-open' : ''">

    <!-- Logo Section -->
    <div class="flex justify-center items-center p-3 border-b border-gray-700">
            <!-- Full logo when sidebar is expanded -->
            <div x-show="sidebarOpen" x-cloak class="sidebar-content">
                <img src="{{ asset('logo_image/ez_fitness_gym_logo.png') }}" 
                    alt="EZ Fitness" 
                    class="h-24 w-auto max-w-[140px] rounded-2xl object-contain hover:scale-105 transition-transform shadow-lg shadow-gray-900/50 hover:shadow-xl hover:shadow-gray-900/40">
            </div>
        
        <!-- Icon/compact logo when sidebar is collapsed -->
        <div x-show="!sidebarOpen" x-cloak class="sidebar-collapsed-content">
                <img src="{{ asset('logo_image/ez_fitness_gym_logo.png') }}" 
                    alt="EZ Fitness" 
                    class="h-8 w-8 rounded-lg object-cover hover:scale-110 transition-transform">
            </div>
        </div>
        <nav class="flex-1 px-2 py-2 space-y-1">
           @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
    <!-- Dashboard -->
    <a href="{{ route('admin.dashboard') }}" @click="profileOpen = false"
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm">Dashboard</span>
    </a>

      

    <!-- MEMBERSHIP SECTION -->
    <div x-show="sidebarOpen" x-cloak class="pt-3 pb-1 px-3 sidebar-content">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Membership</p>
    </div>
    <div x-show="!sidebarOpen" x-cloak class="border-t border-gray-700 my-1"></div>

<a href="{{ route('admin.user_management') }}" @click="profileOpen = false"  
   class="relative flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('admin.user_management') ? 'bg-white/20 text-white' : '' }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
    </svg>
    <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm flex-1">Accounts</span>
    @if($pendingApprovalsCount > 0)
        <span x-show="sidebarOpen" x-cloak class="sidebar-content ml-auto px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse">
            {{ $pendingApprovalsCount }}
        </span>
        <span x-show="!sidebarOpen" x-cloak class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse border-2 border-gray-800"></span>
    @endif
</a>

    <a href="{{ route('admin.plan_management') }}" @click="profileOpen = false"  
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('admin.plan_management') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm">Membership Plans</span>
    </a>

        <a href="{{ route('admin.subscription_management') }}" @click="profileOpen = false"  
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('admin.subscription_management') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm">Subscriptions</span>
    </a>

    <!-- ATTENDANCE SECTION -->
    <div x-show="sidebarOpen" x-cloak class="pt-3 pb-1 px-3 sidebar-content">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Attendance</p>
    </div>
    <div x-show="!sidebarOpen" x-cloak class="border-t border-gray-700 my-1"></div>

    <a href="{{ route('attendance.scanner') }}" @click="profileOpen = false"  
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('attendance.scanner') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm">QR Scanner</span>
    </a>

    <!-- STORE SECTION -->
    <div x-show="sidebarOpen" x-cloak class="pt-3 pb-1 px-3 sidebar-content">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Store</p>
    </div>
    <div x-show="!sidebarOpen" x-cloak class="border-t border-gray-700 my-1"></div>

    <a href="{{ route('products.index') }}" @click="profileOpen = false"
       class="relative flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('products.index') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm flex-1">Inventory</span>
        @if($lowStockCount > 0)
            <span x-show="sidebarOpen" x-cloak class="sidebar-content ml-auto px-2 py-0.5 text-xs font-bold text-white bg-orange-500 rounded-full">
                {{ $lowStockCount }}
            </span>
            <span x-show="!sidebarOpen" x-cloak class="absolute top-1 right-1 w-2.5 h-2.5 bg-orange-500 rounded-full border-2 border-gray-800"></span>
        @endif
    </a>

    <a href="{{ route('pos.index') }}" @click="profileOpen = false"
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('pos.index') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm">POS</span>
    </a>

    <!-- FINANCIAL SECTION -->
    <div x-show="sidebarOpen" x-cloak class="pt-3 pb-1 px-3 sidebar-content">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Financial</p>
    </div>
    <div x-show="!sidebarOpen" x-cloak class="border-t border-gray-700 my-1"></div>

    <a href="{{ route('sales.index') }}" @click="profileOpen = false"
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('sales.index') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm">Sales</span>
    </a>

    <a href="{{ route('transactions.index') }}" @click="profileOpen = false"
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('transactions.index') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm">Transactions</span>
    </a>

    <!-- REPORTS SECTION -->
    <div x-show="sidebarOpen" x-cloak class="pt-3 pb-1 px-3 sidebar-content">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Logs</p>
    </div>
    <div x-show="!sidebarOpen" x-cloak class="border-t border-gray-700 my-1"></div>

    <a href="{{ route('logs.show') }}" @click="profileOpen = false"
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('logs.show') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm">Activity Logs</span>
    </a>

    <!-- CONFIGURATION SECTION -->
    <div x-show="sidebarOpen" x-cloak class="pt-3 pb-1 px-3 sidebar-content">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Configuration</p>
    </div>
    <div x-show="!sidebarOpen" x-cloak class="border-t border-gray-700 my-1"></div>

     @if(auth()->user()->role === 'super_admin')
 <a href="{{ route('admin.branch_management') }}" @click="profileOpen = false"
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('admin.branch_management') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm">Branches</span>
    </a>

       @endif

    <a href="{{ route('categories.index') }}" @click="profileOpen = false"
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('categories.index') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm">Categories</span>
    </a>

@elseif(auth()->user()->role === 'staff')
    <!-- Staff Section -->
   <a href="{{ route('staff.dashboard') }}" @click="profileOpen = false"
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('staff.dashboard') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm">Dashboard</span>
    </a>


    <div x-show="sidebarOpen" x-cloak class="pt-3 pb-1 px-3 sidebar-content">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Membership</p>
    </div>
    <div x-show="!sidebarOpen" x-cloak class="border-t border-gray-700 my-1"></div>

<a href="{{ route('admin.user_management') }}" @click="profileOpen = false"  
   class="relative flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('admin.user_management') ? 'bg-white/20 text-white' : '' }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
    </svg>
    <span x-show="sidebarOpen" x-cloak class="transition-opacity text-sm flex-1">Members</span>
    @if($pendingApprovalsCount > 0)
        <span x-show="sidebarOpen" x-cloak class="ml-auto px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse">
            {{ $pendingApprovalsCount }}
        </span>
        <span x-show="!sidebarOpen" x-cloak class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse border-2 border-gray-800"></span>
    @endif
</a>

    <a href="{{ route('admin.plan_management') }}" @click="profileOpen = false"  
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('admin.plan_management') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="transition-opacity text-sm">Membership Plans</span>
    </a>

         <a href="{{ route('admin.subscription_management') }}" @click="profileOpen = false"  
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('admin.subscription_management') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm">Subscriptions</span>
    </a>


    <!-- ATTENDANCE SECTION -->
    <div x-show="sidebarOpen" x-cloak class="pt-4 pb-2 px-4 sidebar-content">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Attendance</p>
    </div>
    <div x-show="!sidebarOpen" x-cloak class="border-t border-gray-700 my-2"></div>

    <a href="{{ route('attendance.scanner') }}" @click="profileOpen = false"  
       class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('attendance.scanner') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="transition-opacity">QR Scanner</span>
    </a>

    <!-- STORE SECTION -->
    <div x-show="sidebarOpen" x-cloak class="pt-4 pb-2 px-4 sidebar-content">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Store</p>
    </div>
    <div x-show="!sidebarOpen" x-cloak class="border-t border-gray-700 my-2"></div>

    <a href="{{ route('products.index') }}" @click="profileOpen = false"
       class="relative flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('products.index') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="transition-opacity flex-1">Inventory</span>
        @if($lowStockCount > 0)
            <span x-show="sidebarOpen" x-cloak class="ml-auto px-2 py-0.5 text-xs font-bold text-white bg-orange-500 rounded-full">
                {{ $lowStockCount }}
            </span>
            <span x-show="!sidebarOpen" x-cloak class="absolute top-1 right-1 w-2.5 h-2.5 bg-orange-500 rounded-full border-2 border-gray-800"></span>
        @endif
    </a>

    <a href="{{ route('pos.index') }}" @click="profileOpen = false"
       class="flex items-center space-x-2 px-4 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('pos.index') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="transition-opacity">POS</span>
    </a>

   
@elseif(auth()->user()->role === 'member')
   <a href="{{ route('member.dashboard') }}" @click="profileOpen = false"
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('member.dashboard') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm">Home</span>
    </a>

<a href="{{ route('member.member.logs') }}" @click="profileOpen = false"
       class="flex items-center space-x-2 px-3 py-2 text-white hover:bg-white/20 rounded {{ request()->routeIs('member.member.logs') ? 'bg-white/20 text-white' : '' }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <span x-show="sidebarOpen" x-cloak class="sidebar-content text-sm">Attendance History</span>
    </a>

@endif
        </nav>
    </aside>

    <div class="flex-1 @yield('fullscreen', 'flex flex-col')">
        <!-- Header - conditionally positioned for fullscreen pages -->
    <!-- Header Section - Extract and Replace in your layouts/app.blade.php -->
<header
    class="@yield('header-class', 'relative') h-16 bg-white text-white p-4 flex justify-between items-center shadow-sm dark:bg-white dark:text-gray-800 z-30">
    <div class="flex items-center space-x-3">
        <button @click="sidebarOpen = !sidebarOpen"
            class="p-2 rounded hover:bg-gray-100 hover:text-white hover:text-gray-800 text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white focus:outline-none transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <h1 class="text-lg font-semibold text-gray-800">@yield('header', 'Page')</h1>
        
        @if(auth()->user()->role === 'super_admin')
            {{-- Super Admin: Branch Selector Dropdown --}}
            <div x-data="{ branchOpen: false }" class="relative ml-4">
                <button @click="branchOpen = !branchOpen" 
                    class="flex items-center space-x-2 px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                        {{ session('selected_branch_name', 'All Branches') }}
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-700 dark:text-gray-300 transition-transform" 
                        :class="branchOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="branchOpen" x-cloak @click.away="branchOpen = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute left-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-lg z-50 border border-gray-200 dark:border-gray-700 max-h-96 overflow-y-auto">
                    
                    <div class="p-2">
                        <form method="POST" action="{{ route('admin.select-branch') }}">
                            @csrf
                            <button type="submit" name="branch_id" value="all"
                                class="w-full text-left px-4 py-2 text-sm rounded-md transition-colors
                                {{ !session('selected_branch_id') ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                <div class="flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    <span>All Branches</span>
                                </div>
                            </button>
                        </form>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700"></div>

                    <div class="p-2">
                        @php
                            $branches = \App\Models\Branches::orderBy('name')->get();
                        @endphp

                        @forelse($branches as $branch)
                            <form method="POST" action="{{ route('admin.select-branch') }}">
                                @csrf
                                <button type="submit" name="branch_id" value="{{ $branch->branch_id }}"
                                    class="w-full text-left px-4 py-2 mb-2 text-sm rounded-md transition-colors
                                    {{ session('selected_branch_id') == $branch->branch_id ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <div>
                                                <div class="font-medium">{{ $branch->name }}</div>
                                                @if($branch->address)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($branch->address, 30) }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        @if(session('selected_branch_id') == $branch->branch_id)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @endif
                                    </div>
                                </button>
                            </form>
                        @empty
                            <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                No branches available
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @elseif(auth()->user()->branch)
            {{-- Admin/Staff/Members: Display Current Branch (Read-only) --}}
            <div class="ml-4 flex items-center space-x-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                    {{ auth()->user()->branch->name }}
                </span>
            </div>
        @endif
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
                <span class="hidden md:block text-gray-800">{{ $user->first_name ?? $user->username }}</span>
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform text-gray-800"
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
@if($user->role === 'member' && $member)
    @if(!$member->isFullyApproved())
        @if($member->hasIncompleteProfile())
            <p class="text-xs text-red-600 mt-1">
                Profile incomplete
            </p>
        @elseif($member->needsApproval())
            <p class="text-xs text-yellow-600 mt-1">
                Pending approval
            </p>
        @elseif($member->isDisabled || $member->isDisabledForSubscription)
            <p class="text-xs text-red-600 mt-1">
                Access denied
            </p>
        @endif
    @endif
@endif
            </div>

            <div class="py-1">
@if($user->role === 'member' && $member && !$member->isFullyApproved())
    @if($member->hasIncompleteProfile())
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
@endif

               <button
    onclick="openProfileModal()"
    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24"
        stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
    </svg>
    View Profile
</button>

<button
    onclick="openEditProfileModal()"
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
             <button 
    @click="profileOpen = false; setTimeout(() => openLogoutModal(), 100)"
    type="button"
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

@if($user->role === 'member')
    @php
        $branches = \App\Models\Branches::orderBy('name')->get();
              $membershipPlans = collect(); // safe empty default
        $subscriptions = collect();  
    @endphp

    {{-- STEP 1: Complete Profile + Select Branch --}}
    @if(!$member || !$member->sex || !$member->birthday || !$member->mobile_number || !$user->branch_id)
        <div id="completeMembershipModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/50">
            <div class="relative bg-white text-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden max-h-[90vh]">
                <div class="bg-gray-600 text-white p-5 rounded-t-2xl">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <h2 class="text-xl font-semibold">Complete Your Profile</h2>
                    </div>
                    <p class="text-gray-100 text-sm mt-1">Step 1 of 3: Fill in your personal details and select a branch</p>
                </div>

            <form id="completeProfileForm" action="{{ route('profile.complete-member-profile') }}" method="POST" class="p-6 md:p-8 space-y-6 overflow-y-auto max-h-[calc(90vh-180px)]">     @csrf
       

                    {{-- Branch Selection --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Select Your Branch <span class="text-red-500">*</span>
                        </h3>

                        <select name="branch_id" required class="w-full rounded-xl border-2 border-gray-300 bg-gray-50 px-4 py-3 focus:border-gray-800 transition-colors">
                            <option value="">Choose a branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->branch_id }}">{{ $branch->name }} - {{ $branch->address }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Personal Information --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Personal Information
                        </h3>

                        <div class="mb-4">
                            <span class="block text-sm font-medium text-gray-700 mb-2">
                                Sex <span class="text-red-500">*</span>
                            </span>
                            <div class="flex items-center space-x-6">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="sex" value="male" required class="peer sr-only">
                                    <div class="w-16 h-16 rounded-full border-2 border-gray-300 peer-checked:border-gray-800 flex items-center justify-center bg-gray-100 hover:bg-gray-200">
                                        <img src="https://cdn-icons-png.flaticon.com/512/921/921106.png" alt="Male" class="w-10 h-10 object-contain">
                                    </div>
                                    <span class="block text-center mt-1 text-gray-800 text-sm font-medium">Male</span>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="sex" value="female" required class="peer sr-only">
                                    <div class="w-16 h-16 rounded-full border-2 border-gray-300 peer-checked:border-gray-800 flex items-center justify-center bg-gray-100 hover:bg-gray-200">
                                        <img src="https://cdn-icons-png.flaticon.com/512/921/921124.png" alt="Female" class="w-10 h-10 object-contain">
                                    </div>
                                    <span class="block text-center mt-1 text-gray-800 text-sm font-medium">Female</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="birthday" class="block text-sm font-medium text-gray-700">Birthday <span class="text-red-500">*</span></label>
                            <input type="date" name="birthday" id="birthday" required class="mt-2 block w-full rounded-xl border-gray-800 bg-gray-200 px-4 py-3">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700">Height (cm)</label>
                                <input type="number" step="0.1" name="height" id="height" class="mt-2 block w-full rounded-xl border-gray-800 bg-gray-200 px-4 py-3">
                            </div>
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                                <input type="number" step="0.1" name="weight" id="weight" class="mt-2 block w-full rounded-xl border-gray-800 bg-gray-200 px-4 py-3">
                            </div>
                        </div>

                        <div>
                            <label for="mobile_number" class="block text-sm font-medium text-gray-700">Mobile Number <span class="text-red-500">*</span></label>
                            <input type="tel" name="mobile_number" id="mobile_number" placeholder="e.g. 09123456789" pattern="[0-9]{11}" required 
                                class="mt-2 block w-full rounded-xl border-gray-800 bg-gray-200 px-4 py-3">
                        </div>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-green-800">
                                <p class="font-semibold mb-1">Next Steps:</p>
                                <p>After submitting, you'll select a membership plan and subscription.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-200">
                        <button type="submit" class="px-8 py-3 rounded-xl bg-gray-600 text-white hover:bg-gray-700 font-medium transition-colors">
                            Continue to Plan Selection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- STEP 2: Select Membership Plan --}}
  @if($member && $member->sex && $member->birthday && $member->mobile_number && $user->branch_id && !$member->plan_id)
    @php
        $membershipPlans = \App\Models\MembershipPlan::where('branch_id', $user->branch_id)
            ->orderBy('price')->get();
    @endphp
  <div id="selectPlanModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/50">
            <div class="relative bg-white text-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden max-h-[90vh]">
                <div class="bg-gray-600 text-white p-5 rounded-t-2xl">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-xl font-semibold">Membership Plan</h2>
                    </div>
                    <p class="text-white text-sm mt-1">Step 2 of 3: Select your membership plan</p>
                </div>

                <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                    <div class="mb-6">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-green-800">Profile Completed</p>
                                    <p class="text-sm text-green-700">Branch: {{ $user->branch->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-gray-700 mb-6 font-medium">Choose a membership plan:</p>

                    <form action="{{ route('member.select-plan') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($membershipPlans as $plan)
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="plan_id" value="{{ $plan->plan_id }}" required class="peer sr-only">
                                    <div class="border-2 border-gray-300 peer-checked:border-green-500 peer-checked:bg-green-50 rounded-xl p-6 hover:shadow-lg transition-all">
                                        <div class="flex justify-between items-start mb-3">
                                            <h4 class="text-xl font-bold text-gray-800">{{ $plan->name }}</h4>
                                            <div class="peer-checked:block hidden">
                                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                        @if($plan->details)
                                            <p class="text-sm text-gray-600 mb-3">{{ $plan->details }}</p>
                                        @endif
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-3xl font-bold text-gray-900">₱{{ number_format($plan->price, 2) }}</span>
                                        </div>
                                        <p class="text-sm text-gray-500">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $plan->duration_days }} days
                                        </p>
                                    </div>
                                </label>
                            @empty
                                <div class="col-span-2 text-center py-8 text-gray-500">
                                    No membership plans available.
                                </div>
                            @endforelse
                        </div>

                        @if(count($membershipPlans ?? []) > 0)
                            <div class="flex justify-end pt-4 border-t border-gray-200">
                                <button type="submit" class="px-8 py-3 rounded-xl bg-gray-600 text-white hover:bg-gray-700 font-medium transition-colors">
                                    Continue to Subscription
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- STEP 3: Select Subscription --}}
    @if($member && $member->plan_id && !$member->subscription_id && $member->subscription_status === 'pending_selection')
    @php
        $subscriptions = \App\Models\Subscriptions::where('branch_id', $user->branch_id)
            ->orderBy('price')->get();
    @endphp
    <div id="selectSubscriptionModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/50">
            <div class="relative bg-white text-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden max-h-[90vh]">
                <div class="bg-gray-600 text-white p-5 rounded-t-2xl">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h2 class="text-xl font-semibold">Subscription</h2>
                    </div>
                    <p class="text-white text-sm mt-1">Step 3 of 3: Select your subscription</p>
                </div>

                <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                    <div class="mb-6">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-green-800">Membership Plan Selected</p>
                                    <p class="text-sm text-green-700">{{ $member->plan->name }} - ₱{{ number_format($member->plan->price, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-gray-700 mb-6 font-medium">Choose your subscription:</p>

                    <form action="{{ route('member.select-subscription') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($subscriptions as $subscription)
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="subscription_id" value="{{ $subscription->subscription_id }}" required class="peer sr-only">
                                    <div class="border-2 border-gray-300 peer-checked:border-green-500 peer-checked:bg-green-50 rounded-xl p-6 hover:shadow-lg transition-all">
                                        <div class="flex justify-between items-start mb-3">
                                            <h4 class="text-xl font-bold text-gray-800">{{ $subscription->name }}</h4>
                                            <div class="peer-checked:block hidden">
                                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                        @if($subscription->details)
                                            <p class="text-sm text-gray-600 mb-3">{{ $subscription->details }}</p>
                                        @endif
                                        <div class="flex items-baseline gap-1">
                                            <span class="text-3xl font-bold text-gray-900">₱{{ number_format($subscription->price, 2) }}</span>
                                        </div>
                                        <p class="text-sm text-gray-500">{{ $subscription->duration_days }} days</p>
                                    </div>
                                </label>
                            @empty
                                <div class="col-span-2 text-center py-8 text-gray-500">
                                    No subscriptions available.
                                </div>
                            @endforelse
                        </div>

                        @if(count($subscriptions ?? []) > 0)
                            <div class="flex justify-end pt-4 border-t border-gray-200">
                                <button type="submit" class="px-8 py-3 rounded-xl bg-gray-600 text-white hover:bg-gray-700 font-medium transition-colors">
                                    Submit for Approval
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- STEP 4: Waiting for Approval --}}
    @if($member && $member->subscription_id && !$member->isApprovedForSubscription && $member->subscription_status === 'pending_subscription_approval')
        <div id="waitingSubscriptionApprovalModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/50">
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
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Awaiting Admin Approval</h3>
                        <p class="text-gray-600 text-lg mb-4">Your selections are being reviewed.</p>
                        
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg text-left space-y-2">
                            <div>
                                <p class="text-sm text-gray-600">Membership Plan:</p>
                                <p class="font-bold text-gray-800">{{ $member->plan->name }}</p>
                                <p class="text-sm text-gray-500">₱{{ number_format($member->plan->price, 2) }}</p>
                            </div>
                            <div class="border-t pt-2">
                                <p class="text-sm text-gray-600">Subscription:</p>
                                <p class="font-bold text-gray-800">{{ $member->subscription->name }}</p>
                                <p class="text-sm text-gray-500">₱{{ number_format($member->subscription->price, 2) }}</p>
                            </div>
                        </div>
                        
                        <p class="text-gray-500 text-sm mt-4">You'll receive your QR code via email once approved.</p>
                    </div>

                    <div class="space-y-3">
                        <button onclick="checkApprovalStatus(this)" 
                            class="w-full px-6 py-3 rounded-xl bg-yellow-500 text-white hover:bg-yellow-600 font-medium transition-colors flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Check Status
                        </button>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full px-6 py-3 rounded-xl bg-gray-600 text-white hover:bg-gray-700 font-medium transition-colors">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- FINAL: QR Code Approved --}}
    <div id="qrApprovedModal" class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/50">
    <div class="relative bg-white text-gray-800 rounded-2xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden">

        <!-- Header -->
        <div class="bg-green-500 text-white p-5">
            <div class="flex items-center space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-lg font-semibold">Membership Activated!</h2>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">

            <!-- Left Column -->
            <div class="text-center md:text-left">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4 mx-auto md:mx-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-1">Welcome to EZ Fitness!</h3>
                <p class="text-gray-600">Your membership is now active.</p>

                <div class="member-data-section mt-4">
                    <!-- populated by JS -->
                </div>
            </div>

            <!-- Right Column -->
            <div id="qrCodeDisplay" class="hidden text-center bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-600 mb-2">Your QR Code</p>
                <img id="qrCodeImage" src="" alt="QR Code" class="mx-auto w-40 h-40 border rounded">
                <p class="text-xs text-gray-500 mt-2">Also sent to your email</p>
            </div>

        </div>

        <!-- Footer Button -->
        <div class="p-6 pt-0">
            <button onclick="closeQRApprovedModal()"
                class="w-full px-6 py-3 rounded-xl bg-green-500 text-white hover:bg-green-600 font-medium transition">
                Continue to Dashboard
            </button>
        </div>

    </div>
</div>

@if($user->role === 'member' && $member && $member->renewal_pending && !$member->isApprovedForSubscription)
    <div id="renewalCheckStatusModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/50">
        <div class="relative bg-white text-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="bg-yellow-500 text-white p-5 rounded-t-2xl">
                <div class="flex items-center space-x-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-xl font-semibold">Renewal Pending</h2>
                </div>
            </div>

            <div class="p-8 text-center">
                <div class="mb-6">
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Renewal Request Submitted</h3>
                    <p class="text-gray-600 text-lg mb-4">
                        Your renewal request is awaiting admin approval.
                    </p>
                    
                    @if($member->subscription)
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg text-left space-y-2">
                            <div>
                                <p class="text-sm text-gray-600">Subscription:</p>
                                <p class="font-bold text-gray-800">{{ $member->subscription->name }}</p>
                                <p class="text-sm text-gray-500">₱{{ number_format($member->subscription->price, 2) }}</p>
                            </div>
                            @if($member->plan)
                            <div class="border-t pt-2">
                                <p class="text-sm text-gray-600">Plan:</p>
                                <p class="font-bold text-gray-800">{{ $member->plan->name }}</p>
                                <p class="text-sm text-gray-500">₱{{ number_format($member->plan->price, 2) }}</p>
                            </div>
                            @endif
                        </div>
                    @endif
                    
                    <p class="text-gray-500 text-sm mt-4">You'll receive your QR code via email once approved.</p>
                </div>

                <div class="space-y-3">
                    <button onclick="checkRenewalStatus(this)" 
                        id="renewalCheckBtn"
                        class="w-full px-6 py-3 rounded-xl bg-yellow-500 text-white hover:bg-yellow-600 font-medium transition-colors flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Check Renewal Status
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
@endif


@endif




    {{-- VIEW PROFILE MODAL --}}
    <div id="profileModal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/40">
    <div
        class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden border border-gray-200">

        <!-- Header -->
        <div class="flex justify-between items-center p-6 bg-gradient-to-r from-gray-800 to-gray-700">
            <div>
                <h2 class="text-2xl font-bold text-white">My Profile</h2>
                <p class="text-sm text-gray-200 mt-1">View your account information</p>
            </div>
            <button onclick="closeProfileModal()"
                class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-white/10 text-gray-200 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6 max-h-[calc(100vh-16rem)] overflow-y-auto bg-gray-50">

            <!-- Personal Information Section -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Personal Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                        <p class="text-xs font-medium text-gray-500 mb-1">First Name</p>
                        <p class="text-base font-semibold text-gray-900">{{ $user->first_name }}</p>
                    </div>
                    <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                        <p class="text-xs font-medium text-gray-500 mb-1">Last Name</p>
                        <p class="text-base font-semibold text-gray-900">{{ $user->last_name }}</p>
                    </div>
                </div>

                <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <p class="text-xs font-medium text-gray-500 mb-1">Username</p>
                    <p class="text-base font-semibold text-gray-900">{{ $user->username }}</p>
                </div>

                <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <p class="text-xs font-medium text-gray-500 mb-1">Email Address</p>
                    <p class="text-base font-semibold text-gray-900">{{ $user->email }}</p>
                </div>

                <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <p class="text-xs font-medium text-gray-500 mb-2">Role</p>
                    <span
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-full
                        {{ $user->role === 'admin' ? 'bg-red-100 text-red-700 border border-red-200' :
                        ($user->role === 'staff' ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-green-100 text-green-700 border border-green-200') }}">
                        @if($user->role === 'admin')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        @elseif($user->role === 'staff')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        @endif
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>

            <!-- MEMBER PROFILE DETAILS -->
            @if($user->role === 'member')
                @if($member)
                    <div class="space-y-4 pt-4 border-t-2 border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Membership Details
                        </h3>

                        <div class="p-5 bg-gradient-to-br from-gray-800 to-gray-700 rounded-xl shadow-lg">
                            <p class="text-xs font-medium text-gray-300 mb-2">Current Plan</p>
                            <p class="text-2xl font-bold text-white">{{ $plan->name ?? 'N/A' }}</p>
                            @if($plan)
                                <p class="text-sm text-gray-300 mt-1">₱{{ number_format($plan->price, 2) }}</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            @if($member->sex)
                                <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                    <p class="text-xs font-medium text-gray-500 mb-1">Sex</p>
                                    <p class="text-base font-semibold text-gray-900 capitalize">{{ $member->sex }}</p>
                                </div>
                            @endif
                            @if($member->birthday)
                                <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                    <p class="text-xs font-medium text-gray-500 mb-1">Birthday</p>
                                    <p class="text-base font-semibold text-gray-900">{{ \Carbon\Carbon::parse($member->birthday)->format('M d, Y') }}</p>
                                </div>
                            @endif
                        </div>

                        @if($member->height || $member->weight || $member->mobile_number)
                            <div class="space-y-4 pt-4">
                                <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Physical Information
                                </h4>

                                <div class="grid grid-cols-2 gap-4">
                                    @if($member->height)
                                        <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                            <p class="text-xs font-medium text-gray-500 mb-1">Height</p>
                                            <p class="text-base font-semibold text-gray-900">
                                                {{ $member->height }} <span class="text-sm font-normal text-gray-600">cm</span>
                                            </p>
                                        </div>
                                    @endif
                                    @if($member->weight)
                                        <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                            <p class="text-xs font-medium text-gray-500 mb-1">Weight</p>
                                            <p class="text-base font-semibold text-gray-900">
                                                {{ $member->weight }} <span class="text-sm font-normal text-gray-600">kg</span>
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                @if($member->mobile_number)
                                    <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                        <p class="text-xs font-medium text-gray-500 mb-1">Mobile Number</p>
                                        <p class="text-base font-semibold text-gray-900">{{ $member->mobile_number }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-6 bg-amber-50 rounded-xl border-2 border-amber-200">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-amber-900">Profile Incomplete</p>
                                <p class="text-sm text-amber-800 mt-1">Please complete your membership profile to access all features.</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- Footer -->
        <div class="flex justify-end gap-3 p-6 bg-white border-t border-gray-200">
            <button onclick="closeProfileModal()"
                class="px-8 py-2.5 rounded-xl bg-gray-800 text-white hover:bg-gray-700 font-medium transition-colors shadow-lg hover:shadow-xl">
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
                                    @foreach(($plans ?? []) as $plan)
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


@if($user->role === 'member' && $member && $member->status === 'expired' && !$member->renewal_pending)
    {{-- Membership Expired - Renewal Required Modal --}}
    <div id="renewalRequiredModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/50">
        <div class="relative bg-white text-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
            <div class="bg-red-500 text-white p-5 rounded-t-2xl">
                <div class="flex items-center space-x-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h2 class="text-xl font-semibold">Membership Expired</h2>
                </div>
            </div>

            <form action="{{ route('member.request-renewal') }}" method="POST" class="p-8">
                @csrf

                <div class="mb-6 text-center">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Your Membership Has Expired</h3>
                    <p class="text-gray-600 text-lg mb-4">
                        @if($member->suspended_at)
                            Your membership was suspended on {{ \Carbon\Carbon::parse($member->suspended_at)->format('M d, Y') }}.
                        @else
                            Your membership plan expired on {{ \Carbon\Carbon::parse($member->end_date)->format('M d, Y') }}.
                        @endif
                    </p>
                    <p class="text-gray-500 text-sm">Please select a new plan to continue using our services.</p>
                </div>

                <div x-data="{ open: false, selected: '', selectedId: '' }" class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Choose Your New Membership Plan <span class="text-red-500">*</span>
                    </label>

                    <input type="hidden" name="plan_id" x-model="selectedId" required>

                    <button type="button" @click="open = !open"
                        class="w-full flex justify-between items-center rounded-xl border-2 border-gray-300 bg-gray-50 px-4 py-4 hover:border-gray-400 transition-colors">
                        <span x-text="selected || 'Select a membership plan'" class="text-gray-700"></span>
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false"
                         class="absolute mt-2 w-full max-w-xl bg-white border border-gray-300 rounded-xl shadow-xl z-50 max-h-96 overflow-y-auto">
                       @foreach(($plans ?? []) as $plan)
                            <div @click="
                                    selected = '{{ $plan->name }} — ₱{{ number_format($plan->price, 2) }} / {{ $plan->duration_days }} days';
                                    selectedId = '{{ $plan->plan_id }}';
                                    open = false
                                "
                                class="flex justify-between items-center px-6 py-4 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0 transition-colors">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $plan->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $plan->duration_days }} days</p>
                                </div>
                                <p class="font-bold text-lg text-gray-800">₱{{ number_format($plan->price, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-amber-800">
                            <p class="font-semibold mb-1">Note:</p>
                            <p>After selecting a plan, your renewal request will be sent to the admin for approval. You'll receive a confirmation email once approved.</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" name="action" value="renew"
                        class="flex-1 px-6 py-3 rounded-xl bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700 font-medium transition-all shadow-lg">
                        Request Renewal
                    </button>
                    <button type="submit" name="action" value="logout"
                        class="flex-1 px-6 py-3 rounded-xl bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium transition-colors">
                        Logout
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<div id="logoutModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm hidden">
    <div class="absolute inset-0 backdrop-blur bg-opacity-50" onclick="closeLogoutModal()"></div>

    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>

            <h3 class="text-xl font-bold text-center text-gray-900 mb-2">
                Logout Confirmation
            </h3>

            <p class="text-center text-gray-600 mb-6">
                Are you sure you want to logout? You will be redirected to the login page.
            </p>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <div class="flex gap-3">
                    <button type="button" onclick="closeLogoutModal()"
                        class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-lg font-medium transition-colors">
                        Logout
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>






    <script src="//unpkg.com/alpinejs" defer></script>

<script>


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
// Complete Profile Form Validation
document.addEventListener('DOMContentLoaded', function () {
    const completeProfileForm = document.getElementById('completeProfileForm');

    if (completeProfileForm) {
        completeProfileForm.addEventListener('submit', function (e) {
            let valid = true;
            clearAllErrors(this);

            // Branch validation
            const branch = this.querySelector('select[name="branch_id"]');
            if (!branch || !branch.value) {
                showError(branch, 'Please select a branch');
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

function checkRenewalStatus(btn) {
    const button = btn;
    const originalContent = button.innerHTML;

    button.disabled = true;
    button.innerHTML = `<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>`;

    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    if (!csrfToken) {
        Notifications.toast('error', 'Security token not found. Please refresh the page.');
        button.disabled = false;
        button.innerHTML = originalContent;
        return;
    }

    fetch('/member/check-approval', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json',
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
    })
    .then(data => {
        console.log('Renewal approval status:', data);

        if (data.status === 'approved') {
            Notifications.toast('success', 'Your renewal has been approved! Redirecting...');
            setTimeout(() => { window.location.reload(); }, 2000);

        } else if (data.status === 'rejected') {
            const renewalModal = document.getElementById('renewalCheckStatusModal');
            if (renewalModal) renewalModal.classList.add('hidden');
            Notifications.toast('error', data.message || 'Renewal was not approved.');
            setTimeout(() => { window.location.reload(); }, 3000);

        } else {
            Notifications.toast('warning', 'Renewal still pending approval. Please try again in a moment.');
        }
    })
    .catch(error => {
        console.error('Error checking renewal status:', error);
        Notifications.toast('error', 'Error checking status. Please try again.');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalContent;
    });
}

function checkApprovalStatus(btn) {
    const button = btn;
    const originalContent = button.innerHTML;

    button.disabled = true;
    button.innerHTML = `<svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>`;

    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    if (!csrfToken) {
        Notifications.toast('error', 'Security token not found. Please refresh the page.');
        button.disabled = false;
        button.innerHTML = originalContent;
        return;
    }

    fetch('/member/check-approval', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json',
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
    })
    .then(data => {
        console.log('Approval status:', data);

        if (data.status === 'approved') {
            const waitingModal = document.getElementById('waitingSubscriptionApprovalModal');
            if (waitingModal) waitingModal.classList.add('hidden');

            const approvedModal = document.getElementById('qrApprovedModal');
            if (approvedModal) {
                approvedModal.classList.remove('hidden');

                if (data.member_data) {
                    const memberDataSection = approvedModal.querySelector('.member-data-section');
                    if (memberDataSection) {
                        memberDataSection.innerHTML = `
                            <div class="mt-4 p-4 bg-gray-50 rounded-lg text-left space-y-2">
                                <div>
                                    <p class="text-sm text-gray-600">Plan:</p>
                                    <p class="font-bold text-gray-800">${data.member_data.plan}</p>
                                    <p class="text-sm text-gray-500">${data.member_data.plan_price}</p>
                                </div>
                                <div class="border-t pt-2">
                                    <p class="text-sm text-gray-600">Subscription:</p>
                                    <p class="font-bold text-gray-800">${data.member_data.subscription}</p>
                                    <p class="text-sm text-gray-500">${data.member_data.subscription_price}</p>
                                </div>
                                <div class="border-t pt-2">
                                    <p class="text-sm text-gray-600">Valid Until:</p>
                                    <p class="font-bold text-gray-800">${data.member_data.end_date}</p>
                                </div>
                            </div>`;
                    }
                }

                if (data.qr_code_url) {
                    const qrDisplay = document.getElementById('qrCodeDisplay');
                    const qrImage = document.getElementById('qrCodeImage');
                    if (qrDisplay && qrImage) {
                        qrImage.src = data.qr_code_url;
                        qrDisplay.classList.remove('hidden');
                    }
                }
            }

            Notifications.toast('success', data.message || 'Your membership has been approved!');

        } else if (data.status === 'rejected') {
            Notifications.toast('error', data.message || 'Application was not approved.');

        } else {
            Notifications.toast('warning', data.message || 'Still pending approval.');
        }
    })
    .catch(error => {
        console.error('Error checking approval status:', error);
        Notifications.toast('error', 'Error checking status. Please try again.');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalContent;
    });
}

function closeQRApprovedModal() {
    const modal = document.getElementById('qrApprovedModal');
    if (modal) {
        modal.classList.add('hidden');
    }
    window.location.reload();
}

</script>

<!-- Load SweetAlert2 first -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Then load notifications.js -->
<script src="{{ asset('js/notifications.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Notifications.toast('success', @json(session('success')));
            @endif

            @if(session('error'))
                Notifications.toast('error', @json(session('error')));
            @endif

            @if(session('warning'))
                Notifications.toast('warning', @json(session('warning')));
            @endif

            @if(session('info'))
                Notifications.toast('info', @json(session('info')));
            @endif

            @if(session('status'))
                Notifications.toast('success', @json(session('status')));
            @endif
        });
    </script>
    
    @stack('scripts')


    




</body>

</html>
