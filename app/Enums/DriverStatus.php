<?php

namespace App\Enums;

enum DriverStatus: string
{
    case OFFLINE = 'offline';
    case AVAILABLE = 'available';
    case ASSIGNED = 'assigned';
    case ON_TRIP = 'on_trip';

    public function label(): string
    {
        return match ($this) {
            self::OFFLINE => 'Offline',
            self::AVAILABLE => 'Available',
            self::ASSIGNED => 'Assigned',
            self::ON_TRIP => 'On trip',
        };
    }
}