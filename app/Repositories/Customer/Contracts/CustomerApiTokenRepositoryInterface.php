<?php

namespace App\Repositories\Customer\Contracts;

use App\Models\Customer;
use App\Models\CustomerApiToken;

interface CustomerApiTokenRepositoryInterface
{
    public function create(Customer $customer, string $name, string $hashedToken): CustomerApiToken;

    public function findWithCustomerAndUserById(int|string $tokenId): ?CustomerApiToken;

    public function touchLastUsed(CustomerApiToken $token): void;

    public function delete(CustomerApiToken $token): void;
}
