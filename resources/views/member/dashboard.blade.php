@extends('layouts.app')
@section('title', 'Home')
@section('header', 'Home')

@section('content')

<body class="bg-gradient-to-br from-slate-50 via-gray-50 to-gray-50 min-h-screen">
    <div class="w-full px-4 py-6">
        
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-6 mb-6 border border-white/20">
            <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-600 to-gray-600 bg-clip-text text-transparent mb-2">
                Welcome back, {{ Auth::user()->first_name }}!
            </h2>
            <p class="text-gray-600 text-lg">Here's your fitness journey overview</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            
       @php
            $planExpired = false;
            $planDaysLeft = 0;
            $planStatus = 'inactive';
            $isPendingRenewal = false;
            
            if($memberProfile && $memberProfile->plan) { 
                // CHECK: We use the *subscription* date here because the data seems crossed
                $dateToUse = $memberProfile->end_date_for_subscription ?? $memberProfile->end_date; 
                
                $isPendingRenewal = $memberProfile->renewal_pending;
                $now = \Carbon\Carbon::now();
                
                if($dateToUse) {
                    $endDate = \Carbon\Carbon::parse($dateToUse);
                    $planDaysLeft = (int) ceil($now->diffInDays($endDate, false));
                }
                
                if($isPendingRenewal) {
                    $planStatus = 'pending_renewal';
                }
                elseif($memberProfile->status === 'cancelled') {
                    $planStatus = 'cancelled';
                    $planExpired = true;
                }
                elseif($memberProfile->status === 'suspended') {
                    $planStatus = 'suspended';
                    $planDaysLeft = (int) ($memberProfile->plan_days_remaining_before_suspend ?? 0);
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
            {{ !$memberProfile ? 'border-gray-400' : 
               ($planStatus === 'pending_renewal' ? 'border-yellow-500' :
               ($planStatus === 'suspended' ? 'border-orange-500' :
               ($planStatus === 'cancelled' ? 'border-purple-500' : 
               ($planExpired ? 'border-red-500' : 'border-emerald-500')))) }}">
            
            @if($memberProfile && $memberProfile->plan)
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Membership</p>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                        {{ $planStatus === 'pending_renewal' ? 'bg-yellow-100 text-yellow-700' :
                           ($planStatus === 'suspended' ? 'bg-orange-100 text-orange-700' :
                           ($planStatus === 'cancelled' ? 'bg-purple-100 text-purple-700' : 
                           ($planExpired ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'))) }}">
                        {{ $planStatus === 'pending_renewal' ? 'Pending Approval' :
                           ($planStatus === 'suspended' ? 'Suspended' :
                           ($planStatus === 'cancelled' ? 'Cancelled' : 
                           ($planExpired ? 'Expired' : 'Active'))) }}
                    </span>
                </div>

                <h4 class="text-lg font-bold text-gray-800 mb-0.5 truncate">{{ $memberProfile->plan->name }}</h4>
                <p class="text-xs text-gray-600 mb-2">₱{{ number_format($memberProfile->plan->price, 2) }}</p>
                
                @if($planStatus === 'pending_renewal')
                    <div class="mb-2">
                        <h3 class="text-2xl font-black text-yellow-600 mb-0 leading-none">Pending</h3>
                        <p class="text-xs text-gray-500 font-medium">Awaiting admin approval</p>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-2 mt-2">
                        <p class="text-xs text-yellow-800 font-medium">Your renewal request is being processed</p>
                    </div>
                @elseif($planStatus === 'suspended')
                    <div class="mb-2">
                        <h3 class="text-4xl font-black text-orange-600 mb-0 leading-none">
                            {{ $planDaysLeft }}
                        </h3>
                        <p class="text-xs text-gray-500 font-medium">days paused</p>
                    </div>
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-2 mt-2">
                        <p class="text-xs text-orange-800 font-medium">Your membership is paused. Contact admin to resume.</p>
                    </div>
                   
                @elseif($planStatus === 'cancelled')
                    <div class="mb-2">
                        <h3 class="text-2xl font-black text-purple-600 mb-0 leading-none">Cancelled</h3>
                        <p class="text-xs text-gray-500 font-medium">Plan has been cancelled</p>
                    </div>
                    <button onclick="openRenewalModal('membership-first')" class="block w-full text-center bg-purple-600 text-white py-2 px-3 rounded-xl hover:bg-purple-700 transition-colors font-bold text-xs mt-2">
                        Renew Plan
                    </button>
                @else
                    <div class="mb-2">
                        <h3 class="text-4xl font-black {{ $planExpired ? 'text-red-600' : 'text-emerald-600' }} mb-0 leading-none">
                            {{ abs($planDaysLeft) }}
                        </h3>
                        <p class="text-xs text-gray-500 font-medium">days {{ $planDaysLeft > 0 ? 'left' : 'overdue' }}</p>
                    </div>

                    @if($planExpired)
                        <button onclick="openRenewalModal('membership-first')" class="block w-full text-center bg-red-600 text-white py-2 px-3 rounded-xl hover:bg-red-700 transition-colors font-bold text-xs mt-2">
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
                    <button onclick="openRenewalModal('membership-first')" class="inline-block bg-emerald-500 text-white px-4 py-2 rounded-lg hover:bg-emerald-600 transition-colors text-sm font-bold">
                        Get Started
                    </button>
                </div>
            @endif
        </div>

        @php
            $subExpired = false;
            $subDaysLeft = 0;
            $subStatus = 'inactive';
            
            if($memberProfile && $memberProfile->subscription) {
                // CHECK: We use the *plan* end_date here because the data seems crossed
                $subDateToUse = $memberProfile->end_date ?? $memberProfile->end_date_for_subscription;

                $now = \Carbon\Carbon::now();
                
                if($subDateToUse) {
                    $endDate = \Carbon\Carbon::parse($subDateToUse);
                    $subDaysLeft = (int) ceil($now->diffInDays($endDate, false));
                }
                
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
            {{ !$memberProfile || !$memberProfile->subscription ? 'border-gray-400' : ($subStatus === 'cancelled' ? 'border-purple-500' : ($subStatus === 'suspended' ? 'border-orange-500' : ($subExpired ? 'border-red-500' : 'border-gray-500'))) }}">
            
            @if($memberProfile && $memberProfile->subscription)
                <div class="flex items-center justify-between mb-2">
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Subscription</p>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                        {{ $subStatus === 'cancelled' ? 'bg-purple-100 text-purple-700' : 
                           ($subStatus === 'suspended' ? 'bg-orange-100 text-orange-700' : 
                           ($subExpired ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700')) }}">
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
                    <button onclick="openRenewalModal('subscription-first')" class="block w-full text-center bg-purple-600 text-white py-2 px-3 rounded-xl hover:bg-purple-700 transition-colors font-bold text-xs mt-2">
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
                @else
                    <div class="mb-2">
                        <h3 class="text-4xl font-black {{ $subExpired ? 'text-red-600' : 'text-gray-600' }} mb-0 leading-none">
                            {{ abs($subDaysLeft) }}
                        </h3>
                        <p class="text-xs text-gray-500 font-medium">days {{ $subDaysLeft > 0 ? 'left' : 'overdue' }}</p>
                    </div>

                    @if($subExpired)
                        <button onclick="openRenewalModal('subscription-first')" class="block w-full text-center bg-red-600 text-white py-2 px-3 rounded-xl hover:bg-red-700 transition-colors font-bold text-xs mt-2">
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
                    <button onclick="openRenewalModal('subscription-first')" class="inline-block bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors text-sm font-bold">
                        Subscribe
                    </button>
                </div>
            @endif
        </div>

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2">
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-white/20">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Available Plans</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        
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

                        <div id="subscriptions">
                            <h4 class="text-lg font-bold text-gray-700 mb-4 flex items-center">
                                <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                Subscriptions
                            </h4>
                            <div class="space-y-3">
                                @forelse($subscriptions as $subscription)
                                <div class="bg-gradient-to-br from-gray-50 to-white border-2 rounded-xl p-4 hover:shadow-lg hover:border-gray-300 transition-all duration-200
                                    {{ $memberProfile && $memberProfile->subscription_id === $subscription->subscription_id ? 'border-blue-500 bg-gradient-to-br from-blue-50 to-white' : 'border-gray-200' }}">
                                    
                                    @if($memberProfile && $memberProfile->subscription_id === $subscription->subscription_id)
                                        <span class="inline-block bg-blue-500 text-white text-xs px-2 py-1 rounded-full mb-2 font-bold">Active</span>
                                    @endif
                                    
                                    <h5 class="text-base font-bold text-gray-900 mb-1">{{ $subscription->name }}</h5>
                                    <p class="text-xs text-gray-600 mb-3 line-clamp-2">{{ $subscription->details }}</p>

                                    <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                                        <span class="text-sm font-bold text-gray-600">₱{{ number_format($subscription->price) }}</span>
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

            <div>
                <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl p-6 border border-white/20">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Recent Check-ins</h3>
                    <div class="space-y-3 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($recentAttendance as $attendance)
                        <div class="p-4 bg-gradient-to-br from-gray-50 via-purple-50 to-pink-50 rounded-xl border border-gray-100 hover:shadow-md transition-shadow">
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
            <div class="bg-gradient-to-r from-gray-600 to-gray-600 text-white p-6">
                <h3 class="text-2xl font-bold" id="renewalModalTitle">Renew Membership</h3>
                <p class="text-gray-100 text-sm mt-1" id="renewalModalSubtitle">Select a plan to continue</p>
            </div>

            <!-- Step 1: Membership Plan Selection -->
            <div id="step1-membership" class="p-6">
                <div class="mb-4" id="membership-back-btn" style="display: none;">
                    <button type="button" onclick="backToSubscriptionStep()" class="flex items-center text-gray-600 hover:text-gray-800 font-medium text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Subscription
                    </button>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Choose Membership Plan</label>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @php
                            $membershipPlans = \App\Models\MembershipPlan::select('plan_id', 'name', 'price', 'duration_days', 'details')->get();
                        @endphp
                        @forelse($membershipPlans as $plan)
                            <label class="block cursor-pointer">
                                <input type="radio" name="selected_plan_id" value="{{ $plan->plan_id }}" class="peer sr-only plan-radio">
                                <div class="border-2 border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 rounded-xl p-4 transition-all hover:shadow-md">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-gray-800">{{ $plan->name }}</h4>
                                        <div class="peer-checked:block hidden">
                                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @if($plan->details)
                                        <p class="text-xs text-gray-600 mb-2">{{ $plan->details }}</p>
                                    @endif
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-bold text-emerald-600">₱{{ number_format($plan->price, 2) }}</span>
                                        <span class="text-gray-500">{{ $plan->duration_days }} days</span>
                                    </div>
                                </div>
                            </label>
                        @empty
                            <p class="text-gray-500 text-center py-4">No plans available</p>
                        @endforelse
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeRenewalModal()" class="flex-1 px-4 py-3 rounded-xl bg-gray-200 text-gray-700 hover:bg-gray-300 font-semibold transition-colors">
                        Cancel
                    </button>
                    <button type="button" id="membership-next-btn" onclick="proceedToSubscriptionStep()" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 text-white hover:from-emerald-700 hover:to-emerald-800 font-semibold transition-colors shadow-lg">
                        Next: Subscription
                    </button>
                </div>
            </div>

            <!-- Step 2: Subscription Selection -->
            <div id="step2-subscription" class="p-6 hidden">
                <div class="mb-4" id="subscription-back-btn-top">
                    <button type="button" onclick="backToMembershipStep()" class="flex items-center text-gray-600 hover:text-gray-800 font-medium text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Plan
                    </button>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Choose Subscription</label>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @forelse($subscriptions as $subscription)
                            <label class="block cursor-pointer">
                                <input type="radio" name="selected_subscription_id" value="{{ $subscription->subscription_id }}" class="peer sr-only subscription-radio">
                                <div class="border-2 border-gray-200 peer-checked:border-gray-500 peer-checked:bg-gray-50 rounded-xl p-4 transition-all hover:shadow-md">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-gray-800">{{ $subscription->name }}</h4>
                                        <div class="peer-checked:block hidden">
                                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @if($subscription->details)
                                        <p class="text-xs text-gray-600 mb-2">{{ $subscription->details }}</p>
                                    @endif
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="font-bold text-gray-600">₱{{ number_format($subscription->price, 2) }}</span>
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

                <form action="{{ route('member.request-renewal') }}" method="POST" id="renewalForm">
                    @csrf
                    <input type="hidden" name="action" value="renew">
                    <input type="hidden" name="plan_id" id="final_plan_id">
                    <input type="hidden" name="subscription_id" id="final_subscription_id">

                    <div class="flex gap-3">
                        <button type="button" id="subscription-back-btn-bottom" onclick="backToMembershipStep()" class="flex-1 px-4 py-3 rounded-xl bg-gray-200 text-gray-700 hover:bg-gray-300 font-semibold transition-colors">
                            Back
                        </button>
                        
                        <button type="button" id="subscription-next-btn" onclick="proceedToMembershipStep()" style="display: none;" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-gray-600 to-gray-700 text-white hover:from-gray-700 hover:to-gray-800 font-semibold transition-colors shadow-lg">
                            Next: Membership
                        </button>
                        <button type="submit" id="subscription-submit-btn" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-gray-600 to-gray-700 text-white hover:from-gray-700 hover:to-gray-800 font-semibold transition-colors shadow-lg">
                            Submit Renewal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let renewalFlow = 'membership-first';

        function openRenewalModal(flowType = 'membership-first') {
            renewalFlow = flowType;
            document.getElementById('renewalModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            document.querySelectorAll('.plan-radio').forEach(radio => radio.checked = false);
            document.querySelectorAll('.subscription-radio').forEach(radio => radio.checked = false);
            document.getElementById('final_plan_id').value = '';
            document.getElementById('final_subscription_id').value = '';
            
            if (flowType === 'subscription-first') {
                document.getElementById('step1-membership').classList.add('hidden');
                document.getElementById('step2-subscription').classList.remove('hidden');
                document.getElementById('renewalModalTitle').textContent = 'Subscribe';
                document.getElementById('renewalModalSubtitle').textContent = 'Select a subscription to continue';
                
                document.getElementById('subscription-next-btn').style.display = 'block';
                document.getElementById('subscription-submit-btn').style.display = 'none';
                document.getElementById('subscription-back-btn-top').style.display = 'none';
                document.getElementById('subscription-back-btn-bottom').style.display = 'none';
                
                document.getElementById('membership-next-btn').style.display = 'none';
                document.getElementById('membership-back-btn').style.display = 'block';
            } else {
                document.getElementById('step1-membership').classList.remove('hidden');
                document.getElementById('step2-subscription').classList.add('hidden');
                document.getElementById('renewalModalTitle').textContent = 'Renew Membership';
                document.getElementById('renewalModalSubtitle').textContent = 'Select a plan to continue';
                
                document.getElementById('membership-next-btn').style.display = 'block';
                document.getElementById('membership-back-btn').style.display = 'none';
                
                document.getElementById('subscription-next-btn').style.display = 'none';
                document.getElementById('subscription-submit-btn').style.display = 'block';
                document.getElementById('subscription-back-btn-top').style.display = 'block';
                document.getElementById('subscription-back-btn-bottom').style.display = 'block';
            }
        }

        function closeRenewalModal() {
            document.getElementById('renewalModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

    function proceedToSubscriptionStep() {
        const selectedPlan = document.querySelector('input[name="selected_plan_id"]:checked');

        if (!selectedPlan) {
            toastr?.error('Please select a membership plan first') || alert('Please select a membership plan first');
            return;
        }

        document.getElementById('final_plan_id').value = selectedPlan.value;

        document.getElementById('step1-membership').classList.add('hidden');
        document.getElementById('step2-subscription').classList.remove('hidden');

        document.getElementById('renewalModalTitle').textContent = 'Choose Subscription';
        document.getElementById('renewalModalSubtitle').textContent = 'Complete your renewal';


        document.getElementById('subscription-submit-btn').style.display = 'block';
        document.getElementById('subscription-next-btn').style.display = 'none';
        document.getElementById('subscription-back-btn-top').style.display = 'block';
        document.getElementById('subscription-back-btn-bottom').style.display = 'block';
    }


        function proceedToMembershipStep() {
            const selectedSubscription = document.querySelector('input[name="selected_subscription_id"]:checked');
            
            if (!selectedSubscription) {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Please select a subscription first');
                } else {
                    alert('Please select a subscription first');
                }
                return;
            }

            document.getElementById('final_subscription_id').value = selectedSubscription.value;
            
            document.getElementById('step2-subscription').classList.add('hidden');
            document.getElementById('step1-membership').classList.remove('hidden');
            document.getElementById('renewalModalTitle').textContent = 'Choose Membership Plan';
            document.getElementById('renewalModalSubtitle').textContent = 'Complete your subscription';
        }

        function backToMembershipStep() {
            document.getElementById('step1-membership').classList.remove('hidden');
            document.getElementById('step2-subscription').classList.add('hidden');
            document.getElementById('renewalModalTitle').textContent = 'Renew Membership';
            document.getElementById('renewalModalSubtitle').textContent = 'Select a plan to continue';
        }

    function backToSubscriptionStep() {
        document.getElementById('step2-subscription').classList.remove('hidden');
        document.getElementById('step1-membership').classList.add('hidden');

        document.getElementById('renewalModalTitle').textContent = 'Subscribe';
        document.getElementById('renewalModalSubtitle').textContent = 'Select a subscription to continue';

        document.getElementById('subscription-submit-btn').style.display = 'block';
        document.getElementById('subscription-next-btn').style.display = 'none';
    }


        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeRenewalModal();
            }
        });

        document.getElementById('renewalForm').addEventListener('submit', function(e) {
            const planId = document.getElementById('final_plan_id').value;
            const subscriptionId = document.getElementById('final_subscription_id').value;
            
            if (!planId) {
                e.preventDefault();
                if (typeof toastr !== 'undefined') {
                    toastr.error('Please select a membership plan');
                } else {
                    alert('Please select a membership plan');
                }
                return false;
            }
            
            if (!subscriptionId) {
                e.preventDefault();
                if (typeof toastr !== 'undefined') {
                    toastr.error('Please select a subscription');
                } else {
                    alert('Please select a subscription');
                }
                return false;
            }
            
            const selectedPlan = document.querySelector('input[name="selected_plan_id"]:checked');
            const selectedSubscription = document.querySelector('input[name="selected_subscription_id"]:checked');
            
            if (selectedPlan) {
                document.getElementById('final_plan_id').value = selectedPlan.value;
            }
            if (selectedSubscription) {
                document.getElementById('final_subscription_id').value = selectedSubscription.value;
            }
        });
    </script>
</body>

@endsection