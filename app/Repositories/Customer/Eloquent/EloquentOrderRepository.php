<?php

namespace App\Repositories\Customer\Eloquent;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Payments\Data\PaymentResultData;
use App\Repositories\Customer\Contracts\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function getForCustomer(Customer $customer): Collection
    {
        return Order::query()
            ->where('customer_id', $customer->id)
            ->with(['orderBaskets' => fn ($query) => $query->orderBy('id'), 'payment'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function findByIdForCustomer(Customer $customer, int $orderId): ?Order
    {
        return Order::query()
            ->where('customer_id', $customer->id)
            ->whereKey($orderId)
            ->with(['orderBaskets' => fn ($query) => $query->orderBy('id'), 'payment'])
            ->first();
    }

    public function createPaidOrder(Customer $customer, array $review, PaymentResultData $paymentResult): Order
    {
        $order = Order::query()->create([
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending,
            'currency' => $review['currency'],
            'recipient_name' => $review['recipient']['name'],
            'recipient_phone' => $review['recipient']['phone'],
            'delivery_address' => $review['recipient']['delivery_address'],
            'notes' => $review['recipient']['notes'],
            'paid_at' => $paymentResult->paidAt,
        ]);

        foreach ($review['basket_lines'] as $line) {
            $order->orderBaskets()->create([
                'basket_id' => $line['basket_id'],
                'store_id' => $line['store_id'],
                'quantity' => $line['quantity'],
            ]);
        }

        $order->payment()->create([
            'provider' => $paymentResult->provider,
            'provider_reference' => $paymentResult->providerReference,
            'currency' => $review['currency'],
            'amount' => $review['subtotal'],
            'status' => $paymentResult->status,
            'paid_at' => $paymentResult->paidAt,
        ]);

        return $order->load(['orderBaskets' => fn ($query) => $query->orderBy('id'), 'payment']);
    }
}
