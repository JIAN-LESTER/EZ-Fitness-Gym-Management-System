<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define role-based gates
        Gate::define('is-admin', function ($user) {
            return in_array($user->role, ['admin', 'super_admin']);
        });

        Gate::define('is-staff', function ($user) {
            return $user->role === 'staff';
        });

        Gate::define('is-admin-or-staff', function ($user) {
            return in_array($user->role, ['admin', 'super_admin', 'staff']);
        });

        Gate::define('is-member', function ($user) {
            return $user->role === 'member';
        });

        Gate::define('is-super-admin', function ($user) {
            return $user->role === 'super_admin';
        });

        // Permission-based gates (you can extend these based on your needs)
        Gate::define('manage-users', function ($user) {
            return in_array($user->role, ['admin', 'super_admin']);
        });

        Gate::define('manage-members', function ($user) {
            return in_array($user->role, ['admin', 'super_admin', 'staff']);
        });

        Gate::define('manage-products', function ($user) {
            return in_array($user->role, ['admin', 'super_admin', 'staff']);
        });

        Gate::define('manage-sales', function ($user) {
            return in_array($user->role, ['admin', 'super_admin', 'staff']);
        });

        Gate::define('manage-branches', function ($user) {
            return in_array($user->role, ['admin', 'super_admin']);
        });

        Gate::define('view-reports', function ($user) {
            return in_array($user->role, ['admin', 'super_admin', 'staff']);
        });

        Gate::define('manage-subscriptions', function ($user) {
            return in_array($user->role, ['admin', 'super_admin']);
        });

        Gate::define('manage-plans', function ($user) {
            return in_array($user->role, ['admin', 'super_admin']);
        });

        Gate::define('scan-attendance', function ($user) {
            return in_array($user->role, ['admin', 'super_admin', 'staff']);
        });

        Gate::define('view-attendance-logs', function ($user) {
            return in_array($user->role, ['admin', 'super_admin', 'staff', 'member']);
        });
    }
}
