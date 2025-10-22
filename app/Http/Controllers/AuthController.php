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

  
        if ($user->role === 'member') {
            MemberProfile::create([
                'user_id' => $user->user_id,
                'status' => 'inactive',
            ]);
        }

        return redirect()
            ->route('loginForm')
            ->with('success', 'Registration successful! You can log in now.');
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
}