<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminRaceResultController;
use App\Http\Controllers\Admin\AdminResultTransferController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'IRON API',
        'status' => 'ok',
        'version' => 'v1',
        'environment' => app()->environment(),
        'time' => now()->toIso8601String(),
    ]);
});

// Email verification route (web route, not API)
Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

// Password reset routes
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');
Route::get('/reset-password/success', [PasswordResetController::class, 'showSuccess'])
    ->name('password.reset.success');

// Generic login route (redirects to admin login)
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Admin authentication routes (public)
Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest:web')->group(function (): void {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    Route::middleware('auth:web')->group(function (): void {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
});

// Admin protected routes
Route::middleware(['auth:web', EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function (): void {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Race results
    Route::get('/race-results/pending', [AdminRaceResultController::class, 'pending'])->name('race-results.pending');
    Route::get('/race-results/create', [AdminRaceResultController::class, 'create'])->name('race-results.create');
    Route::post('/race-results', [AdminRaceResultController::class, 'store'])->name('race-results.store');
    Route::post('/race-results/{raceResult}/approve', [AdminRaceResultController::class, 'approve'])->name('race-results.approve');
    Route::post('/race-results/{raceResult}/reject', [AdminRaceResultController::class, 'reject'])->name('race-results.reject');

    // Result transfer requests
    Route::get('/transfer-requests', [AdminResultTransferController::class, 'index'])->name('transfer-requests.index');
    Route::get('/transfer-requests/stats', [AdminResultTransferController::class, 'stats'])->name('transfer-requests.stats');
    Route::get('/transfer-requests/{id}', [AdminResultTransferController::class, 'show'])->name('transfer-requests.show');
    Route::post('/transfer-requests/{id}/approve', [AdminResultTransferController::class, 'approve'])->name('transfer-requests.approve');
    Route::post('/transfer-requests/{id}/reject', [AdminResultTransferController::class, 'reject'])->name('transfer-requests.reject');
});
