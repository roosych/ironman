<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\User\ProfileController;
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
});

// Protected authentication routes
Route::middleware('auth:sanctum')->prefix('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('v1.auth.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('v1.auth.user');
    Route::post('/email/resend-verification', [AuthController::class, 'sendVerificationEmail'])
        ->name('v1.auth.resend-verification');
});

// Protected user routes
Route::middleware('auth:sanctum')->prefix('user')->group(function (): void {
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('v1.user.profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('v1.user.profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'setAvatar'])->name('v1.user.profile.avatar.set');

    // Photos
    Route::get('/photos', [ProfileController::class, 'getPhotos'])->name('v1.user.photos.index');
    Route::post('/photos', [ProfileController::class, 'uploadPhotos'])->name('v1.user.photos.upload');
    Route::delete('/photos/{photoId}', [ProfileController::class, 'deletePhoto'])->name('v1.user.photos.delete');
});