<?php

namespace App\Repositories\Customer\Contracts;

use App\Models\CustomerCheckoutIntent;

interface CheckoutIntentRepositoryInterface
{
    public function create(array $attributes): CustomerCheckoutIntent;
}
