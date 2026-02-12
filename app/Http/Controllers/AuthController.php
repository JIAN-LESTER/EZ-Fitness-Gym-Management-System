<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Member_Profile;
use App\Models\member;
use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Str;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

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
            'branch_id' => null, // **NEW: No branch assigned yet for self-registration**
        ]);

        $user->sendEmailVerificationNotification();

        if ($user->role === 'member') {
            MemberProfile::create([
                'user_id' => $user->user_id,
                'status' => 'inactive',
            ]);
        }

        // **UPDATED: Remove branch_id from logs (will be fetched from user relationship)**
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
            'login' => 'required|string|max:100',
            'password' => 'required|string',
        ], [
            'login.required' => 'Username or email is required',
            'password.required' => 'Password is required',
        ]);

        // Determine if input is email or username
        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Find user by email or username (case-insensitive)
        if ($fieldType === 'email') {
            $user = User::whereRaw('LOWER(email) = ?', [strtolower($request->login)])->first();
        } else {
            $user = User::whereRaw('LOWER(username) = ?', [strtolower($request->login)])->first();
        }

        if (!$user) {
            return back()->with('error', 'No account found')->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->with('error', 'Incorrect credentials.')
                ->withInput();
        }

        if (!$user->hasVerifiedEmail()) {
            return back()->withInput()
                ->with('error', 'Your email is not verified.')
                ->with('resend_user_id', $user->user_id);
        }

        Auth::login($user);

        $currentUser = Auth::user();
        $branchId = $currentUser->role === 'super_admin'
            ? session('selected_branch_id')
            : $currentUser->branch_id;

        Logs::create([
            'user_id' => $user->user_id,
            'branch_id' => $branchId,
            'action' => "{$user->last_name} has logged in successfully.",
            'timestamp' => now(),
        ]);

        // Role-based dashboard redirection
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('admin.dashboard')->with('success', 'Logged in successfully');
        }

        if ($user->role === 'member') {
            $profile = MemberProfile::where('user_id', $user->user_id)->first();

            if ($profile && $profile->status === 'inactive') {
                return redirect()->route('member.dashboard')->with('completeMembershipModal', true);
            }

            return redirect()->route('member.dashboard')->with('success', 'Login successful!');
        } elseif ($user->role === 'staff') {
            return redirect()->route('staff.dashboard')->with('success', 'Logged in successfully');
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
       
         $branchId = $user->role === 'super_admin'
                    ? session('selected_branch_id')
                    : $user->branch_id;


               Logs::create([
            'user_id' => $user->user_id,
            'branch_id' => $branchId,
            'action' => "{$user->last_name} has logged out successfully.",
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

        // **UPDATED: Remove branch_id from logs**
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

        // Double-check that the hash matches the user's email
        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return redirect('/login')->with('error', 'Invalid verification link.');
        }

        // Mark the email as verified
        $user->markEmailAsVerified();
        event(new Verified($user));

        // **UPDATED: Remove branch_id from logs**
        Logs::create([
            'user_id' => $user->user_id,
            'action' => "{$user->last_name} has been verified.",
            'timestamp' => now(),
        ]);

        return redirect('/login')->with('success', 'Email verified successfully. You may now log in.');
    }


    /**
     * Show the form to request a password reset link.
     */
    public function showForgotPasswordForm()
    {
        return view('authentication.forgot-password');
    }

    /**
     * Handle the password reset link request.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.exists' => 'We could not find an account with that email address',
        ]);

        // Send the password reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            // Log the action
            $user = User::where('email', $request->email)->first();
            
            if ($user) {

              $branchId = $user->role === 'super_admin'
                    ? session('selected_branch_id')
                    : $user->branch_id;


               Logs::create([
            'user_id' => $user->user_id,
            'branch_id' => $branchId,
            'action' => "{$user->last_name} has requested password reset.",
            'timestamp' => now(),
        ]);
            }

            return back()->with('status', 'Password reset link sent! Please check your email.');
        }

        return back()->with('error', 'Unable to send password reset link. Please try again.');
    }

    /**
     * Show the password reset form.
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('authentication.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Handle the password reset.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => 'Email is required',
            'email.exists' => 'We could not find an account with that email address',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password.confirmed' => 'Password confirmation does not match',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                          $branchId = $user->role === 'super_admin'
                    ? session('selected_branch_id')
                    : $user->branch_id;


               Logs::create([
            'user_id' => $user->user_id,
            'branch_id' => $branchId,
            'action' => "{$user->last_name} has reset password successfully.",
            'timestamp' => now(),
        ]);

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('loginForm')->with('success', 'Password reset successfully! You can now log in with your new password.');
        }

        return back()->with('error', 'This password reset link is invalid or has expired. Please request a new one.');
    }
}