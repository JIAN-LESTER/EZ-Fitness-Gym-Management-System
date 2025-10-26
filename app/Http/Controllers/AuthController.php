<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Member_Profile;
use App\Models\member;
use App\Models\MemberProfile;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Str;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\URL;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        return view('authentication.login');
    }

    public function showRegisterForm()
    {
        return view('authentication.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:100|unique:users,email',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
        ]);


        $user = User::create([

            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'member',
            'status' => 'active',
        ]);

        $user->sendEmailVerificationNotification();


        if ($user->role === 'member') {
            MemberProfile::create([
                'user_id' => $user->user_id,
                'status' => 'inactive',
            ]);
        }

        return redirect()
            ->route('loginForm')
            ->with('success', 'Registration successful. Please verify your email to continue.');
    }


    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::whereRaw('LOWER(username) = ?', [strtolower($request->username)])->first();
        if (!$user) {
            return back()->with('error', 'No account found')->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {


            return back()
                ->with('error', 'Incorrect username or password.')
                ->withInput();
        }

        if (!$user->hasVerifiedEmail()) {
            // return a helpful response with the user id so UI can show a resend button
            return back()->withInput()
                ->with('error', 'Your email is not verified.')
                ->with('resend_user_id', $user->user_id);
        }

        Auth::login($user);



        // Role-based dashboard redirection
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'member') {

            $profile = MemberProfile::where('user_id', $user->user_id)->first();

            if ($profile->status === 'inactive') {
                return redirect()->route('member.dashboard')->with('completeMembershipModal', true);
            }

            return redirect()->route('member.dashboard');
        }
    }


    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            Logs::create([
                'user_id' => $user->user_id,
                'action' => "Logged out",
                'timestamp' => now(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }



    /**
     * Resend verification email
     * Expects POST with 'user_id'
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,user_id',
        ]);

        $user = User::find($request->user_id);

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Email already verified.');
        }


        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link has been sent to your email. Please check your inbox (and spam).');
    }


    /**
     * Verification handler — user clicks link in email and is marked as verified.
     * This route uses signed URL and expects both id & hash.
     */
    public function verify(Request $request, $id, $hash)
    {
        // Find the user by their ID from the URL
        $user = User::findOrFail($id);

        // If already verified, redirect
        if ($user->hasVerifiedEmail()) {
            return redirect('/login')->with('success', 'Your email is already verified. You may log in.');
        }

        // Check if the link is valid (not expired, not tampered)
        if (!URL::hasValidSignature($request)) {
            return redirect('/login')->with('error', 'Invalid or expired verification link.');
        }

        // Double-check that the hash matches the user’s email
        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return redirect('/login')->with('error', 'Invalid verification link.');
        }

        // Mark the email as verified
        $user->markEmailAsVerified();
        event(new Verified($user));

        return redirect('/login')->with('success', 'Email verified successfully. You may now log in.');
    }
}