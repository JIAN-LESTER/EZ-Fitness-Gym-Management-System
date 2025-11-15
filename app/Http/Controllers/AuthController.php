<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Member_Profile;
use App\Models\member;
use App\Models\MemberProfile;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Str;
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
            'username' => 'required|string|max:50|min:4|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.unique' => 'The email has already been taken',
            'email.required' => 'Email is required',
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'username.unique' => 'The username has already been taken',
            'username.required' => 'Username is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password.confirmed' => 'Password confirmation does not match',
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

        Logs::create([
            'user_id' => $user->user_id,
            'action' => "{$user->last_name} created his own account.",
            'timestamp' => now(),
        ]);


        return redirect()
            ->route('loginForm')
            ->with('success', 'Registration successful! Verification link has been sent to your email.');
    }


    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username is required',
            'password.required' => 'Password is required',

            'password.min' => 'Password must be at least 6 characters',

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

            return back()->withInput()
                ->with('error', 'Your email is not verified.')
                ->with('resend_user_id', $user->user_id);
        }

        Logs::create([
            'user_id' => $user->user_id,
            'action' => "{$user->last_name} has logged in successfully.",
            'timestamp' => now(),
        ]);

        Auth::login($user);



        // Role-based dashboard redirection
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Logged in successfully');
        }

        if ($user->role === 'member') {

            $profile = MemberProfile::where('user_id', $user->user_id)->first();

            if ($profile->status === 'inactive') {
                return redirect()->route('member.dashboard')->with('completeMembershipModal', true);
            }

            return redirect()->route('member.dashboard')->with('success', 'Login successful!');
        }
    }

    public function checkUsername(Request $request)
    {
        $exists = User::where('username', $request->username)->exists();
        return response()->json(['taken' => $exists]);
    }

    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['taken' => $exists]);
    }


    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            Logs::create([
                'user_id' => $user->user_id,
                'action' => "{$user->last_name} has logged out",
                'timestamp' => now(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();



        return redirect('/login')->with('success', 'Logged out successfully.');
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

        Logs::create([
            'user_id' => $user->user_id,
            'action' => "{$user->last_name} has resent the verification email.",
            'timestamp' => now(),
        ]);

        return back()->with('success', 'Verification link has been sent to your email. Please check your inbox (and spam).');
    }


    /**
     * Verification handler — user clicks link in email and is marked as verified.
     * This route uses signed URL and expects both id & hash.
     */
    public function verify(Request $request, $id, $hash)
    {

        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return redirect('/login')->with('success', 'Your email is already verified. You may log in.');
        }

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

        Logs::create([
            'user_id' => $user->user_id,
            'action' => "{$user->last_name} has been verified.",
            'timestamp' => now(),
        ]);

        return redirect('/login')->with('success', 'Email verified successfully. You may now log in.');
    }
}