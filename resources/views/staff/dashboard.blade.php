@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Staff Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400">Welcome back, {{ auth()->user()->first_name }}!</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Stats</h3>
            <p class="text-gray-600 dark:text-gray-400">Dashboard content will be displayed here.</p>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 md:col-span-2">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h3>
            <p class="text-gray-600 dark:text-gray-400">Recent sales, check-ins, and other activities will appear here.</p>
        </div>
    </div>
</div>
@endsection