<?php

namespace Tests\Feature\Dispatch;

use App\Enums\DriverStatus;
use App\Enums\TripStatus;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use App\Services\Dispatch\AssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_available_driver_can_be_assigned(): void
    {
        $actor = User::factory()->dispatcher()->create();

        $trip = Trip::factory()->create();

        $driver = Driver::factory()
            ->available()
            ->create();

        $updated = app(AssignmentService::class)->assign(
            $trip,
            $driver,
            $actor,
            expectedVersion: 1,
        );

        $this->assertSame(
            $driver->id,
            $updated->driver_id
        );

        $this->assertSame(
            TripStatus::ASSIGNED,
            $updated->status
        );

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'status' => DriverStatus::ASSIGNED->value,
        ]);

        $this->assertDatabaseHas('trip_status_histories', [
            'trip_id' => $trip->id,
            'previous_status' => TripStatus::PENDING->value,
            'new_status' => TripStatus::ASSIGNED->value,
            'changed_by' => $actor->id,
        ]);
    }

    public function test_unavailable_driver_is_rejected(): void
{
    $this->expectException(
        \App\Exceptions\DriverUnavailableException::class
    );

    $actor = User::factory()->dispatcher()->create();

    $trip = Trip::factory()->create();

    $driver = Driver::factory()
        ->assigned()
        ->create();

    app(AssignmentService::class)->assign(
        $trip,
        $driver,
        $actor,
        expectedVersion: 1,
    );
}
}
