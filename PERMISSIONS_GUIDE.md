# Role-Based Access Control & Permissions Guide

This guide explains how to use the role-based access control (RBAC) and permission-based access system in your application.

## Overview

The application now has:
1. **Friendly error pages** instead of exception errors for unauthorized access
2. **Enhanced middleware** with better error messages
3. **Laravel Gates** for permission-based access control

## Using Middleware (Route Protection)

### Available Middleware

- `admin` - Only admin and super_admin can access
- `staff` - Only staff can access
- `admin_or_staff` - Admin, super_admin, or staff can access

### Example Usage in Routes

```php
// Admin only routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index']);
    Route::get('/admin/users', [UserController::class, 'index']);
});

// Staff only routes
Route::middleware(['auth', 'staff'])->group(function () {
    Route::get('/staff/dashboard', [StaffController::class, 'index']);
});

// Admin or Staff routes
Route::middleware(['auth', 'admin_or_staff'])->group(function () {
    Route::get('/attendance/scanner', [AttendanceController::class, 'scanner']);
});
```

## Using Gates (Permission-Based Access)

### Available Gates

**Role-based Gates:**
- `is-admin` - Check if user is admin or super_admin
- `is-staff` - Check if user is staff
- `is-admin-or-staff` - Check if user is admin, super_admin, or staff
- `is-member` - Check if user is member
- `is-super-admin` - Check if user is super_admin

**Permission-based Gates:**
- `manage-users` - Can manage users (admin, super_admin)
- `manage-members` - Can manage members (admin, super_admin, staff)
- `manage-products` - Can manage products (admin, super_admin, staff)
- `manage-sales` - Can manage sales (admin, super_admin, staff)
- `manage-branches` - Can manage branches (admin, super_admin)
- `view-reports` - Can view reports (admin, super_admin, staff)
- `manage-subscriptions` - Can manage subscriptions (admin, super_admin)
- `manage-plans` - Can manage plans (admin, super_admin)
- `scan-attendance` - Can scan attendance (admin, super_admin, staff)
- `view-attendance-logs` - Can view attendance logs (all roles)

### Using Gates in Controllers

```php
use Illuminate\Support\Facades\Gate;

public function index()
{
    // This will show friendly error page if user doesn't have permission
    Gate::authorize('manage-users');
    
    // Or check without throwing exception
    if (Gate::allows('manage-users')) {
        // User has permission
    }
    
    // Or check if denied
    if (Gate::denies('manage-users')) {
        return redirect()->back()->with('error', 'Access denied');
    }
}
```

### Using Gates in Blade Templates

```blade
{{-- Show content only if user has permission --}}
@can('manage-users')
    <a href="{{ route('admin.users') }}">Manage Users</a>
@endcan

{{-- Show content if user doesn't have permission --}}
@cannot('manage-users')
    <p>You don't have permission to manage users.</p>
@endcannot

{{-- Check multiple permissions --}}
@canany(['manage-users', 'manage-members'])
    <a href="#">User Management</a>
@endcanany

{{-- Check all permissions --}}
@canall(['manage-users', 'manage-branches'])
    <a href="#">Admin Panel</a>
@endcanall
```

### Using Gates in Route Middleware

You can also use gates directly in routes:

```php
Route::get('/admin/users', [UserController::class, 'index'])
    ->middleware('can:manage-users');
```

## Error Handling

### What Happens When Access is Denied?

1. **Web Requests**: Users see a friendly 403 error page with:
   - Clear error message
   - Their current role displayed
   - Options to go back or return to dashboard

2. **API/JSON Requests**: Returns JSON response:
   ```json
   {
       "success": false,
       "message": "Access Denied",
       "error": "Access Denied - This area is restricted..."
   }
   ```

### Customizing Error Messages

Error messages are automatically generated based on:
- The middleware being used
- The user's current role
- The context of the request

## Adding New Permissions

To add new permissions, edit `app/Providers/AuthServiceProvider.php`:

```php
Gate::define('your-permission-name', function ($user) {
    // Define who can access this permission
    return in_array($user->role, ['admin', 'super_admin']);
});
```

## Best Practices

1. **Use Middleware for Route Protection**: Protect entire route groups
2. **Use Gates for Fine-Grained Control**: Check permissions within controllers or views
3. **Combine Both**: Use middleware for route-level protection and gates for feature-level checks
4. **Always Provide Feedback**: The system now automatically shows friendly error messages

## Example: Complete Route Protection

```php
// Protect route with middleware
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])
        ->middleware('can:manage-users'); // Additional permission check
    
    Route::get('/admin/users/{id}', [UserController::class, 'show'])
        ->middleware('can:manage-users');
});
```

In your controller:

```php
public function index()
{
    // Additional check (optional, since middleware already checks)
    Gate::authorize('manage-users');
    
    // Your logic here
}
```

In your Blade view:

```blade
@can('manage-users')
    <div class="user-management">
        <!-- User management UI -->
    </div>
@endcan
```
