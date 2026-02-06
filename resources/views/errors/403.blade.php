<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
    x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))"
    :class="darkMode ? 'dark' : ''">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Access Denied - Unauthorized</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo_image/ez_fitness_gym_logo.png') }}">
</head>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 text-center">
        <!-- Icon -->
        <div class="mb-6">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 dark:bg-red-900/20">
                <i class="fas fa-lock text-4xl text-red-600 dark:text-red-400"></i>
            </div>
        </div>

        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            Access Denied
        </h1>

        <!-- Message -->
        <p class="text-gray-600 dark:text-gray-300 mb-6">
            @if(isset($exception) && $exception->getMessage())
                {{ $exception->getMessage() }}
            @else
                You don't have permission to access this resource.
            @endif
        </p>

        <!-- Additional Info -->
        @auth
            <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    <i class="fas fa-info-circle mr-2"></i>
                    Your current role: <strong>{{ ucfirst(auth()->user()->role) }}</strong>
                </p>
            </div>
        @endauth

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url()->previous() }}" 
               class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Go Back
            </a>
            
            @auth
                <a href="{{ route('member.dashboard') }}" 
                   class="px-6 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                    <i class="fas fa-home mr-2"></i>Go to Dashboard
                </a>
            @else
                <a href="{{ route('loginForm') }}" 
                   class="px-6 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login
                </a>
            @endauth
        </div>

        <!-- Help Text -->
        <p class="mt-6 text-xs text-gray-500 dark:text-gray-400">
            If you believe this is an error, please contact your administrator.
        </p>
    </div>
</body>

</html>
