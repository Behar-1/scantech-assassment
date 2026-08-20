<?php

namespace App\Enums;

enum TripStatus: string
{
    case PENDING = 'pending';
    case ASSIGNED = 'assigned';
    case DRIVER_ARRIVING = 'driver_arriving';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ASSIGNED => 'Assigned',
            self::DRIVER_ARRIVING => 'Driver arriving',
            self::IN_PROGRESS => 'In progress',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::COMPLETED,
            self::CANCELLED,
        ], true);
    }

    public function isCancellable(): bool
    {
        return ! $this->isTerminal();
    }

    /**
     * @return list<self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDING => [
                self::ASSIGNED,
                self::CANCELLED,
            ],
            self::ASSIGNED => [
                self::DRIVER_ARRIVING,
                self::CANCELLED,
            ],
            self::DRIVER_ARRIVING => [
                self::IN_PROGRESS,
                self::CANCELLED,
            ],
            self::IN_PROGRESS => [
                self::COMPLETED,
                self::CANCELLED,
            ],
            self::COMPLETED,
            self::CANCELLED => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }
}