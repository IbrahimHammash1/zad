<?php

namespace App\Http\Resources;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerOrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lines = $this->orderBaskets->map(function ($line): array {
            $lineTotal = (float) $line->basket_price * (int) $line->quantity;

            return [
                'id' => $line->id,
                'basket_id' => $line->basket_id,
                'basket_name' => $line->basket_name,
                'basket_price' => $line->basket_price,
                'store_id' => $line->store_id,
                'store_name' => $line->store_name,
                'quantity' => $line->quantity,
                'line_total' => number_format($lineTotal, 2, '.', ''),
            ];
        })->values();

        $subtotal = $lines->reduce(
            fn (float $carry, array $line): float => $carry + (float) $line['line_total'],
            0.0,
        );

        return [
            'id' => $this->id,
            'status' => $this->formatStatus($this->status),
            'currency' => $this->currency,
            'recipient' => [
                'name' => $this->recipient_name,
                'phone' => $this->recipient_phone,
                'delivery_address' => $this->delivery_address,
                'notes' => $this->notes,
            ],
            'basket_lines' => $lines,
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'payment' => $this->whenLoaded('payment', fn (): ?array => $this->payment ? [
                'provider' => $this->payment->provider?->value,
                'status' => $this->payment->status?->value,
                'amount' => $this->payment->amount,
                'currency' => $this->payment->currency,
                'provider_reference' => $this->payment->provider_reference,
                'paid_at' => $this->payment->paid_at?->toISOString(),
            ] : null),
            'created_at' => $this->created_at?->toISOString(),
            'paid_at' => $this->paid_at?->toISOString(),
        ];
    }

    protected function formatStatus(?OrderStatus $status): array
    {
        return [
            'value' => $status?->value,
            'label' => $status?->label(),
        ];
    }
}
