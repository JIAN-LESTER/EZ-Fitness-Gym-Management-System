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
use App\Models\Logs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class MemberProfileController extends Controller
{
    public function dashboard()
    {
        $member = Auth::user();
        $plans = MembershipPlan::all();
        $memberProfile = MemberProfile::where('user_id', $member->user_id)->first();

        return view('member.dashboard', compact('member', 'plans', 'memberProfile'));
    }

    public function editProfile(string $memberId)
    {
        $member = User::findOrFail($memberId);
        $memberProfile = MemberProfile::where('user_id', $member->user_id)->first();

        return view('profile.edit_profile', compact('member', 'memberProfile'));
    }

    /**
     * Complete member profile for the FIRST TIME (inactive -> waiting for approval)
     * This creates the profile and waits for admin approval
     */
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

        // Fill member profile data
        $memberProfile->fill([
            'plan_id' => $plan->plan_id,
            'sex' => $validated['sex'],
            'birthday' => $validated['birthday'],
            'height' => $validated['height'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'mobile_number' => $validated['mobile_number'],
            'status' => 'inactive', // Keep inactive until approved
            'isApproved' => false, // Requires admin approval
            'isDisabled' => false,
            'start_date' => null, // Will be set on approval
            'end_date' => null, // Will be set on approval
            'renewal_pending' => false,
        ]);

        $memberProfile->save();

        // Refresh the member relationship
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

    public function checkProfileCompletion(string $memberId)
    {
        $member = User::findOrFail($memberId);
        $memberProfile = MemberProfile::where('user_id', $member->user_id)->first();

        if (!$memberProfile) {
            return redirect()->back()->with('showProfileModal', true);
        }

        return null;
    }

    /**
     * Check approval status via AJAX
     */
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
            'message' => 'Your membership application was not approved. Please contact support.'
        ]);
    }

    if ($member->isApproved && $member->status === 'active') {
        // Force refresh from database to get latest QR code
        $member->refresh();
        
        $qrCodeUrl = null;
        
        if ($member->qr_code) {
            // Check if QR code file actually exists
            $fullPath = storage_path("app/public/{$member->qr_code}");
            
            if (file_exists($fullPath)) {
                $qrCodeUrl = asset("storage/{$member->qr_code}");
                \Log::info("QR code found for user", [
                    'user_id' => $user->user_id,
                    'qr_path' => $member->qr_code,
                    'url' => $qrCodeUrl
                ]);
            } else {
                \Log::warning("QR code file not found", [
                    'user_id' => $user->user_id,
                    'expected_path' => $fullPath
                ]);
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

    public function requestRenewal(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        if ($request->action === 'skip') {
            $member->update(['renewal_pending' => false]);
            return redirect()->back()->with('info', 'Renewal skipped. You can renew later from your profile.');
        }

        $validated = $request->validate([
            'plan_id' => 'required|exists:membership_plans,plan_id',
        ]);

        $member->update([
            'plan_id' => $validated['plan_id'],
            'isApproved' => false, // Requires admin approval
            'isDisabled' => false,
            'renewal_pending' => true,
        ]);

        Logs::create([
            'user_id' => $user->user_id,
            'action' => "{$user->first_name} {$user->last_name} requested membership renewal.",
            'timestamp' => now(),
        ]);

        return redirect()->route('member.dashboard')->with('success', 'Renewal request submitted! Awaiting admin approval.');
    }
}