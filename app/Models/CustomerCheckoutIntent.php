<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerCheckoutIntent extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'currency',
        'recipient_name',
        'recipient_phone',
        'delivery_address',
        'notes',
        'line_items',
        'subtotal',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'line_items' => 'array',
            'subtotal' => 'decimal:2',
            'expires_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
