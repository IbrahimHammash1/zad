<?php

namespace App\Services\Customer;

use App\Models\Basket;
use App\Repositories\Customer\Contracts\BasketRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerBasketService
{
    public function __construct(protected BasketRepositoryInterface $basketRepository) {}

    public function listAvailable(): Collection
    {
        return $this->basketRepository->getAvailableForCustomer();
    }

    public function getAvailableBySlug(string $slug): Basket
    {
        $basket = $this->basketRepository->findActiveBySlug($slug);

        if (! $basket) {
            throw new NotFoundHttpException();
        }

        return $basket;
    }
}
