<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Basket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'fixed_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'fixed_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return Storage::disk('public')->url($this->image);
    }

    public function basketItems(): HasMany
    {
        return $this->hasMany(BasketItem::class);
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'basket_items')
            ->withPivot(['quantity', 'sort_order'])
            ->withTimestamps();
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'basket_store')->withTimestamps();
    }

    public function orderBaskets(): HasMany
    {
        return $this->hasMany(OrderBasket::class);
    }

    public function scopeAvailableForCustomer(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereHas('stores', fn (Builder $storeQuery): Builder => $storeQuery->where('is_active', true));
    }
}
