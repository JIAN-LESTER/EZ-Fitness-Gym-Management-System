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
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mail;



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

    public function updateProfile(Request $request)
    {
        $member = Auth::user();

        $validated = $request->validate([
            'plan_id' => 'required|exists:membership_plans,plan_id',
            'sex' => 'required|in:male,female',
            'birthday' => 'required|date',
            'height' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
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
            'status' => $memberProfile->exists ? $memberProfile->status : 'inactive',
            'start_date' => $memberProfile->start_date ?? now(),
        ]);

        $memberProfile->status = $memberProfile->status === 'inactive' ? 'active' : $memberProfile->status;
        $memberProfile->save();


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

        $memberProfile->qr_code = $qrRelativePath; // relative path
        $memberProfile->save();

        $qrUrl = asset("storage/{$qrRelativePath}");
        // Send QR code via email
        Mail::to($member->email)->send(new MemberQRCodeMail($member, storage_path("app/public/{$qrRelativePath}")));

        // Log action
        Logs::create([
            'user_id' => $member->user_id,
            'action' => "Completed membership profile setup for user: {$member->fname} {$member->lname}",
            'timestamp' => now(),
        ]);

        // Return success with QR popup
        return redirect()
            ->route('member.dashboard', $member->user_id)
            ->with([
                'success' => 'Membership profile completed successfully! Your QR has been emailed.',
                'show_qr_popup' => true,
                'qr_path' => $qrUrl,
            ]);
    }


    public function profile(string $memberId)
    {
        $member = User::findOrFail($memberId);
        $memberProfile = MemberProfile::where('user_id', $member->user_id)->first();

        return view('profile.profile', compact('member', 'memberProfile'));
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
