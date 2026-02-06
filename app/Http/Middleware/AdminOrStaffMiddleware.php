<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrStaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized - Authentication required');
        }

        $user = auth()->user();
        
        if (!in_array($user->role, ['admin', 'super_admin', 'staff'])) {
            abort(403, 'Unauthorized - Admin or Staff access only');
        }

        return $next($request);
    }
}
