<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class CheckMembershipExpiration
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->role === 'member' && $user->member) {
            $member = $user->member;

            // Check if membership has expired
            if ($member->end_date && Carbon::parse($member->end_date)->isPast() && ! $member->renewal_pending) {
                // Flag for renewal
                $member->update(['renewal_pending' => true]);

                // Show renewal modal
                session()->flash('membership_expired', true);
            }
        }

        return $next($request);
    }
}
