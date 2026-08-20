<?php

namespace App\Services\Dispatch;

use App\Enums\ActivityAction;
use App\Exceptions\StaleTripException;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TripFareService
{
    public function __construct(
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function update(
        Trip $trip,
        float $fare,
        User $actor,
        int $expectedVersion,
    ): Trip {
        return DB::transaction(function () use (
            $trip,
            $fare,
            $actor,
            $expectedVersion
        ) {
            $lockedTrip = Trip::query()
                ->whereKey($trip->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedTrip->version !== $expectedVersion) {
                $this->activityLog->record(
                    actor: $actor,
                    action: ActivityAction::TRIP_CONCURRENCY_CONFLICT,
                    trip: $lockedTrip,
                    previousValues: [
                        'estimated_fare' => $lockedTrip->estimated_fare,
                        'version' => $lockedTrip->version,
                    ],
                    newValues: [
                        'attempted_fare' => $fare,
                        'expected_version' => $expectedVersion,
                        'current_version' => $lockedTrip->version,
                    ],
                    reason: 'Optimistic concurrency conflict',
                );

                throw new StaleTripException(
                    $lockedTrip->id,
                    $expectedVersion,
                    $lockedTrip->version,
                );
            }

            $previous = [
                'estimated_fare' => $lockedTrip->estimated_fare,
                'version' => $lockedTrip->version,
            ];

            $lockedTrip->estimated_fare = $fare;
            $lockedTrip->version++;
            $lockedTrip->save();

            $this->activityLog->record(
                actor: $actor,
                action: ActivityAction::TRIP_FARE_UPDATED,
                trip: $lockedTrip,
                previousValues: $previous,
                newValues: [
                    'estimated_fare' => $lockedTrip->estimated_fare,
                    'version' => $lockedTrip->version,
                ],
            );

            return $lockedTrip->fresh(['driver']);
        });
    }
}