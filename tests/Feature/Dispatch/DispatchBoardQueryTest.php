<?php

namespace Tests\Feature\Dispatch;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use App\Queries\DispatchBoardQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DispatchBoardQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatch_board_uses_eager_loaded_drivers(): void
    {
        User::factory()->dispatcher()->create();

        $drivers = Driver::factory()->count(5)->create();

        Trip::factory()
            ->count(30)
            ->sequence(fn ($sequence) => [
                'driver_id' => $drivers[$sequence->index % 5]->id,
            ])
            ->create();

        DB::enableQueryLog();

        app(DispatchBoardQuery::class)->trips(
            search: '',
            status: null,
            driverId: null,
            perPage: 15,
        );

        $queries = DB::getQueryLog();

        $this->assertLessThan(
            10,
            count($queries),
            'Dispatch board query count suggests an N+1 regression.'
        );
    }
}