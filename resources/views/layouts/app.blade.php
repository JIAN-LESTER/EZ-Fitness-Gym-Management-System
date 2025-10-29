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
    <aside
        class="bg-gray-800 text-white dark:bg-white dark:text-gray-800 shadow-md flex flex-col transition-all duration-800"
        :class="sidebarOpen ? 'w-60' : 'w-16'">

        <div class="p-4 font-bold text-white dark:text-gray-800 text-lg truncate">
            <span x-show="sidebarOpen" x-cloak class="transition-opacity">EZ Fitness</span>
            <span x-show="!sidebarOpen" x-cloak class="transition-opacity">EZ</span>
        </div>

        <nav class="flex-1 px-2 space-y-2">
            @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" @click="profileOpen = false"
                                class="flex items-center space-x-2 px-4 py-2 dark:text-gray-800 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white rounded {{ request()->routeIs('admin.dashboard') ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4 0h8" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Dashboard</span>
                            </a>

                            <a href="{{ route('admin.user_management') }}" @click="profileOpen = false" class="flex items-center space-x-2 px-4 py-2 dark:text-gray-800 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white rounded 
                   {{ request()->routeIs('admin.user_management') ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Users</span>
                            </a>

                            <a href="{{ route('admin.plan_management') }}" @click="profileOpen = false" class="flex items-center space-x-2 px-4 py-2 dark:text-gray-800 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white rounded 
                   {{ request()->routeIs('admin.plan_management') ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Membership Plans</span>
                            </a>
                                  <a href="#" @click="profileOpen = false"
                                class="flex items-center space-x-2 px-4 py-2 dark:text-gray-800 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white rounded {{ request()->routeIs('logs.show') ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Inventory</span>
                            </a>
                                  <a href="#" @click="profileOpen = false"
                                class="flex items-center space-x-2 px-4 py-2 dark:text-gray-800 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white rounded {{ request()->routeIs('logs.show') ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Sales</span>
                            </a>
                                      <a href="#" @click="profileOpen = false"
                                class="flex items-center space-x-2 px-4 py-2 dark:text-gray-800 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white rounded {{ request()->routeIs('logs.show') ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Transactions</span>
                            </a>
                                      <a href="#" @click="profileOpen = false"
                                class="flex items-center space-x-2 px-4 py-2 dark:text-gray-800 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white rounded {{ request()->routeIs('logs.show') ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Attendance Logs</span>
                            </a>
                            <a href="{{ route('logs.show') }}" @click="profileOpen = false"
                                class="flex items-center space-x-2 px-4 py-2 dark:text-gray-800 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white rounded {{ request()->routeIs('logs.show') ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span x-show="sidebarOpen" x-cloak class="transition-opacity">Logs</span>
                            </a>

            @elseif(auth()->user()->role === 'member')
                <a href="{{ route('member.dashboard') }}" @click="profileOpen = false"
                    class="flex items-center space-x-2 px-4 py-2 dark:text-gray-800 hover:bg-white hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-white rounded {{ request()->routeIs('user.dashboard') ? 'bg-white text-gray-800 dark:bg-gray-800 dark:text-white' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m-4 0h8" />
                    </svg>
                    <span x-show="sidebarOpen" x-cloak class="transition-opacity">Dashboard</span>
                </a>

                <a href="#" @click="profileOpen = false"
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
        <header
            class="@yield('header-class', 'relative') bg-white text-gray-800 p-4 flex justify-between items-center shadow-sm dark:bg-gray-800 dark:text-white z-30">
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
                <button @click="profileOpen = !profileOpen" class="flex items-center space-x-2 focus:outline-none px-3 py-2 rounded transition-colors 
               hover:bg-gray-700 hover:text-white">
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


    @if($user->role === 'member' && $member && $member->status === 'inactive')
        <div id="completeMembershipModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-sm">
            <div class="relative bg-white text-gray-800 dark:bg-white dark:text-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden">

                <div class="bg-gray-600 text-white p-5 rounded-t-2xl">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <h2 class="text-xl font-semibold">Complete Your Membership Profile</h2>
                    </div>
                    <p class="text-gray-100 text-sm mt-1">Please complete your membership information to continue.</p>
                </div>

                <form id="completeProfileForm" action="{{ route('profile.complete-member-profile') }}" method="POST"
                    class="p-6 md:p-8 space-y-6 relative z-10">
                    @csrf
                    @method('PUT')

     <div x-data="{ open: false, selected: '', selectedId: '' }" class="relative">
    <label for="plan_id" class="block text-sm font-medium text-gray-800">
        Membership Plan <span class="text-red-500">*</span>
    </label>

    <!-- Hidden input that actually submits the value -->
    <input type="hidden" name="plan_id" x-model="selectedId">

    <button type="button" @click="open = !open"
        class="mt-2 w-full flex justify-between items-center rounded-xl border border-gray-800 bg-gray-200 px-4 py-3">
        <span x-text="selected || 'Select a plan'"></span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 9l-7 7-7-7" />
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

  <!-- Flex container for avatars -->
  <div class="flex items-center space-x-6">
    <!-- Male -->
    <label class="relative cursor-pointer">
      <input type="radio" name="sex" value="male" {{ old('sex')=='male' ? 'checked' : '' }} class="peer sr-only">
      <div class="w-16 h-16 rounded-full border-2 border-gray-300 peer-checked:border-gray-800 
                  flex items-center justify-center transition duration-200 bg-gray-100 hover:bg-gray-200">
        <img src="https://cdn-icons-png.flaticon.com/512/921/921106.png" 
             alt="Male avatar" class="w-10 h-10 object-contain opacity-90">
      </div>
      <span class="block text-center mt-1 text-gray-800 text-sm font-medium">Male</span>
    </label>

    <!-- Female -->
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

  <!-- ✅ Error message outside the flex container -->
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
                            class="mt-2 block w-full rounded-xl border-gray-800 bg-gray-200 dark:border-gray-600 
                                 px-4 py-3 focus:ring-gray-500 focus:border-gray-500">
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
                                class="mt-2 block w-full rounded-xl border-gray-800 bg-gray-200 dark:border-gray-600 
                                       px-4 py-3 focus:ring-gray-500 focus:border-gray-500">
                                               @error('height')
          <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
        @enderror
                        </div>
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-800">
                                Weight (kg)
                            </label>
                            <input type="number" step="0.1" name="weight" id="weight"
                                class="mt-2 block w-full rounded-xl border-gray-800 bg-gray-200 dark:border-gray-600 
                                       px-4 py-3 focus:ring-gray-500 focus:border-gray-500">
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
                            class="mt-2 block w-full rounded-xl border-gray-800 bg-gray-200 dark:border-gray-600 
                                   px-4 py-3 focus:ring-gray-500 focus:border-gray-500">
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
                    @if($memberProfile)
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
                                    {{ $memberProfile->plan->name ?? 'N/A' }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                @if($memberProfile->sex)
                                    <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sex</p>
                                        <p class="text-base font-semibold text-gray-900 dark:text-white capitalize">
                                            {{ $memberProfile->sex }}</p>
                                    </div>
                                @endif
                                @if($memberProfile->birthday)
                                    <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Birthday</p>
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($memberProfile->birthday)->format('M d, Y') }}</p>
                                    </div>
                                @endif
                            </div>

                            @if($memberProfile->height || $memberProfile->weight || $memberProfile->mobile_number)
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
                                        @if($memberProfile->height)
                                            <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Height</p>
                                                <p class="text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ $memberProfile->height }} <span class="text-sm font-normal">cm</span></p>
                                            </div>
                                        @endif
                                        @if($memberProfile->weight)
                                            <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Weight</p>
                                                <p class="text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ $memberProfile->weight }} <span class="text-sm font-normal">kg</span></p>
                                            </div>
                                        @endif
                                    </div>

                                    @if($memberProfile->mobile_number)
                                        <div class="p-4 bg-gray-200 text-gray-800 dark:bg-gray-900/50 rounded-xl">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Mobile Number</p>
                                            <p class="text-base font-semibold text-gray-900 dark:text-white">
                                                {{ $memberProfile->mobile_number }}</p>
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
                    @if($user->role === 'member' && $memberProfile)
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
                                        <option value="{{ $plan->plan_id }}" {{ $memberProfile->plan_id == $plan->plan_id ? 'selected' : '' }}>
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
                                        <option value="male" {{ $memberProfile->sex == 'male' ? 'selected' : '' }}>Male
                                        </option>
                                        <option value="female" {{ $memberProfile->sex == 'female' ? 'selected' : '' }}>Female
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label for="birthday"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Birthday</label>
                                    <input type="date" name="birthday" id="birthday" value="{{ $memberProfile->birthday }}"
                                        class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="height"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Height
                                        (cm)</label>
                                    <input type="number" step="0.1" name="height" id="height"
                                        value="{{ $memberProfile->height }}"
                                        class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                                </div>
                                <div>
                                    <label for="weight"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Weight
                                        (kg)</label>
                                    <input type="number" step="0.1" name="weight" id="weight"
                                        value="{{ $memberProfile->weight }}"
                                        class="block w-full rounded-xl border-gray-800 bg-gray-200 text-gray-800  px-4 py-3 focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-all">
                                </div>
                            </div>

                            <div>
                                <label for="mobile_number"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-800 mb-2">Mobile
                                    Number</label>
                                <input type="tel" name="mobile_number" id="mobile_number"
                                    value="{{ $memberProfile->mobile_number }}"
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