<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;

use App\Mail\MemberQRCodeMail;
use App\Models\User;
use App\Models\MemberProfile;
use App\Models\MembershipPlan;
use App\Models\Logs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
     * Complete member profile for the FIRST TIME (inactive -> active)
     * This generates QR code and sends email
     */
    public function completeMemberProfile(Request $request)
{
    $member = Auth::user();

    $validated = $request->validate([
        'plan_id' => 'required|exists:membership_plans,plan_id',
        'sex' => 'required|in:male,female',
        'birthday' => 'required|date',
        'height' => 'required|numeric|min:0',
        'weight' => 'required|numeric|min:0',
        'mobile_number' => 'required|string|max:15',
    ]);

    $memberProfile = MemberProfile::firstOrNew(['user_id' => $member->user_id]);
    $plan = MembershipPlan::find($validated['plan_id']);

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
    ]);

    $memberProfile->save();

    Logs::create([
        'user_id' => $member->user_id,
        'action' => "Completed membership profile - Awaiting approval: {$member->first_name} {$member->last_name}",
        'timestamp' => now(),
    ]);

    return redirect()
        ->route('member.dashboard', $member->user_id)
        ->with('success', 'Profile completed! Awaiting admin approval.');
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
