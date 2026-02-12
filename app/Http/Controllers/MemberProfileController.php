<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Logs;
use App\Models\MemberProfile;
use App\Models\MembershipPlan;
use App\Models\Subscriptions;
use App\Services\CacheService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MemberProfileController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // Dashboard
    // ─────────────────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $user = Auth::user();

        // Always fresh – never cache critical membership state
        $memberProfile = MemberProfile::select([
                'member_id', 'user_id', 'plan_id', 'subscription_id',
                'start_date', 'end_date',
                'start_date_for_subscription', 'end_date_for_subscription',
                'subscription_status', 'status', 'qr_code',
                'isApproved', 'isApprovedForSubscription',
                'isDisabled', 'isDisabledForSubscription',
                'renewal_pending', 'suspended_at',
                'days_remaining_before_suspend',
                'plan_days_remaining_before_suspend',
            ])
            ->with([
                'plan:plan_id,name,price,duration_days',
                'subscription:subscription_id,name,price,duration_days',
                'user:user_id,first_name,last_name,email,branch_id',
            ])
            ->where('user_id', $user->user_id)
            ->first();

        if ($memberProfile) {
            \Log::info('Dashboard loaded member profile', [
                'member_id'                          => $memberProfile->member_id,
                'status'                             => $memberProfile->status,
                'subscription_status'                => $memberProfile->subscription_status,
                'plan_days_remaining_before_suspend' => $memberProfile->plan_days_remaining_before_suspend,
                'days_remaining_before_suspend'      => $memberProfile->days_remaining_before_suspend,
                'end_date'                           => $memberProfile->end_date,
                'end_date_for_subscription'          => $memberProfile->end_date_for_subscription,
            ]);
        }

        // Current gym occupancy – short-lived cache
        $currentOccupancy = CacheService::remember(
            'gym_occupancy',
            'realtime',
            fn () => Attendance::whereDate('check_in_time', Carbon::today())
                ->where('status', 'checked_in')
                ->when($user->branch_id, fn ($q) =>
                    $q->whereHas('member.user', fn ($q2) =>
                        $q2->where('branch_id', $user->branch_id)
                    )
                )
                ->count(),
            $user->branch_id
        );

        // Subscription days left + derived status
        $daysLeft         = null;
        $membershipStatus = null;
        $isExpiringSoon   = false;

        if ($memberProfile && $memberProfile->end_date_for_subscription) {
            $daysLeft = (int) ceil(
                Carbon::now()->diffInDays(
                    Carbon::parse($memberProfile->end_date_for_subscription),
                    false
                )
            );

            if ($daysLeft < 0) {
                $membershipStatus = 'expired';
                if ($memberProfile->subscription_status !== 'expired') {
                    $memberProfile->update(['subscription_status' => 'expired']);
                    CacheService::forgetPattern('member_profile');
                }
            } elseif ($daysLeft <= 7) {
                $membershipStatus = 'expiring_soon';
                $isExpiringSoon   = true;
            } else {
                $membershipStatus = 'active';
            }
        } elseif ($memberProfile) {
            $membershipStatus = $memberProfile->subscription_status;
        }

        // Plans – branch-scoped, cached per branch
        $plans = CacheService::remember(
            'membership_plans',
            'daily',
            fn () => MembershipPlan::select('plan_id', 'name', 'price', 'duration_days', 'details', 'branch_id')
                ->where('branch_id', $user->branch_id)
                ->orderBy('price')
                ->get(),
            $user->branch_id
        );

        // Subscriptions – branch-scoped, cached per branch
        $subscriptions = CacheService::remember(
            'available_subscriptions',
            'daily',
            fn () => Subscriptions::select('subscription_id', 'name', 'price', 'duration_days', 'branch_id')
                ->where('branch_id', $user->branch_id)
                ->orderBy('price')
                ->get(),
            $user->branch_id
        );

        // Recent attendance
        $recentAttendance = CacheService::remember(
            'recent_attendance',
            'stats',
            fn () => Attendance::select('attendance_id', 'member_id', 'check_in_time', 'check_out_time', 'status')
                ->where('member_id', $memberProfile?->member_id)
                ->orderBy('check_in_time', 'desc')
                ->limit(10)
                ->get(),
            $memberProfile?->member_id
        );

        // Attendance counts
        $attendanceCounts = CacheService::remember(
            'attendance_counts',
            'stats',
            fn () => Attendance::selectRaw(
                    'COUNT(*) as total,
                     SUM(CASE WHEN MONTH(check_in_time) = ? AND YEAR(check_in_time) = ? THEN 1 ELSE 0 END) as this_month',
                    [Carbon::now()->month, Carbon::now()->year]
                )
                ->where('member_id', $memberProfile?->member_id)
                ->first(),
            $memberProfile?->member_id
        );

        return view('member.dashboard', [
            'member'            => $user,
            'user'              => $user,
            'plans'             => $plans,
            'memberProfile'     => $memberProfile,
            'daysRemaining'     => $daysLeft,
            'daysLeft'          => $daysLeft,
            'currentOccupancy'  => $currentOccupancy,
            'membershipStatus'  => $membershipStatus,
            'isExpiringSoon'    => $isExpiringSoon,
            'subscriptions'     => $subscriptions,
            'recentAttendance'  => $recentAttendance,
            'totalCheckIns'     => $attendanceCounts->total ?? 0,
            'thisMonthCheckIns' => $attendanceCounts->this_month ?? 0,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Complete member profile
    // ─────────────────────────────────────────────────────────────────────────

    public function completeMemberProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,branch_id'],

            'sex' => ['required', Rule::in(['male', 'female'])],

            // Real date, not future, member must be at least 13 years old
            'birthday' => [
                'required',
                'date',
                'before_or_equal:today',
                'before:' . now()->subYears(13)->toDateString(),
            ],

            // Sensible physical bounds (cm / kg)
            'height' => ['nullable', 'numeric', 'min:50', 'max:300'],
            'weight' => ['nullable', 'numeric', 'min:10', 'max:500'],

            // Philippine mobile: 09XXXXXXXXX or +639XXXXXXXXX
            'mobile_number' => ['required', 'string', 'regex:/^(\+?63|0)9\d{9}$/'],
        ], [
            'birthday.before'          => 'You must be at least 13 years old to register.',
            'birthday.before_or_equal' => 'Birthday cannot be in the future.',
            'mobile_number.regex'      => 'Enter a valid Philippine mobile number (e.g. 09171234567).',
            'height.min'               => 'Height must be at least 50 cm.',
            'height.max'               => 'Height cannot exceed 300 cm.',
            'weight.min'               => 'Weight must be at least 10 kg.',
            'weight.max'               => 'Weight cannot exceed 500 kg.',
        ]);

        $memberProfile = MemberProfile::firstOrNew(['user_id' => $user->user_id]);

        $memberProfile->fill([
            'sex'                       => $validated['sex'],
            'birthday'                  => $validated['birthday'],
            'height'                    => $validated['height'] ?? null,
            'weight'                    => $validated['weight'] ?? null,
            'mobile_number'             => $validated['mobile_number'],
            'status'                    => 'inactive',
            'subscription_status'       => 'pending_selection',
            'isApproved'                => false,
            'isApprovedForSubscription' => false,
            'isDisabled'                => false,
            'isDisabledForSubscription' => false,
            'plan_id'                   => null,
            'subscription_id'           => null,
        ]);

        $user->update(['branch_id' => $validated['branch_id']]);
        $memberProfile->save();
        $user->load('member');

        CacheService::forgetPattern('member_profile');

        Logs::create([
            'user_id'   => $user->user_id,
            'branch_id' => $validated['branch_id'],
            'action'    => "Profile completed: {$user->first_name} {$user->last_name}",
            'timestamp' => now(),
        ]);

        return redirect()
            ->route('member.dashboard')
            ->with('success', 'Profile completed! Please select a membership plan.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Select plan
    // ─────────────────────────────────────────────────────────────────────────

    public function selectPlan(Request $request)
    {
        $user   = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()->back()->with('error', 'Please complete your profile first.');
        }

        $validated = $request->validate([
            // plan_id must exist AND belong to the authenticated user's branch
            'plan_id' => [
                'required',
                Rule::exists('membership_plans', 'plan_id')
                    ->where('branch_id', $user->branch_id),
            ],
        ], [
            'plan_id.required' => 'Please select a membership plan.',
            'plan_id.exists'   => 'The selected plan is not available for your branch.',
        ]);

        $plan = MembershipPlan::findOrFail($validated['plan_id']);

        $member->update([
            'plan_id'             => $plan->plan_id,
            'subscription_status' => 'pending_selection',
            'start_date'          => now(),
            'end_date'            => now()->addDays($plan->duration_days),
        ]);

        CacheService::forgetPattern('member_profile');

        Logs::create([
            'user_id'   => $user->user_id,
            'branch_id' => $user->branch_id,
            'action'    => "Selected plan: {$plan->name}",
            'timestamp' => now(),
        ]);

        return redirect()->route('member.dashboard')
            ->with('success', 'Plan selected! Please choose a subscription.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Select subscription
    // ─────────────────────────────────────────────────────────────────────────

    public function selectSubscription(Request $request)
    {
        $user   = Auth::user();
        $member = $user->member;

        if (!$member || !$member->plan_id) {
            return redirect()->back()->with('error', 'Please select a membership plan first.');
        }

        $validated = $request->validate([
            // subscription_id must exist AND belong to the authenticated user's branch
            'subscription_id' => [
                'required',
                Rule::exists('subscriptions', 'subscription_id')
                    ->where('branch_id', $user->branch_id),
            ],
        ], [
            'subscription_id.required' => 'Please select a subscription.',
            'subscription_id.exists'   => 'The selected subscription is not available for your branch.',
        ]);

        $subscription = Subscriptions::findOrFail($validated['subscription_id']);

        $member->update([
            'subscription_id'           => $subscription->subscription_id,
            'subscription_status'       => 'pending_subscription_approval',
            'isApprovedForSubscription' => false,
        ]);

        CacheService::forgetPattern('member_profile');

        Logs::create([
            'user_id'   => $user->user_id,
            'branch_id' => $user->branch_id,
            'action'    => "Selected subscription: {$subscription->name} – awaiting approval",
            'timestamp' => now(),
        ]);

        return redirect()->route('member.dashboard')
            ->with('success', 'Subscription selected! Awaiting admin approval.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Check approval status (AJAX polling)
    // ─────────────────────────────────────────────────────────────────────────

    public function checkApprovalStatus()
    {
        $user   = Auth::user();
        $member = MemberProfile::where('user_id', $user->user_id)->first();

        if (!$member) {
            return response()->json(['status' => 'no_profile', 'message' => 'No member profile found.']);
        }

        if ($member->isDisabled || $member->isDisabledForSubscription) {
            return response()->json(['status' => 'rejected', 'message' => 'Your membership application was not approved.']);
        }

        if ($member->isApproved && $member->isApprovedForSubscription && $member->subscription_status === 'active') {
            $member    = $member->fresh(['plan', 'subscription']);
            $qrCodeUrl = null;

            CacheService::forgetPattern('member_profile');

            if ($member->qr_code) {
                $fullPath = storage_path("app/public/{$member->qr_code}");
                if (file_exists($fullPath)) {
                    $qrCodeUrl = asset("storage/{$member->qr_code}") . '?v=' . time();
                } else {
                    \Log::warning("QR code missing for member {$member->member_id}", ['path' => $fullPath]);
                }
            }

            return response()->json([
                'status'      => 'approved',
                'message'     => 'Your membership has been fully approved!',
                'qr_code_url' => $qrCodeUrl,
                'member_data' => [
                    'plan'               => $member->plan->name ?? 'N/A',
                    'plan_price'         => $member->plan ? '₱' . number_format($member->plan->price, 2) : 'N/A',
                    'subscription'       => $member->subscription->name ?? 'N/A',
                    'subscription_price' => $member->subscription ? '₱' . number_format($member->subscription->price, 2) : 'N/A',
                    'start_date'         => $member->start_date_for_subscription
                        ? Carbon::parse($member->start_date_for_subscription)->format('M d, Y') : 'N/A',
                    'end_date'           => $member->end_date_for_subscription
                        ? Carbon::parse($member->end_date_for_subscription)->format('M d, Y') : 'N/A',
                    'status'             => $member->subscription_status,
                ],
            ]);
        }

        return response()->json([
            'status'       => 'pending',
            'message'      => 'Your application is still pending approval.',
            'pending_step' => !$member->isApproved ? 'profile' : 'subscription',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Request renewal
    // ─────────────────────────────────────────────────────────────────────────

    public function requestRenewal(Request $request)
    {
        $user   = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()->back()->with('error', 'No membership profile found.');
        }

        // Logout shortcut (preserve existing behaviour)
        if ($request->input('action') === 'logout') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('info', 'You have been logged out.');
        }

        // Block duplicate pending requests before any validation runs
        if ($member->renewal_pending) {
            return redirect()->route('member.dashboard')
                ->with('error', 'You already have a pending renewal request. Please wait for admin approval.');
        }

        $validated = $request->validate([
            // Both IDs must belong to the user's own branch
            'plan_id' => [
                'required',
                Rule::exists('membership_plans', 'plan_id')
                    ->where('branch_id', $user->branch_id),
            ],
            'subscription_id' => [
                'required',
                Rule::exists('subscriptions', 'subscription_id')
                    ->where('branch_id', $user->branch_id),
            ],
        ], [
            'plan_id.required'         => 'Please select a membership plan.',
            'plan_id.exists'           => 'The selected plan is not available for your branch.',
            'subscription_id.required' => 'Please select a subscription.',
            'subscription_id.exists'   => 'The selected subscription is not available for your branch.',
        ]);

        $plan         = MembershipPlan::findOrFail($validated['plan_id']);
        $subscription = Subscriptions::findOrFail($validated['subscription_id']);

        $member->update([
            'plan_id'                   => $plan->plan_id,
            'subscription_id'           => $subscription->subscription_id,
            'isApproved'                => false,
            'isApprovedForSubscription' => false,
            'isDisabledForSubscription' => false,
            'renewal_pending'           => true,
            'subscription_status'       => 'pending_subscription_approval',
            'status'                    => 'pending_approval',
        ]);

        CacheService::forgetPattern('member_profile');

        Logs::create([
            'user_id'   => $user->user_id,
            'branch_id' => $user->branch_id,
            'action'    => "Renewal requested – Plan: {$plan->name}, Subscription: {$subscription->name}",
            'timestamp' => now(),
        ]);

        return redirect()->route('member.dashboard')
            ->with('success', 'Renewal request submitted! Awaiting admin approval.');
    }
}