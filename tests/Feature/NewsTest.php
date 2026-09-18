<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\NewsArticle;
use App\Models\NewsComment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guest users can view news.
     */
    public function test_guest_users_can_view_news(): void
    {
        NewsArticle::create([
            'title' => 'EUR/USD Market Update',
            'image' => 'https://example.com/image.jpg',
            'description' => 'EUR/USD edges higher.',
            'url' => 'https://example.com/eurusd-1',
            'source' => 'FXStreet',
            'published_at' => now(),
        ]);

        $response = $this->get('/news');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('News/Index')
            ->has('news')
        );
    }

    /**
     * Test that authenticated users can view news.
     */
    public function test_authenticated_users_can_view_news(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/news');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('News/Index')
            ->has('news')
        );
    }

    /**
     * Test that authenticated users can post a comment on a news article.
     */
    public function test_authenticated_users_can_comment_on_news(): void
    {
        $user = User::factory()->create(['name' => 'John Trader']);
        $article = NewsArticle::create([
            'title' => 'Gold Technical Analysis',
            'image' => 'https://example.com/gold.jpg',
            'description' => 'Gold price approaches key resistance.',
            'url' => 'https://example.com/gold-analysis-1',
            'source' => 'FXStreet',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($user)->post("/news/{$article->id}/comments", [
            'content' => 'Great technical breakdown, watching $2400 level closely.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('news_comments', [
            'news_article_id' => $article->id,
            'user_id' => $user->id,
            'content' => 'Great technical breakdown, watching $2400 level closely.',
        ]);
        $this->assertEquals(1, $article->fresh()->comments()->count());
    }

    /**
     * Test that guest users cannot comment on news.
     */
    public function test_guest_users_cannot_comment_on_news(): void
    {
        $article = NewsArticle::create([
            'title' => 'USD/JPY Update',
            'url' => 'https://example.com/usdjpy-1',
            'description' => 'BoJ rate expectations.',
            'published_at' => now(),
        ]);

        $response = $this->post("/news/{$article->id}/comments", [
            'content' => 'Guest comment attempt',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('news_comments', 0);
    }
}
