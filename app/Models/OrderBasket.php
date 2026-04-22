<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class OrderBasket extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'basket_id',
        'store_id',
        'quantity',
        'basket_name',
        'basket_price',
        'store_name',
    ];

    protected function casts(): array
    {
        return [
            'basket_price' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (OrderBasket $orderBasket): void {
            static::ensureValidBasket($orderBasket);
            static::ensureValidStore($orderBasket);
            static::ensureApprovedStoreAssignment($orderBasket);
            static::ensureValidQuantity($orderBasket);
        });

        static::creating(function (OrderBasket $orderBasket): void {
            static::fillHistoricalValues($orderBasket);
        });

        static::updating(function (OrderBasket $orderBasket): void {
            if (
                $orderBasket->isDirty(['basket_id', 'store_id']) ||
                blank($orderBasket->basket_name) ||
                blank($orderBasket->basket_price) ||
                blank($orderBasket->store_name)
            ) {
                static::fillHistoricalValues($orderBasket);
            }
        });
    }

    protected static function ensureValidBasket(OrderBasket $orderBasket): void
    {
        $isValidBasket = Basket::query()
            ->whereKey($orderBasket->basket_id)
            ->where('is_active', true)
            ->exists();

        if (! $isValidBasket) {
            throw ValidationException::withMessages([
                'basket_id' => 'Order baskets can only reference active baskets.',
            ]);
        }
    }

    protected static function ensureValidStore(OrderBasket $orderBasket): void
    {
        $isValidStore = Store::query()
            ->whereKey($orderBasket->store_id)
            ->where('is_active', true)
            ->exists();

        if (! $isValidStore) {
            throw ValidationException::withMessages([
                'store_id' => 'Order baskets can only reference active stores.',
            ]);
        }
    }

    protected static function ensureApprovedStoreAssignment(OrderBasket $orderBasket): void
    {
        $isApprovedPair = Basket::query()
            ->whereKey($orderBasket->basket_id)
            ->whereHas('stores', fn ($query) => $query->whereKey($orderBasket->store_id))
            ->exists();

        if (! $isApprovedPair) {
            throw ValidationException::withMessages([
                'store_id' => 'The selected store is not approved for this basket.',
            ]);
        }
    }

    protected static function ensureValidQuantity(OrderBasket $orderBasket): void
    {
        if ($orderBasket->quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Order basket quantity must be at least 1.',
            ]);
        }
    }

    protected static function fillHistoricalValues(OrderBasket $orderBasket): void
    {
        $basket = Basket::query()->findOrFail($orderBasket->basket_id);
        $store = Store::query()->findOrFail($orderBasket->store_id);

        $orderBasket->basket_name = $orderBasket->basket_name ?: $basket->name;
        $orderBasket->basket_price = $orderBasket->basket_price ?: $basket->fixed_price;
        $orderBasket->store_name = $orderBasket->store_name ?: $store->name;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function basket(): BelongsTo
    {
        return $this->belongsTo(Basket::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}
