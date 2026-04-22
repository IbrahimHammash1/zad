<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasketDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'fixed_price' => $this->fixed_price,
            'materials' => $this->basketItems->map(fn ($basketItem): array => [
                'id' => $basketItem->material->id,
                'name' => $basketItem->material->name,
                'slug' => $basketItem->material->slug,
                'unit' => $basketItem->material->unit,
                'quantity' => $basketItem->quantity,
                'sort_order' => $basketItem->sort_order,
            ])->values(),
            'approved_stores' => $this->stores->map(fn ($store): array => [
                'id' => $store->id,
                'name' => $store->name,
                'phone' => $store->phone,
                'address' => $store->address,
            ])->values(),
        ];
    }
}
