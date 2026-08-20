<?php

namespace Tests\Feature\Dispatch;

use App\Enums\UserRole;
use App\Livewire\DispatchBoard;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_without_dispatch_permission_cannot_assign(): void
    {
        $administrator = User::factory()->administrator()->create();

        $trip = Trip::factory()->create();

        $this->actingAs($administrator);

        Livewire::test(DispatchBoard::class)
            ->set('targetDriverId', 1)
            ->call('assignDriver', $trip->id)
            ->assertForbidden();
    }
}