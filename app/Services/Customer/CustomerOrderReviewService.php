<?php

namespace App\Services\Customer;

use App\Models\Basket;
use App\Repositories\Customer\Contracts\BasketRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class CustomerOrderReviewService
{
    public function __construct(protected BasketRepositoryInterface $basketRepository) {}

    public function review(array $payload): array
    {
        $normalizedLines = $this->normalizeLines($payload['basket_lines']);
        $basketIds = $normalizedLines->pluck('basket_id')->unique()->values()->all();
        $baskets = $this->basketRepository->getActiveByIdsForCheckout($basketIds);

        if (count($basketIds) !== $baskets->count()) {
            throw ValidationException::withMessages([
                'basket_lines' => 'One or more selected baskets are invalid or inactive.',
            ]);
        }

        $reviewLines = [];
        $subtotal = 0.0;

        foreach ($normalizedLines as $line) {
            /** @var Basket $basket */
            $basket = $baskets->get($line['basket_id']);
            $store = $basket->stores->firstWhere('id', $line['store_id']);

            if (! $store) {
                throw ValidationException::withMessages([
                    'basket_lines' => sprintf(
                        'Store %d is not approved for basket %d.',
                        $line['store_id'],
                        $line['basket_id'],
                    ),
                ]);
            }

            $unitPrice = (float) $basket->fixed_price;
            $lineTotal = $unitPrice * $line['quantity'];
            $subtotal += $lineTotal;

            $reviewLines[] = [
                'basket_id' => $basket->id,
                'basket_name' => $basket->name,
                'basket_slug' => $basket->slug,
                'store_id' => $store->id,
                'store_name' => $store->name,
                'quantity' => $line['quantity'],
                'unit_price' => $this->formatMoney($unitPrice),
                'line_total' => $this->formatMoney($lineTotal),
            ];
        }

        return [
            'currency' => 'USD',
            'recipient' => [
                'name' => $payload['recipient_name'],
                'phone' => $payload['recipient_phone'],
                'delivery_address' => $payload['delivery_address'],
                'notes' => $payload['notes'] ?? null,
            ],
            'basket_lines' => $reviewLines,
            'subtotal' => $this->formatMoney($subtotal),
        ];
    }

    protected function normalizeLines(array $basketLines): Collection
    {
        return collect($basketLines)
            ->map(fn (array $line): array => [
                'basket_id' => (int) $line['basket_id'],
                'store_id' => (int) $line['store_id'],
                'quantity' => (int) $line['quantity'],
            ])
            ->groupBy(fn (array $line): string => $line['basket_id'].'_'.$line['store_id'])
            ->map(function (Collection $group): array {
                $first = $group->first();

                return [
                    'basket_id' => $first['basket_id'],
                    'store_id' => $first['store_id'],
                    'quantity' => $group->sum('quantity'),
                ];
            })
            ->values();
    }

    protected function formatMoney(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }
}
