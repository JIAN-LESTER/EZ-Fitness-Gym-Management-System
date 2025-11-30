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
use App\Models\Attendance;
use App\Models\Logs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class MemberProfileController extends Controller
{
    public function dashboard()
{
    $user = Auth::user();
    $plans = MembershipPlan::all();
    
    // Get user's member profile
    $memberProfile = MemberProfile::with('plan', 'user')
        ->where('user_id', $user->user_id)
        ->first();

    // Get current gym occupancy
    $currentOccupancy = Attendance::whereDate('check_in_time', Carbon::today())
        ->where('status', 'checked_in')
        ->count();

    // Calculate days left on membership (if exists)
    $daysLeft = null;
    $membershipStatus = null;
    $isExpiringSoon = false;
    
    if ($memberProfile && $memberProfile->start_date && $memberProfile->end_date) {
        $now = Carbon::now();
        $endDate = Carbon::parse($memberProfile->end_date);
        
        // Calculate days remaining
        $daysLeft = $now->diffInDays($endDate, false);
        $daysLeft = (int) ceil($daysLeft);
        
        // Determine membership status
        if ($daysLeft < 0) {
            $membershipStatus = 'expired';
            // Auto-update status if needed
            if ($memberProfile->status === 'active') {
                $memberProfile->update(['status' => 'expired']);
            }
        } elseif ($daysLeft <= 7) {
            $membershipStatus = 'expiring_soon';
            $isExpiringSoon = true;
        } else {
            $membershipStatus = 'active';
        }
    } elseif ($memberProfile) {
        // Has profile but no dates set (pending approval)
        $membershipStatus = $memberProfile->status;
        $daysLeft = null;
    }

    // Get all available membership plans
    $membershipPlans = MembershipPlan::orderBy('price', 'asc')->get();

    // Get user's attendance history (last 10 check-ins)
    $recentAttendance = Attendance::where('member_id', $memberProfile?->member_id)
        ->orderBy('check_in_time', 'desc')
        ->limit(10)
        ->get();

    // Calculate attendance statistics
    $totalCheckIns = Attendance::where('member_id', $memberProfile?->member_id)->count();
    $thisMonthCheckIns = Attendance::where('member_id', $memberProfile?->member_id)
        ->whereMonth('check_in_time', Carbon::now()->month)
        ->whereYear('check_in_time', Carbon::now()->year)
        ->count();

    // Legacy variables for backward compatibility
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
        'membershipPlans',
        'recentAttendance',
        'totalCheckIns',
        'thisMonthCheckIns'
    ));
}

    public function completeMemberProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'plan_id' => 'required|exists:membership_plans,plan_id',
            'sex' => 'required|in:male,female',
            'birthday' => 'required|date',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'mobile_number' => 'required|string|max:15',
        ]);

        $memberProfile = MemberProfile::firstOrNew(['user_id' => $user->user_id]);
        $plan = MembershipPlan::find($validated['plan_id']);

        $memberProfile->fill([
            'plan_id' => $plan->plan_id,
            'sex' => $validated['sex'],
            'birthday' => $validated['birthday'],
            'height' => $validated['height'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'mobile_number' => $validated['mobile_number'],
            'status' => 'inactive',
            'isApproved' => false,
            'isDisabled' => false,
            'start_date' => null,
            'end_date' => null,
            'renewal_pending' => false,
        ]);

        $memberProfile->save();
        $user->load('member');

        Logs::create([
            'user_id' => $user->user_id,
            'action' => "Completed membership profile - Awaiting approval: {$user->first_name} {$user->last_name} - Plan: {$plan->name}",
            'timestamp' => now(),
        ]);

        return redirect()
            ->route('member.dashboard')
            ->with('success', 'Profile submitted successfully! Awaiting admin approval.');
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
            'plan_id' => 'required|exists:membership_plans,plan_id',
        ]);

        $plan = MembershipPlan::find($validated['plan_id']);

        $member->update([
            'plan_id' => $validated['plan_id'],
            'isApproved' => false,
            'isDisabled' => false,
            'renewal_pending' => true,
            'status' => 'expired', // Keep expired until approved
        ]);

        Logs::create([
            'user_id' => $user->user_id,
            'action' => "Requested membership renewal - Plan: {$plan->name}",
            'timestamp' => now(),
        ]);

        return redirect()->route('member.dashboard')
            ->with('success', 'Renewal request submitted! Awaiting admin approval.');
    }

    public function checkApprovalStatus()
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return response()->json([
                'status' => 'no_profile',
                'message' => 'No member profile found'
            ]);
        }

        if ($member->isDisabled) {
            return response()->json([
                'status' => 'rejected',
                'message' => 'Your membership application was not approved.'
            ]);
        }

        if ($member->isApproved && $member->status === 'active') {
            $member->refresh();
            
            $qrCodeUrl = null;
            if ($member->qr_code) {
                $fullPath = storage_path("app/public/{$member->qr_code}");
                if (file_exists($fullPath)) {
                    $qrCodeUrl = asset("storage/{$member->qr_code}");
                }
            }

            return response()->json([
                'status' => 'approved',
                'message' => 'Your membership has been approved!',
                'qr_code_url' => $qrCodeUrl,
                'member_data' => [
                    'plan' => $member->plan->name ?? 'N/A',
                    'start_date' => $member->start_date,
                    'end_date' => $member->end_date,
                ]
            ]);
        }

        return response()->json([
            'status' => 'pending',
            'message' => 'Your application is still pending approval'
        ]);
    }
}