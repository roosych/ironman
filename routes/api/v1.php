<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AthleteController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\RaceResultController;
use App\Http\Controllers\Api\V1\RankingController;
use App\Http\Controllers\Api\V1\UpcomingRaceController;
use App\Http\Controllers\Api\V1\User\FcmTokenController;
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
    Route::put('/locale', [AuthController::class, 'updateLocale'])->name('v1.auth.locale.update');
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

    // Photos
    Route::get('/photos', [ProfileController::class, 'getPhotos'])->name('v1.user.photos.index');
    Route::post('/photos', [ProfileController::class, 'uploadPhotos'])->name('v1.user.photos.upload');
    Route::delete('/photos/{photoId}', [ProfileController::class, 'deletePhoto'])->name('v1.user.photos.delete');

    // FCM Tokens
    Route::post('/fcm-token', [FcmTokenController::class, 'store'])->name('v1.user.fcm-token.store');
    Route::delete('/fcm-token', [FcmTokenController::class, 'destroy'])->name('v1.user.fcm-token.destroy');
});

// Protected profile selection routes (for profile selection screen)
Route::middleware('auth:sanctum')->prefix('profiles')->group(function (): void {
    Route::post('/link', [ProfileController::class, 'link'])->name('v1.profiles.link');
    Route::post('/create', [ProfileController::class, 'create'])->name('v1.profiles.create');
});

// Protected notifications routes
Route::middleware('auth:sanctum')->prefix('notifications')->group(function (): void {
    Route::get('/', [NotificationController::class, 'index'])->name('v1.notifications.index');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('v1.notifications.read');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('v1.notifications.read-all');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('v1.notifications.destroy');
    
    // Test endpoint (development only)
    Route::post('/test', [NotificationController::class, 'sendTest'])->name('v1.notifications.test');
});

// Public athletes routes
Route::get('/athletes', [AthleteController::class, 'index'])
    ->middleware('throttle:60,1')
    ->name('v1.athletes.index');
Route::get('/athletes/{id}', [AthleteController::class, 'show'])
    ->middleware('throttle:60,1')
    ->name('v1.athletes.show');
Route::get('/athletes/{id}/records', [AthleteController::class, 'records'])
    ->middleware('throttle:60,1')
    ->name('v1.athletes.records');
Route::get('/athletes/{id}/photos', [AthleteController::class, 'photos'])
    ->middleware('throttle:60,1')
    ->name('v1.athletes.photos');

// Public rankings routes
Route::get('/rankings', [RankingController::class, 'index'])
    ->middleware('throttle:60,1')
    ->name('v1.rankings.index');

// Public upcoming races routes
Route::get('/upcoming-races', [UpcomingRaceController::class, 'index'])
    ->middleware('throttle:60,1')
    ->name('v1.upcoming-races.index');

// Protected upcoming races routes
Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/upcoming-races', [UpcomingRaceController::class, 'store'])
        ->name('v1.upcoming-races.store');
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
    
    // Race results approval
    Route::get('/race-results/pending', [AdminController::class, 'pendingRaceResults'])
        ->name('v1.admin.race-results.pending');
    Route::post('/race-results/{raceResult}/approve', [AdminController::class, 'approveRaceResult'])
        ->name('v1.admin.race-results.approve');
    Route::delete('/race-results/{raceResult}/reject', [AdminController::class, 'rejectRaceResult'])
        ->name('v1.admin.race-results.reject');
});
