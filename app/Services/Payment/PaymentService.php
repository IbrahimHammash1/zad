<?php

namespace App\Services\Payment;

use App\Payments\Contracts\PaymentProviderInterface;
use App\Payments\Data\PaymentRequestData;
use App\Payments\Data\PaymentResultData;

class PaymentService
{
    public function __construct(protected PaymentProviderInterface $paymentProvider) {}

    public function charge(PaymentRequestData $paymentRequest): PaymentResultData
    {
        return $this->paymentProvider->charge($paymentRequest);
    }
}
