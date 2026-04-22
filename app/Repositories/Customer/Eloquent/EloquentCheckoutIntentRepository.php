<?php

namespace App\Repositories\Customer\Eloquent;

use App\Models\CustomerCheckoutIntent;
use App\Repositories\Customer\Contracts\CheckoutIntentRepositoryInterface;

class EloquentCheckoutIntentRepository implements CheckoutIntentRepositoryInterface
{
    public function create(array $attributes): CustomerCheckoutIntent
    {
        return CustomerCheckoutIntent::query()->create($attributes);
    }
}
