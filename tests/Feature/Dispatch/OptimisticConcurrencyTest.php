<?php

namespace Tests\Feature\Dispatch;

use App\Exceptions\StaleTripException;
use App\Models\Trip;
use App\Models\User;
use App\Services\Dispatch\TripFareService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OptimisticConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_stale_fare_update_does_not_overwrite_current_value(): void
    {
        $trip = Trip::factory()->create([
            'estimated_fare' => 25.00,
            'version' => 2,
        ]);

        $actor = User::factory()->dispatcher()->create();

        $this->expectException(StaleTripException::class);

        app(TripFareService::class)->update(
            trip: $trip,
            fare: 99.00,
            actor: $actor,
            expectedVersion: 1,
        );

        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'estimated_fare' => 25.00,
            'version' => 2,
        ]);
    }
}