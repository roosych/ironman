<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\PasswordChanged;
use App\Events\ProfileSynced;
use App\Events\RaceApproved;
use App\Events\RaceCreated;
use App\Listeners\SendPasswordChangedNotification;
use App\Models\PersonalAccessToken;
use App\Listeners\SendProfileSyncedNotification;
use App\Listeners\SendRaceApprovedNotification;
use App\Listeners\SendRaceCreatedNotification;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        RaceCreated::class => [
            SendRaceCreatedNotification::class,
        ],
        RaceApproved::class => [
            SendRaceApprovedNotification::class,
        ],
        ProfileSynced::class => [
            SendProfileSyncedNotification::class,
        ],
        PasswordChanged::class => [
            SendPasswordChangedNotification::class,
        ],
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
