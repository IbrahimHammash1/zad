<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Assigned => 'Assigned',
            self::InProgress => 'In Progress',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * @return array<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Pending, self::Assigned, self::Cancelled],
            self::Assigned => [self::Assigned, self::InProgress, self::Cancelled],
            self::InProgress => [self::InProgress, self::Delivered, self::Cancelled],
            self::Delivered => [self::Delivered],
            self::Cancelled => [self::Cancelled],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    /**
     * @return array<string, string>
     */
    public static function options(?self $current = null): array
    {
        $cases = $current?->allowedTransitions() ?? self::cases();

        return collect($cases)
            ->mapWithKeys(fn (self $status): array => [$status->value => $status->label()])
            ->all();
    }
}
