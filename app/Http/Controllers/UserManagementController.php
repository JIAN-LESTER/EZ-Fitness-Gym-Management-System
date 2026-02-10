<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateMemberQRCode;
use App\Mail\MemberQRCodeMail;
use App\Models\Logs;
use App\Models\MemberProfile;
use App\Models\Sales;
use App\Models\Transactions;
use App\Models\User;
use App\Models\Branches;
use App\Services\CacheService;
use Cache;
use Carbon\Traits\Timestamp;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Mail;
use Storage;

class UserManagementController extends Controller
{
  /**
   * Summary of viewUsers
   * @param Request $request
   */
   public function viewUsers(Request $request)
    {
        $search = $request->get('search');
        $roles = $request->get('roles', []);
        $statuses = $request->get('user_status', []);
        $currentUser = Auth::user();

        $branchId = null;

        if ($currentUser->role === 'super_admin') {
            $branchId = session('selected_branch_id');
        } else {
            $branchId = $currentUser->branch_id;
        }

        // Calculate pending approvals count
        $pendingApprovalsCount = MemberProfile::where(function($query) {
                // Members with plan and subscription but not approved
                $query->whereNotNull('plan_id')
                      ->whereNotNull('subscription_id')
                      ->where('isApprovedForSubscription', false)
                      ->where('isDisabledForSubscription', false);
            })
            ->orWhere(function($query) {
                // Members with incomplete profiles (no plan or subscription)
                $query->where(function($q) {
                    $q->whereNull('plan_id')
                      ->orWhereNull('subscription_id');
                })
                ->where('subscription_status', '!=', 'denied')
                ->where('isDisabled', false);
            })
            ->when($branchId, function($query) use ($branchId) {
                // Filter by branch if applicable
                $query->whereHas('user', function($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            })
            ->count();

        // Build the base query
        $query = User::query()
            ->with(['member' => function($query) {
                $query->select('member_id', 'user_id', 'plan_id', 'subscription_id', 'isApprovedForSubscription', 'isDisabledForSubscription', 'sex', 'birthday', 'mobile_number', 'subscription_status', 'isApproved', 'isDisabled', 'renewal_pending');
            }, 'member.plan:plan_id,name,price', 'member.subscription:subscription_id,name,price', 'branch:branch_id,name'])
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
            ->when($branchId, function ($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            });

        // Get all users
        $allUsers = $query->get();

        // Sort with custom logic
        $sortedUsers = $allUsers->sort(function($a, $b) {
            // Priority 1: Members with pending approval (plan + subscription but not approved)
            $aPending = $a->role === 'member' 
                && $a->member 
                && $a->member->plan_id 
                && $a->member->subscription_id
                && (!$a->member->isApprovedForSubscription || $a->member->renewal_pending)
                && !$a->member->isDisabledForSubscription;
                
            $bPending = $b->role === 'member' 
                && $b->member 
                && $b->member->plan_id 
                && $b->member->subscription_id
                && (!$b->member->isApprovedForSubscription || $b->member->renewal_pending)
                && !$b->member->isDisabledForSubscription;

            if ($aPending && !$bPending) return -1;
            if (!$aPending && $bPending) return 1;

            // Priority by role
            $roleOrder = ['member' => 2, 'staff' => 3, 'admin' => 4, 'super_admin' => 5];
            $aOrder = $roleOrder[$a->role] ?? 6;
            $bOrder = $roleOrder[$b->role] ?? 6;

            if ($aOrder !== $bOrder) {
                return $aOrder - $bOrder;
            }

            // If same priority, sort by created_at desc
            return $b->created_at <=> $a->created_at;
        })->values();

        // Manually paginate
        $perPage = 12;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedItems = $sortedUsers->slice($offset, $perPage)->values();
        
        $users = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $sortedUsers->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $plans = \App\Models\MembershipPlan::select('plan_id', 'name', 'price')->get();
        $branches = Branches::select('branch_id', 'name')->orderBy('name')->get();

        return view('admin.user-management', compact(
            'users',
            'search',
            'roles',
            'statuses',
            'plans',
            'branches',
            'pendingApprovalsCount'
        ));
    }

    // Staff view - only shows members
    public function viewMembersForStaff(Request $request)
    {
        $search = $request->get('search');
        $statuses = $request->get('user_status', []);
        $currentUser = Auth::user();

        $users = User::query()
            ->with('member.plan', 'branch')
            ->where('role', 'member') // Only show members
            ->where('branch_id', $currentUser->branch_id) // Only from staff's branch
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->when(!empty($statuses), function ($query) use ($statuses) {
                return $query->whereIn('status', $statuses);
            })
            ->orderByRaw("
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM member_profiles 
                        WHERE member_profiles.user_id = users.user_id 
                        AND member_profiles.isApproved = 0
                    ) THEN 1
                    ELSE 2
                END
            ")
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->appends($request->query());

        $plans = \App\Models\MembershipPlan::all();
        $branches = Branches::orderBy('name')->get();

        return view('staff.user-management', compact(
            'users',
            'search',
            'statuses',
            'plans',
            'branches'
        ));
    }

    public function create()
    {
        return view('admin.CRUD.add_user');
    }

    public function store(Request $request)
{
    $currentUser = Auth::user();
    $branchId = $currentUser->role === 'super_admin'
        ? session('selected_branch_id')
        : $currentUser->branch_id;

    $validationRules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed',
        'role' => 'nullable|in:member,admin,staff,super_admin',
        'plan_id' => 'nullable|exists:membership_plans,plan_id',
        'subscription_id' => 'nullable|exists:subscriptions,subscription_id',
        'sex' => 'nullable|in:male,female',
        'birthday' => 'nullable|date',
        'height' => 'nullable|numeric',
        'weight' => 'nullable|numeric',
        'mobile_number' => 'nullable|string|max:20',
        'payment_method' => 'nullable|in:cash,gcash',
        'reference_code' => 'nullable|string|max:255',
    ];

    if ($currentUser->role === 'super_admin') {
        $validationRules['branch_id'] = 'required|exists:branches,branch_id';
    }

    $validated = $request->validate($validationRules, [
        'first_name.required' => 'First name is required',
        'last_name.required' => 'Last name is required',
        'username.required' => 'Username is required',
        'email.required' => 'Email is required',
        'username.unique' => 'The username has already been taken',
        'email.unique' => 'The email has already been taken',
        'password.required' => 'Password is required',
        'password.min' => 'Password must be at least 6 characters',
        'password.confirmed' => 'Password confirmation does not match',
        'branch_id.required' => 'Branch is required',
        'branch_id.exists' => 'Selected branch does not exist',
    ]);

    if ($currentUser->role === 'super_admin') {
        $branchId = $validated['branch_id'];
    } else {
        $branchId = $currentUser->branch_id;
    }

    // Create user with auto-verified email
    $user = User::create([
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'username' => $validated['username'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
        'role' => $validated['role'] ?? 'member',
        'status' => 'active',
        'branch_id' => $branchId,
        'email_verified_at' => now(),
    ]);

    // Create member profile if applicable
    if ($user->role === 'member' && ($request->has('plan_id') || $request->has('sex'))) {
        $plan = $request->has('plan_id') ? \App\Models\MembershipPlan::find($validated['plan_id']) : null;
        $subscription = $request->has('subscription_id') ? \App\Models\Subscriptions::find($validated['subscription_id']) : null;
        $paymentMethod = $validated['payment_method'] ?? 'cash';
        $referenceCode = $validated['reference_code'] ?? null;

        $memberProfile = MemberProfile::create([
            'user_id' => $user->user_id,
            'plan_id' => $validated['plan_id'] ?? null,
            'subscription_id' => $validated['subscription_id'] ?? null,
            'sex' => $validated['sex'] ?? null,
            'birthday' => $validated['birthday'] ?? null,
            'height' => $validated['height'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'mobile_number' => $validated['mobile_number'] ?? null,
            'status' => ($plan && $subscription) ? 'active' : 'inactive',
            'subscription_status' => ($plan && $subscription) ? 'active' : 'pending_selection',
            'isApproved' => ($plan && $subscription) ? true : false,
            'isApprovedForSubscription' => ($plan && $subscription) ? true : false,
            'start_date' => $plan ? now() : null,
            'end_date' => $plan ? now()->addDays($plan->duration_days) : null,
            'start_date_for_subscription' => $subscription ? now() : null,
            'end_date_for_subscription' => $subscription ? now()->addDays($subscription->duration_days) : null,
        ]);

        // Process payments if both plan and subscription are selected
        if ($plan && $subscription) {
            // Create sale for plan
            $planSale = Sales::create([
                'user_id' => $currentUser->user_id,
                'branch_id' => $user->branch_id,
                'total_amount' => $plan->price,
                'tax' => 0,
                'discount' => 0,
                'payment_method' => $paymentMethod,
                'reference_code' => $referenceCode,
                'status' => 'paid',
                'type' => 'memberships',
            ]);

            $planSale->items()->create([
                'plan_id' => $plan->plan_id,
                'product_id' => null,
                'subscription_id' => null,
                'quantity' => 1,
                'price' => $plan->price,
                'sub_total' => $plan->price,
            ]);

            Transactions::create([
                'sales_id' => $planSale->sales_id,
                'type' => 'memberships',
                'performed_by' => $currentUser->user_id,
                'quantity' => 1,
                'timestamp' => now(),
            ]);

            // Create sale for subscription
            $subscriptionSale = Sales::create([
                'user_id' => $currentUser->user_id,
                'branch_id' => $user->branch_id,
                'total_amount' => $subscription->price,
                'tax' => 0,
                'discount' => 0,
                'payment_method' => $paymentMethod,
                'reference_code' => $referenceCode,
                'status' => 'paid',
                'type' => 'subscriptions',
            ]);

            $subscriptionSale->items()->create([
                'plan_id' => null,
                'product_id' => null,
                'subscription_id' => $subscription->subscription_id,
                'quantity' => 1,
                'price' => $subscription->price,
                'sub_total' => $subscription->price,
            ]);

            Transactions::create([
                'sales_id' => $subscriptionSale->sales_id,
                'type' => 'subscriptions',
                'performed_by' => $currentUser->user_id,
                'quantity' => 1,
                'timestamp' => now(),
            ]);

            // Generate and send QR code
            $this->generateAndSendQRCode($user, $memberProfile, $plan, $subscription);
        }
    }

    Logs::create([
        'user_id' => $currentUser->user_id,
        'branch_id' => $branchId,
        'action' => "{$currentUser->last_name} added a new user: {$validated['last_name']}.",
        'timestamp' => now(),
    ]);

    $successMessage = 'User created successfully.';
    if ($user->role === 'member' && $request->has('plan_id') && $request->has('subscription_id')) {
        $successMessage .= ' QR code sent to ' . $user->email;
    }

    return redirect()->back()->with('success', $successMessage);
}

    public function show(string $id)
    {
        $currentUser = Auth::user();
        $user = User::with(['member.plan', 'logs', 'branch'])->findOrFail($id);

        // Authorization check for staff and admin
        if ($currentUser->role === 'staff' && $user->role !== 'member') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($currentUser->role === 'admin' && $user->branch_id !== $currentUser->branch_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($user);
    }

    public function edit($id)
    {
        $currentUser = Auth::user();
        $user = User::with('member', 'branch')->findOrFail($id);

        // Authorization check for staff
        if ($currentUser->role === 'staff' && $user->role !== 'member') {
            return response()->json(['error' => 'You can only edit members'], 403);
        }

        // Authorization check for admin
        if ($currentUser->role === 'admin' && $user->branch_id !== $currentUser->branch_id) {
            return response()->json(['error' => 'You can only edit users from your branch'], 403);
        }

        $response = [
            'user_id' => $user->user_id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
            'branch_id' => $user->branch_id,
        ];

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
    $currentUser = Auth::user();
    $branchId = $currentUser->role === 'super_admin'
        ? session('selected_branch_id')
        : $currentUser->branch_id;

    $user = User::findOrFail($id);
    $previousRole = $user->role;

    // Authorization checks
    if ($currentUser->role === 'staff' && $user->role !== 'member') {
        return redirect()->back()->with('error', 'You can only edit members');
    }

    if ($currentUser->role === 'admin' && $user->branch_id !== $currentUser->branch_id) {
        return redirect()->back()->with('error', 'You can only edit users from your branch');
    }

    $validationRules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users,username,' . $id . ',user_id',
        'email' => 'required|email|unique:users,email,' . $id . ',user_id',
        'password' => 'nullable|min:6',
        'role' => 'required|in:member,admin,staff,super_admin',
        'status' => 'nullable|in:active,inactive',
        'plan_id' => 'nullable|exists:membership_plans,plan_id',
        'subscription_id' => 'nullable|exists:subscriptions,subscription_id',
        'sex' => 'nullable|in:male,female',
        'birthday' => 'nullable|date',
        'height' => 'nullable|numeric',
        'weight' => 'nullable|numeric',
        'mobile_number' => 'nullable|string|max:20',
        'payment_method' => 'nullable|in:cash,gcash',
        'reference_code' => 'nullable|string|max:255',
    ];

    if ($currentUser->role === 'super_admin') {
        $validationRules['branch_id'] = 'required|exists:branches,branch_id';
    }

    $validated = $request->validate($validationRules);

    $user->first_name = $validated['first_name'];
    $user->last_name = $validated['last_name'];
    $user->username = $validated['username'];
    $user->email = $validated['email'];

    if (!empty($validated['password'])) {
        $user->password = bcrypt($validated['password']);
    }

    $user->role = $validated['role'];
    $user->status = $validated['status'];

    if ($currentUser->role === 'super_admin' && isset($validated['branch_id'])) {
        $user->branch_id = $validated['branch_id'];
    }

    if (!$user->email_verified_at) {
        $user->email_verified_at = now();
    }

    $user->save();

    if ($user->role === 'member') {
        $memberData = [
            'plan_id' => $validated['plan_id'] ?? null,
            'subscription_id' => $validated['subscription_id'] ?? null,
            'sex' => $validated['sex'] ?? null,
            'birthday' => $validated['birthday'] ?? null,
            'height' => $validated['height'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'mobile_number' => $validated['mobile_number'] ?? null,
        ];

        if ($user->member) {
            $oldPlanId = $user->member->plan_id;
            $oldSubscriptionId = $user->member->subscription_id;
            
            $user->member->update($memberData);

            // If plan or subscription changed and both are now set, process payment and regenerate QR
            if (($oldPlanId != $validated['plan_id'] || $oldSubscriptionId != $validated['subscription_id']) 
                && $validated['plan_id'] && $validated['subscription_id']) {
                
                $plan = \App\Models\MembershipPlan::find($validated['plan_id']);
                $subscription = \App\Models\Subscriptions::find($validated['subscription_id']);
                $paymentMethod = $validated['payment_method'] ?? 'cash';
                $referenceCode = $validated['reference_code'] ?? null;

                // Process new payments if changed
                if ($oldPlanId != $validated['plan_id']) {
                    $planSale = Sales::create([
                        'user_id' => $currentUser->user_id,
                        'branch_id' => $user->branch_id,
                        'total_amount' => $plan->price,
                        'tax' => 0,
                        'discount' => 0,
                        'payment_method' => $paymentMethod,
                        'reference_code' => $referenceCode,
                        'status' => 'paid',
                        'type' => 'memberships',
                    ]);

                    $planSale->items()->create([
                        'plan_id' => $plan->plan_id,
                        'product_id' => null,
                        'subscription_id' => null,
                        'quantity' => 1,
                        'price' => $plan->price,
                        'sub_total' => $plan->price,
                    ]);

                    Transactions::create([
                        'sales_id' => $planSale->sales_id,
                        'type' => 'memberships',
                        'performed_by' => $currentUser->user_id,
                        'quantity' => 1,
                        'timestamp' => now(),
                    ]);
                }

                if ($oldSubscriptionId != $validated['subscription_id']) {
                    $subscriptionSale = Sales::create([
                        'user_id' => $currentUser->user_id,
                        'branch_id' => $user->branch_id,
                        'total_amount' => $subscription->price,
                        'tax' => 0,
                        'discount' => 0,
                        'payment_method' => $paymentMethod,
                        'reference_code' => $referenceCode,
                        'status' => 'paid',
                        'type' => 'subscriptions',
                    ]);

                    $subscriptionSale->items()->create([
                        'plan_id' => null,
                        'product_id' => null,
                        'subscription_id' => $subscription->subscription_id,
                        'quantity' => 1,
                        'price' => $subscription->price,
                        'sub_total' => $subscription->price,
                    ]);

                    Transactions::create([
                        'sales_id' => $subscriptionSale->sales_id,
                        'type' => 'subscriptions',
                        'performed_by' => $currentUser->user_id,
                        'quantity' => 1,
                        'timestamp' => now(),
                    ]);
                }

                // Update dates and status
                $user->member->update([
                    'start_date' => now(),
                    'end_date' => now()->addDays($plan->duration_days),
                    'start_date_for_subscription' => now(),
                    'end_date_for_subscription' => now()->addDays($subscription->duration_days),
                    'status' => 'active',
                    'subscription_status' => 'active',
                    'isApproved' => true,
                    'isApprovedForSubscription' => true,
                ]);

                // Regenerate QR code
                $this->generateAndSendQRCode($user, $user->member, $plan, $subscription);
            }
        } else if ($request->has('plan_id') && $request->has('subscription_id')) {
            $plan = \App\Models\MembershipPlan::find($validated['plan_id']);
            $subscription = \App\Models\Subscriptions::find($validated['subscription_id']);
            $paymentMethod = $validated['payment_method'] ?? 'cash';
            $referenceCode = $validated['reference_code'] ?? null;

            $memberProfile = MemberProfile::create(array_merge($memberData, [
                'user_id' => $user->user_id,
                'status' => 'active',
                'subscription_status' => 'active',
                'isApproved' => true,
                'isApprovedForSubscription' => true,
                'start_date' => now(),
                'end_date' => now()->addDays($plan->duration_days),
                'start_date_for_subscription' => now(),
                'end_date_for_subscription' => now()->addDays($subscription->duration_days),
            ]));

            // Process payments for new member
            $planSale = Sales::create([
                'user_id' => $currentUser->user_id,
                'branch_id' => $user->branch_id,
                'total_amount' => $plan->price,
                'tax' => 0,
                'discount' => 0,
                'payment_method' => $paymentMethod,
                'reference_code' => $referenceCode,
                'status' => 'paid',
                'type' => 'memberships',
            ]);

            $planSale->items()->create([
                'plan_id' => $plan->plan_id,
                'product_id' => null,
                'subscription_id' => null,
                'quantity' => 1,
                'price' => $plan->price,
                'sub_total' => $plan->price,
            ]);

            Transactions::create([
                'sales_id' => $planSale->sales_id,
                'type' => 'memberships',
                'performed_by' => $currentUser->user_id,
                'quantity' => 1,
                'timestamp' => now(),
            ]);

            $subscriptionSale = Sales::create([
                'user_id' => $currentUser->user_id,
                'branch_id' => $user->branch_id,
                'total_amount' => $subscription->price,
                'tax' => 0,
                'discount' => 0,
                'payment_method' => $paymentMethod,
                'reference_code' => $referenceCode,
                'status' => 'paid',
                'type' => 'subscriptions',
            ]);

            $subscriptionSale->items()->create([
                'plan_id' => null,
                'product_id' => null,
                'subscription_id' => $subscription->subscription_id,
                'quantity' => 1,
                'price' => $subscription->price,
                'sub_total' => $subscription->price,
            ]);

            Transactions::create([
                'sales_id' => $subscriptionSale->sales_id,
                'type' => 'subscriptions',
                'performed_by' => $currentUser->user_id,
                'quantity' => 1,
                'timestamp' => now(),
            ]);

            if ($previousRole !== 'member') {
                $this->generateAndSendQRCode($user, $memberProfile, $plan, $subscription);
            }
        }
    } else {
        if ($user->member) {
            $user->member->delete();
        }
    }

    Logs::create([
        'user_id' => $currentUser->user_id,
        'branch_id' => $branchId,
        'action' => "{$currentUser->last_name} updated user: {$validated['last_name']}.",
        'timestamp' => now(),
    ]);

    return redirect()->back()->with('success', 'User updated successfully');
}
    public function destroy(string $id)
    {
        $currentUser = Auth::user();

                     $branchId = $currentUser->role === 'super_admin'
                    ? session('selected_branch_id')
                    : $currentUser->branch_id;
        $userToDelete = User::where('user_id', $id)->firstOrFail();

        // Authorization checks
        if ($currentUser->role === 'staff' && $userToDelete->role !== 'member') {
            return redirect()->back()->with('error', 'You can only delete members');
        }

        if ($currentUser->role === 'admin' && $userToDelete->branch_id !== $currentUser->branch_id) {
            return redirect()->back()->with('error', 'You can only delete users from your branch');
        }

        if ($userToDelete->role === 'super_admin') {
            return redirect()->back()->with('error', 'Super Admin cannot be deleted.');
        }

        if ($userToDelete->role === 'admin' && $currentUser->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Only Super Admin can delete Admin users.');
        }

        if ($userToDelete->user_id === $currentUser->user_id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        Logs::create([
            'user_id' => $currentUser->user_id,
            'branch_id' => $branchId,
            'action' => "{$currentUser->last_name} deleted user: {$userToDelete->last_name}.",
            'timestamp' => now(),
        ]);

        $userToDelete->delete();

        return redirect()->back()->with('success', 'User deleted successfully');
    }

public function approveSubscription(Request $request, $memberId)
{
    try {
        $member = MemberProfile::with(['user:user_id,first_name,last_name,email,branch_id', 'plan:plan_id,name,price,duration_days', 'subscription:subscription_id,name,price,duration_days'])
            ->findOrFail($memberId);
        
        $currentUser = Auth::user();
        $branchId = $currentUser->role === 'super_admin'
            ? session('selected_branch_id')
            : $currentUser->branch_id;
        
        $user = $member->user;
        $plan = $member->plan;
        $subscription = $member->subscription;

        // Authorization check
        if ($currentUser->role === 'admin' && $user->branch_id !== $currentUser->branch_id) {
            return redirect()->back()->with('error', 'You can only approve members from your branch.');
        }

        // Validation
        if (!$plan) {
            return redirect()->back()->with('error', 'Cannot approve: Member has no plan selected.');
        }

        if (!$subscription) {
            return redirect()->back()->with('error', 'Cannot approve: Member has no subscription selected.');
        }

        // Get payment details
        $paymentMethod = $request->input('payment_method', 'cash');
        $referenceCode = $request->input('reference_code');
        $isRenewal = $member->renewal_pending;

        if ($paymentMethod === 'gcash' && empty($referenceCode)) {
            return redirect()->back()->with('error', 'GCash reference code is required for GCash payments.');
        }

        \DB::beginTransaction();
        
        try {
            // Create sale for SUBSCRIPTION
            $subscriptionSale = Sales::create([
                'user_id' => $currentUser->user_id,
                'branch_id' => $user->branch_id,
                'total_amount' => $subscription->price,
                'tax' => 0,
                'discount' => 0,
                'payment_method' => $paymentMethod,
                'reference_code' => $referenceCode,
                'status' => 'paid',
                'type' => 'subscriptions',
            ]);

            $subscriptionSale->items()->create([
                'plan_id' => null,
                'product_id' => null,
                'subscription_id' => $subscription->subscription_id,
                'quantity' => 1,
                'price' => $subscription->price,
                'sub_total' => $subscription->price,
            ]);

            Transactions::create([
                'sales_id' => $subscriptionSale->sales_id,
                'type' => 'subscriptions',
                'performed_by' => $currentUser->user_id,
                'quantity' => 1,
                'timestamp' => now(),
            ]);

            // Calculate new end dates based on current state
            $now = now();
            
            // For subscription end date
            if ($isRenewal && $member->end_date_for_subscription && \Carbon\Carbon::parse($member->end_date_for_subscription)->isFuture()) {
                // If renewing and current subscription hasn't expired, add to existing end date
                $newSubscriptionEndDate = \Carbon\Carbon::parse($member->end_date_for_subscription)
                    ->addDays($subscription->duration_days);
            } else {
                // If new or expired, start from now
                $newSubscriptionEndDate = $now->copy()->addDays($subscription->duration_days);
            }

            // For plan end date
            if ($isRenewal && $member->end_date && \Carbon\Carbon::parse($member->end_date)->isFuture()) {
                // If plan hasn't expired, add to existing end date
                $newPlanEndDate = \Carbon\Carbon::parse($member->end_date)
                    ->addDays($plan->duration_days);
            } else {
                // If new or expired, start from now
                $newPlanEndDate = $now->copy()->addDays($plan->duration_days);
            }

            // Update member profile with ALL required fields
            $member->isApproved = true;
            $member->isApprovedForSubscription = true;
            $member->isDisabled = false;
            $member->isDisabledForSubscription = false;
            $member->subscription_status = 'active';
            $member->status = 'active';
            $member->renewal_pending = false;
            $member->approved_at = $isRenewal ? $member->approved_at : now(); // Keep original approval date if renewal
            $member->approved_at_for_subscription = now();
            $member->start_date_for_subscription = $isRenewal ? $member->start_date_for_subscription : now(); // Keep original start if renewal
            $member->end_date_for_subscription = $newSubscriptionEndDate;
            $member->start_date = $isRenewal ? $member->start_date : now(); // Keep original start if renewal
            $member->end_date = $newPlanEndDate;
            $member->suspended_at = null;
            $member->days_remaining_before_suspend = null;
            $member->plan_days_remaining_before_suspend = null; // Clear paused plan days
            
            // Save the member profile
            $member->save();

            Logs::create([
                'user_id' => $currentUser->user_id,
                'branch_id' => $branchId,
                'action' => ($isRenewal ? 'Approved renewal' : 'Approved subscription') . " for: {$user->first_name} {$user->last_name} - Subscription: {$subscription->name} (₱{$subscription->price}) - Payment: {$paymentMethod}",
                'timestamp' => now(),
            ]);

            \DB::commit();

            // Clear all caches BEFORE generating QR
            CacheService::forgetPattern('member_profile');
            CacheService::forgetPattern('recent_attendance');
            CacheService::forgetPattern('attendance_counts');
            Cache::forget("member_profile:stats:{$user->user_id}");
            
            // Refresh member data before generating QR
            $member->refresh();
            
            // Generate and send QR code
            $this->generateAndSendQRCode($user, $member, $plan, $subscription);
            
            $message = $isRenewal
                ? "Renewal approved! QR code sent to {$user->email}"
                : "Subscription approved! QR code sent to {$user->email}";

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Database transaction failed during approval", [
                'member_id' => $memberId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }

    } catch (\Exception $e) {
        \Log::error("Error during subscription approval", [
            'member_id' => $memberId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()->with('error', 'Error approving subscription: ' . $e->getMessage());
    }
}


/**
 * Optimized profile approval - also queue QR if needed
 */
public function approveProfile(Request $request, $memberId)
{
    try {
        $member = MemberProfile::with(['user:user_id,first_name,last_name,email,branch_id', 'plan:plan_id,name,price,duration_days'])
            ->findOrFail($memberId);
        
        $currentUser = Auth::user();
        $branchId = $currentUser->role === 'super_admin'
            ? session('selected_branch_id')
            : $currentUser->branch_id;
        $user = $member->user;
        $plan = $member->plan;

        // Authorization check
        if ($currentUser->role === 'admin' && $user->branch_id !== $currentUser->branch_id) {
            return redirect()->back()->with('error', 'You can only approve members from your branch.');
        }

        // Validation
        if (!$plan) {
            return redirect()->back()->with('error', 'Cannot approve: Member has no plan selected.');
        }

        // Get payment details from request
        $paymentMethod = $request->input('payment_method', 'cash');
        $referenceCode = $request->input('reference_code');

        if ($paymentMethod === 'gcash' && empty($referenceCode)) {
            return redirect()->back()->with('error', 'GCash reference code is required for GCash payments.');
        }

        \DB::beginTransaction();
        
        try {
            // Create sale for MEMBERSHIP PLAN
            $planSale = Sales::create([
                'user_id' => $currentUser->user_id,
                'branch_id' => $user->branch_id,
                'total_amount' => $plan->price,
                'tax' => 0,
                'discount' => 0,
                'payment_method' => $paymentMethod,
                'reference_code' => $referenceCode,
                'status' => 'paid',
                'type' => 'memberships',
            ]);

            $planSale->items()->create([
                'plan_id' => $plan->plan_id,
                'product_id' => null,
                'subscription_id' => null,
                'quantity' => 1,
                'price' => $plan->price,
                'sub_total' => $plan->price,
            ]);

            Transactions::create([
                'sales_id' => $planSale->sales_id,
                'type' => 'memberships',
                'performed_by' => $currentUser->user_id,
                'quantity' => 1,
                'timestamp' => now(),
            ]);

            // Update member profile - approved but waiting for subscription
            $member->update([
                'isApproved' => true,
                'isDisabled' => false,
                'status' => 'approved',
                'subscription_status' => 'pending_selection',
                'approved_at' => now(),
                'start_date' => now(),
                'end_date' => now()->addDays($plan->duration_days),
            ]);

            Logs::create([
                'user_id' => $currentUser->user_id,
                'branch_id' => $branchId,
                'action' => "Approved profile & processed plan payment for: {$user->first_name} {$user->last_name} - Plan: {$plan->name} (₱{$plan->price}) - Payment: {$paymentMethod}",
                'timestamp' => now(),
            ]);

            \DB::commit();

            return redirect()->route('admin.user_management')->with('success', "Profile approved and plan payment processed! Member can now select a subscription.");

        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }

    } catch (\Exception $e) {
        \Log::error("Error during profile approval", [
            'member_id' => $memberId,
            'error' => $e->getMessage()
        ]);

        return redirect()->back()->with('error', 'Error approving profile: ' . $e->getMessage());
    }
}

/**
 * Optimized QR generation - now synchronous but faster
 * Use this if you don't want to set up queues
 */
private function generateAndSendQRCode($user, $memberProfile, $plan, $subscription)
{
    if (!$plan || !$subscription) {
        throw new \Exception("QR Code generation failed: Both plan and subscription are required");
    }

    try {
        // Reload relationships to ensure we have latest data
        $memberProfile->refresh();
        $memberProfile->load(['user', 'plan', 'subscription']);
        
        // Simplified QR data
        $qrData = json_encode([
            'email' => $user->email,
            'id' => $memberProfile->member_id,
            'name' => "{$user->first_name} {$user->last_name}",
            'plan' => $plan->name,
            'sub' => $subscription->name,
            'exp' => $memberProfile->end_date_for_subscription,
        ]);

        $qrRelativePath = "qr/member_{$user->user_id}.png";
        $fullPath = storage_path("app/public/{$qrRelativePath}");

        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        // Generate QR code
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($qrData)
            ->encoding(new Encoding('UTF-8'))
            ->size(250)
            ->margin(5)
            ->build();

        $result->saveToFile($fullPath);

        if (!file_exists($fullPath) || filesize($fullPath) === 0) {
            throw new \Exception("QR code file creation failed");
        }

        $memberProfile->qr_code = $qrRelativePath;
        $memberProfile->save();

        // CHANGE: Send immediately instead of queue
        Mail::to($user->email)->send(new MemberQRCodeMail($memberProfile, $fullPath));

        \Log::info("QR Code generated and sent via email", [
            'user_id' => $user->user_id,
            'email' => $user->email,
            'path' => $qrRelativePath
        ]);

    } catch (\Exception $e) {
        \Log::error("QR Code generation/email failed", [
            'user_id' => $user->user_id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        // Don't throw - allow approval to succeed even if QR fails
    }
}

    /**
     * Deny member access - WITH SWEETALERT
     */
    public function deny($memberId)
{
    $member = MemberProfile::findOrFail($memberId);
    $currentUser = Auth::user();
    $user = $member->user;

    if ($currentUser->role === 'admin' && $user->branch_id !== $currentUser->branch_id) {
        return redirect()->back()->with('error', 'You can only deny members from your branch.');
    }

    $member->update([
        'isApproved' => false,
        'isDisabled' => true,
        'isApprovedForSubscription' => false,
        'isDisabledForSubscription' => true,
        'subscription_status' => 'denied',
        'status' => 'denied',
    ]);

    Logs::create([
        'user_id' => $currentUser->user_id,
        'branch_id' => $currentUser->role === 'super_admin'
            ? session('selected_branch_id')
            : $currentUser->branch_id,
        'action' => "Denied membership for: {$user->first_name} {$user->last_name}",
        'timestamp' => now(),
    ]);

    return redirect()->back()->with('success', 'Member access denied.');
}

    /**
     * Suspend member - Pause their days
     */
public function suspendMember($memberId)
{
    try {
        $member = MemberProfile::findOrFail($memberId);
        $user = $member->user;
        $currentUser = Auth::user();

        if ($currentUser->role === 'admin' && $user->branch_id !== $currentUser->branch_id) {
            return redirect()->back()->with('error', 'You can only suspend members from your branch.');
        }

        $now = now();

        // Calculate days remaining for SUBSCRIPTION
        $subscriptionDaysRemaining = 0;
        if ($member->end_date_for_subscription) {
            $endDate = \Carbon\Carbon::parse($member->end_date_for_subscription);
            $subscriptionDaysRemaining = max(0, (int) ceil($now->diffInDays($endDate, false)));
        }

        // Calculate days remaining for MEMBERSHIP PLAN
        $planDaysRemaining = 0;
        if ($member->end_date) {
            $endDate = \Carbon\Carbon::parse($member->end_date);
            $planDaysRemaining = max(0, (int) ceil($now->diffInDays($endDate, false)));
        }

        \Log::info('Suspending member', [
            'member_id' => $memberId,
            'subscription_days' => $subscriptionDaysRemaining,
            'plan_days' => $planDaysRemaining,
            'subscription_end' => $member->end_date_for_subscription,
            'plan_end' => $member->end_date,
        ]);

        // Update member with suspended status and save BOTH remaining days
        $member->update([
            'subscription_status' => 'suspended',
            'status' => 'suspended', // Also suspend the membership status
            'suspended_at' => $now,
            'days_remaining_before_suspend' => $subscriptionDaysRemaining, // Subscription days
            'plan_days_remaining_before_suspend' => $planDaysRemaining, // Plan days
        ]);

        // Clear caches
        CacheService::forgetPattern('member_profile');
        Cache::forget("member_profile:stats:{$user->user_id}");

        Logs::create([
            'user_id' => Auth::id(),
            'branch_id' => $currentUser->role === 'super_admin'
                ? session('selected_branch_id')
                : $currentUser->branch_id,
            'action' => "Suspended membership for: {$user->first_name} {$user->last_name} (Subscription: {$subscriptionDaysRemaining} days, Plan: {$planDaysRemaining} days paused)",
            'timestamp' => now(),
        ]);

        return redirect()->back()->with('success', "Member {$user->first_name} {$user->last_name} has been suspended. Subscription: {$subscriptionDaysRemaining} days paused, Plan: {$planDaysRemaining} days paused.");
    } catch (\Exception $e) {
        \Log::error("Error suspending member", [
            'member_id' => $memberId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()->with('error', 'Error suspending member: ' . $e->getMessage());
    }
}

/**
 * Resume member - Restore BOTH subscription AND membership remaining days
 */
public function resumeMember($memberId)
{
    try {
        $member = MemberProfile::findOrFail($memberId);
        $user = $member->user;
        $currentUser = Auth::user();

        if ($currentUser->role === 'admin' && $user->branch_id !== $currentUser->branch_id) {
            return redirect()->back()->with('error', 'You can only resume members from your branch.');
        }

        // Check if member was suspended
        if ($member->subscription_status !== 'suspended') {
            return redirect()->back()->with('error', 'This member is not currently suspended.');
        }

        // Restore subscription days
        $subscriptionDaysToRestore = $member->days_remaining_before_suspend ?? 0;
        $newSubscriptionEndDate = now()->addDays($subscriptionDaysToRestore);

        // Restore membership plan days
        $planDaysToRestore = $member->plan_days_remaining_before_suspend ?? 0;
        $newPlanEndDate = now()->addDays($planDaysToRestore);

        \Log::info('Resuming member', [
            'member_id' => $memberId,
            'subscription_days_to_restore' => $subscriptionDaysToRestore,
            'plan_days_to_restore' => $planDaysToRestore,
            'new_subscription_end' => $newSubscriptionEndDate,
            'new_plan_end' => $newPlanEndDate,
        ]);

        $member->update([
            'subscription_status' => 'active',
            'status' => 'active',
            'suspended_at' => null,
            'end_date_for_subscription' => $newSubscriptionEndDate,
            'end_date' => $newPlanEndDate,
            'days_remaining_before_suspend' => null,
            'plan_days_remaining_before_suspend' => null,
            'renewal_pending' => false, // Clear renewal pending flag
        ]);

        // Clear caches
        CacheService::forgetPattern('member_profile');
        Cache::forget("member_profile:stats:{$user->user_id}");

        Logs::create([
            'user_id' => Auth::id(),
            'branch_id' => $currentUser->role === 'super_admin'
                ? session('selected_branch_id')
                : $currentUser->branch_id,
            'action' => "Resumed membership for: {$user->first_name} {$user->last_name} (Subscription: {$subscriptionDaysToRestore} days, Plan: {$planDaysToRestore} days restored)",
            'timestamp' => now(),
        ]);

        return redirect()->back()->with('success', "Member {$user->first_name} {$user->last_name} has been resumed. Subscription: {$subscriptionDaysToRestore} days restored, Plan: {$planDaysToRestore} days restored.");
    } catch (\Exception $e) {
        \Log::error("Error resuming member", [
            'member_id' => $memberId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()->with('error', 'Error resuming member: ' . $e->getMessage());
    }
}


    /**
     * Cancel Plan - Member needs to renew everything
     */
    public function cancelPlan($memberId)
    {
        try {
            $member = MemberProfile::findOrFail($memberId);
            $user = $member->user;
            $currentUser = Auth::user();

            if ($currentUser->role === 'admin' && $user->branch_id !== $currentUser->branch_id) {
                return redirect()->back()->with('error', 'You can only cancel plans for members from your branch.');
            }

            $member->update([
                'subscription_status' => 'cancelled',
                'status' => 'cancelled',
                'end_date' => now(),
                'end_date_for_subscription' => now(),
                'suspended_at' => null,
                'days_remaining_before_suspend' => null,
            ]);

            Logs::create([
                'user_id' => Auth::id(),
                'branch_id' => $currentUser->role === 'super_admin'
                    ? session('selected_branch_id')
                    : $currentUser->branch_id,
                'action' => "Cancelled plan for: {$user->first_name} {$user->last_name}",
                'timestamp' => now(),
            ]);

            return redirect()->back()->with('success', "Plan cancelled for {$user->first_name} {$user->last_name}. Member needs to renew.");
        } catch (\Exception $e) {
            \Log::error("Error cancelling plan", [
                'member_id' => $memberId,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error cancelling plan: ' . $e->getMessage());
        }
    }
}