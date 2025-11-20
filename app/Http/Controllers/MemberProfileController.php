<?php

namespace App\Http\Controllers;

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
        ], [
            'sex.required' => 'Please select you sex',
            'mobile_number.required' => 'Mobile number is required',
            'weight.required' => 'Weight is required',
            'height.required' => 'Height is required',
            'birthday.required' => 'Birthday is required',
            'plan_id.required' => 'Membership plan is required',
        ]);

        $memberProfile = MemberProfile::firstOrNew(['user_id' => $member->user_id]);
        $plan = MembershipPlan::find($validated['plan_id']);

        // Check if this is first-time completion
        $isFirstTime = !$memberProfile->exists || $memberProfile->status === 'inactive';

        $memberProfile->fill([
            'plan_id' => $plan->plan_id,
            'sex' => $validated['sex'],
            'birthday' => $validated['birthday'],
            'height' => $validated['height'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'mobile_number' => $validated['mobile_number'],
            'status' => 'active', // Set to active
            'start_date' => $memberProfile->start_date ?? now(),
        ]);

        $memberProfile->save();

        // Only generate QR and send email if first-time completion
        if ($isFirstTime) {
            $qrData = [
                'name' => "{$member->first_name} {$member->last_name}",
                'email' => $member->email,
                'plan' => $plan->name,
                'price' => $plan->price,
            ];

            $qrText = json_encode($qrData);
            $qrRelativePath = "qr/member_{$member->user_id}.png";
            Storage::disk('public')->makeDirectory('qr');

            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($qrText)
                ->encoding(new Encoding('UTF-8'))
                ->size(300)
                ->margin(10)
                ->build();

            $result->saveToFile(storage_path("app/public/{$qrRelativePath}"));

            $memberProfile->qr_code = $qrRelativePath;
            $memberProfile->save();

            $qrUrl = asset("storage/{$qrRelativePath}");

            // Send email with QR code
            Mail::to($member->email)->send(new MemberQRCodeMail($memberProfile, storage_path("app/public/{$qrRelativePath}")));


            Logs::create([
                'user_id' => $member->user_id,
                'action' => "Completed membership profile setup for user: {$member->first_name} {$member->last_name}",
                'timestamp' => now(),
            ]);

            return redirect()
                ->route('member.dashboard', $member->user_id)
                ->with([
                    'success' => 'Membership profile completed successfully! Your QR code has been emailed.',
                    'show_qr_popup' => true,
                    'qr_path' => $qrUrl,
                ]);
        }


        Logs::create([
            'user_id' => $member->user_id,
            'action' => "Updated membership profile for user: {$member->first_name} {$member->last_name}",
            'timestamp' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Membership profile updated successfully!');
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
}
