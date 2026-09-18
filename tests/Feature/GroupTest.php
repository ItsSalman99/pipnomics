<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guest users can view groups index.
     */
    public function test_guest_users_can_view_groups_index(): void
    {
        $response = $this->get('/groups');

        $response->assertStatus(200);
        $response->assertViewIs('groups.index');
        $response->assertViewHas('groups');
    }

    /**
     * Test that guest users can view group show page.
     */
    public function test_guest_users_can_view_group_show(): void
    {
        $owner = User::factory()->create();
        $group = Group::forceCreate([
            'name' => 'Forex Elite',
            'description' => 'Traders of forex majors',
            'owner_id' => $owner->id
        ]);

        $response = $this->get("/groups/{$group->id}");

        $response->assertStatus(200);
        $response->assertViewIs('groups.show');
        $response->assertViewHas('group');
        $response->assertViewHas('isMember', false);
    }

    /**
     * Test that guest users cannot view group create page.
     */
    public function test_guest_users_cannot_view_group_create(): void
    {
        $response = $this->get('/groups/create');
        $response->assertRedirect('/login');
    }

    /**
     * Test that guest users cannot store a group.
     */
    public function test_guest_users_cannot_store_group(): void
    {
        $response = $this->post('/groups', [
            'name' => 'New Group',
            'description' => 'Description'
        ]);
        $response->assertRedirect('/login');
    }

    /**
     * Test that guest users cannot join a group.
     */
    public function test_guest_users_cannot_join_group(): void
    {
        $owner = User::factory()->create();
        $group = Group::forceCreate([
            'name' => 'Forex Elite',
            'description' => 'Traders of forex majors',
            'owner_id' => $owner->id
        ]);

        $response = $this->post("/groups/{$group->id}/join");
        $response->assertRedirect('/login');
    }

    /**
     * Test that guest users cannot leave a group.
     */
    public function test_guest_users_cannot_leave_group(): void
    {
        $owner = User::factory()->create();
        $group = Group::forceCreate([
            'name' => 'Forex Elite',
            'description' => 'Traders of forex majors',
            'owner_id' => $owner->id
        ]);

        $response = $this->post("/groups/{$group->id}/leave");
        $response->assertRedirect('/login');
    }

    /**
     * Test that authenticated users can view groups index.
     */
    public function test_authenticated_users_can_view_groups_index(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/groups');

        $response->assertStatus(200);
    }

    /**
     * Test that authenticated users can access create group page.
     */
    public function test_authenticated_users_can_access_create_group(): void
    {
        $user = User::factory()->create(['is_premium' => false]);
        $response = $this->actingAs($user)->get('/groups/create');
        $response->assertStatus(200);
    }

    /**
     * Test that authenticated users can create a group.
     */
    public function test_authenticated_users_can_create_group(): void
    {
        $user = User::factory()->create(['is_premium' => false]);
        $response = $this->actingAs($user)->post('/groups', [
            'name' => 'Trading Group',
            'description' => 'Discussions for everyone'
        ]);

        $group = Group::where('name', 'Trading Group')->first();
        $this->assertNotNull($group);
        $this->assertEquals($user->id, $group->owner_id);
        $response->assertRedirect(route('groups.show', $group));
    }
}
