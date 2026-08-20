<?php

namespace App\Policies;

use App\Models\Trip;
use App\Models\User;

class TripPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canDispatchTrips();
    }

    public function view(User $user, Trip $trip): bool
    {
        return $user->canDispatchTrips();
    }

    public function assign(User $user, Trip $trip): bool
    {
        if (!$user->canDispatchTrips()) {
            return false;
        }

        if ($trip->driver_id !== null) {
            return $user->isSupervisor();
        }

        return true;
    }

    public function changeStatus(User $user, Trip $trip): bool
    {
        return $user->canDispatchTrips();
    }

    public function cancel(User $user, Trip $trip): bool
    {
        return $user->isSupervisor();
    }

    public function updateFare(User $user, Trip $trip): bool
    {
        return $user->canDispatchTrips();
    }
}