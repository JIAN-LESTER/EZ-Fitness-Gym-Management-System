@extends('layouts.app')
@section('title', 'Home')
@section('header', 'Home')

@section('content')

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <div class="w-full px-4 py-6">
        
        <!-- Welcome Section -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-6 mb-6 border border-white/20">
            <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-2">
                Welcome back, {{ Auth::user()->first_name }}!
            </h2>
            <p class="text-gray-600 text-lg">Here's your fitness journey overview</p>
        </div>

        <!-- Top Stats Row - 4 Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            
            <!-- Membership Status Card -->
@php
    $planExpired = false;
    $planDaysLeft = 0;
    $planStatus = 'inactive';
    
    if($memberProfile && $memberProfile->plan && $memberProfile->end_date) {
        $now = \Carbon\Carbon::now();
        $endDate = \Carbon\Carbon::parse($memberProfile->end_date);
        $planDaysLeft = (int) ceil($now->diffInDays($endDate, false));
        
        if($memberProfile->status === 'cancelled') {
            $planStatus = 'cancelled';
            $planExpired = true;
        }
        elseif($planDaysLeft <= 0) {
            $planStatus = 'expired';
            $planExpired = true;
        }
        else {
            $planStatus = 'active';
            $planExpired = false;
        }
    }
@endphp

<div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-5 border-l-4 h-52 transform hover:scale-105 transition-all duration-300
    {{ !$memberProfile ? 'border-gray-400' : ($planStatus === 'cancelled' ? 'border-purple-500' : ($planExpired ? 'border-red-500' : 'border-emerald-500')) }}">
    
    @if($memberProfile && $memberProfile->plan)
        <div class="flex items-center justify-between mb-2">
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Membership</p>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                {{ $planStatus === 'cancelled' ? 'bg-purple-100 text-purple-700' : ($planExpired ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700') }}">
                {{ $planStatus === 'cancelled' ? 'Cancelled' : ($planExpired ? 'Expired' : 'Active') }}
            </span>
        </div>

        <h4 class="text-lg font-bold text-gray-800 mb-0.5 truncate">{{ $memberProfile->plan->name }}</h4>
        <p class="text-xs text-gray-600 mb-2">₱{{ number_format($memberProfile->plan->price, 2) }}</p>
        
        @if($planStatus === 'cancelled')
            <div class="mb-2">
                <h3 class="text-2xl font-black text-purple-600 mb-0 leading-none">Cancelled</h3>
                <p class="text-xs text-gray-500 font-medium">Plan has been cancelled</p>
            </div>
            <button onclick="openRenewalModal()" class="block w-full text-center bg-purple-600 text-white py-2 px-3 rounded-xl hover:bg-purple-700 transition-colors font-bold text-xs mt-2">
                Renew Plan
            </button>
        @elseif($memberProfile->end_date)
            <div class="mb-2">
                <h3 class="text-4xl font-black {{ $planExpired ? 'text-red-600' : 'text-emerald-600' }} mb-0 leading-none">
                    {{ abs($planDaysLeft) }}
                </h3>
                <p class="text-xs text-gray-500 font-medium">days {{ $planDaysLeft > 0 ? 'left' : 'overdue' }}</p>
            </div>

            @if($planExpired)
                <button onclick="openRenewalModal()" class="block w-full text-center bg-red-600 text-white py-2 px-3 rounded-xl hover:bg-red-700 transition-colors font-bold text-xs mt-2">
                    Renew Now
                </button>
            @endif
        @endif
    @else
        <div class="flex flex-col items-center justify-center h-full text-center">
            <svg class="w-14 h-14 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            <p class="text-gray-600 font-bold text-base mb-3">No Active Plan</p>
            <button onclick="openRenewalModal()" class="inline-block bg-emerald-500 text-white px-4 py-2 rounded-lg hover:bg-emerald-600 transition-colors text-sm font-bold">
                Get Started
            </button>
        </div>
    @endif
</div>

<!-- Subscription Card -->
@php
    $subExpired = false;
    $subDaysLeft = 0;
    $subStatus = 'inactive';
    
    if($memberProfile && $memberProfile->subscription && $memberProfile->end_date_for_subscription) {
        $now = \Carbon\Carbon::now();
        $endDate = \Carbon\Carbon::parse($memberProfile->end_date_for_subscription);
        $subDaysLeft = (int) ceil($now->diffInDays($endDate, false));
        
        if($memberProfile->subscription_status === 'cancelled') {
            $subStatus = 'cancelled';
            $subExpired = true;
        }
        elseif($memberProfile->subscription_status === 'suspended') {
            $subStatus = 'suspended';
            $subDaysLeft = $memberProfile->days_remaining_before_suspend ?? 0;
        }
        elseif($subDaysLeft <= 0) {
            $subStatus = 'expired';
            $subExpired = true;
        }
        else {
            $subStatus = 'active';
            $subExpired = false;
        }
    }
@endphp

<div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-5 border-l-4 h-52 transform hover:scale-105 transition-all duration-300
    {{ !$memberProfile || !$memberProfile->subscription ? 'border-gray-400' : ($subStatus === 'cancelled' ? 'border-purple-500' : ($subStatus === 'suspended' ? 'border-orange-500' : ($subExpired ? 'border-red-500' : 'border-blue-500'))) }}">
    
    @if($memberProfile && $memberProfile->subscription)
        <div class="flex items-center justify-between mb-2">
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Subscription</p>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                {{ $subStatus === 'cancelled' ? 'bg-purple-100 text-purple-700' : 
                   ($subStatus === 'suspended' ? 'bg-orange-100 text-orange-700' : 
                   ($subExpired ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')) }}">
                {{ $subStatus === 'cancelled' ? 'Cancelled' : 
                   ($subStatus === 'suspended' ? 'Suspended' : 
                   ($subExpired ? 'Expired' : 'Active')) }}
            </span>
        </div>

        <h4 class="text-lg font-bold text-gray-800 mb-0.5 truncate">{{ $memberProfile->subscription->name }}</h4>
        <p class="text-xs text-gray-600 mb-2">₱{{ number_format($memberProfile->subscription->price, 2) }}</p>
        
        @if($subStatus === 'cancelled')
            <div class="mb-2">
                <h3 class="text-2xl font-black text-purple-600 mb-0 leading-none">Cancelled</h3>
                <p class="text-xs text-gray-500 font-medium">Subscription cancelled</p>
            </div>
            <button onclick="openRenewalModal()" class="block w-full text-center bg-purple-600 text-white py-2 px-3 rounded-xl hover:bg-purple-700 transition-colors font-bold text-xs mt-2">
                Subscribe Again
            </button>
        @elseif($subStatus === 'suspended')
            <div class="mb-2">
                <h3 class="text-4xl font-black text-orange-600 mb-0 leading-none">
                    {{ $subDaysLeft }}
                </h3>
                <p class="text-xs text-gray-500 font-medium">days paused</p>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-2 mt-2">
                <p class="text-xs text-orange-800 font-medium">Your subscription is paused. Contact admin to resume.</p>
            </div>
        @elseif($memberProfile->end_date_for_subscription)
            <div class="mb-2">
                <h3 class="text-4xl font-black {{ $subExpired ? 'text-red-600' : 'text-blue-600' }} mb-0 leading-none">
                    {{ abs($subDaysLeft) }}
                </h3>
                <p class="text-xs text-gray-500 font-medium">days {{ $subDaysLeft > 0 ? 'left' : 'overdue' }}</p>
            </div>

            @if($subExpired)
                <button onclick="openRenewalModal()" class="block w-full text-center bg-red-600 text-white py-2 px-3 rounded-xl hover:bg-red-700 transition-colors font-bold text-xs mt-2">
                    Renew Now
                </button>
            @endif
        @endif
    @else
        <div class="flex flex-col items-center justify-center h-full text-center">
            <svg class="w-14 h-14 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-gray-600 font-bold text-base mb-3">No Subscription</p>
            <button onclick="openRenewalModal()" class="inline-block bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors text-sm font-bold">
                Subscribe
            </button>
        </div>
    @endif
</div>

            <!-- Gym Occupancy Card -->
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6 border-l-4 border-violet-500 h-52 transform hover:scale-105 transition-all duration-300">
                <div class="flex flex-col h-full">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Gym Occupancy</p>
                        <div class="bg-violet-100 p-2 rounded-xl">
                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    
                    <div class="flex-1 flex flex-col justify-center">
                        <h3 class="text-5xl font-black text-violet-600 mb-2">{{ $currentOccupancy }}</h3>
                        <p class="text-sm text-gray-600 font-medium mb-4">Members in gym now</p>
                    </div>
                    
                    <div class="mt-auto">
                        @if($currentOccupancy < 10)
                            <span class="inline-flex items-center bg-emerald-100 text-emerald-700 px-3 py-2 rounded-xl text-xs font-bold w-full justify-center">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                                Low Traffic
                            </span>
                        @elseif($currentOccupancy < 25)
                            <span class="inline-flex items-center bg-amber-100 text-amber-700 px-3 py-2 rounded-xl text-xs font-bold w-full justify-center">
                                <span class="w-2 h-2 bg-amber-500 rounded-full mr-2 animate-pulse"></span>
                                Moderate Traffic
                            </span>
                        @else
                            <span class="inline-flex items-center bg-red-100 text-red-700 px-3 py-2 rounded-xl text-xs font-bold w-full justify-center">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse"></span>
                                High Traffic
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- My Check-ins Card -->
            <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-5 border-l-4 border-purple-500 h-52 transform hover:scale-105 transition-all duration-300">
                <div class="flex flex-col h-full">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">My Check-ins</p>
                        <div class="bg-purple-100 p-2 rounded-xl">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    
                    <div class="flex-1 flex flex-col justify-center mb-1">
                        <h3 class="text-5xl font-black text-purple-600 mb-0 leading-none">{{ $thisMonthCheckIns }}</h3>
                        <p class="text-sm text-gray-600 font-medium">This month</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-1 rounded-xl border border-purple-100">
                        <p class="text-xs text-gray-500 mb-0.5 font-semibold">Total Check-ins</p>
                        <p class="text-2xl font-black text-gray-800">{{ $totalCheckIns }}</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column - Plans & Subscriptions (2 columns) -->
            <div class="lg:col-span-2">
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-white/20">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Available Plans</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
                        <!-- Membership Plans Column -->
                        <div id="plans">
                            <h4 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                                Membership Plans
                            </h4>
                            <div class="space-y-3">
                                @forelse($plans as $plan)
                                <div class="bg-gradient-to-br from-gray-50 to-white border-2 rounded-xl p-4 hover:shadow-lg hover:border-emerald-300 transition-all duration-200
                                    {{ $memberProfile && $memberProfile->plan_id === $plan->plan_id ? 'border-emerald-500 bg-gradient-to-br from-emerald-50 to-white' : 'border-gray-200' }}">
                                    
                                    @if($memberProfile && $memberProfile->plan_id === $plan->plan_id)
                                        <span class="inline-block bg-emerald-500 text-white text-xs px-2 py-1 rounded-full mb-2 font-bold">Active</span>
                                    @endif
                                    
                                    <h5 class="text-base font-bold text-gray-900 mb-1">{{ $plan->name }}</h5>
                                    <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $plan->details }}</p>

                                    <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                                        <span class="text-sm font-bold text-emerald-600">₱{{ number_format($plan->price) }}</span>
                                        <span class="text-xs text-gray-500 font-semibold">{{ $plan->duration_days }} days</span>
                                    </div>
                                </div>
                                @empty
                                <p class="text-gray-500 text-center py-4 text-sm">No plans available</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Subscriptions Column -->
                        <div id="subscriptions">
                            <h4 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                Subscriptions
                            </h4>
                            <div class="space-y-3">
                                @forelse($subscriptions as $subscription)
                                <div class="bg-gradient-to-br from-gray-50 to-white border-2 rounded-xl p-4 hover:shadow-lg hover:border-blue-300 transition-all duration-200
                                    {{ $memberProfile && $memberProfile->subscription_id === $subscription->subscription_id ? 'border-blue-500 bg-gradient-to-br from-blue-50 to-white' : 'border-gray-200' }}">
                                    
                                    @if($memberProfile && $memberProfile->subscription_id === $subscription->subscription_id)
                                        <span class="inline-block bg-blue-500 text-white text-xs px-2 py-1 rounded-full mb-2 font-bold">Active</span>
                                    @endif
                                    
                                    <h5 class="text-base font-bold text-gray-900 mb-1">{{ $subscription->name }}</h5>
                                    <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $subscription->details }}</p>

                                    <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                                        <span class="text-sm font-bold text-blue-600">₱{{ number_format($subscription->price) }}</span>
                                        <span class="text-xs text-gray-500 font-semibold">{{ $subscription->duration_days }} days</span>
                                    </div>
                                </div>
                                @empty
                                <p class="text-gray-500 text-center py-4 text-sm">No subscriptions available</p>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Right Column - Recent Check-ins (1 column) -->
            <div>
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-white/20">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Recent Check-ins</h3>
                    <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($recentAttendance as $attendance)
                        <div class="p-4 bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 rounded-xl border border-blue-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-bold text-gray-800 text-sm">
                                    {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('M d, Y') }}
                                </p>
                                @if($attendance->status === 'checked_in')
                                    <span class="bg-emerald-100 text-emerald-700 text-xs px-2.5 py-1 rounded-full font-bold flex items-center">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="bg-gray-100 text-gray-700 text-xs px-2.5 py-1 rounded-full font-bold">Completed</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 font-medium">
                                <span class="text-xs text-gray-500">In:</span> {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}
                            </p>
                            @if($attendance->check_out_time)
                                <p class="text-sm text-gray-600 font-medium">
                                    <span class="text-xs text-gray-500">Out:</span> {{ \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') }}
                                </p>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-gray-500 font-medium">No check-in history yet</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Renewal Modal -->
    <div id="renewalModal" class="fixed inset-0 z-50 hidden flex items-center justify-center backdrop-blur-sm bg-black/50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6">
                <h3 class="text-2xl font-bold">Renew Membership</h3>
                <p class="text-blue-100 text-sm mt-1">Select a subscription to renew your access</p>
            </div>

            <form action="{{ route('member.request-renewal') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="action" value="renew">

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Choose Subscription</label>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @forelse($subscriptions as $subscription)
                            <label class="block cursor-pointer">
                                <input type="radio" name="subscription_id" value="{{ $subscription->subscription_id }}" required class="peer sr-only">
                                <div class="border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 rounded-xl p-4 transition-all hover:shadow-md">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-gray-800">{{ $subscription->name }}</h4>
                                        <div class="peer-checked:block hidden">
                                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @if($subscription->details)
                                        <p class="text-xs text-gray-600 mb-2">{{ $subscription->details }}</p>
                                    @endif
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-bold text-blue-600">₱{{ number_format($subscription->price, 2) }}</span>
                                        <span class="text-gray-500">{{ $subscription->duration_days }} days</span>
                                    </div>
                                </div>
                            </label>
                        @empty
                            <p class="text-gray-500 text-center py-4">No subscriptions available</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-6">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-amber-800">Your renewal request will be sent to admin for approval. You'll receive a QR code via email once approved.</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeRenewalModal()" class="flex-1 px-4 py-3 rounded-xl bg-gray-200 text-gray-700 hover:bg-gray-300 font-semibold transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 font-semibold transition-colors shadow-lg">
                        Submit Renewal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <script>
        function openRenewalModal() {
            document.getElementById('renewalModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRenewalModal() {
            document.getElementById('renewalModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRenewalModal();
            }
        });
    </script>
</body>

@endsection