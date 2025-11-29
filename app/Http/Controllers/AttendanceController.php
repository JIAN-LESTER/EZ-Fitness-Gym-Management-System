<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\MemberProfile;
use App\Models\Logs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Show QR Scanner Page (Admin/Staff only)
     */
    public function scanner()
    {
        $user = Auth::user();

        // Allow both admin and staff to access scanner
        if (!in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'Unauthorized access. Only admin and staff can access the scanner.');
        }

        // Get today's check-ins for display
        $todayAttendances = Attendance::with(['member.user', 'member.plan'])
            ->whereDate('check_in_time', Carbon::today())
            ->orderBy('check_in_time', 'desc')
            ->get();

        return view('attendance.scanner', compact('todayAttendances'));
    }

    /**
     * Process QR Code Scan and Log Attendance
     */
    public function scan(Request $request)
    {
        // Check if user is admin or staff
        $currentUser = Auth::user();
        if (!in_array($currentUser->role, ['admin', 'staff'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admin and staff can scan QR codes.'
            ], 403);
        }

        try {
            $qrData = json_decode($request->qr_data, true);

            if (!$qrData || !isset($qrData['email'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code format'
                ], 400);
            }

            // Find user by email from QR
            $user = User::where('email', $qrData['email'])->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member not found'
                ], 404);
            }

            // Get member profile
            $memberProfile = MemberProfile::where('user_id', $user->user_id)->first();

            if (!$memberProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member profile not found'
                ], 404);
            }

            // Check if member is active
            if ($memberProfile->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Member account is not active'
                ], 403);
            }

            // Check if already checked in today
            $today = Carbon::today();
            $existingAttendance = Attendance::where('member_id', $memberProfile->member_id)
                ->whereDate('check_in_time', $today)
                ->first();

            // If already checked in, process check-out
            if ($existingAttendance) {
                // Check if already checked out
                if ($existingAttendance->check_out_time) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Already checked out today at ' . Carbon::parse($existingAttendance->check_out_time)->format('h:i A'),
                        'member' => [
                            'name' => $user->first_name . ' ' . $user->last_name,
                            'plan' => $memberProfile->plan->name ?? 'N/A',
                        ]
                    ], 409);
                }

                // Process check-out
                $checkOutTime = now();
                $checkInTime = Carbon::parse($existingAttendance->check_in_time);
                $duration = $checkInTime->diffInMinutes($checkOutTime);

                $existingAttendance->update([
                    'check_out_time' => $checkOutTime,
                    'duration' => $duration,
                    'status' => 'checked_out',
                ]);

                // Log the check-out action
                Logs::create([
                    'user_id' => $user->user_id,
                    'action' => "Member checked out: {$user->first_name} {$user->last_name} (Scanned by: {$currentUser->role} - {$currentUser->first_name} {$currentUser->last_name})",
                    'timestamp' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'action' => 'checkout',
                    'message' => 'Check-out successful!',
                    'member' => [
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'email' => $user->email,
                        'plan' => $memberProfile->plan->name ?? 'N/A',
                        'check_in_time' => $checkInTime->format('M d, Y h:i A'),
                        'check_out_time' => $checkOutTime->format('M d, Y h:i A'),
                        'duration' => $this->formatDuration($duration),
                    ]
                ]);
            }

            // Create new attendance record
            $attendance = Attendance::create([
                'member_id' => $memberProfile->member_id,
                'check_in_time' => now(),
                'status' => 'checked_in',
            ]);

            // Log the action
            Logs::create([
                'user_id' => $user->user_id,
                'action' => "Member checked in: {$user->first_name} {$user->last_name}",
                'timestamp' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-in successful!',
                'member' => [
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'plan' => $memberProfile->plan->name ?? 'N/A',
                    'check_in_time' => $attendance->check_in_time->format('M d, Y h:i A'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get today's attendance records (AJAX endpoint for live updates)
     */
    public function getTodayAttendance()
    {
        $currentUser = Auth::user();
        if (!in_array($currentUser->role, ['admin', 'staff'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $attendances = Attendance::with(['member.user', 'member.plan'])
            ->whereDate('check_in_time', Carbon::today())
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
                    'name' => $attendance->member->user->first_name . ' ' . $attendance->member->user->last_name,
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
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        $query = Attendance::with(['member.user', 'member.plan'])
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
        $memberProfile = MemberProfile::where('user_id', $user->user_id)->first();

        if (!$memberProfile) {
            return redirect()->back()->with('error', 'Member profile not found');
        }

        $attendances = Attendance::where('member_id', $memberProfile->member_id)
            ->orderBy('check_in_time', 'desc')
            ->paginate(20);

        return view('attendance.member_logs', compact('attendances'));
    }
}
