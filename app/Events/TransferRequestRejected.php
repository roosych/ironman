<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\ResultTransferRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransferRequestRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ResultTransferRequest $transferRequest
    ) {}
}