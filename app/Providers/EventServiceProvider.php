<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\RaceApproved;
use App\Events\RaceCreated;
use App\Events\PasswordChanged;
use App\Events\ProfileSynced;
use App\Events\TransferRequestCreated;
use App\Listeners\SendRaceApprovedNotification;
use App\Listeners\SendRaceCreatedNotification;
use App\Listeners\SendPasswordChangedNotification;
use App\Listeners\SendProfileSyncedNotification;
use App\Listeners\NotifyAdminsOfNewRaceResult;
use App\Listeners\NotifyAdminsOfNewTransferRequest;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        RaceApproved::class => [
            SendRaceApprovedNotification::class,
        ],
        RaceCreated::class => [
            SendRaceCreatedNotification::class,
            NotifyAdminsOfNewRaceResult::class,
        ],
        PasswordChanged::class => [
            SendPasswordChangedNotification::class,
        ],
        ProfileSynced::class => [
            SendProfileSyncedNotification::class,
        ],
        TransferRequestCreated::class => [
            NotifyAdminsOfNewTransferRequest::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
