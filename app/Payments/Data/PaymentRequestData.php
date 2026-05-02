<?php

namespace App\Payments\Data;

class PaymentRequestData
{
    public function __construct(
        public readonly string $amount,
        public readonly string $currency,
        public readonly int $customerId,
        public readonly string $recipientName,
    ) {}
}
