<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Branches;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\MembershipPlan;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class MembershipPlanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $currentUser = Auth::user();

        $branchId = null;

        if ($currentUser->role === 'super_admin') {
            // Super admin can see selected branch or all branches
            $branchId = session('selected_branch_id');
        } else {
            // Other roles see only their branch
            $branchId = $currentUser->branch_id;
        }

        $plans = MembershipPlan::query()
            ->withCount('members')
            ->with('branch')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('price', 'like', "%{$search}%");
                });
            })
            ->when($branchId, function ($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->query());

        // Get branches for dropdown (only for forms)
        $branches = Branches::orderBy('name')->get();

        return view('admin.plan-management', compact('plans', 'search', 'branches'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // Determine branch_id
        $branchId = null;
        if ($currentUser->role === 'super_admin') {
            // Super admin can select branch
            $validated = $request->validate([
                'branch_id' => 'required|exists:branches,branch_id',
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('membership_plans')->where(function ($query) use ($request) {
                        return $query->where('branch_id', $request->branch_id);
                    }),
                ],
                'details' => 'nullable|string|max:255',
                'price' => 'required|numeric|min:0',
                'duration_days' => 'required|integer|min:1',
            ], [
                'branch_id.required' => 'Branch is required',
                'name.required' => 'Plan name is required',
                'name.unique' => 'This plan name already exists in this branch',
            ]);

        } else {
            // Other roles use their own branch
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('membership_plans')->where(function ($query) use ($currentUser) {
                        return $query->where('branch_id', $currentUser->branch_id);
                    }),
                ],
                'details' => 'nullable|string|max:255',
                'price' => 'required|numeric|min:0',
                'duration_days' => 'required|integer|min:1',
            ], [
                'name.required' => 'Plan name is required',
                'name.unique' => 'This plan name already exists in your branch',
            ]);


            $validated['branch_id'] = $currentUser->branch_id;
        }

        Logs::create([
            'user_id' => $currentUser->user_id,
            'action' => "{$currentUser->last_name} added a new plan: {$validated['name']}.",
            'timestamp' => now(),
        ]);

        MembershipPlan::create($validated);

        return redirect()->route('admin.plan_management')->with('success', 'Plan created successfully.');
    }

    public function update(Request $request, $id)
    {
        $plan = MembershipPlan::findOrFail($id);
        $currentUser = Auth::user();

        if ($currentUser->role === 'super_admin') {
            $validated = $request->validate([
                'branch_id' => 'required|exists:branches,branch_id',
                'name' => 'required|string|max:255|unique:membership_plans,name,' . $id . ',plan_id',
                'details' => 'nullable|string|max:255',
                'price' => 'required|numeric|min:0',
                'duration_days' => 'required|integer|min:1',
            ], [
                'branch_id.required' => 'Branch is required',
                'name.required' => 'Plan name is required',
                'name.unique' => 'The plan name has already been taken',
                'price.required' => 'Price is required',
                'price.numeric' => 'Price must be a valid number',
                'duration_days.required' => 'Duration (in days) is required',
            ]);
        } else {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:membership_plans,name,' . $id . ',plan_id',
                'details' => 'nullable|string|max:255',
                'price' => 'required|numeric|min:0',
                'duration_days' => 'required|integer|min:1',
            ], [
                'name.required' => 'Plan name is required',
                'name.unique' => 'The plan name has already been taken',
                'price.required' => 'Price is required',
                'price.numeric' => 'Price must be a valid number',
                'duration_days.required' => 'Duration (in days) is required',
            ]);

            $validated['branch_id'] = $currentUser->branch_id;
        }

        Logs::create([
            'user_id' => $currentUser->user_id,
            'action' => "{$currentUser->last_name} updated a plan: {$validated['name']}.",
            'timestamp' => now(),
        ]);

        $plan->update($validated);

        return redirect()->route('admin.plan_management')->with('success', 'Plan updated successfully.');
    }

    public function destroy($id)
    {
        $plan = MembershipPlan::findOrFail($id);

        $authUser = Auth::user();
        Logs::create([
            'user_id' => $authUser->user_id,
            'action' => "{$authUser->last_name} deleted a plan: {$plan['name']}.",
            'timestamp' => now(),
        ]);

        $plan->delete();

        return redirect()->route('admin.plan_management')->with('success', 'Plan deleted successfully.');
    }
}