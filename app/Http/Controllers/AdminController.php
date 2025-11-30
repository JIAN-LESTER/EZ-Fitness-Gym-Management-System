<?php

namespace App\Http\Controllers;

use App\Models\MemberProfile;
use App\Models\MembershipPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    public function dashboard()
    {
        $member = Auth::user();
        $plans = MembershipPlan::all();
        $memberProfile = MemberProfile::where('user_id', $member->user_id)->first();

        return view('admin.dashboard', compact('member', 'plans', 'memberProfile'));
    }
}
