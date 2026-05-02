<?php

namespace App\Services\Customer;

use App\Models\Customer;
use App\Models\Order;
use App\Repositories\Customer\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerOrderService
{
    public function __construct(protected OrderRepositoryInterface $orderRepository) {}

    public function listForCustomer(Customer $customer): Collection
    {
        return $this->orderRepository->getForCustomer($customer);
    }

    public function getByIdForCustomer(Customer $customer, int $orderId): Order
    {
        $order = $this->orderRepository->findByIdForCustomer($customer, $orderId);

        if (! $order) {
            throw new NotFoundHttpException;
        }

        return $order;
    }
}
