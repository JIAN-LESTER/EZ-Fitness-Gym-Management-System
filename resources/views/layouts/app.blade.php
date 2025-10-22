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
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body x-data="{ sidebarOpen: true }"
    class="flex h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

    <?php $user = Auth::user();

  $member = $user->member;
    
    
    ?>

    <!-- Sidebar -->
    <aside class="bg-gray-800 text-white dark:bg-white dark:text-gray-800 shadow-md flex flex-col transition-all duration-300"
        :class="sidebarOpen ? 'w-60' : 'w-16'">

        <div class="p-4 font-bold text-white dark:text-gray-800 text-lg truncate">
            <span x-show="sidebarOpen" x-cloak class="transition-opacity">EZ Fitness</span>
            <span x-show="!sidebarOpen" x-cloak class="transition-opacity">EZ</span>
        </div>

        <nav class="flex-1 px-2 space-y-2">
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}"
                  @click="profileOpen = false"
                    class="flex items-center space-x-2 px-4 py-2 dark:text-gray-800 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white rounded {{ request()->routeIs('admin.dashboard') ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4 0h8" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">Dashboard</span>
                </a>

                 <a href="{{ route('admin.user_management') }}"
                 @click="profileOpen = false"
                    class="flex items-center space-x-2 px-4 py-2 dark:text-gray-800 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white rounded {{ request()->routeIs('admin.user_management') ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">User Management</span>
                </a>

                <a href="#"
                     @click="profileOpen = false"
                    class="flex items-center space-x-2 px-4 py-2 dark:text-gray-800 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white rounded {{ request()->routeIs('logs.show') ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">Logs</span>
                </a>

            @elseif(auth()->user()->role === 'member')
                <a href="{{ route('member.dashboard') }}"
                    @click="profileOpen = false"
                    class="flex items-center space-x-2 px-4 py-2 dark:text-gray-800 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white rounded {{ request()->routeIs('user.dashboard') ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4 0h8" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">Dashboard</span>
                </a>

                 <a href="#"
                    @click="profileOpen = false"
                    class="flex items-center space-x-2 px-4 py-2 dark:text-gray-800 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white rounded {{ request()->routeIs('user.dashboard') ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4 0h8" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">Store</span>
                </a>
            @endif

           
        </nav>
    </aside>


    <div class="flex-1 @yield('fullscreen', 'flex flex-col')">
        <!-- Header - conditionally positioned for fullscreen pages -->
        <header class="@yield('header-class', 'relative') bg-white text-gray-800 p-4 flex justify-between items-center shadow-sm dark:bg-gray-800 dark:text-white z-30">
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="p-2 rounded hover:bg-gray-800 hover:text-white dark:hover:bg-white dark:hover:text-gray-800 focus:outline-none transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h1 class="text-lg font-semibold">@yield('header', 'Page')</h1>
            </div>

     <div x-data="{ profileOpen: false }" class="relative">
    <button @click="profileOpen = !profileOpen"
        class="flex items-center space-x-2 focus:outline-none px-3 py-2 rounded transition-colors 
               hover:bg-gray-700 hover:text-white">
        <div class="flex items-center space-x-2">
            <div class="relative">
                <div class="w-8 h-8 bg-gray-800 text-white dark:bg-white dark:text-gray-800 rounded-full flex items-center justify-center font-semibold transition-colors duration-200">
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

    <div x-show="profileOpen" 
         x-cloak
         @click.away="profileOpen = false" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-lg z-50 border border-gray-200">

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
            @if($user->role === 'member' && $member && $member->status === 'inactive')
                <button onclick="opencompleteMembershipModal(); document.querySelector('[x-data]').__x.$data.profileOpen = false"
                    class="flex items-center w-full px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    Complete Profile
                </button>
            @endif

            <button onclick="openProfileModal(); document.querySelector('[x-data]').__x.$data.profileOpen = false"
                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                View Profile
            </button>

            <button onclick="openEditProfileModal(); document.querySelector('[x-data]').__x.$data.profileOpen = false"
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


   @if($user->role === 'member' && $member && $member->status === 'inactive')
    <div id="completeMembershipModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">
            
            <div class="bg-green-600 text-white p-5 rounded-t-2xl">
                <div class="flex items-center space-x-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <h2 class="text-xl font-semibold">Complete Your Profile</h2>
                </div>
                <p class="text-green-100 text-sm mt-1">Please complete your membership information to continue.</p>
            </div>

            <form id="completeProfileForm" action="{{ route('profile.update') }}" method="POST"
                class="p-6 md:p-8 space-y-6 relative z-10">
                @csrf
                @method('PUT')

                <div>
                    <label for="plan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Membership Plan <span class="text-red-500">*</span>
                    </label>
                    <select name="plan_id" id="plan_id" required
                        class="mt-2 block w-full rounded-xl border-gray-300 bg-gray-200 dark:border-gray-600 
                               dark:bg-gray-800 dark:text-gray-200 px-4 py-3 focus:ring-green-500 focus:border-green-500">
                        <option value="">Select a plan</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->plan_id }}">{{ $plan->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sex <span class="text-red-500">*</span>
                    </span>
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="sex" value="male" required
                                class="text-green-600 focus:ring-green-500">
                            <span class="text-gray-700 dark:text-gray-300">Male</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="sex" value="female" required
                                class="text-green-600 focus:ring-green-500">
                            <span class="text-gray-700 dark:text-gray-300">Female</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="birthday" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Birthday <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="birthday" id="birthday" required
                        class="mt-2 block w-full rounded-xl border-gray-300 bg-gray-200 dark:border-gray-600 
                               dark:bg-gray-800 dark:text-gray-200 px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Height (cm)
                        </label>
                        <input type="number" step="0.1" name="height" id="height"
                            class="mt-2 block w-full rounded-xl border-gray-300 bg-gray-200 dark:border-gray-600 
                                   dark:bg-gray-800 dark:text-gray-200 px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Weight (kg)
                        </label>
                        <input type="number" step="0.1" name="weight" id="weight"
                            class="mt-2 block w-full rounded-xl border-gray-300 bg-gray-200 dark:border-gray-600 
                                   dark:bg-gray-800 dark:text-gray-200 px-4 py-3 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>

                <div>
                    <label for="mobile_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Mobile Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="mobile_number" id="mobile_number" placeholder="e.g. 09123456789"
                        required pattern="[0-9]{11}"
                        class="mt-2 block w-full rounded-xl border-gray-300 bg-gray-200 dark:border-gray-600 
                               dark:bg-gray-800 dark:text-gray-200 px-4 py-3 focus:ring-green-500 focus:border-green-500">
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                        class="px-6 py-3 rounded-xl bg-green-600 text-white hover:bg-green-700 font-medium">
                        Complete Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif


 
{{-- VIEW PROFILE MODAL --}}
<div id="profileModal" class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/40">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden border border-gray-200 dark:border-gray-700">

        <div class="flex justify-between items-center p-6 bg-gray-50 dark:bg-gray-900/50">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">My Profile</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">View your account information</p>
            </div>
            <button onclick="closeProfileModal()" 
                class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-6 space-y-6 max-h-[calc(100vh-16rem)] overflow-y-auto">
            
            {{-- Personal Information Section --}}
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Personal Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">First Name</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $user->first_name }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Last Name</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $user->last_name }}</p>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Username</p>
                    <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $user->username }}</p>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Email Address</p>
                    <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $user->email }}</p>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Role</p>
                    <span class="inline-block px-3 py-1 text-sm rounded-full 
                        {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : 
                           ($user->role === 'staff' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>

            {{-- MEMBER PROFILE DETAILS - Only show if user is a member --}}
            @if($user->role === 'member')
                @if($memberProfile)
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Membership Details
                        </h3>
                        
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800">
                            <p class="text-xs font-medium text-green-600 dark:text-green-400 mb-1">Current Plan</p>
                            <p class="text-lg font-bold text-green-900 dark:text-green-300">{{ $memberProfile->plan->name ?? 'N/A' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            @if($memberProfile->sex)
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sex</p>
                                    <p class="text-base font-semibold text-gray-900 dark:text-white capitalize">{{ $memberProfile->sex }}</p>
                                </div>
                            @endif
                            @if($memberProfile->birthday)
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Birthday</p>
                                    <p class="text-base font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($memberProfile->birthday)->format('M d, Y') }}</p>
                                </div>
                            @endif
                        </div>

                        @if($memberProfile->height || $memberProfile->weight || $memberProfile->mobile_number)
                            <div class="space-y-4">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Physical Information
                                </h4>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    @if($memberProfile->height)
                                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Height</p>
                                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $memberProfile->height }} <span class="text-sm font-normal">cm</span></p>
                                        </div>
                                    @endif
                                    @if($memberProfile->weight)
                                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Weight</p>
                                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $memberProfile->weight }} <span class="text-sm font-normal">kg</span></p>
                                        </div>
                                    @endif
                                </div>

                                @if($memberProfile->mobile_number)
                                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Mobile Number</p>
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $memberProfile->mobile_number }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div class="p-6 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-amber-900 dark:text-amber-300">Profile Incomplete</p>
                                <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">Please complete your membership profile to access all features.</p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <div class="flex justify-end gap-3 p-6 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
            <button onclick="closeProfileModal()"
                class="px-6 py-2.5 rounded-xl bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 font-medium transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

{{-- EDIT PROFILE MODAL - Updated for non-members --}}
<div id="editProfileModal" class="hidden fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm bg-black/40">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden border border-gray-200 dark:border-gray-700">

        <div class="flex justify-between items-center p-6 bg-gray-50 dark:bg-gray-900/50">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Profile</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update your account information</p>
            </div>
            <button onclick="closeEditProfileModal()" 
                class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="max-h-[calc(100vh-12rem)] overflow-y-auto">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                {{-- Personal Information Section --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Personal Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
                            <input type="text" name="first_name" id="first_name" value="{{ $user->first_name }}"
                                class="block w-full rounded-xl border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name</label>
                            <input type="text" name="last_name" id="last_name" value="{{ $user->last_name }}"
                                class="block w-full rounded-xl border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                        </div>
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
                        <input type="text" name="username" id="username" value="{{ $user->username }}"
                            class="block w-full rounded-xl border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ $user->email }}"
                            class="block w-full rounded-xl border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                    </div>
                </div>

                {{-- Membership Information Section - Only for members --}}
                @if($user->role === 'member')
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Membership Details
                        </h3>

                        <div>
                            <label for="plan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Membership Plan</label>
                            <select name="plan_id" id="plan_id"
                                class="block w-full rounded-xl border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                                <option value="">Select a plan</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->plan_id }}" 
                                        {{ $memberProfile && $memberProfile->plan_id == $plan->plan_id ? 'selected' : '' }}>
                                        {{ $plan->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="sex" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sex</label>
                                <select name="sex" id="sex"
                                    class="block w-full rounded-xl border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                                    <option value="male" {{ $memberProfile && $memberProfile->sex == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $memberProfile && $memberProfile->sex == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div>
                                <label for="birthday" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Birthday</label>
                                <input type="date" name="birthday" id="birthday" value="{{ $memberProfile->birthday ?? '' }}"
                                    class="block w-full rounded-xl border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Height (cm)</label>
                                <input type="number" step="0.1" name="height" id="height" value="{{ $memberProfile->height ?? '' }}"
                                    class="block w-full rounded-xl border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Weight (kg)</label>
                                <input type="number" step="0.1" name="weight" id="weight" value="{{ $memberProfile->weight ?? '' }}"
                                    class="block w-full rounded-xl border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                            </div>
                        </div>

                        <div>
                            <label for="mobile_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mobile Number</label>
                            <input type="tel" name="mobile_number" id="mobile_number" value="{{ $memberProfile->mobile_number ?? '' }}"
                                class="block w-full rounded-xl border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                        </div>
                    </div>
                @endif

                {{-- Security Section --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Change Password
                        <span class="text-xs font-normal text-gray-500 dark:text-gray-400">(Optional)</span>
                    </h3>

                    <div>
                        <label for="old_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                        <input type="password" name="old_password" id="old_password"
                            class="block w-full rounded-xl border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                            <input type="password" name="new_password" id="new_password"
                                class="block w-full rounded-xl border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                class="block w-full rounded-xl border-gray-300 bg-gray-50 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 p-6 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeEditProfileModal()"
                    class="px-6 py-2.5 rounded-xl bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 font-medium transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-6 py-2.5 rounded-xl bg-green-600 text-white hover:bg-green-700 font-medium transition-colors shadow-lg shadow-green-500/30">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>




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

          // Close modals on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeProfileModal();
                closeEditProfileModal();
            }
        });

        // Close modals when clicking outside
        document.getElementById('profileModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'profileModal') closeProfileModal();
        });

        document.getElementById('editProfileModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'editProfileModal') closeEditProfileModal();
        });

      
        function opencompleteMembershipModal() {
            const modal = document.getElementById('completeMembershipModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closecompleteMembershipModal() {
            const modal = document.getElementById('completeMembershipModal');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

     
        document.addEventListener("DOMContentLoaded", function () {
            @if(session('showProfileModal'))
                openEditProfileModal();
            @elseif($member && $member->status === 'inactive')
            
                setTimeout(function () {
                    opencompleteMembershipModal();
                }, 1000);
            @endif
        });

   
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeProfileModal();
                closeEditProfileModal();
                @if($member && $member->status === 'inactive')
                    closecompleteMembershipModal();
                @endif
            }
        });


        @if($member && $member->status === 'inactive')
            document.addEventListener('click', function (event) {
                const modal = document.getElementById('completeMembershipModal');
                if (modal && event.target === modal) {
         
                    event.preventDefault();
                }
            });
        @endif
    </script>

    @stack('scripts')
</body>

</html>