<?php

namespace App\Repositories\Customer\Eloquent;

use App\Models\Customer;
use App\Models\Order;
use App\Repositories\Customer\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function getForCustomer(Customer $customer): Collection
    {
        return Order::query()
            ->where('customer_id', $customer->id)
            ->with(['orderBaskets' => fn ($query) => $query->orderBy('id')])
            ->orderByDesc('created_at')
            ->get();
    }

    public function findByIdForCustomer(Customer $customer, int $orderId): ?Order
    {
        return Order::query()
            ->where('customer_id', $customer->id)
            ->whereKey($orderId)
            ->with(['orderBaskets' => fn ($query) => $query->orderBy('id')])
            ->first();
    }
}
