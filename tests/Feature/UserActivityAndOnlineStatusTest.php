<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\NewsArticle;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserActivityAndOnlineStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_login_updates_online_status_and_logs_activity(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'is_online' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');

        $user->refresh();
        $this->assertTrue($user->is_online);
        $this->assertNotNull($user->last_login_at);
        $this->assertNotNull($user->last_seen_at);

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $user->id,
            'activity_type' => 'login',
        ]);
    }

    public function test_user_logout_updates_online_status_and_logs_activity(): void
    {
        $user = User::factory()->create([
            'is_online' => true,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');

        $user->refresh();
        $this->assertFalse($user->is_online);
        $this->assertNotNull($user->last_logout_at);

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $user->id,
            'activity_type' => 'logout',
        ]);
    }

    public function test_user_registration_sets_online_and_logs_activity(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Trader',
            'email' => 'john@pipfolio.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertRedirect('/dashboard');

        $user = User::where('email', 'john@pipfolio.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->is_online);
        $this->assertNotNull($user->last_login_at);

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $user->id,
            'activity_type' => 'register',
        ]);
    }

    public function test_news_comment_logs_activity(): void
    {
        $user = User::factory()->create();
        $article = NewsArticle::create([
            'title' => 'EUR/USD Surge',
            'url' => 'https://www.fxstreet.com/news/eurusd-surge-12345',
            'description' => 'EUR/USD is surging today.',
            'source' => 'FXStreet',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->post("/news/{$article->id}/comments", [
            'content' => 'Great technical breakdown on EUR/USD!',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('news_comments', [
            'user_id' => $user->id,
            'news_article_id' => $article->id,
            'content' => 'Great technical breakdown on EUR/USD!',
        ]);

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $user->id,
            'activity_type' => 'comment_created',
        ]);
    }

    public function test_group_actions_log_activities(): void
    {
        $user = User::factory()->create(['is_premium' => true]);

        // Group creation
        $response = $this->actingAs($user)->post('/groups', [
            'name' => 'Gold Scalpers Club',
            'description' => 'Trading XAU/USD daily setups',
        ]);

        $group = Group::where('name', 'Gold Scalpers Club')->first();
        $this->assertNotNull($group);

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $user->id,
            'activity_type' => 'group_created',
        ]);

        // Post creation
        $response = $this->actingAs($user)->post("/groups/{$group->id}/posts", [
            'title' => 'Gold Resistance at 2750',
            'content' => 'Watch for pullback at resistance level.',
        ]);

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $user->id,
            'activity_type' => 'post_created',
        ]);

        // Another user joining & leaving
        $otherUser = User::factory()->create();
        $this->actingAs($otherUser)->post("/groups/{$group->id}/join");

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $otherUser->id,
            'activity_type' => 'group_joined',
        ]);

        $this->actingAs($otherUser)->post("/groups/{$group->id}/leave");

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $otherUser->id,
            'activity_type' => 'group_left',
        ]);
    }

    public function test_profile_update_and_premium_toggle_log_activity(): void
    {
        $user = User::factory()->create();

        // Profile update
        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Updated Trader Name',
            'email' => $user->email,
        ]);

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $user->id,
            'activity_type' => 'profile_updated',
        ]);

        // Premium toggle
        $response = $this->actingAs($user)->post('/profile/toggle-premium');

        $this->assertDatabaseHas('user_activities', [
            'user_id' => $user->id,
            'activity_type' => 'premium_toggled',
        ]);
    }

    public function test_profile_page_loads_with_activity_history(): void
    {
        $user = User::factory()->create();
        UserActivity::log($user, 'login', 'Logged in from test browser');

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Profile/Edit')
            ->has('activities', 1)
        );
    }

    public function test_dashboard_shows_only_currently_online_traders(): void
    {
        // 2 online users
        $onlineUser1 = User::factory()->create(['is_online' => true, 'last_seen_at' => now()]);
        $onlineUser2 = User::factory()->create(['is_online' => true, 'last_seen_at' => now()->subMinutes(2)]);
        // 1 offline user
        $offlineUser = User::factory()->create(['is_online' => false, 'last_seen_at' => now()->subHours(2)]);
        // 1 stale online user (>15 min)
        $staleUser = User::factory()->create(['is_online' => true, 'last_seen_at' => now()->subMinutes(30)]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('onlineTraders', 2)
            ->where('onlineTraders.0.id', $onlineUser1->id)
            ->where('onlineTraders.1.id', $onlineUser2->id)
        );

        // Stale user was automatically marked offline
        $staleUser->refresh();
        $this->assertFalse($staleUser->is_online);
    }
}
