<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                    'error' => 'You must be logged in to access this resource.',
                ], 401);
            }
            abort(403, 'Authentication required - Please login to access this resource.');
        }

        $user = auth()->user();

        // Allow both admin and super_admin roles
        if (! in_array($user->role, ['admin', 'super_admin'])) {
            $message = 'Access Denied - This area is restricted to administrators only. Your current role: '.ucfirst($user->role);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access Denied',
                    'error' => $message,
                ], 403);
            }

            abort(403, $message);
        }

        return $next($request);
    }
}
