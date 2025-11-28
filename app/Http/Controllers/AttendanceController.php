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
        // Only admin/staff can access
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access');
        }

        return view('attendance.scanner');
    }

    /**
     * Process QR Code Scan and Log Attendance
     */
    public function scan(Request $request)
    {
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

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already checked in today at ' . $existingAttendance->check_in_time->format('h:i A'),
                    'member' => [
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'plan' => $memberProfile->plan->name ?? 'N/A',
                    ]
                ], 409);
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

        // AJAX request for scanner page
        if ($request->ajax()) {
            $checkins = $query->whereDate('check_in_time', today())->get()->map(function ($attendance) {
                return [
                    'name' => $attendance->member->user->first_name . ' ' . $attendance->member->user->last_name,
                    'email' => $attendance->member->user->email,
                    'plan' => $attendance->member->plan->name ?? 'N/A',
                    'check_in_time' => $attendance->check_in_time->format('g:i A'),
                    'status' => $attendance->status,
                ];
            });

            return response()->json([
                'checkins' => $checkins
            ]);
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

    /**
     * Get today's check-ins for scanner page (AJAX)
     */
    public function todayCheckins(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $todayCheckins = Attendance::with(['member.user', 'member.plan'])
            ->whereDate('check_in_time', today())
            ->orderBy('check_in_time', 'desc')
            ->get()
            ->map(function ($attendance) {
                return [
                    'name' => $attendance->member->user->first_name . ' ' . $attendance->member->user->last_name,
                    'email' => $attendance->member->user->email,
                    'plan' => $attendance->member->plan->name ?? 'N/A',
                    'check_in_time' => $attendance->check_in_time->format('g:i A'),
                    'check_out_time' => $attendance->check_out_time ? $attendance->check_out_time->format('g:i A') : null,
                    'duration' => $attendance->duration ? $this->formatDuration($attendance->duration) : null,
                    'status' => $attendance->check_out_time ? 'check_out' : 'check_in',
                ];
            });

        return response()->json([
            'checkins' => $todayCheckins
        ]);
    }

    /**
     * Format duration in human-readable format (whole numbers only)
     */
    private function formatDuration($minutes)
    {
        // Ensure we're working with a whole number
        $minutes = (int) $minutes;

        if ($minutes < 1) {
            return '0 minutes';
        }

        if ($minutes < 60) {
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return $hours . ' hour' . ($hours > 1 ? 's' : '');
        }

        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ' . $remainingMinutes . ' minute' . ($remainingMinutes > 1 ? 's' : '');
    }

    /**
     * Process Check-out
     */
    /**
     * Process QR Code Check-out
     */
    public function checkout(Request $request)
    {
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

            // Find today's check-in that hasn't been checked out
            $today = Carbon::today();
            $attendance = Attendance::where('member_id', $memberProfile->member_id)
                ->whereDate('check_in_time', $today)
                ->whereNull('check_out_time')
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active check-in found for today'
                ], 404);
            }

            // Update check-out time and calculate duration
            $checkOutTime = now();

            // CORRECTED: check_out_time - check_in_time = duration
            $duration = $checkOutTime->diffInMinutes($attendance->check_in_time);

            // Ensure it's a whole number
            $duration = (int) $duration;

            $attendance->update([
                'check_out_time' => $checkOutTime,
                'status' => 'checked_out',
                'duration' => $duration,
            ]);

            // Log the action
            Logs::create([
                'user_id' => $user->user_id,
                'action' => "Member checked out: {$user->first_name} {$user->last_name}",
                'timestamp' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-out successful!',
                'member' => [
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                    'check_in_time' => $attendance->check_in_time->format('h:i A'),
                    'check_out_time' => $checkOutTime->format('h:i A'),
                    'duration' => $this->formatDuration($duration),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing check-out: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Auto Check-out at End of Day (Scheduled Task)
     */
    public function autoCheckout()
    {
        $today = Carbon::today();
        $uncheckedOut = Attendance::whereDate('check_in_time', $today)
            ->whereNull('check_out_time')
            ->get();

        foreach ($uncheckedOut as $attendance) {
            $checkOutTime = Carbon::today()->setHour(23)->setMinute(59)->setSecond(59);
            $duration = $checkOutTime->diffInMinutes($attendance->check_in_time);

            $attendance->update([
                'check_out_time' => $checkOutTime,
                'status' => 'auto_checked_out',
                'duration' => $duration,
            ]);
        }

        return response()->json(['message' => 'Auto check-out completed']);
    }
}
