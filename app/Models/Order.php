<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'delivery_agent_id',
        'status',
        'currency',
        'recipient_name',
        'recipient_phone',
        'delivery_address',
        'notes',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Order $order): void {
            static::ensureValidInitialStatus($order);
            static::ensureValidStatusTransition($order);
            static::ensureActiveDeliveryAgent($order);
            static::ensureDeliveryAgentForOperationalStatuses($order);
        });

        static::created(function (Order $order): void {
            $order->statusHistories()->create([
                'from_status' => null,
                'to_status' => $order->status?->value,
                'changed_by_user_id' => Auth::id(),
                'notes' => 'Order created.',
                'changed_at' => now(),
            ]);
        });

        static::updated(function (Order $order): void {
            if ($order->wasChanged('status')) {
                $order->statusHistories()->create([
                    'from_status' => $order->getRawOriginal('status'),
                    'to_status' => $order->status?->value,
                    'changed_by_user_id' => Auth::id(),
                    'notes' => null,
                    'changed_at' => now(),
                ]);
            }

            if ($order->wasChanged('delivery_agent_id')) {
                $order->statusHistories()->create([
                    'from_status' => $order->status?->value,
                    'to_status' => $order->status?->value,
                    'changed_by_user_id' => Auth::id(),
                    'notes' => static::formatDeliveryAgentChangeNote(
                        $order->getRawOriginal('delivery_agent_id'),
                        $order->delivery_agent_id !== null ? (int) $order->delivery_agent_id : null,
                    ),
                    'changed_at' => now(),
                ]);
            }
        });
    }

    protected static function ensureValidInitialStatus(Order $order): void
    {
        if (! $order->exists && $order->status !== OrderStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => 'New orders must start with Pending status.',
            ]);
        }
    }

    protected static function ensureValidStatusTransition(Order $order): void
    {
        if (! $order->exists || ! $order->isDirty('status')) {
            return;
        }

        $previousStatus = OrderStatus::from($order->getRawOriginal('status'));

        if (! $previousStatus->canTransitionTo($order->status)) {
            throw ValidationException::withMessages([
                'status' => sprintf(
                    'Invalid status transition from %s to %s.',
                    $previousStatus->label(),
                    $order->status->label(),
                ),
            ]);
        }
    }

    protected static function ensureActiveDeliveryAgent(Order $order): void
    {
        if ($order->delivery_agent_id === null) {
            return;
        }

        $isActiveAgent = DeliveryAgent::query()
            ->whereKey($order->delivery_agent_id)
            ->where('is_active', true)
            ->exists();

        if (! $isActiveAgent) {
            throw ValidationException::withMessages([
                'delivery_agent_id' => 'Orders can only be assigned to active delivery agents.',
            ]);
        }
    }

    protected static function ensureDeliveryAgentForOperationalStatuses(Order $order): void
    {
        if ($order->delivery_agent_id !== null) {
            return;
        }

        if (in_array($order->status, [OrderStatus::Assigned, OrderStatus::InProgress, OrderStatus::Delivered], true)) {
            throw ValidationException::withMessages([
                'delivery_agent_id' => 'Assigned, In Progress, and Delivered orders require a delivery agent.',
            ]);
        }
    }

    protected static function formatDeliveryAgentChangeNote(?int $previousAgentId, ?int $currentAgentId): string
    {
        $previousAgentName = $previousAgentId
            ? DeliveryAgent::query()->whereKey($previousAgentId)->value('name')
            : null;
        $currentAgentName = $currentAgentId
            ? DeliveryAgent::query()->whereKey($currentAgentId)->value('name')
            : null;

        return sprintf(
            'Delivery agent changed from %s to %s.',
            $previousAgentName ?? 'Unassigned',
            $currentAgentName ?? 'Unassigned',
        );
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliveryAgent(): BelongsTo
    {
        return $this->belongsTo(DeliveryAgent::class);
    }

    public function orderBaskets(): HasMany
    {
        return $this->hasMany(OrderBasket::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
