<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_agent_id');
    }

    public function operationalOrders(): HasMany
    {
        return $this->orders()
            ->whereIn('status', [
                OrderStatus::Pending->value,
                OrderStatus::Assigned->value,
                OrderStatus::InProgress->value,
            ])
            ->latest();
    }
}
