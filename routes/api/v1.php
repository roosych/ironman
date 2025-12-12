<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
*/

// Public authentication routes
Route::prefix('auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register'])->name('v1.auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('v1.auth.login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('v1.auth.forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('v1.auth.reset-password');
    // Note: Email verification moved to web.php as it's a web route (link from email)
});

// Protected authentication routes
Route::middleware('auth:sanctum')->prefix('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('v1.auth.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('v1.auth.user');
    Route::post('/email/resend-verification', [AuthController::class, 'sendVerificationEmail'])
        ->name('v1.auth.resend-verification');
});

// только верифицированным пользователям
// Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    
// });