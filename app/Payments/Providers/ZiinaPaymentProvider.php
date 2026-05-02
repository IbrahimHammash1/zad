<?php

namespace App\Payments\Providers;

use App\Enums\PaymentProvider;
use App\Enums\PaymentStatus;
use App\Payments\Contracts\PaymentProviderInterface;
use App\Payments\Data\PaymentRequestData;
use App\Payments\Data\PaymentResultData;
use Illuminate\Support\Str;

class ZiinaPaymentProvider implements PaymentProviderInterface
{
    public function charge(PaymentRequestData $paymentRequest): PaymentResultData
    {
        return new PaymentResultData(
            provider: PaymentProvider::Ziina,
            status: PaymentStatus::Succeeded,
            providerReference: 'ziina_'.Str::uuid()->toString(),
            paidAt: now(),
        );
    }
}
