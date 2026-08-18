<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StarterSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_assessment_role_selector_is_available(): void
    {
        $this->get('/assessment-role')->assertOk();
    }
}
