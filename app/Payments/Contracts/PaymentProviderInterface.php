<?php

namespace App\Payments\Contracts;

use App\Payments\Data\PaymentRequestData;
use App\Payments\Data\PaymentResultData;

interface PaymentProviderInterface
{
    public function charge(PaymentRequestData $paymentRequest): PaymentResultData;
}
