<?php

namespace App\Repositories\Customer\Contracts;

use App\Models\Customer;

interface CustomerRepositoryInterface
{
    public function createForUser(int $userId, array $attributes): Customer;

    public function loadProfile(Customer $customer): Customer;
}
