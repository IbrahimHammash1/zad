<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email' => $this->user?->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'preferred_locale' => $this->preferred_locale,
        ];
    }
}
