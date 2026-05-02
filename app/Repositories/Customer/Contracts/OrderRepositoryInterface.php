<?php

namespace App\Repositories\Customer\Contracts;

use App\Models\Customer;
use App\Models\Order;
use App\Payments\Data\PaymentResultData;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function getForCustomer(Customer $customer): Collection;

    public function findByIdForCustomer(Customer $customer, int $orderId): ?Order;

    public function createPaidOrder(Customer $customer, array $review, PaymentResultData $paymentResult): Order;
}
