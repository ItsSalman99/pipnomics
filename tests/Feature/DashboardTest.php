<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guest users can view dashboard / home page with top groups.
     */
    public function test_guest_users_can_view_dashboard_with_groups(): void
    {
        $owner = User::factory()->create();
        for ($i = 1; $i <= 12; $i++) {
            $group = Group::create([
                'name' => "Community Group {$i}",
                'description' => "Description for group {$i}",
                'owner_id' => $owner->id,
            ]);
            $group->members()->attach($owner->id);
            $group->posts()->create([
                'user_id' => $owner->id,
                'content' => "Test post for group {$i}",
            ]);
        }

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
        $response->assertViewHas('groups');
        $response->assertViewHas('newsItems');
    }

    /**
     * Test that authenticated users can view dashboard with top groups.
     */
    public function test_authenticated_users_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
        $response->assertViewHas('groups');
    }
}

