<?php

namespace App\Repositories\Customer\Contracts;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface
{
    public function getForCustomer(Customer $customer): Collection;

    public function findByIdForCustomer(Customer $customer, int $orderId): ?Order;
}
