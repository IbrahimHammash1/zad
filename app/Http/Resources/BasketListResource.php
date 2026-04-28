<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BasketListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'fixed_price' => $this->fixed_price,
            'image_url' => $this->image_url,
            'materials_count' => $this->basketItems->count(),
            'approved_stores_count' => $this->stores->count(),
        ];
    }
}
