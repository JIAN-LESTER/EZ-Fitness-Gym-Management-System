<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchesController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// ─────────────────────────────────────────────────────────────────────────────
// Public / Auth Routes
// ─────────────────────────────────────────────────────────────────────────────

Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('loginForm');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('registerForm');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/check-username', [AuthController::class, 'checkUsername'])->name('check.username');
Route::get('/check-email', [AuthController::class, 'checkEmail'])->name('check.email');

// Email Verification
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
    ->name('verification.send');

// ─────────────────────────────────────────────────────────────────────────────
// Dashboards
// ─────────────────────────────────────────────────────────────────────────────

Route::get('/member/dashboard', [MemberProfileController::class, 'dashboard'])->middleware('auth')->name('member.dashboard');
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('admin.dashboard');
Route::get('/staff/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('staff.dashboard');

// ─────────────────────────────────────────────────────────────────────────────
// Authenticated Routes
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(['auth'])->group(function () {

    // ── Profile ──────────────────────────────────────────────────────────────
    // Single definition - uses ProfileController (the correct one)
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile.profile');
    Route::get('/profile/edit/{userID}', [MemberProfileController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');

    // ── Member Onboarding Flow ────────────────────────────────────────────────

    // GET fallback: prevents "Method not supported" error if user hits URL directly
    Route::get('/profile/complete-member-profile', function () {
        return redirect()->route('member.dashboard');
    });

    Route::post('/profile/complete-member-profile', [MemberProfileController::class, 'completeMemberProfile'])
        ->name('profile.complete-member-profile');

    Route::post('/member/select-plan', [MemberProfileController::class, 'selectPlan'])
        ->name('member.select-plan');

    Route::post('/member/select-subscription', [MemberProfileController::class, 'selectSubscription'])
        ->name('member.select-subscription');

    Route::get('/member/check-approval', [MemberProfileController::class, 'checkApprovalStatus'])
        ->name('member.check-approval');

    Route::post('/member/request-renewal', [MemberProfileController::class, 'requestRenewal'])
        ->name('member.request-renewal');

    // ── Subscriptions ─────────────────────────────────────────────────────────
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('admin.subscription_management');
    Route::post('/admin/subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::put('/admin/subscriptions/{id}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::delete('/admin/subscriptions/{id}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');

    // ── Branches ──────────────────────────────────────────────────────────────
    Route::get('/admin/branches', [BranchesController::class, 'index'])->name('admin.branch_management');
    Route::post('/admin/branches', [BranchesController::class, 'store'])->name('branches.store');
    Route::put('/admin/branches/{id}', [BranchesController::class, 'update'])->name('branches.update');
    Route::delete('/admin/branches/{id}', [BranchesController::class, 'destroy'])->name('branches.destroy');
    Route::get('/admin/branches/{branch_id}/edit', [BranchesController::class, 'edit'])->name('branches.edit');
    Route::post('/admin/select-branch', [BranchesController::class, 'selectBranch'])->name('admin.select-branch');

    // ── User / Account Management ─────────────────────────────────────────────
    Route::get('/account-management', [UserManagementController::class, 'viewUsers'])->name('admin.user_management');
    Route::get('/member-management', [UserManagementController::class, 'viewMembersForStaff'])->name('staff.user_management');
    Route::get('/admin/user_crud/plans-by-branch', [UserManagementController::class, 'getPlansByBranch']);

    Route::prefix('admin/user_crud')->name('admin.')->group(function () {
        Route::get('/create', [UserManagementController::class, 'create'])->name('users-create');
        Route::post('/store', [UserManagementController::class, 'store'])->name('users-store');
        Route::get('/show/{id}', [UserManagementController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [UserManagementController::class, 'edit'])->name('users-edit');
        Route::put('/update/{id}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [UserManagementController::class, 'destroy'])->name('users-destroy');
        Route::get('/approve-profile/{member}', [UserManagementController::class, 'approveProfile'])->name('approve-profile');
        Route::get('/approve-subscription/{member}', [UserManagementController::class, 'approveSubscription'])->name('approve-subscription');
        Route::get('/deny/{member}', [UserManagementController::class, 'deny'])->name('deny');
        Route::get('/suspend/{member}', [UserManagementController::class, 'suspendMember'])->name('suspend');
        Route::get('/resume/{member}', [UserManagementController::class, 'resumeMember'])->name('resume');
        Route::get('/cancel-plan/{member}', [UserManagementController::class, 'cancelPlan'])->name('cancel-plan');
    });

    // ── Membership Plans ──────────────────────────────────────────────────────
    Route::get('/plan-management', [MembershipPlanController::class, 'index'])->name('admin.plan_management');
    Route::post('/plans', [MembershipPlanController::class, 'store'])->name('plans.store');
    Route::put('/plans/{id}', [MembershipPlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{id}', [MembershipPlanController::class, 'destroy'])->name('plans.destroy');

    // ── Products / Inventory ──────────────────────────────────────────────────
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{id}', [ProductController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
    });

    // ── POS ───────────────────────────────────────────────────────────────────
    Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
    Route::post('/pos/add-to-cart', [POSController::class, 'addToCart'])->name('pos.addToCart');
    Route::get('/pos/get-cart', [POSController::class, 'getCart'])->name('pos.getCart');
    Route::post('/pos/update-cart-item', [POSController::class, 'updateCartItem'])->name('pos.updateCartItem');
    Route::post('/pos/remove-cart-item', [POSController::class, 'removeCartItem'])->name('pos.removeCartItem');
    Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');

    // ── Sales ─────────────────────────────────────────────────────────────────
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', [SalesController::class, 'index'])->name('index');
        Route::get('/{id}', [SalesController::class, 'show'])->name('show');
        Route::delete('/{id}', [SalesController::class, 'destroy'])->name('sales.destroy');
    });

    // ── Transactions ──────────────────────────────────────────────────────────
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
        Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    });

    // ── Categories ────────────────────────────────────────────────────────────
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoriesController::class, 'index'])->name('index');
        Route::post('/', [CategoriesController::class, 'store'])->name('store');
        Route::get('/{id}', [CategoriesController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [CategoriesController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CategoriesController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoriesController::class, 'destroy'])->name('destroy');
    });

    // ── Attendance ────────────────────────────────────────────────────────────
    Route::get('/attendance/scanner', [AttendanceController::class, 'scanner'])->name('attendance.scanner');
    Route::post('/attendance/scan', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::get('/attendance/today', [AttendanceController::class, 'getTodayAttendance'])->name('attendance.today');
    Route::get('/admin/attendance', [AttendanceController::class, 'adminLogs'])->name('attendance.admin.logs');
    Route::get('/member/attendance', [AttendanceController::class, 'memberLogs'])->name('member.member.logs');

    // ── Logs ──────────────────────────────────────────────────────────────────
    Route::get('/logs', [LogController::class, 'viewLogs'])->name('logs.show');

    // ── QR Code (utility) ─────────────────────────────────────────────────────
    Route::get('/save-qrcode', function () {
        $fileName = 'qrcode.png';
        QrCode::format('png')->size(300)->generate('Hello, world!', storage_path("app/public/{$fileName}"));

        return "QR code saved as {$fileName}";
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Staff Routes
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(['auth', 'staff'])->group(function () {
    Route::get('/staff/quick-stats', [StaffController::class, 'getQuickStats'])->name('staff.quick-stats');
    Route::get('/staff/sales-chart', [StaffController::class, 'getSalesChartData'])->name('staff.sales-chart');
    Route::get('/staff/recent-activity', [StaffController::class, 'getRecentActivity'])->name('staff.recent-activity');
    Route::get('/staff/inventory-alerts', [StaffController::class, 'getInventoryAlerts'])->name('staff.inventory-alerts');
    Route::get('/staff/products', [StaffController::class, 'productsIndex'])->name('staff.products.index');
    Route::get('/staff/sales-report', [StaffController::class, 'salesReport'])->name('staff.sales.report');
});

Route::get('/check-availability', [AuthController::class, 'checkAvailability'])->name('check.availability');
