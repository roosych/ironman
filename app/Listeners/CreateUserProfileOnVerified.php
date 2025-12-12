<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\UserProfile;
use Illuminate\Auth\Events\Verified;

class CreateUserProfileOnVerified
{
    /**
     * Handle the event.
     *
     * Create a default profile with 'athlete' role when user verifies email.
     */
    public function handle(Verified $event): void
    {
        $user = $event->user;

        // Use firstOrCreate to prevent duplicate profiles
        UserProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['role' => 'athlete']
        );
    }
}
