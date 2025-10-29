<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Auth;
use Illuminate\Http\Request;
use App\Models\MembershipPlan;

class MembershipPlanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $plans = MembershipPlan::withCount('members')->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('price', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->appends($request->query());

        return view('admin.plan-management', compact('plans', 'search'));
    }

    public function store(Request $request)
    {
     
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:membership_plans',
            'details' => 'nullable|string|max:255|',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
        ],[
            'name.required' => 'Plan name is required',
            'name.unique' => 'The plan name has already been taken',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a valid number',
            'duration_days.required' => 'Duration (in days) is required',
        ]);

             $authUser = Auth::user();
             Logs::create([
            'user_id' => $authUser->user_id,
            'action' => "{$authUser->last_name} added a new plan: {$validated['name']}.",
            'timestamp' => now(),
        ]);


        MembershipPlan::create($validated);

      
        return redirect()->route('admin.plan_management')->with('success', 'Plan created successfully.');
    }

    public function update(Request $request, $id)
    {
        $plan = MembershipPlan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:membership_plans,name,' . $id . ',plan_id',
            'details' => 'nullable|string|max:255|',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
        ],[
            'name.required' => 'Plan name is required',
            'name.unique' => 'The plan name has already been taken',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a valid number',
            'duration_days.required' => 'Duration (in days) is required',
        ]);

                      $authUser = Auth::user();
        Logs::create([
            'user_id' => $authUser->user_id,
            'action' => "{$authUser->last_name} updated a plan: {$validated['name']}.",
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
