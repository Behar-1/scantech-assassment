<?php

namespace App\Services\Dispatch;

use App\Enums\ActivityAction;
use App\Enums\DriverStatus;
use App\Enums\TripStatus;
use App\Exceptions\DriverUnavailableException;
use App\Exceptions\StaleTripException;
use App\Exceptions\TripNotAssignableException;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\TripStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignmentService
{
    public function __construct(
        private readonly ActivityLogService $activityLog,
    ) {
    }

    public function assign(
        Trip $trip,
        Driver $targetDriver,
        User $actor,
        ?int $expectedVersion = null,
    ): Trip {
        return DB::transaction(function () use (
            $trip,
            $targetDriver,
            $actor,
            $expectedVersion
        ) {
            /*
             * Lock all affected drivers in deterministic ID order.
             *
             * This is important for reassignment and concurrent requests.
             */
            $driverIds = collect([
                $trip->driver_id,
                $targetDriver->id,
            ])
                ->filter()
                ->unique()
                ->sort()
                ->values();

            $lockedDrivers = Driver::query()
                ->whereIn('id', $driverIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

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

            if (
                $currentStatus !== TripStatus::PENDING
                && $lockedTrip->driver_id !== null
            ) {
                /*
                 * Reassignment is a supervisor-level use case and the
                 * policy decides whether this actor is allowed to perform it.
                 */
            }

            if (
                $currentStatus !== TripStatus::PENDING
                && $lockedTrip->driver_id === null
            ) {
                throw new TripNotAssignableException();
            }

            $target = $lockedDrivers->get($targetDriver->id);

            if (!$target || $target->status !== DriverStatus::AVAILABLE) {
                throw new DriverUnavailableException();
            }

            $oldDriver = $lockedTrip->driver_id
                ? $lockedDrivers->get($lockedTrip->driver_id)
                : null;

            $previous = [
                'driver_id' => $lockedTrip->driver_id,
                'status' => $lockedTrip->status->value,
                'version' => $lockedTrip->version,
            ];

            $isReassignment = $oldDriver !== null
                && $oldDriver->id !== $target->id;

            if ($isReassignment) {
                $oldDriver->status = DriverStatus::AVAILABLE;
                $oldDriver->save();
            }

            $target->status = DriverStatus::ASSIGNED;
            $target->save();

            $previousStatus = $lockedTrip->status;

            if ($lockedTrip->status === TripStatus::PENDING) {
                $lockedTrip->status = TripStatus::ASSIGNED;
            }

            $lockedTrip->driver_id = $target->id;
            $lockedTrip->version++;

            $lockedTrip->save();

            TripStatusHistory::query()->create([
                'trip_id' => $lockedTrip->id,
                'previous_status' => $previousStatus?->value,
                'new_status' => $lockedTrip->status->value,
                'changed_by' => $actor->id,
                'metadata' => [
                    'source' => 'assignment_service',
                    'reassignment' => $isReassignment,
                    'previous_driver_id' => $oldDriver?->id,
                    'new_driver_id' => $target->id,
                ],
                'created_at' => now(),
            ]);

            $action = $isReassignment
                ? ActivityAction::TRIP_REASSIGNED
                : ActivityAction::TRIP_ASSIGNED;

            $this->activityLog->record(
                actor: $actor,
                action: $action,
                trip: $lockedTrip,
                previousValues: $previous,
                newValues: [
                    'driver_id' => $lockedTrip->driver_id,
                    'status' => $lockedTrip->status->value,
                    'version' => $lockedTrip->version,
                ],
            );

            return $lockedTrip->fresh(['driver']);
        });
    }
}