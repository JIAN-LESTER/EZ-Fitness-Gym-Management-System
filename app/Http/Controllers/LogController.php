<?php

namespace App\Http\Controllers;

use App\Models\Logs;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Log;

class LogController extends Controller
{
    public function viewLogs(Request $request)
{
    $currentUser = Auth::user();
    $search = $request->get('search');
    $filter = $request->get('filter', 'action');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    // Determine branch filter
    $branchId = null;
    if ($currentUser->role === 'super_admin') {
        $branchId = session('selected_branch_id');
    } else {
        $branchId = $currentUser->branch_id;
    }

    $logs = Logs::query()->with('user');

    if ($search) {
        if ($filter === 'user') {
            $logs->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%");
            });
        } elseif ($filter === 'action') {
            $logs->where('action', 'like', "%{$search}%");
        } else {
            $logs->where(function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%");
                })
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('created_at', 'like', "%{$search}%");
            });
        }
    }

    if ($startDate) {
        $logs->whereDate('created_at', '>=', $startDate);
    }

    if ($endDate) {
        $logs->whereDate('created_at', '<=', $endDate);
    }

  
    if ($branchId) {
        $logs->whereHas('user', function ($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        });
    }

    $logs = $logs->orderBy('created_at', 'desc')->paginate(12)->appends($request->query());

    return view('admin.logs', compact('logs', 'search', 'filter'));
}
}
