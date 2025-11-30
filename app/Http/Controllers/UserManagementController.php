<?php

namespace App\Http\Controllers;

use App\Mail\MemberQRCodeMail;
use App\Models\Logs;
use App\Models\MemberProfile;
use App\Models\Sales;
use App\Models\Transactions;
use App\Models\User;
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
            ->orderByRaw("
                CASE 
                    WHEN role = 'member' AND EXISTS (
                        SELECT 1 FROM member_profiles 
                        WHERE member_profiles.user_id = users.user_id 
                        AND member_profiles.isApproved = 0
                    ) THEN 1
                    WHEN role = 'member' THEN 2
                    WHEN role = 'staff' THEN 3
                    WHEN role = 'admin' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('created_at', 'desc')
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

    // Staff view - only shows members
    public function viewMembersForStaff(Request $request)
    {
        $search = $request->get('search');
        $statuses = $request->get('user_status', []);

        $users = User::query()
            ->with('member.plan')
            ->where('role', 'member') // Only show members
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

        return view('staff.user-management', compact(
            'users',
            'search',
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

        // Create user with auto-verified email (accounts created by admin/staff are trusted)
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'] ?? 'member',
            'status' => 'active',
            'email_verified_at' => now(), // Auto-verify accounts created by admin/staff
        ]);

        // Create member profile if applicable
        if ($user->role === 'member' && ($request->has('plan_id') || $request->has('sex'))) {
            $plan = \App\Models\MembershipPlan::find($validated['plan_id']);

            $memberProfile = MemberProfile::create([
                'user_id' => $user->user_id,
                'plan_id' => $validated['plan_id'] ?? null,
                'sex' => $validated['sex'] ?? null,
                'birthday' => $validated['birthday'] ?? null,
                'height' => $validated['height'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'mobile_number' => $validated['mobile_number'] ?? null,
                'status' => 'active',
                'isApproved' => true,
                'start_date' => now(),
                'end_date' => $plan ? now()->addDays($plan->duration_days) : null,
            ]);

            // Generate and send QR code if plan exists
            if ($plan) {
                $this->generateAndSendQRCode($user, $memberProfile, $plan);
            }
        }

        Logs::create([
            'user_id' => $authUser->user_id,
            'action' => "{$authUser->last_name} added a new user: {$validated['last_name']}.",
            'timestamp' => now(),
        ]);

        $successMessage = 'User created successfully.';
        if ($user->role === 'member' && $request->has('plan_id')) {
            $successMessage .= ' QR code sent to ' . $user->email;
        }

        return redirect()->back()->with('success', $successMessage);
    }

    public function show(string $id)
    {
        $user = User::with(['member.plan', 'logs'])->findOrFail($id);

        // Authorization check for staff
        if (Auth::user()->role === 'staff' && $user->role !== 'member') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($user);
    }

    public function edit($id)
    {
        $user = User::with('member')->findOrFail($id);

        // Authorization check for staff
        if (Auth::user()->role === 'staff' && $user->role !== 'member') {
            return response()->json(['error' => 'You can only edit members'], 403);
        }

        $response = [
            'user_id' => $user->user_id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'status' => $user->status,
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
        $user = User::findOrFail($id);
        $previousRole = $user->role;

        // Authorization check for staff
        if (Auth::user()->role === 'staff' && $user->role !== 'member') {
            return redirect()->back()->with('error', 'You can only edit members');
        }

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

        // Ensure email_verified_at is set for updated accounts
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
        }

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
                $plan = \App\Models\MembershipPlan::find($validated['plan_id']);

                $memberProfile = MemberProfile::create(array_merge($memberData, [
                    'user_id' => $user->user_id,
                    'status' => 'active',
                    'isApproved' => true,
                    'start_date' => now(),
                    'end_date' => $plan ? now()->addDays($plan->duration_days) : null,
                ]));

                if ($previousRole !== 'member' && $plan) {
                    $this->generateAndSendQRCode($user, $memberProfile, $plan);
                }
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

        return redirect()->back()->with('success', 'User updated successfully');
    }

    public function destroy(string $id)
    {
        $currentUser = Auth::user();
        $userToDelete = User::where('user_id', $id)->firstOrFail();

        // Authorization check for staff
        if ($currentUser->role === 'staff' && $userToDelete->role !== 'member') {
            return redirect()->back()->with('error', 'You can only delete members');
        }

        if ($userToDelete->role === 'admin') {
            return redirect()->back()
                ->with('error', 'Admin cannot be deleted.');
        }

        if ($userToDelete->user_id === $currentUser->user_id) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

        Logs::create([
            'user_id' => $currentUser->user_id,
            'action' => "{$currentUser->last_name} deleted user: {$userToDelete->last_name}.",
            'timestamp' => now(),
        ]);

        $userToDelete->delete();

        return redirect()->back()
            ->with('success', 'User deleted successfully');
    }

    public function approve($memberId)
    {
        try {
            $member = MemberProfile::findOrFail($memberId);
            $currentUser = Auth::user();
            $user = $member->user;
            $plan = $member->plan;

            if (!$plan) {
                return redirect()->back()
                    ->with('error', 'Cannot approve: Member has no membership plan assigned.');
            }

            $paymentMethod = request()->query('payment', 'cash');
            $referenceCode = request()->query('reference', null);
            $isRenewal = $member->renewal_pending;

            // Validate reference code for GCash payments
            if ($paymentMethod === 'gcash' && empty($referenceCode)) {
                return redirect()->back()
                    ->with('error', 'GCash reference code is required for GCash payments.');
            }

            $member->update([
                'isApproved' => true,
                'isDisabled' => false,
                'status' => 'active',
                'approved_at' => now(),
                'renewal_pending' => false,
                'start_date' => now(),
                'end_date' => now()->addDays($plan->duration_days),
                'suspended_at' => null,
                'days_remaining_before_suspend' => null,
            ]);

            $member->refresh();

            $this->generateAndSendQRCode($user, $member, $plan);

            $sale = Sales::create([
                'user_id' => $currentUser->user_id,
                'total_amount' => $plan->price,
                'tax' => 0,
                'discount' => 0,
                'payment_method' => $paymentMethod,
                'reference_code' => $referenceCode,
                'status' => 'paid',
                'type' => 'memberships',
            ]);

            $sale->items()->create([
                'plan_id' => $plan->plan_id,
                'product_id' => null,
                'quantity' => 1,
                'price' => $plan->price,
                'sub_total' => $plan->price,
            ]);

            $actionType = $isRenewal ? 'Approved renewal' : 'Approved membership';

            Transactions::create([
                'sales_id' => $sale->sales_id,
                'type' => 'memberships',
                'performed_by' => $currentUser->user_id,
                'quantity' => 1,
                'timestamp' => now(),
            ]);

            Logs::create([
                'user_id' => $currentUser->user_id,
                'action' => "{$actionType} for: {$user->first_name} {$user->last_name} - Plan: {$plan->name} - Payment: {$paymentMethod}",
                'timestamp' => now(),
            ]);

            $message = $isRenewal
                ? "Renewal approved! QR code sent to {$user->email}."
                : "Member approved! QR code sent to {$user->email}.";

            return redirect()->back()
                ->with('success', $message . " Sale recorded.");
        } catch (\Exception $e) {
            \Log::error("Error during approval", [
                'member_id' => $memberId,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Error approving member: ' . $e->getMessage());
        }
    }

    public function deny($memberId)
    {
        $member = MemberProfile::findOrFail($memberId);

        $member->update([
            'isApproved' => false,
            'isDisabled' => true,
        ]);

        return redirect()->back()
            ->with('success', 'Member access denied.');
    }

    private function generateAndSendQRCode($user, $memberProfile, $plan)
    {
        if (!$plan) {
            throw new \Exception("QR Code generation failed: No plan provided");
        }

        try {
            $qrData = [
                'member_id' => $memberProfile->member_id,
                'name' => "{$user->first_name} {$user->last_name}",
                'email' => $user->email,
                'plan' => $plan->name,
                'price' => $plan->price,
                'start_date' => $memberProfile->start_date,
                'end_date' => $memberProfile->end_date,
            ];

            $qrText = json_encode($qrData);
            $qrRelativePath = "qr/member_{$user->user_id}.png";
            $fullPath = storage_path("app/public/{$qrRelativePath}");

            $directory = dirname($fullPath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($qrText)
                ->encoding(new Encoding('UTF-8'))
                ->size(300)
                ->margin(10)
                ->build();

            $result->saveToFile($fullPath);

            if (!file_exists($fullPath)) {
                throw new \Exception("QR code file was not created");
            }

            $fileSize = filesize($fullPath);
            if ($fileSize === 0) {
                throw new \Exception("QR code file is empty");
            }

            $memberProfile->qr_code = $qrRelativePath;
            $memberProfile->save();

            \Log::info("QR Code generated successfully", [
                'path' => $qrRelativePath,
                'file_size' => $fileSize
            ]);

            try {
                Mail::to($user->email)->send(new MemberQRCodeMail($memberProfile, $fullPath));
                \Log::info("QR Code email sent to: {$user->email}");
            } catch (\Exception $e) {
                \Log::error("Failed to send QR code email: " . $e->getMessage());
            }
        } catch (\Exception $e) {
            \Log::error("QR Code generation failed", [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function suspendMember($memberId)
    {
        try {
            $member = MemberProfile::findOrFail($memberId);
            $user = $member->user;

            $member->update([
                'status' => 'expired',
                'suspended_at' => now(),
                'days_remaining_before_suspend' => $member->end_date
                    ? max(0, now()->diffInDays($member->end_date, false))
                    : 0,
            ]);

            Logs::create([
                'user_id' => Auth::id(),
                'action' => "Suspended membership for: {$user->first_name} {$user->last_name}",
                'timestamp' => now(),
            ]);

            return redirect()->back()
                ->with('success', "Member {$user->first_name} {$user->last_name} has been suspended.");
        } catch (\Exception $e) {
            \Log::error("Error suspending member", [
                'member_id' => $memberId,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Error suspending member: ' . $e->getMessage());
        }
    }

    public function reactivateMember($memberId)
    {
        try {
            $member = MemberProfile::findOrFail($memberId);
            $user = $member->user;

            if ($member->days_remaining_before_suspend > 0) {
                $newEndDate = now()->addDays($member->days_remaining_before_suspend);
            } else {
                $newEndDate = $member->end_date;
            }

            $member->update([
                'status' => 'active',
                'suspended_at' => null,
                'end_date' => $newEndDate,
                'days_remaining_before_suspend' => null,
            ]);

            Logs::create([
                'user_id' => Auth::id(),
                'action' => "Reactivated membership for: {$user->first_name} {$user->last_name}",
                'timestamp' => now(),
            ]);

            return redirect()->back()
                ->with('success', "Member {$user->first_name} {$user->last_name} has been reactivated.");
        } catch (\Exception $e) {
            \Log::error("Error reactivating member", [
                'member_id' => $memberId,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Error reactivating member: ' . $e->getMessage());
        }
    }
}
