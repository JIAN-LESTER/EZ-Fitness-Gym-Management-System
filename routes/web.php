    <?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailVerificationController;

use App\Http\Controllers\MemberProfileController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SnapshotsController;
use App\Http\Controllers\TwoFactorAuthController;
use App\Http\Controllers\UserManagementController;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapsController;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\ProductController;

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

Route::get('/member/dashboard', [MemberProfileController::class, 'dashboard'])->name('member.dashboard');
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');


Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [MemberProfileController::class, 'profile'])->name('profile.profile');
    Route::get('/profile/edit/{userID}', [MemberProfileController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile/update', [MemberProfileController::class, 'updateProfile'])->name('profile.update');
});




Route::get('/member/dashboard', [MemberProfileController::class, 'dashboard'])->name('member.dashboard');
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');


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
});

Route::get('/user-management', [UserManagementController::class, 'viewUsers'])->name('admin.user_management');



// Email verification routes

// Resend verification 
Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
    ->name('verification.send');

Route::get('/user-management', [UserManagementController::class, 'viewUsers'])->name('admin.user_management');


Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/plan-management', [App\Http\Controllers\MembershipPlanController::class, 'index'])->name('admin.plan_management');
    Route::post('/plans', [App\Http\Controllers\MembershipPlanController::class, 'store'])->name('plans.store');
    Route::put('/plans/{id}', [App\Http\Controllers\MembershipPlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{id}', [App\Http\Controllers\MembershipPlanController::class, 'destroy'])->name('plans.destroy');
});





// Email verification routes

// Resend verification 
Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
    ->name('verification.send');

// Verification link
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');


    use App\Http\Controllers\LogController;

Route::get('/logs', [LogController::class, 'viewLogs'])->name('logs.show');



Route::get('/check-username', [AuthController::class, 'checkUsername'])->name('check.username');
Route::get('/check-email', [AuthController::class, 'checkEmail'])->name('check.email');


