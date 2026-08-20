<?php

namespace App\Enums;

enum ActivityAction: string
{
    case TRIP_ASSIGNED = 'trip.assigned';
    case TRIP_REASSIGNED = 'trip.reassigned';
    case TRIP_CANCELLED = 'trip.cancelled';
    case TRIP_STATUS_CHANGED = 'trip.status_changed';
    case TRIP_FARE_UPDATED = 'trip.fare_updated';
    case TRIP_CONCURRENCY_CONFLICT = 'trip.concurrency_conflict';
}