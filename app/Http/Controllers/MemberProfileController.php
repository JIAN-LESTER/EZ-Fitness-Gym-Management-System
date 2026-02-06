<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use App\Mail\MemberQRCodeMail;
use App\Models\User;
use App\Models\MemberProfile;
use App\Models\MembershipPlan;
use App\Models\Subscriptions;
use App\Models\Attendance;
use App\Models\Logs;
use App\Services\CacheService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class MemberProfileController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get user's member profile with optimized relations (cached)
        $memberProfile = CacheService::remember(
            'member_profile',
            'stats',
            fn() => MemberProfile::select([
                    'member_id', 'user_id', 'plan_id', 'subscription_id', 
                    'start_date_for_subscription', 'end_date_for_subscription',
                    'subscription_status', 'qr_code'
                ])
                ->with([
                    'plan:plan_id,name,price,duration_days',
                    'subscription:subscription_id,name,price,duration_days',
                    'user:user_id,first_name,last_name,email,branch_id'
                ])
                ->where('user_id', $user->user_id)
                ->first(),
            $user->user_id
        );

        // Optimized current gym occupancy (short cache for real-time feel)
        $currentOccupancy = CacheService::remember(
            'gym_occupancy',
            'realtime',
            fn() => Attendance::whereDate('check_in_time', Carbon::today())
                ->where('status', 'checked_in')
                ->when($user->branch_id, function ($query) use ($user) {
                    return $query->whereHas('member.user', function ($q) use ($user) {
                        $q->where('branch_id', $user->branch_id);
                    });
                })
                ->count(),
            $user->branch_id
        );

        // Calculate days left on membership
        $daysLeft = null;
        $membershipStatus = null;
        $isExpiringSoon = false;
        
        if ($memberProfile && $memberProfile->start_date_for_subscription && $memberProfile->end_date_for_subscription) {
            $now = Carbon::now();
            $endDate = Carbon::parse($memberProfile->end_date_for_subscription);
            
            $daysLeft = $now->diffInDays($endDate, false);
            $daysLeft = (int) ceil($daysLeft);
            
            if ($daysLeft < 0) {
                $membershipStatus = 'expired';
                if ($memberProfile->subscription_status === 'active') {
                    $memberProfile->update(['subscription_status' => 'expired']);
                    CacheService::forgetPattern('member_profile');
                }
            } elseif ($daysLeft <= 7) {
                $membershipStatus = 'expiring_soon';
                $isExpiringSoon = true;
            } else {
                $membershipStatus = 'active';
            }
        } elseif ($memberProfile) {
            $membershipStatus = $memberProfile->subscription_status;
            $daysLeft = null;
        }

        // Get available subscriptions (cached daily)
        $subscriptions = CacheService::remember(
            'available_subscriptions',
            'daily',
            fn() => Subscriptions::select('subscription_id', 'name', 'price', 'duration_days', 'branch_id')
                ->where('branch_id', $user->branch_id)
                ->orderBy('price', 'asc')
                ->get(),
            $user->branch_id
        );

        // Get user's attendance history (cached with short TTL)
        $recentAttendance = CacheService::remember(
            'recent_attendance',
            'stats',
            fn() => Attendance::select('attendance_id', 'member_id', 'check_in_time', 'check_out_time', 'status')
                ->where('member_id', $memberProfile?->member_id)
                ->orderBy('check_in_time', 'desc')
                ->limit(10)
                ->get(),
            $memberProfile?->member_id
        );

        // Optimized attendance counts (cached)
        $attendanceCounts = CacheService::remember(
            'attendance_counts',
            'stats',
            fn() => Attendance::selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN MONTH(check_in_time) = ? AND YEAR(check_in_time) = ? THEN 1 ELSE 0 END) as this_month
                ', [Carbon::now()->month, Carbon::now()->year])
                ->where('member_id', $memberProfile?->member_id)
                ->first(),
            $memberProfile?->member_id
        );

        $totalCheckIns = $attendanceCounts->total ?? 0;
        $thisMonthCheckIns = $attendanceCounts->this_month ?? 0;

        // Get plans (cached daily)
        $plans = CacheService::remember(
            'membership_plans',
            'daily',
            fn() => MembershipPlan::select('plan_id', 'name', 'price', 'duration_days')->get()
        );

        $member = $user;
        $daysRemaining = $daysLeft;

        return view('member.dashboard', compact(
            'member',
            'user',
            'plans',
            'memberProfile',
            'daysRemaining',
            'daysLeft',
            'currentOccupancy',
            'membershipStatus',
            'isExpiringSoon',
            'subscriptions',
            'recentAttendance',
            'totalCheckIns',
            'thisMonthCheckIns'
        ));
    }

    public function completeMemberProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,branch_id',
            'sex' => 'required|in:male,female',
            'birthday' => 'required|date',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'mobile_number' => 'required|string|max:15',
        ]);

        $memberProfile = MemberProfile::firstOrNew(['user_id' => $user->user_id]);

        $memberProfile->fill([
            'sex' => $validated['sex'],
            'birthday' => $validated['birthday'],
            'height' => $validated['height'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'mobile_number' => $validated['mobile_number'],
            'status' => 'inactive',
            'subscription_status' => 'pending_selection',
            'isApproved' => false,
            'isApprovedForSubscription' => false,
            'isDisabled' => false,
            'isDisabledForSubscription' => false,
            'plan_id' => null,
            'subscription_id' => null,
        ]);

        // Update user's branch
        $user->update(['branch_id' => $validated['branch_id']]);

        $memberProfile->save();
        $user->load('member');

        // Clear member profile cache
        CacheService::forgetPattern('member_profile');

        Logs::create([
            'user_id' => $user->user_id,
            'branch_id' => $validated['branch_id'],
            'action' => "Completed profile - Ready to select plan: {$user->first_name} {$user->last_name}",
            'timestamp' => now(),
        ]);

        return redirect()
            ->route('member.dashboard')
            ->with('success', 'Profile completed! Please select a membership plan.');
    }

    public function selectPlan(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()->back()->with('error', 'Please complete your profile first.');
        }

        $validated = $request->validate([
            'plan_id' => 'required|exists:membership_plans,plan_id',
        ]);

        $plan = MembershipPlan::find($validated['plan_id']);

        $member->update([
            'plan_id' => $validated['plan_id'],
            'subscription_status' => 'pending_selection',
            'start_date' => now(),
            'end_date' => now()->addDays($plan->duration_days),
        ]);

        // Clear member profile cache
        CacheService::forgetPattern('member_profile');

        Logs::create([
            'user_id' => $user->user_id,
            'branch_id' => $user->branch_id,
            'action' => "Selected membership plan - {$plan->name} - Ready to select subscription",
            'timestamp' => now(),
        ]);

        return redirect()->route('member.dashboard')
            ->with('success', 'Plan selected! Please choose a subscription.');
    }

    public function selectSubscription(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member || !$member->plan_id) {
            return redirect()->back()->with('error', 'Please select a membership plan first.');
        }

        $validated = $request->validate([
            'subscription_id' => 'required|exists:subscriptions,subscription_id',
        ]);

        $subscription = Subscriptions::find($validated['subscription_id']);

        $member->update([
            'subscription_id' => $validated['subscription_id'],
            'subscription_status' => 'pending_subscription_approval',
            'isApprovedForSubscription' => false,
        ]);

        // Clear member profile cache
        CacheService::forgetPattern('member_profile');

        Logs::create([
            'user_id' => $user->user_id,
            'branch_id' => $user->branch_id,
            'action' => "Selected subscription - {$subscription->name} - Awaiting admin approval",
            'timestamp' => now(),
        ]);

        return redirect()->route('member.dashboard')
            ->with('success', 'Subscription selected! Awaiting admin approval.');
    }

    public function checkApprovalStatus()
    {
        $user = Auth::user();
        
        // Force refresh from database, bypassing cache
        $member = MemberProfile::where('user_id', $user->user_id)->first();

        if (!$member) {
            return response()->json([
                'status' => 'no_profile',
                'message' => 'No member profile found'
            ]);
        }

        // Check if rejected
        if ($member->isDisabled || $member->isDisabledForSubscription) {
            return response()->json([
                'status' => 'rejected',
                'message' => 'Your membership application was not approved.'
            ]);
        }

        // Check if both profile AND subscription are approved
        if ($member->isApproved && $member->isApprovedForSubscription && $member->subscription_status === 'active') {
            // Force refresh with relations
            $member = $member->fresh(['plan', 'subscription']);
            
            // Clear old cache and update
            CacheService::forgetPattern('member_profile');
            
            $qrCodeUrl = null;
            if ($member->qr_code) {
                $fullPath = storage_path("app/public/{$member->qr_code}");
                if (file_exists($fullPath)) {
                    $qrCodeUrl = asset("storage/{$member->qr_code}") . '?v=' . time();
                } else {
                    \Log::warning("QR code file not found for member {$member->member_id}", [
                        'expected_path' => $fullPath,
                        'qr_code_field' => $member->qr_code
                    ]);
                }
            }

            return response()->json([
                'status' => 'approved',
                'message' => 'Your membership has been fully approved!',
                'qr_code_url' => $qrCodeUrl,
                'member_data' => [
                    'plan' => $member->plan->name ?? 'N/A',
                    'plan_price' => $member->plan ? '₱' . number_format($member->plan->price, 2) : 'N/A',
                    'subscription' => $member->subscription->name ?? 'N/A',
                    'subscription_price' => $member->subscription ? '₱' . number_format($member->subscription->price, 2) : 'N/A',
                    'start_date' => $member->start_date_for_subscription ? Carbon::parse($member->start_date_for_subscription)->format('M d, Y') : 'N/A',
                    'end_date' => $member->end_date_for_subscription ? Carbon::parse($member->end_date_for_subscription)->format('M d, Y') : 'N/A',
                    'status' => $member->subscription_status,
                ]
            ]);
        }

        // Still pending
        return response()->json([
            'status' => 'pending',
            'message' => 'Your application is still pending approval',
            'pending_step' => !$member->isApproved ? 'profile' : 'subscription'
        ]);
    }

    public function requestRenewal(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()->back()->with('error', 'No membership profile found.');
        }

        if ($request->action === 'logout') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('info', 'You have been logged out.');
        }

        $validated = $request->validate([
            'subscription_id' => 'required|exists:subscriptions,subscription_id',
        ]);

        $subscription = Subscriptions::find($validated['subscription_id']);

        $member->update([
            'subscription_id' => $validated['subscription_id'],
            'isApprovedForSubscription' => false,
            'isDisabledForSubscription' => false,
            'renewal_pending' => true,
            'subscription_status' => 'expired',
        ]);

        // Clear member profile cache
        CacheService::forgetPattern('member_profile');

        Logs::create([
            'user_id' => $user->user_id,
            'branch_id' => $user->branch_id,
            'action' => "Requested membership renewal - Subscription: {$subscription->name}",
            'timestamp' => now(),
        ]);

        return redirect()->route('member.dashboard')
            ->with('success', 'Renewal request submitted! Awaiting admin approval.');
    }
}