<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function baskets(): BelongsToMany
    {
        return $this->belongsToMany(Basket::class, 'basket_store')->withTimestamps();
    }

    public function orderBaskets(): HasMany
    {
        return $this->hasMany(OrderBasket::class);
    }
}
