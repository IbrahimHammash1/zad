<?php

namespace App\Repositories\Customer\Eloquent;

use App\Models\Customer;
use App\Repositories\Customer\Contracts\CustomerRepositoryInterface;

class EloquentCustomerRepository implements CustomerRepositoryInterface
{
    public function createForUser(int $userId, array $attributes): Customer
    {
        return Customer::query()->create([
            'user_id' => $userId,
            'full_name' => $attributes['full_name'],
            'phone' => $attributes['phone'],
            'country' => $attributes['country'],
            'preferred_locale' => $attributes['preferred_locale'],
            'is_active' => true,
        ]);
    }

    public function loadProfile(Customer $customer): Customer
    {
        return $customer->loadMissing('user');
    }
}
