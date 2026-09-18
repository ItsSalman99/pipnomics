<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\NewsArticle;
use App\Services\NewsService;

class NewsController extends Controller
{
    protected NewsService $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    public function index()
    {
        // Sync from FXStreet and fetch latest articles from database with comments
        $newsList = $this->newsService->syncAndGetLatest(50);

        return Inertia::render('News/Index', ['news' => $newsList]);
    }

    public function storeComment(Request $request, NewsArticle $news)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $news->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        \App\Models\UserActivity::log(
            $request->user(),
            'comment_created',
            "Commented on news: \"{$news->title}\"",
            [
                'news_id' => $news->id,
                'news_title' => $news->title,
                'comment_id' => $comment->id,
            ],
            $request
        );

        return back()->with('success', 'Comment posted successfully.');
    }
}
