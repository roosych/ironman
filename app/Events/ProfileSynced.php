<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProfileSynced
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user,
        public UserProfile $profile
    ) {}
}
