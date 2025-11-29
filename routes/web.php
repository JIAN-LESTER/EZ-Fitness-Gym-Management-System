        <?php

        use App\Http\Controllers\AdminController;
        use App\Http\Controllers\AlertController;
        use App\Http\Controllers\AuthController;
        use App\Http\Controllers\CategoriesController;
        use App\Http\Controllers\DashboardController;
        use App\Http\Controllers\EmailVerificationController;

        use App\Http\Controllers\MemberProfileController;
        use App\Http\Controllers\MembershipPlanController;
        use App\Http\Controllers\POSController;
        use App\Http\Controllers\ProfileController;
        use App\Http\Controllers\SalesController;
        use App\Http\Controllers\SnapshotsController;
        use App\Http\Controllers\TransactionController;
        use App\Http\Controllers\TwoFactorAuthController;
        use App\Http\Controllers\UserManagementController;
        use App\Http\Controllers\StaffController;
        use App\Models\SalesItem;
        use Illuminate\Support\Facades\Route;
        use App\Http\Controllers\MapsController;
        use SimpleSoftwareIO\QrCode\Facades\QrCode;
        use Illuminate\Foundation\Auth\EmailVerificationRequest;
        use App\Http\Controllers\ProductController;

        use App\Http\Controllers\AttendanceController;


        Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('loginForm');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('registerForm');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::middleware(['auth'])->group(function () {

            Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
                Route::get('/dashboard', function () {
                    return view('admin.dashboard');
                })->name('admin.dashboard');
            });

            Route::prefix('member')->name('member.')->group(function () {
                Route::get('/dashboard', function () {
                    return view('member.dashboard');
                })->name('member.dashboard');
            });
        });

        Route::middleware(['auth'])->group(function () {
            Route::get('/profile', [MemberProfileController::class, 'profile'])->name('profile.profile');
            Route::get('/profile/edit/{userID}', [MemberProfileController::class, 'editProfile'])->name('profile.edit');
            Route::put('/profile/update', [MemberProfileController::class, 'updateProfile'])->name('profile.update');
        });

        Route::get('/member/dashboard', [MemberProfileController::class, 'dashboard'])->name('member.dashboard');
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');


        Route::middleware(['auth'])->group(function () {
            Route::get('/profile', [ProfileController::class, 'profile'])->name('profile.profile');
            Route::get('/profile/edit/{userID}', [MemberProfileController::class, 'editProfile'])->name('profile.edit');

            // For first-time profile completion (inactive members)
            Route::put('/profile/complete-membership', [MemberProfileController::class, 'completeMemberProfile'])->name('profile.complete-member-profile');

            // For updating existing profiles (active members)
            Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        });


        Route::get('/save-qrcode', function () {

            $fileName = 'qrcode.png';


            QrCode::format('png')
                ->size(300)
                ->generate('Hello, world!', storage_path("app/public/{$fileName}"));

            return "QR code saved as {$fileName}";
        });

        Route::prefix('admin/user_crud')->name('admin.')->group(function () {
            Route::get('/create', [UserManagementController::class, 'create'])->name('users-create');
            Route::post('/store', [UserManagementController::class, 'store'])->name('users-store');
            Route::get('/show/{id}', [UserManagementController::class, 'show'])->name('show');
            Route::get('/edit/{id}', [UserManagementController::class, 'edit'])->name('users-edit');
            Route::put('/update/{id}', [UserManagementController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [UserManagementController::class, 'destroy'])->name('users-destroy');
            Route::get('/approve/{member}', [UserManagementController::class, 'approve'])
                ->name('admin.members.approve');
            Route::get('/deny/{member}', [UserManagementController::class, 'deny'])
                ->name('admin.members.deny');
        });

        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::post('/', [ProductController::class, 'store'])->name('store');
            Route::get('/{id}', [ProductController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ProductController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
        });

        Route::middleware(['auth'])->group(function () {
            // POS Main Page
            Route::get('/pos', [POSController::class, 'index'])->name('pos.index');

            // Cart Operations
            Route::post('/pos/add-to-cart', [POSController::class, 'addToCart'])->name('pos.addToCart');
            Route::get('/pos/get-cart', [POSController::class, 'getCart'])->name('pos.getCart');
            Route::post('/pos/update-cart-item', [POSController::class, 'updateCartItem'])->name('pos.updateCartItem');
            Route::post('/pos/remove-cart-item', [POSController::class, 'removeCartItem'])->name('pos.removeCartItem');

            // Checkout
            Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
        });

        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/', [SalesController::class, 'index'])->name('index');
            Route::get('/{id}', [SalesController::class, 'show'])->name('show');
        });

        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [TransactionController::class, 'index'])->name('index');
            Route::get('/{id}', [TransactionController::class, 'show'])->name('show');
        });

        Route::post('/member/request-renewal', [MemberProfileController::class, 'requestRenewal'])->name('member.request-renewal');
        Route::get('/user-management', [UserManagementController::class, 'viewUsers'])->name('admin.user_management');
        Route::get('/member/check-approval', [MemberProfileController::class, 'checkApprovalStatus'])
            ->name('member.check-approval');

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoriesController::class, 'index'])->name('index');
            Route::post('/', [CategoriesController::class, 'store'])->name('store');
            Route::get('/{id}', [CategoriesController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [CategoriesController::class, 'edit'])->name('edit');
            Route::put('/{id}', [CategoriesController::class, 'update'])->name('update');
            Route::delete('/{id}', [CategoriesController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('admin')->middleware(['auth'])->group(function () {
            Route::get('/plan-management', [App\Http\Controllers\MembershipPlanController::class, 'index'])->name('admin.plan_management');
            Route::post('/plans', [App\Http\Controllers\MembershipPlanController::class, 'store'])->name('plans.store');
            Route::put('/plans/{id}', [App\Http\Controllers\MembershipPlanController::class, 'update'])->name('plans.update');
            Route::delete('/plans/{id}', [App\Http\Controllers\MembershipPlanController::class, 'destroy'])->name('plans.destroy');
        });

        // Verification link
        Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
            ->middleware(['signed'])
            ->name('verification.verify');

        // Resend verification 
        Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
            ->name('verification.send');

        use App\Http\Controllers\LogController;

        Route::get('/logs', [LogController::class, 'viewLogs'])->name('logs.show');


        Route::get('/check-username', [AuthController::class, 'checkUsername'])->name('check.username');
        Route::get('/check-email', [AuthController::class, 'checkEmail'])->name('check.email');


        // Attendance Routes (add after your existing routes)
        Route::middleware(['auth'])->group(function () {

            // QR Scanner (Admin/Staff only)
            Route::get('/attendance/scanner', [AttendanceController::class, 'scanner'])
                ->name('attendance.scanner');

            // Process QR Scan
            Route::post('/attendance/scan', [AttendanceController::class, 'scan'])
                ->name('attendance.scan');

            // Get today's attendance (AJAX endpoint)
            Route::get('/attendance/today', [AttendanceController::class, 'getTodayAttendance'])
                ->name('attendance.today');

            // Admin - View all attendance logs
            Route::get('/admin/attendance', [AttendanceController::class, 'adminLogs'])
                ->middleware('admin')
                ->name('attendance.admin.logs');

            // Member - View own attendance logs
            Route::get('/member/attendance', [AttendanceController::class, 'memberLogs'])
                ->name('attendance.member.logs');
        });

        // STAFF
        Route::middleware(['auth', 'staff'])->group(function () {
            // Dashboard
            Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');

            // API endpoints for dashboard
            Route::get('/staff/quick-stats', [StaffController::class, 'getQuickStats'])->name('staff.quick-stats');
            Route::get('/staff/sales-chart', [StaffController::class, 'getSalesChartData'])->name('staff.sales-chart');
            Route::get('/staff/recent-activity', [StaffController::class, 'getRecentActivity'])->name('staff.recent-activity');
            Route::get('/staff/inventory-alerts', [StaffController::class, 'getInventoryAlerts'])->name('staff.inventory-alerts');

            // Staff product management
            Route::get('/staff/products', [StaffController::class, 'productsIndex'])->name('staff.products.index');

            // Staff sales reports
            Route::get('/staff/sales-report', [StaffController::class, 'salesReport'])->name('staff.sales.report');
        });
