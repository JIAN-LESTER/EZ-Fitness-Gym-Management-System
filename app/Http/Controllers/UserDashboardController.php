<?php

namespace App\Http\Controllers;

use App\Models\MemberProfile;
use App\Models\MembershipPlan;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get user's member profile
        $memberProfile = MemberProfile::with('plan')
            ->where('user_id', $user->user_id)
            ->first();

        // Get current gym occupancy
        $currentOccupancy = Attendance::whereDate('check_in_time', Carbon::today())
            ->where('status', 'checked_in')
            ->count();

        // Calculate days left on membership (if exists)
        $daysLeft = null;
        $membershipStatus = null;
        $isExpiringSoon = false;
        
        if ($memberProfile) {
            $daysLeft = $memberProfile->daysRemaining();
            
            if ($memberProfile->isExpired()) {
                $membershipStatus = 'expired';
            } elseif ($memberProfile->isExpiringSoon(7)) {
                $membershipStatus = 'expiring_soon';
                $isExpiringSoon = true;
            } else {
                $membershipStatus = 'active';
            }
        }

        // Get all available membership plans (introductory plans list)
        $membershipPlans = MembershipPlan::orderBy('price', 'asc')->get();

        // Get user's attendance history (last 10 check-ins)
        $recentAttendance = Attendance::where('member_id', $memberProfile?->member_id)
            ->orderBy('check_in_time', 'desc')
            ->limit(10)
            ->get();

        // Calculate attendance statistics
        $totalCheckIns = Attendance::where('member_id', $memberProfile?->member_id)->count();
        $thisMonthCheckIns = Attendance::where('member_id', $memberProfile?->member_id)
            ->whereMonth('check_in_time', Carbon::now()->month)
            ->whereYear('check_in_time', Carbon::now()->year)
            ->count();

        return view('member.dashboard', compact(
            'memberProfile',
            'currentOccupancy',
            'daysLeft',
            'membershipStatus',
            'isExpiringSoon',
            'membershipPlans',
            'recentAttendance',
            'totalCheckIns',
            'thisMonthCheckIns'
        ));
    }
}