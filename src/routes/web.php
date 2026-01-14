<?php

use App\Http\Controllers\Admin\BookingApprovalController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Api\SlotController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Rep\BookingController as RepBookingController;
use App\Http\Controllers\Rep\DashboardController as RepDashboardController;
use App\Http\Controllers\Rep\ProfileController;
use App\Http\Controllers\SuperAdmin\ConfigController;
use App\Http\Controllers\SuperAdmin\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\TodayAppointmentsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1'); // 5 attempts per minute
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:3,60'); // 3 registrations per hour
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
        ->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
        ->middleware('throttle:3,1') // 3 attempts per minute
        ->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
        ->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:5,1') // 5 attempts per minute
        ->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ========================================
// TWO-FACTOR AUTHENTICATION ROUTES
// ========================================
use App\Http\Controllers\TwoFactorController;

// 2FA Challenge (during login)
Route::middleware('guest')->group(function () {
    Route::get('/2fa/challenge', [TwoFactorController::class, 'show2FAChallenge'])->name('2fa.challenge');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify2FA'])->name('2fa.verify');
    Route::post('/2fa/verify-recovery', [TwoFactorController::class, 'verifyRecoveryCode'])->name('2fa.verify.recovery');
});

// 2FA Management (for authenticated users)
Route::middleware('auth')->prefix('2fa')->name('2fa.')->group(function () {
    Route::get('/setup', [TwoFactorController::class, 'show2FASetup'])->name('setup');
    Route::post('/enable', [TwoFactorController::class, 'enable2FA'])->name('enable');
    Route::post('/confirm', [TwoFactorController::class, 'confirm2FA'])->name('confirm');
    Route::post('/disable', [TwoFactorController::class, 'disable2FA'])->name('disable');
    Route::post('/recovery-codes/regenerate', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('recovery.regenerate');

    // Trusted Devices Management
    Route::get('/trusted-devices', [TwoFactorController::class, 'manageTrustedDevices'])->name('trusted-devices');
    Route::delete('/trusted-devices/{device}', [TwoFactorController::class, 'revokeTrustedDevice'])->name('trusted-devices.revoke');
    Route::post('/trusted-devices/revoke-all', [TwoFactorController::class, 'revokeAllTrustedDevices'])->name('trusted-devices.revoke-all');
});

// ========================================
// EMAIL VERIFICATION ROUTES (NEW)
// ========================================
// Email verification notice (needs auth)
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Email verification handler (NO AUTH - signed URL provides security)
Route::get('/email/verify/{id}/{hash}', function (Request $request) {
    $userId = $request->route('id');
    $user = \App\Models\User::findOrFail($userId);
    
    // If user is logged in and verifying their own email, log them out first
    if (Auth::check() && Auth::id() == $userId && !$user->hasVerifiedEmail()) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
    
    if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification link.');
    }
    
    if ($user->hasVerifiedEmail()) {
        \Log::info('Email already verified', ['user_id' => $user->id]);
        return redirect('/login')->with('info', 'Email already verified. You can login now.');
    }
    
    \Log::info('Verification started', ['user_id' => $user->id]);
    
    $user->markEmailAsVerified();
    
    \Log::info('After markEmailAsVerified', ['verified' => $user->hasVerifiedEmail()]);
    
    event(new \Illuminate\Auth\Events\Verified($user));
    
    \Log::info('After event', ['is_active' => $user->fresh()->is_active]);
    
    // Clear any cached routes/config
    \Artisan::call('route:clear');
    \Artisan::call('config:clear');
    
// Clear any lingering session data
$request->session()->flush();

return redirect('/login')->with('success', 'Email verified successfully! Your account is now active. You can login now.');
    
})->middleware(['signed'])->name('verification.verify');
// Resend verification email (needs auth)
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('info', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ========================================
// Representative Routes
Route::prefix('rep')->name('rep.')->middleware(['auth', 'role:representative'])->group(function () {
    Route::get('/dashboard', [RepDashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [RepBookingController::class, 'index'])->name('index');
        Route::get('/create', [RepBookingController::class, 'create'])->name('create');
        Route::post('/', [RepBookingController::class, 'store'])->name('store');
        Route::post('/{booking}/cancel', [RepBookingController::class, 'cancelPending'])->name('cancel');
        Route::get('/history', [RepBookingController::class, 'history'])->name('history');
    });
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Admin Routes (Pharmacy Admin + Super Admin)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:pharmacy_admin,super_admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Booking Approval Routes
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/pending', [BookingApprovalController::class, 'pending'])->name('pending');
        Route::post('/{booking}/approve', [BookingApprovalController::class, 'approve'])->name('approve');
        Route::post('/{booking}/reject', [BookingApprovalController::class, 'reject'])->name('reject');
        Route::post('/{booking}/cancel', [BookingApprovalController::class, 'cancel'])->name('cancel');
        Route::get('/', [AdminBookingController::class, 'index'])->name('index');
    });
    
    // Department Management (Super Admin Only)
    Route::resource('departments', DepartmentController::class)
    ->except(['show'])
    ->middleware('role:super_admin');

    // ADD THESE REPORT ROUTES HERE:
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/today', [TodayAppointmentsController::class, 'index'])->name('today');
        Route::get('/today/pdf', [TodayAppointmentsController::class, 'pdf'])->name('today.pdf');
        Route::get('/today/print', [TodayAppointmentsController::class, 'print'])->name('today.print');
    });

    // Statistics Dashboard Routes (NEW)
    Route::prefix('statistics')->name('statistics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\StatisticsController::class, 'index'])->name('index');
        Route::get('/export-excel', [\App\Http\Controllers\StatisticsController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export-pdf', [\App\Http\Controllers\StatisticsController::class, 'exportPdf'])->name('export.pdf');
    });

    // Schedule Management
    Route::resource('schedules', ScheduleController::class)->except(['show']);
});

// Super Admin Routes
Route::prefix('super-admin')->name('super-admin.')->middleware(['auth', 'role:super_admin'])->group(function () {
    // User Management
    Route::resource('users', UserController::class)->except(['show']);
    Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle');
    Route::delete('/users/{user}/force-delete', [UserController::class, 'forceDelete'])->name('users.forceDelete');
    
    // System Configuration
    Route::get('/config', [ConfigController::class, 'edit'])->name('config.edit');
    Route::put('/config', [ConfigController::class, 'update'])->name('config.update');
});

// AJAX Endpoints
Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('/slots/available', [SlotController::class, 'getAvailable'])->name('api.slots.available');
});
