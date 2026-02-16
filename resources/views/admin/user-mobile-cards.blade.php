@forelse($users as $user)
    @if($isStaff && $user->role !== 'member')
        @continue
    @elseif($isAdmin && $user->role === 'super_admin')
        @continue
    @endif
    
    @php $visibleRowCount++; @endphp

    @php
        $isOwnAccount = ($user->user_id == $currentAuthId);
        $needsApproval = $user->member && (
            $user->member->needsApproval()
            || (!$user->member->isApproved && !$user->member->isDisabled && !$user->member->isDisabledForSubscription)
        );
        $isDenied = $user->member && ($user->member->isDisabled || $user->member->isDisabledForSubscription);
        $isSuspended = $user->member && $user->member->subscription_status === 'suspended';
        $isCancelled = $user->member && ($user->member->subscription_status === 'cancelled' || $user->member->status === 'cancelled');

        $planExpired = false;
        $subExpired  = false;
        $planDaysAgo = 0;
        $subDaysAgo  = 0;

        if ($user->role === 'member' && $user->member
            && $user->member->isApproved && $user->member->isApprovedForSubscription
            && !$isCancelled && !$isSuspended) {
            if ($user->member->end_date && now()->gt($user->member->end_date)) {
                $planExpired = true;
                $planDaysAgo = (int) now()->diffInDays($user->member->end_date);
            }
            if ($user->member->end_date_for_subscription && now()->gt($user->member->end_date_for_subscription)) {
                $subExpired = true;
                $subDaysAgo = (int) now()->diffInDays($user->member->end_date_for_subscription);
            }
        }

        $bothExpired = $planExpired && $subExpired;

        $displayStatus = $user->status;
        if ($user->role === 'member' && $user->member) {
            if ($isCancelled) {
                $displayStatus = 'cancelled';
            } elseif ($isSuspended) {
                $displayStatus = 'suspended';
            } elseif ($user->member->isApproved && $user->member->isApprovedForSubscription) {
                if ($bothExpired) {
                    $displayStatus = 'both_expired';
                } elseif ($planExpired) {
                    $displayStatus = 'plan_expired';
                } elseif ($subExpired) {
                    $displayStatus = 'sub_expired';
                } else {
                    $displayStatus = 'active';
                }
            } else {
                $displayStatus = 'inactive';
            }
        }

        $canDelete = false;
        if (!$isOwnAccount && $user->role !== 'super_admin') {
            if ($isSuperAdmin) {
                $canDelete = true;
            } elseif ($isAdmin) {
                $canDelete = in_array($user->role, ['member', 'staff']);
            } elseif ($isStaff) {
                $canDelete = $user->role === 'member';
            }
        }
    @endphp

    <div class="user-mobile-card" onclick="showUser('{{ $user->user_id }}')">
        <!-- Header with avatar and name -->
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3 flex-1 min-w-0">
                <div class="w-12 h-12 rounded-full flex-shrink-0 {{ $isOwnAccount ? 'bg-blue-200' : ($needsApproval ? 'bg-yellow-200' : ($isDenied ? 'bg-red-200' : 'bg-gray-200')) }} flex items-center justify-center text-gray-800 font-bold text-sm shadow-md">
                    {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate text-base">
                        {{ $user->first_name }} {{ $user->last_name }}
                        @if($isOwnAccount)
                            <span class="text-xs text-blue-600 font-medium">(You)</span>
                        @endif
                    </p>
                    <p class="text-gray-500 text-sm truncate">@{{ $user->username }}</p>
                    <p class="text-gray-500 text-xs truncate mt-0.5">{{ $user->email }}</p>
                </div>
            </div>
            
            @if(!$needsApproval && !$isDenied)
                <button onclick="event.stopPropagation(); toggleActionsMenu(event, '{{ $user->user_id }}')"
                    class="flex-shrink-0 text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors ml-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                    </svg>
                </button>

                <!-- Actions Menu (mobile optimized) -->
                <div id="actionsMenu-{{ $user->user_id }}"
                    class="hidden fixed bg-white rounded-xl shadow-2xl border border-gray-200 z-[9999] overflow-hidden">
                    <div class="py-2">
                        <button onclick="event.stopPropagation(); editUser('{{ $user->user_id }}')"
                            class="w-full text-left px-5 py-3 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.586-6.586a2 2 0 112.828 2.828L11.828 13.828a2 2 0 01-1.414.586H9v-2a2 2 0 01.586-1.414z" />
                            </svg>
                            <span>Edit User</span>
                        </button>

                        @if($user->role === 'member' && $user->member)
                            @php
                                $isActive = $user->member->isApproved
                                    && $user->member->isApprovedForSubscription
                                    && in_array($user->member->subscription_status, ['active', 'subscribed']);
                            @endphp

                            @if($isSuspended)
                                <button onclick="event.stopPropagation(); resumeMember('{{ $user->member->member_id }}')"
                                    class="w-full text-left px-5 py-3 text-sm text-green-600 hover:bg-gray-100 flex items-center gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Resume</span>
                                </button>
                            @elseif($isActive)
                                <button onclick="event.stopPropagation(); suspendMember('{{ $user->member->member_id }}')"
                                    class="w-full text-left px-5 py-3 text-sm text-orange-600 hover:bg-gray-100 flex items-center gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Suspend</span>
                                </button>

                                <button onclick="event.stopPropagation(); cancelPlan('{{ $user->member->member_id }}')"
                                    class="w-full text-left px-5 py-3 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <span>Cancel Plan</span>
                                </button>
                            @endif
                        @endif

                        @if($canDelete)
                            <button onclick="event.stopPropagation(); openDeleteModal('{{ route('admin.users-destroy', $user->user_id) }}', '{{ $user->first_name }} {{ $user->last_name }}')"
                                class="w-full text-left px-5 py-3 text-sm text-red-600 hover:bg-gray-100 flex items-center gap-3 border-t border-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3" />
                                </svg>
                                <span>Delete User</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Role and Status Badges -->
        <div class="flex flex-wrap items-center gap-2 mb-3">
            <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                {{ $user->role === 'super_admin' ? 'bg-red-100 text-red-700' :
                    ($user->role === 'admin' ? 'bg-orange-100 text-orange-700' :
                        ($user->role === 'staff' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700')) }}">
                {{ ucfirst(str_replace('_', ' ', $user->role)) }}
            </span>

            @if($needsApproval)
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Pending Approval</span>
            @elseif($isDenied)
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Denied</span>
            @elseif($displayStatus === 'active')
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Active</span>
            @elseif($displayStatus === 'suspended')
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-700">Suspended</span>
            @elseif($displayStatus === 'cancelled')
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">Cancelled</span>
            @elseif(in_array($displayStatus, ['both_expired', 'plan_expired']))
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Expired</span>
            @elseif($displayStatus === 'sub_expired')
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Sub Expired</span>
            @else
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-600">{{ ucfirst($displayStatus) }}</span>
            @endif
        </div>

        <!-- Plan Info -->
        @if($user->role === 'member' && $user->member && !$isDenied)
            <div class="bg-gray-50 rounded-lg p-3 mb-3">
                <p class="text-xs text-gray-500 mb-1">Membership Plan</p>
                @if($user->member->plan)
                    <p class="font-semibold text-gray-900 text-sm">{{ $user->member->plan->name }}</p>
                    <p class="text-xs text-gray-600 mt-0.5">₱{{ number_format($user->member->plan->price ?? 0, 2) }}</p>
                    @if($user->member->subscription)
                        <p class="text-xs text-blue-600 mt-1">+ {{ $user->member->subscription->name }}</p>
                    @endif
                @else
                    <p class="text-sm text-gray-500">No plan assigned</p>
                @endif
            </div>
        @endif

        <!-- Date Created -->
        <div class="text-xs text-gray-500">
            Joined {{ $user->created_at->format('M d, Y') }}
        </div>

        <!-- Approval Buttons for Pending Users -->
        @if($needsApproval)
            <div class="flex gap-2 mt-3 pt-3 border-t border-gray-200" onclick="event.stopPropagation()">
                <button onclick="approveSubscription('{{ $user->member->member_id }}')"
                    class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2.5 bg-green-500 text-white rounded-lg hover:bg-green-600 font-semibold text-sm transition-all duration-200 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Approve</span>
                </button>

                <button onclick="denyMember('{{ $user->member->member_id }}')"
                    class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 font-semibold text-sm transition-all duration-200 shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Deny</span>
                </button>
            </div>
        @endif
    </div>
@empty
    <div class="text-center py-12">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-lg font-medium text-gray-500">No members found</p>
        <p class="text-sm mt-1 text-gray-400">Try adjusting your search or filter criteria</p>
    </div>
@endforelse

@if(($isStaff || $isAdmin) && $visibleRowCount === 0 && $users->total() > 0)
    <div class="text-center py-12">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <p class="text-lg font-medium text-gray-500">No members found</p>
        <p class="text-sm mt-1 text-gray-400">Try adjusting your search or filter criteria</p>
    </div>
@endif
