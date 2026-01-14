<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\RaceResultController;
use App\Http\Controllers\Api\V1\User\PasswordController;
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
    // Password
    Route::put('/password', [PasswordController::class, 'update'])->name('v1.user.password.update');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('v1.user.profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('v1.user.profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'setAvatar'])->name('v1.user.profile.avatar.set');
    Route::post('/profile/request-sync', [ProfileController::class, 'requestSync'])->name('v1.user.profile.request-sync');

    // Photos
    Route::get('/photos', [ProfileController::class, 'getPhotos'])->name('v1.user.photos.index');
    Route::post('/photos', [ProfileController::class, 'uploadPhotos'])->name('v1.user.photos.upload');
    Route::delete('/photos/{photoId}', [ProfileController::class, 'deletePhoto'])->name('v1.user.photos.delete');
});

// Public race results routes
Route::get('/race-results', [RaceResultController::class, 'index'])->name('v1.race-results.index');
Route::get('/race-results/{raceResult}', [RaceResultController::class, 'show'])->name('v1.race-results.show');
Route::get('/profiles/{userProfile}/race-results', [RaceResultController::class, 'profileResults'])->name('v1.profiles.race-results');

// Protected race results routes
Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/race-results', [RaceResultController::class, 'store'])->name('v1.race-results.store');
    Route::put('/race-results/{raceResult}', [RaceResultController::class, 'update'])->name('v1.race-results.update');
    Route::delete('/race-results/{raceResult}', [RaceResultController::class, 'destroy'])->name('v1.race-results.destroy');
});

// Admin routes
Route::middleware('auth:sanctum')->prefix('admin')->group(function (): void {
    Route::post('/users/{user}/link-profile/{profile}', [AdminController::class, 'linkProfileToUser'])
        ->name('v1.admin.users.link-profile');
});
