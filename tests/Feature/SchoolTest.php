<?php

namespace Tests\Feature;

use App\Models\SchoolApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_school_registration_page(): void
    {
        $response = $this->get('/school');

        $response->assertStatus(200);
        $response->assertViewIs('school.index');
    }

    public function test_authenticated_user_can_view_school_registration_page(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/school');

        $response->assertStatus(200);
        $response->assertViewIs('school.index');
    }

    public function test_guest_can_submit_school_application(): void
    {
        $response = $this->post('/school', [
            'name' => 'Sarah Johnson',
            'email' => 'sarah@example.com',
            'phone' => '+15551234567',
            'experience_level' => 'beginner',
            'preferred_track' => 'technical_analysis',
            'schedule' => 'weekends',
            'goals' => 'Learn chart patterns and risk management'
        ]);

        $this->assertDatabaseHas('school_applications', [
            'name' => 'Sarah Johnson',
            'email' => 'sarah@example.com',
            'phone' => '+15551234567',
            'experience_level' => 'beginner',
            'preferred_track' => 'technical_analysis',
            'schedule' => 'weekends',
            'status' => 'pending'
        ]);

        $response->assertRedirect(route('school.index'));
    }

    public function test_authenticated_user_can_submit_school_application_via_ajax(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/school', [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '+447700900077',
            'experience_level' => 'intermediate',
            'preferred_track' => 'macro_fundamentals',
            'schedule' => 'mentorship',
            'goals' => 'Macro fundamentals mastery'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true
        ]);

        $this->assertDatabaseHas('school_applications', [
            'user_id' => $user->id,
            'email' => $user->email,
            'preferred_track' => 'macro_fundamentals'
        ]);
    }
}
