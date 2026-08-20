<?php

namespace Tests\Feature\Dispatch;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use App\Services\Dispatch\ActivityLogService;
use App\Services\Dispatch\AssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class AtomicityTest extends TestCase
{
    use RefreshDatabase;

    public function test_assignment_rolls_back_when_a_required_step_fails(): void
    {
        $actor = User::factory()->dispatcher()->create();

        $trip = Trip::factory()->create();

        $driver = Driver::factory()->available()->create();

        $this->mock(ActivityLogService::class)
            ->shouldReceive('record')
            ->once()
            ->andThrow(new RuntimeException('Audit failure'));

        try {
            app(AssignmentService::class)->assign(
                trip: $trip,
                targetDriver: $driver,
                actor: $actor,
                expectedVersion: 1,
            );
        } catch (RuntimeException) {
            // Expected.
        }

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'driver_id' => null,
            'version' => 1,
        ]);

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'status' => 'available',
        ]);

        $this->assertDatabaseMissing('trip_status_histories', [
            'trip_id' => $trip->id,
        ]);
    }
}