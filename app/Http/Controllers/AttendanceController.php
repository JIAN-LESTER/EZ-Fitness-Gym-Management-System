<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Logs;
use App\Models\MemberProfile;
use App\Models\MembershipPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Get branch ID based on user role
     */
    private function getBranchId()
    {
        $currentUser = Auth::user();

        if ($currentUser->role === 'super_admin') {
            return session('selected_branch_id');
        }

        return $currentUser->branch_id;
    }

    /**
     * Show QR Scanner Page (Admin/Staff only)
     */
    public function scanner()
    {
        $user = Auth::user();

        if (! in_array($user->role, ['admin', 'staff', 'super_admin'])) {
            abort(403, 'Unauthorized access. Only admin and staff can access the scanner.');
        }

        $branchId = $this->getBranchId();

        // Get today's check-ins for display - filtered by branch
        $todayAttendances = Attendance::with(['member.user', 'member.plan'])
            ->whereDate('check_in_time', Carbon::today('Asia/Manila'))
            ->when($branchId, function ($query) use ($branchId) {
                return $query->whereHas('member.user', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            })
            ->orderBy('check_in_time', 'desc')
            ->get();

        return view('admin.scanner', compact('todayAttendances'));
    }

    /**
     * Process QR Code Scan and Log Attendance
     */
    public function scan(Request $request)
    {
        $currentUser = Auth::user();
        if (! in_array($currentUser->role, ['admin', 'staff', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admin and staff can scan QR codes.',
            ], 403);
        }

        try {
            $qrData = json_decode($request->qr_data, true);

            if (! $qrData || ! isset($qrData['email'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code format',
                ], 400);
            }

            $user = User::where('email', $qrData['email'])->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found',
                ], 404);
            }

            // **BRANCH VALIDATION**
            $branchId = $this->getBranchId();
            if ($branchId && $user->branch_id != $branchId) {
                return response()->json([
                    'success' => false,
                    'message' => 'This member belongs to a different branch',
                ], 403);
            }

            $memberProfile = MemberProfile::where('user_id', $user->user_id)->first();

            if (! $memberProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member profile not found',
                ], 404);
            }

            if ($memberProfile->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Member account is not active',
                ], 403);
            }

            $today = Carbon::today('Asia/Manila');
            $existingAttendance = Attendance::where('member_id', $memberProfile->member_id)
                ->whereDate('check_in_time', $today)
                ->whereNull('check_out_time')
                ->first();

            if ($existingAttendance) {
                // Process check-out
                $checkOutTime = Carbon::now('Asia/Manila');
                $checkInTime = Carbon::parse($existingAttendance->check_in_time);
                $duration = $checkInTime->diffInMinutes($checkOutTime);

                $existingAttendance->update([
                    'check_out_time' => $checkOutTime,
                    'duration' => $duration,
                    'status' => 'checked_out',
                ]);

                Logs::create([
                    'user_id' => $user->user_id,
                    'branch_id' => $branchId,
                    'action' => "Member checked out: {$user->first_name} {$user->last_name} at {$checkOutTime->format('h:i A')} - Duration: {$this->formatDuration($duration)} (Scanned by: {$currentUser->role} - {$currentUser->first_name} {$currentUser->last_name})",
                    'timestamp' => $checkOutTime,
                ]);

                return response()->json([
                    'success' => true,
                    'action' => 'checkout',
                    'message' => 'Check-out successful!',
                    'member' => [
                        'name' => $user->first_name.' '.$user->last_name,
                        'email' => $user->email,
                        'plan' => $memberProfile->plan->name ?? 'N/A',
                        'check_in_time' => $checkInTime->format('M d, Y h:i A'),
                        'check_out_time' => $checkOutTime->format('M d, Y h:i A'),
                        'duration' => $this->formatDuration($duration),
                    ],
                ]);
            }

            // Create new attendance record
            $checkInTime = Carbon::now('Asia/Manila');
            $attendance = Attendance::create([
                'member_id' => $memberProfile->member_id,
                'branch_id' => $branchId,
                'check_in_time' => $checkInTime,
                'status' => 'checked_in',
            ]);

            Logs::create([
                'user_id' => $user->user_id,
                'branch_id' => $branchId,
                'action' => "Member checked in: {$user->first_name} {$user->last_name} at {$checkInTime->format('h:i A')} (Scanned by: {$currentUser->role} - {$currentUser->first_name} {$currentUser->last_name})",
                'timestamp' => $checkInTime,
            ]);

            return response()->json([
                'success' => true,
                'action' => 'checkin',
                'message' => 'Check-in successful!',
                'member' => [
                    'name' => $user->first_name.' '.$user->last_name,
                    'email' => $user->email,
                    'plan' => $memberProfile->plan->name ?? 'N/A',
                    'check_in_time' => $attendance->check_in_time->format('M d, Y h:i A'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing QR code: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get today's attendance records (AJAX endpoint for live updates)
     */
    public function getTodayAttendance()
    {
        $currentUser = Auth::user();
        if (! in_array($currentUser->role, ['admin', 'staff', 'super_admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $branchId = $this->getBranchId();

        $attendances = Attendance::with(['member.user', 'member.plan'])
            ->whereDate('check_in_time', Carbon::today('Asia/Manila'))
            ->when($branchId, function ($query) use ($branchId) {
                return $query->whereHas('member.user', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            })
            ->orderBy('check_in_time', 'desc')
            ->get()
            ->map(function ($attendance) {
                $checkInTime = Carbon::parse($attendance->check_in_time);
                $duration = null;

                if ($attendance->check_out_time) {
                    $checkOutTime = Carbon::parse($attendance->check_out_time);
                    $duration = $this->formatDuration($attendance->duration);
                }

                return [
                    'name' => $attendance->member->user->first_name.' '.$attendance->member->user->last_name,
                    'plan' => $attendance->member->plan->name ?? 'N/A',
                    'check_in_time' => $checkInTime->format('h:i A'),
                    'check_out_time' => $attendance->check_out_time ? Carbon::parse($attendance->check_out_time)->format('h:i A') : null,
                    'duration' => $duration,
                    'status' => $attendance->status,
                ];
            });

        return response()->json($attendances);
    }

    /**
     * Format duration in hours and minutes
     */
    private function formatDuration($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }

        return "{$mins}m";
    }

    /**
     * Show All Attendance Logs (Admin only)
     */
    public function adminLogs(Request $request)
    {
        $currentUser = Auth::user();

        if (! in_array($currentUser->role, ['admin', 'super_admin'])) {
            abort(403, 'Unauthorized access');
        }

        $branchId = $this->getBranchId();

        $query = Attendance::with(['member.user', 'member.plan'])
            ->when($branchId, function ($query) use ($branchId) {
                return $query->whereHas('member.user', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            })
            ->orderBy('check_in_time', 'desc');

        // Filter by date if provided
        if ($request->has('date') && $request->date) {
            $query->whereDate('check_in_time', $request->date);
        }

        // Filter by search (member name)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('member.user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        $attendances = $query->paginate(20);

        return view('attendance.admin_logs', compact('attendances'));
    }

    /**
     * Show Member's Own Attendance Logs
     */
    public function memberLogs()
    {
        $user = Auth::user();
        $memberProfile = MemberProfile::with(['plan', 'subscription'])
            ->where('user_id', $user->user_id)
            ->first();

        if (! $memberProfile) {
            return redirect()->back()->with('error', 'Member profile not found');
        }

        // Get paginated attendances with null check for check_in_time
        $attendances = Attendance::where('member_id', $memberProfile->member_id)
            ->whereNotNull('check_in_time')
            ->orderBy('check_in_time', 'desc')
            ->paginate(20);

        // Calculate statistics from all attendances (not paginated)
        $allAttendances = Attendance::where('member_id', $memberProfile->member_id)
            ->whereNotNull('check_in_time')
            ->orderBy('check_in_time', 'desc')
            ->get();

        // Calculate this month's count
        $thisMonthCount = 0;
        try {
            $thisMonthCount = $allAttendances->filter(function ($attendance) {
                return $attendance->check_in_time &&
                       Carbon::parse($attendance->check_in_time)->isCurrentMonth();
            })->count();
        } catch (\Exception $e) {
            $thisMonthCount = 0;
        }

        // Get last check-in (safely)
        $lastCheckIn = $allAttendances->first();

        // Get membership plans for the layout
        $plans = MembershipPlan::all();

        return view('member.member_logs', compact(
            'attendances',
            'memberProfile',
            'plans',
            'thisMonthCount',
            'lastCheckIn'
        ));
    }
}
