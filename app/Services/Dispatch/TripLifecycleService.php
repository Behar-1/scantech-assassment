<?php

namespace App\Services\Dispatch;

use App\Enums\ActivityAction;
use App\Enums\DriverStatus;
use App\Enums\TripStatus;
use App\Exceptions\InvalidTripTransitionException;
use App\Exceptions\StaleTripException;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\TripStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TripLifecycleService
{
    public function __construct(
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function transition(
        Trip $trip,
        TripStatus $targetStatus,
        User $actor,
        ?int $expectedVersion = null,
        ?string $reason = null,
    ): Trip {
        return DB::transaction(function () use (
            $trip,
            $targetStatus,
            $actor,
            $expectedVersion,
            $reason
        ) {
            $lockedTrip = Trip::query()
                ->whereKey($trip->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $expectedVersion !== null
                && $lockedTrip->version !== $expectedVersion
            ) {
                throw new StaleTripException(
                    $lockedTrip->id,
                    $expectedVersion,
                    $lockedTrip->version,
                );
            }

            $currentStatus = $lockedTrip->status;

            if (!$currentStatus->canTransitionTo($targetStatus)) {
                throw new InvalidTripTransitionException(
                    $currentStatus,
                    $targetStatus,
                );
            }

            $driver = null;

            if ($lockedTrip->driver_id !== null) {
                $driver = Driver::query()
                    ->whereKey($lockedTrip->driver_id)
                    ->lockForUpdate()
                    ->first();
            }

            $previous = [
                'status' => $currentStatus->value,
                'driver_id' => $lockedTrip->driver_id,
                'version' => $lockedTrip->version,
            ];

            $lockedTrip->status = $targetStatus;
            $lockedTrip->version++;

            $lockedTrip->save();

            if (
                $targetStatus === TripStatus::COMPLETED
                || $targetStatus === TripStatus::CANCELLED
            ) {
                if ($driver) {
                    $driver->status = DriverStatus::AVAILABLE;
                    $driver->save();
                }
            }

            TripStatusHistory::query()->create([
                'trip_id' => $lockedTrip->id,
                'previous_status' => $currentStatus->value,
                'new_status' => $targetStatus->value,
                'changed_by' => $actor->id,
                'metadata' => [
                    'source' => 'trip_lifecycle_service',
                    'reason' => $reason,
                ],
                'created_at' => now(),
            ]);

            $action = $targetStatus === TripStatus::CANCELLED
                ? ActivityAction::TRIP_CANCELLED
                : ActivityAction::TRIP_STATUS_CHANGED;

            $this->activityLog->record(
                actor: $actor,
                action: $action,
                trip: $lockedTrip,
                previousValues: $previous,
                newValues: [
                    'status' => $lockedTrip->status->value,
                    'driver_id' => $lockedTrip->driver_id,
                    'version' => $lockedTrip->version,
                ],
                reason: $reason,
            );

            return $lockedTrip->fresh(['driver']);
        });
    }
}