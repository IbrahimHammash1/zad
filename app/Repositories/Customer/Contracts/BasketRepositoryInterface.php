<?php

namespace App\Repositories\Customer\Contracts;

use App\Models\Basket;
use Illuminate\Database\Eloquent\Collection;

interface BasketRepositoryInterface
{
    public function getAvailableForCustomer(): Collection;

    public function findAvailableForCustomerBySlug(string $slug): ?Basket;

    public function getActiveByIdsForCheckout(array $basketIds): Collection;
}
