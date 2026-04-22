<?php

namespace App\Repositories\Customer\Eloquent;

use App\Models\Basket;
use App\Repositories\Customer\Contracts\BasketRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentBasketRepository implements BasketRepositoryInterface
{
    public function getAvailableForCustomer(): Collection
    {
        return Basket::query()
            ->availableForCustomer()
            ->with($this->customerRelations())
            ->orderBy('name')
            ->get();
    }

    public function findAvailableForCustomerBySlug(string $slug): ?Basket
    {
        return Basket::query()
            ->availableForCustomer()
            ->where('slug', $slug)
            ->with($this->customerRelations())
            ->first();
    }

    public function getActiveByIdsForCheckout(array $basketIds): Collection
    {
        return Basket::query()
            ->whereIn('id', $basketIds)
            ->where('is_active', true)
            ->with([
                'stores' => fn ($query) => $query->where('is_active', true),
            ])
            ->get()
            ->keyBy('id');
    }

    protected function customerRelations(): array
    {
        return [
            'basketItems' => fn ($query) => $query->orderBy('sort_order')->with('material'),
            'stores' => fn ($query) => $query->where('is_active', true)->orderBy('name'),
        ];
    }
}
