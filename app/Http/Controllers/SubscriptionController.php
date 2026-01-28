<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Subscriptions;
use App\Models\Branches;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
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
        $branch_filter = $request->get('branch_id');

        $subscriptions = Subscriptions::query()
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

        $branches = Branches::orderBy('name')->get();

        return view('admin.subscriptions', compact('subscriptions', 'search', 'branches', 'branch_filter'));
    }

    public function store(Request $request)
    {


        $authUser = Auth::user();
                     $branchId = $authUser->role === 'super_admin'
                    ? session('selected_branch_id')
                    : $authUser->branch_id;

        if ($authUser->role === 'super_admin') {

            $validated = $request->validate([
                'branch_id' => 'required|exists:branches,branch_id',
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('subscriptions')->where(function ($query) use ($request) {
                        return $query->where('branch_id', $request->branch_id);
                    }),
                ],
                'details' => 'nullable|string|max:255',
                'price' => 'required|numeric|min:0',
                'duration_days' => 'required|integer|min:1',
            ], [
                'name.required' => 'Subscription name is required',
                'name.unique' => 'This subscription name already exists in this branch',
                'branch_id.required' => 'Branch is required',
                'branch_id.exists' => 'Selected branch does not exist',
                'price.required' => 'Price is required',
                'price.numeric' => 'Price must be a valid number',
                'duration_days.required' => 'Duration (in days) is required',
            ]);

        } else {

            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('subscriptions')->where(function ($query) use ($authUser) {
                        return $query->where('branch_id', $authUser->branch_id);
                    }),
                ],
                'details' => 'nullable|string|max:255',
                'price' => 'required|numeric|min:0',
                'duration_days' => 'required|integer|min:1',
            ], [
                'name.required' => 'Subscription name is required',
                'name.unique' => 'This subscription name already exists in your branch',
                'price.required' => 'Price is required',
                'price.numeric' => 'Price must be a valid number',
                'duration_days.required' => 'Duration (in days) is required',
            ]);

            // Force branch_id for non-super admins
            $validated['branch_id'] = $authUser->branch_id;
        }

        Logs::create([
            'user_id' => $authUser->user_id,
            'branch_id' => $branchId,
            'action' => "{$authUser->last_name} added a new subscription: {$validated['name']}.",
            'timestamp' => now(),
        ]);

        Subscriptions::create($validated);

        return redirect()->route('admin.subscription_management')->with('success', 'Subscription created successfully.');
    }

    public function update(Request $request, $id)
    {
        $subscription = Subscriptions::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,branch_id',
            'details' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
        ], [
            'name.required' => 'Subscription name is required',
            'branch_id.required' => 'Branch is required',
            'branch_id.exists' => 'Selected branch does not exist',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a valid number',
            'duration_days.required' => 'Duration (in days) is required',
        ]);

        $authUser = Auth::user();

                     $branchId = $authUser->role === 'super_admin'
                    ? session('selected_branch_id')
                    : $authUser->branch_id;

        Logs::create([
            'user_id' => $authUser->user_id,
            'branch_id' => $branchId,
            'action' => "{$authUser->last_name} updated a subscription: {$validated['name']}.",
            'timestamp' => now(),
        ]);

        $subscription->update($validated);

        return redirect()->route('admin.subscription_management')->with('success', 'Subscription updated successfully.');
    }

    public function destroy($id)
    {
        $subscription = Subscriptions::findOrFail($id);

        $authUser = Auth::user();

                     $branchId = $authUser->role === 'super_admin'
                    ? session('selected_branch_id')
                    : $authUser->branch_id;

        Logs::create([
            'user_id' => $authUser->user_id,
            'branch_id' => $branchId,
            'action' => "{$authUser->last_name} deleted a subscription: {$subscription->name}.",
            'timestamp' => now(),
        ]);

        $subscription->delete();

        return redirect()->route('admin.subscription_management')->with('success', 'Subscription deleted successfully.');
    }
}