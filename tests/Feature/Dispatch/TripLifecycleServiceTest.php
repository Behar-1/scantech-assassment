<?php

namespace Tests\Feature\Dispatch;

use App\Enums\DriverStatus;
use App\Enums\TripStatus;
use App\Exceptions\InvalidTripTransitionException;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use App\Services\Dispatch\TripLifecycleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripLifecycleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_trip_cannot_be_completed_directly(): void
    {
        $this->expectException(
            InvalidTripTransitionException::class
        );

        $trip = Trip::factory()->create([
            'status' => TripStatus::PENDING,
            'version' => 1,
        ]);

        $actor = User::factory()->dispatcher()->create();

        app(TripLifecycleService::class)->transition(
            trip: $trip,
            targetStatus: TripStatus::COMPLETED,
            actor: $actor,
            expectedVersion: 1,
        );
    }

    public function test_completion_releases_driver(): void
    {
        $driver = Driver::factory()
            ->assigned()
            ->create();

        $trip = Trip::factory()
            ->assigned($driver)
            ->create([
                'version' => 1,
            ]);

        $actor = User::factory()->dispatcher()->create();

        app(TripLifecycleService::class)->transition(
            trip: $trip,
            targetStatus: TripStatus::COMPLETED,
            actor: $actor,
            expectedVersion: 1,
        );

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'status' => DriverStatus::AVAILABLE->value,
        ]);
    }

    public function test_cancellation_releases_driver(): void
    {
        $driver = Driver::factory()
            ->assigned()
            ->create();

        $trip = Trip::factory()
            ->assigned($driver)
            ->create([
                'version' => 1,
            ]);

        $actor = User::factory()->supervisor()->create();

        app(TripLifecycleService::class)->transition(
            trip: $trip,
            targetStatus: TripStatus::CANCELLED,
            actor: $actor,
            expectedVersion: 1,
        );

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'status' => DriverStatus::AVAILABLE->value,
        ]);
    }
}