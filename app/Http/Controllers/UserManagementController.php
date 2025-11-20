<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{

    public function viewUsers(Request $request)
    {
        $search = $request->get('search');
        $roles = $request->get('roles', []);
        $statuses = $request->get('user_status', []);

        $users = User::query()
            ->with('member.plan')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when(!empty($roles), function ($query) use ($roles) {
                return $query->whereIn('role', $roles);
            })
            ->when(!empty($statuses), function ($query) use ($statuses) {
                return $query->whereIn('status', $statuses);
            })
            ->paginate(12)
            ->appends($request->query());


        $plans = \App\Models\MembershipPlan::all();

        return view('admin.user-management', compact(
            'users',
            'search',
            'roles',
            'statuses',
            'plans'
        ));
    }

    public function create()
    {
        return view('admin.CRUD.add_user');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'nullable|in:member,admin,staff',

            'plan_id' => 'nullable|exists:membership_plans,plan_id',
            'sex' => 'nullable|in:male,female',
            'birthday' => 'nullable|date',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'mobile_number' => 'nullable|string|max:20',
        ], [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'username.required' => 'Username is required',
            'email.required' => 'Email is required',
            'username.unique' => 'The username has already been taken',
            'email.unique' => 'The email has already been taken',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password.confirmed' => 'Password confirmation does not match',


        ]);

        $authUser = Auth::user();


        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'] ?? 'member',
            'status' => 'active',
        ]);


        if ($user->role === 'member' && ($request->has('plan_id') || $request->has('sex'))) {
            MemberProfile::create([
                'user_id' => $user->user_id,
                'plan_id' => $validated['plan_id'] ?? null,
                'sex' => $validated['sex'] ?? null,
                'birthday' => $validated['birthday'] ?? null,
                'height' => $validated['height'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'mobile_number' => $validated['mobile_number'] ?? null,
                'status' => 'inactive',
            ]);
        }

        Logs::create([
            'user_id' => $authUser->user_id,
            'action' => "{$authUser->last_name} added a new user: {$validated['last_name']}.",
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.user_management')->with('success', 'User created successfully');
    }

    public function show(string $id)
    {
        $user = User::with(['member.plan', 'logs'])->findOrFail($id);

        return response()->json($user);
    }

    public function edit($id)
    {
        $user = User::with('member')->findOrFail($id);

        $response = [
            'user_id' => $user->user_id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
        ];

        // Add member data if exists
        if ($user->member) {
            $response['member'] = [
                'plan_id' => $user->member->plan_id,
                'sex' => $user->member->sex,
                'birthday' => $user->member->birthday,
                'height' => $user->member->height,
                'weight' => $user->member->weight,
                'mobile_number' => $user->member->mobile_number,
            ];
        }

        return response()->json($response);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id . ',user_id',
            'email' => 'required|email|unique:users,email,' . $id . ',user_id',
            'password' => 'nullable|min:6',
            'role' => 'required|in:member,admin,staff',
            'status' => 'nullable|in:active,inactive',

            'plan_id' => 'nullable|exists:membership_plans,plan_id',
            'sex' => 'nullable|in:male,female',
            'birthday' => 'nullable|date',
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'mobile_number' => 'nullable|string|max:20',
        ], [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'username.required' => 'Username is required',
            'username.unique' => 'The username has already been taken',
            'email.required' => 'Email is required',
            'email.unique' => 'The email has already been taken',

        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->role = $validated['role'];
        $user->status = $validated['status'];

        $user->save();


        if ($user->role === 'member') {
            $memberData = [
                'plan_id' => $validated['plan_id'] ?? null,
                'sex' => $validated['sex'] ?? null,
                'birthday' => $validated['birthday'] ?? null,
                'height' => $validated['height'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'mobile_number' => $validated['mobile_number'] ?? null,
            ];


            if ($user->member) {

                $user->member->update($memberData);
            } else if ($request->has('plan_id') || $request->has('sex')) {

                MemberProfile::create(array_merge($memberData, [
                    'user_id' => $user->user_id,
                    'status' => 'inactive',
                ]));
            }
        } else {

            if ($user->member) {
                $user->member->delete();
            }
        }

        $authUser = Auth::user();

        Logs::create([
            'user_id' => $authUser->user_id,
            'action' => "{$authUser->last_name} updated user: {$validated['last_name']}.",
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.user_management')->with('success', 'User updated successfully');
    }

    public function destroy(string $id)
    {
        $currentUser = Auth::user();
        $userToDelete = User::where('user_id', $id)->firstOrFail();

        if ($userToDelete->role === 'admin') {
            return redirect()->route('admin.user_management')
                ->with('error', 'Admin cannot be deleted.');
        }

        if ($userToDelete->user_id === $currentUser->user_id) {
            return redirect()->route('admin.user_management')
                ->with('error', 'You cannot delete your own account.');
        }

        Logs::create([
            'user_id' => $currentUser->user_id,
            'action' => "{$currentUser->last_name} deleted user: {$userToDelete->last_name}.",
            'timestamp' => now(),
        ]);

        $userToDelete->delete();



        return redirect()->route('admin.user_management')
            ->with('success', 'User deleted successfully');
    }
}
