<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class StaffMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
public function handle(Request $request, Closure $next)
{
    if (Auth::check() && Auth::user()->role === 'staff') {
        return $next($request);
    }
    
    if (Auth::check() && Auth::user()->role === 'member') {
        return redirect()->route('member.dashboard');
    }
    
    return redirect('/')->with('error', 'You do not have staff access.');
}
}
