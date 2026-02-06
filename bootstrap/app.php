<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register custom middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'staff' => \App\Http\Middleware\StaffMiddleware::class,
            'admin_or_staff' => \App\Http\Middleware\AdminOrStaffMiddleware::class,
        ]);
    })
    ->withSchedule(function ($schedule): void {
        $schedule->command('member:check-expiration')
            ->hourly()
            ->timezone('Asia/Manila')
            ->name('check-member-expiration-hourly');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Render custom error pages for authorization errors
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'error' => 'You must be logged in to access this resource.'
                ], 401);
            }
            
            return redirect()->route('loginForm')->with('error', 'Please login to access this resource.');
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This action is unauthorized.',
                    'error' => $e->getMessage() ?: 'You don\'t have permission to perform this action.'
                ], 403);
            }
            
            // Render the custom 403 error page
            return response()->view('errors.403', [
                'exception' => $e
            ], 403);
        });

        // Handle HTTP exceptions (like abort(403))
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() === 403) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Forbidden.',
                        'error' => $e->getMessage() ?: 'You don\'t have permission to access this resource.'
                    ], 403);
                }
                
                return response()->view('errors.403', [
                    'exception' => $e
                ], 403);
            }
        });
    })->create();
