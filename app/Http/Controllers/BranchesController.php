<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use App\Models\Branches;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BranchesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $branches = Branches::withCount(['users', 'subscriptions'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.branches', compact('branches', 'search'));
    }

    /**
     * Show the form for editing the specified branch.
     * Returns JSON for AJAX requests.
     */
    public function edit($branch_id)
    {
        $branch = Branches::where('branch_id', $branch_id)->firstOrFail();
        
        // Return JSON for AJAX requests
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($branch);
        }
        
        // Otherwise return view (if needed)
        return view('admin.branches-edit', compact('branch'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:branches',
            'country' => 'required|string|max:100',
            'region' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'street' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Branch name is required',
            'name.unique' => 'The branch name has already been taken',
            'country.required' => 'Country is required',
            'region.required' => 'Region is required',
            'province.required' => 'Province is required',
            'city.required' => 'City/Municipality is required',
            'street.required' => 'Street name is required',
        ]);

        // Combine address parts into single address field
        $addressParts = array_filter([
            $validated['building'] ?? null,
            $validated['street'],
            $validated['city'],
            $validated['province'],
            $validated['region']
        ]);
        
        $address = implode(', ', $addressParts);

        $authUser = Auth::user();
        $branchId = $authUser->role === 'super_admin'
                ? session('selected_branch_id')
                : $authUser->branch_id;

        Logs::create([
            'user_id' => $authUser->user_id,
            'branch_id' => $branchId,
            'action' => "{$authUser->last_name} added a new branch: {$validated['name']}.",
            'timestamp' => now(),
        ]);

        Branches::create([
            'name' => $validated['name'],
            'address' => $address,
        ]);

        return redirect()->route('admin.branch_management')->with('success', 'Branch created successfully.');
    }

    public function update(Request $request, $branch_id)
    {
        // Use where() instead of findOrFail() since primary key is branch_id, not id
        $branch = Branches::where('branch_id', $branch_id)->firstOrFail();

        $validated = $request->validate([
            // Updated to specify branch_id as the column to ignore during unique check
            'name' => 'required|string|max:255|unique:branches,name,' . $branch_id . ',branch_id',
            'country' => 'required|string|max:100',
            'region' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'street' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Branch name is required',
            'name.unique' => 'The branch name has already been taken',
            'country.required' => 'Country is required',
            'region.required' => 'Region is required',
            'province.required' => 'Province is required',
            'city.required' => 'City/Municipality is required',
            'street.required' => 'Street name is required',
        ]);

        // Combine address parts into single address field
        $addressParts = array_filter([
            $validated['building'] ?? null,
            $validated['street'],
            $validated['city'],
            $validated['province'],
            $validated['region']
        ]);
        
        $address = implode(', ', $addressParts);

        $authUser = Auth::user();
        $branchId = $authUser->role === 'super_admin'
                ? session('selected_branch_id')
                : $authUser->branch_id;

        Logs::create([
            'user_id' => $authUser->user_id,
            'branch_id'=> $branchId,
            'action' => "{$authUser->last_name} updated a branch: {$validated['name']}.",
            'timestamp' => now(),
        ]);

        $branch->update([
            'name' => $validated['name'],
            'address' => $address,
        ]);

        return redirect()->route('admin.branch_management')->with('success', 'Branch updated successfully.');
    }

    public function destroy($branch_id)
    {
        // Use where() instead of findOrFail() since primary key is branch_id, not id
        $branch = Branches::where('branch_id', $branch_id)->firstOrFail();

        // Check if branch has associated data
        $hasUsers = $branch->users()->count() > 0;
        $hasSubscriptions = $branch->subscriptions()->count() > 0;

        if ($hasUsers || $hasSubscriptions) {
            return redirect()->route('admin.branch_management')
                ->with('error', 'Cannot delete branch with associated users or subscriptions.');
        }

        $authUser = Auth::user();
        $branchId = $authUser->role === 'super_admin'
                ? session('selected_branch_id')
                : $authUser->branch_id;

        Logs::create([
            'user_id' => $authUser->user_id,
            'branch_id' => $branchId,
            'action' => "{$authUser->last_name} deleted a branch: {$branch->name}.",
            'timestamp' => now(),
        ]);

        $branch->delete();

        return redirect()->route('admin.branch_management')->with('success', 'Branch deleted successfully.');
    }

    public function selectBranch(Request $request)
    {
        if (auth()->user()->role !== 'super_admin') {
            return back()->with('error', 'Unauthorized access');
        }

        $request->validate([
            'branch_id' => 'required|string'
        ]);

        if ($request->branch_id === 'all') {
            session()->forget(['selected_branch_id', 'selected_branch_name']);
            return back()->with('success', 'Viewing all branches');
        }

        $branch = Branches::where('branch_id', $request->branch_id)->first();

        if (!$branch) {
            return back()->with('error', 'Branch not found or inactive');
        }

        session([
            'selected_branch_id' => $branch->branch_id,
            'selected_branch_name' => $branch->name
        ]);

        return back()->with('success', 'Switched to ' . $branch->name);
    }

    protected function getSelectedBranchId()
    {
        return session('selected_branch_id');
    }

    protected function applyBranchFilter($query, $column = 'branch_id')
    {
        $branchId = $this->getSelectedBranchId();
        
        if ($branchId) {
            $query->where($column, $branchId);
        }
        
        return $query;
    }
}