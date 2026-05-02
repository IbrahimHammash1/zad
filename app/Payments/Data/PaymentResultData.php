<?php

namespace App\Payments\Data;

use App\Enums\PaymentProvider;
use App\Enums\PaymentStatus;
use Illuminate\Support\Carbon;

class PaymentResultData
{
    public function __construct(
        public readonly PaymentProvider $provider,
        public readonly PaymentStatus $status,
        public readonly string $providerReference,
        public readonly Carbon $paidAt,
    ) {}
}
