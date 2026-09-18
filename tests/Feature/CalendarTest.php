<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guest users can view the calendar.
     */
    public function test_guest_users_can_view_calendar(): void
    {
        $response = $this->get('/calendar');

        $response->assertStatus(200);
        $response->assertViewIs('calendar.index');
        $response->assertViewHas('events');
    }

    /**
     * Test that authenticated users can access the calendar and receive events.
     */
    public function test_authenticated_users_can_view_calendar(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/calendar');

        $response->assertStatus(200);
        $response->assertViewIs('calendar.index');
        $response->assertViewHas('events');
    }
}

