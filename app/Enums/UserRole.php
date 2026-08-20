<?php

namespace App\Enums;

enum UserRole: string
{
    case DISPATCHER = 'dispatcher';
    case SUPERVISOR = 'supervisor';
    case ADMINISTRATOR = 'administrator';

    public function label(): string
    {
        return match ($this) {
            self::DISPATCHER => 'Dispatcher',
            self::SUPERVISOR => 'Supervisor',
            self::ADMINISTRATOR => 'Administrator',
        };
    }

    public function canDispatch(): bool
    {
        return match ($this) {
            self::DISPATCHER,
            self::SUPERVISOR => true,
            self::ADMINISTRATOR => false,
        };
    }

    public function canCancel(): bool
    {
        return $this === self::SUPERVISOR;
    }

    public function canReassign(): bool
    {
        return $this === self::SUPERVISOR;
    }
}