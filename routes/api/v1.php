<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\RaceResultController;
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

// Public race results routes
Route::get('/race-results', [RaceResultController::class, 'index'])->name('v1.race-results.index');
Route::get('/race-results/{raceResult}', [RaceResultController::class, 'show'])->name('v1.race-results.show');
Route::get('/users/{user}/race-results', [RaceResultController::class, 'userResults'])->name('v1.users.race-results');

// Protected race results routes
Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/race-results', [RaceResultController::class, 'store'])->name('v1.race-results.store');
    Route::put('/race-results/{raceResult}', [RaceResultController::class, 'update'])->name('v1.race-results.update');
    Route::delete('/race-results/{raceResult}', [RaceResultController::class, 'destroy'])->name('v1.race-results.destroy');
});