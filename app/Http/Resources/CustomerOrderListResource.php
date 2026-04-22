<?php

namespace App\Http\Resources;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerOrderListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $subtotal = $this->orderBaskets->reduce(
            fn (float $carry, $line): float => $carry + ((float) $line->basket_price * (int) $line->quantity),
            0.0,
        );

        return [
            'id' => $this->id,
            'status' => $this->formatStatus($this->status),
            'currency' => $this->currency,
            'basket_lines_count' => $this->orderBaskets->count(),
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'created_at' => $this->created_at?->toISOString(),
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
