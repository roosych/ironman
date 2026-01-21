<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\RaceResult;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RaceCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public RaceResult $raceResult
    ) {}
}
