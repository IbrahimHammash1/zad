<?php

namespace App\Repositories\Customer\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function createCustomerUser(array $attributes): User;

    public function findCustomerUserByEmail(string $email): ?User;
}
