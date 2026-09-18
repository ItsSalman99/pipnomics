<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Group;
use App\Models\NewsArticle;
use App\Models\NewsComment;
use App\Services\NewsService;

use App\Models\User;

class DashboardController extends Controller
{
    protected NewsService $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    public function index()
    {
        // Auto-expire stale online users inactive for more than 15 minutes
        User::where('is_online', true)
            ->where('last_seen_at', '<', now()->subMinutes(15))
            ->update(['is_online' => false]);

        // Get currently online users
        $onlineTraders = User::where('is_online', true)
            ->select('id', 'name', 'email', 'is_premium', 'is_online', 'last_seen_at')
            ->orderByDesc('last_seen_at')
            ->get();

        // Top 10 community forums by maximum members or posts
        $groups = Group::withCount(['members', 'posts'])
            ->with('owner:id,name,is_online')
            ->orderByRaw('(members_count + posts_count) DESC')
            ->orderByDesc('members_count')
            ->orderByDesc('posts_count')
            ->limit(10)
            ->get();

        // Get news and analysis from database (synced from FXStreet)
        $allArticles = $this->newsService->syncAndGetLatest(30);

        $news = $allArticles->filter(fn ($item) => !$item->is_analysis)->values();
        $analysis = $allArticles->filter(fn ($item) => $item->is_analysis)->values();

        // Real comments from database on news articles
        $comments = NewsComment::with(['user:id,name,is_online', 'newsArticle:id,title,url'])
            ->latest()
            ->limit(12)
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'username' => $c->user->name ?? 'Trader',
                    'is_online' => $c->user->is_online ?? false,
                    'comment' => $c->content,
                    'article_title' => $c->newsArticle->title ?? 'Market Update',
                    'article_url' => $c->newsArticle->url ?? '#',
                    'published_at' => $c->created_at->toIso8601String(),
                ];
            });

        return Inertia::render('Dashboard', [
            'groups' => $groups,
            'newsItems' => $news,
            'analysisItems' => $analysis,
            'commentsItems' => $comments,
            'onlineTraders' => $onlineTraders,
            'onlineTradersCount' => $onlineTraders->count(),
            'canLogin' => \Illuminate\Support\Facades\Route::has('login'),
            'canRegister' => \Illuminate\Support\Facades\Route::has('register'),
        ]);
    }
}
