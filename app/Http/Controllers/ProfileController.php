<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile(string $memberId)
    {
        $member = User::findOrFail($memberId);
        $memberProfile = MemberProfile::where('user_id', $member->user_id)->first();

        return view('profile.profile', compact('member', 'memberProfile'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->user_id . ',user_id',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'old_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ], [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'username.required' => 'Username is required',
            'username.unique' => 'The username has already been taken',
            'email.required' => 'Email is required',
            'email.unique' => 'The email has already been taken',
            'old_password.required_with' => 'Current password is required to set a new password',
            'new_password.min' => 'New password must be at least 6 characters',
            'new_password.confirmed' => 'New password confirmation does not match',
        ]);

        // Update basic user info
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];

        // Handle password change
        if ($request->filled('new_password')) {
            if (!Hash::check($request->old_password, $user->password)) {
                return back()->withErrors(['old_password' => 'The current password is incorrect.'])->withInput();
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        // Update member profile if user is a member
        if ($user->role === 'member') {
            $memberProfile = MemberProfile::where('user_id', $user->user_id)->first();

            if ($memberProfile) {
                $memberValidated = $request->validate([
                    'plan_id' => 'nullable|exists:membership_plans,plan_id',
                    'sex' => 'nullable|in:male,female',
                    'birthday' => 'nullable|date',
                    'height' => 'nullable|numeric|min:0',
                    'weight' => 'nullable|numeric|min:0',
                    'mobile_number' => 'nullable|string|max:15',
                ], [
                    'plan_id.exists' => 'The selected plan is invalid.',
                ]);

                if ($request->filled('plan_id')) {
                    $memberProfile->plan_id = $memberValidated['plan_id'];
                }
                if ($request->filled('sex')) {
                    $memberProfile->sex = $memberValidated['sex'];
                }
                if ($request->filled('birthday')) {
                    $memberProfile->birthday = $memberValidated['birthday'];
                }
                if ($request->filled('height')) {
                    $memberProfile->height = $memberValidated['height'];
                }
                if ($request->filled('weight')) {
                    $memberProfile->weight = $memberValidated['weight'];
                }
                if ($request->filled('mobile_number')) {
                    $memberProfile->mobile_number = $memberValidated['mobile_number'];
                }

                $memberProfile->save();
            }
        }

        // Log the update
        Logs::create([
            'user_id' => $user->user_id,
            'action' => "Updated profile for user: {$user->first_name} {$user->last_name}",
            'timestamp' => now(),
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}
