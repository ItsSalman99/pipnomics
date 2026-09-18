<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\NewsArticle;
use App\Models\NewsComment;
use App\Services\NewsService;

class NewsController extends Controller
{
    protected NewsService $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    public function index(Request $request)
    {
        // Sync from FXStreet and fetch latest articles from database with comments & likes
        $newsList = $this->newsService->syncAndGetLatest(50);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['news' => $newsList]);
        }

        return view('news.index', ['news' => $newsList]);
    }

    public function show(Request $request, NewsArticle $news)
    {
        // Eager load counts and threaded comments
        $news->loadCount(['comments', 'likes']);
        $news->load([
            'topLevelComments' => function ($query) {
                $query->with([
                    'user:id,name,is_online',
                    'replies.user:id,name,is_online',
                ])->latest();
            },
        ]);

        // Recommended / other recent stories for the sidebar
        $relatedNews = NewsArticle::where('id', '!=', $news->id)
            ->withCount('comments')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $isLiked = $request->user() ? $news->isLikedBy($request->user()) : false;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'article' => $news,
                'is_liked' => $isLiked,
                'related' => $relatedNews,
            ]);
        }

        return view('news.show', [
            'news' => $news,
            'isLiked' => $isLiked,
            'relatedNews' => $relatedNews,
        ]);
    }

    public function toggleLike(Request $request, NewsArticle $news)
    {
        $user = $request->user();
        $existingLike = $news->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
            $message = 'Removed like from this article.';
        } else {
            $news->likes()->create(['user_id' => $user->id]);
            $liked = true;
            $message = 'Article liked successfully!';

            \App\Models\UserActivity::log(
                $user,
                'news_liked',
                "Liked news: \"{$news->title}\"",
                [
                    'news_id' => $news->id,
                    'news_title' => $news->title,
                ],
                $request
            );
        }

        $likesCount = $news->likes()->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $likesCount,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    public function storeComment(Request $request, NewsArticle $news)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1500',
            'parent_id' => 'nullable|integer|exists:news_comments,id',
        ]);

        $parentId = $validated['parent_id'] ?? null;

        // Verify parent comment belongs to this news article if supplied
        if ($parentId) {
            $parentComment = NewsComment::where('id', $parentId)
                ->where('news_article_id', $news->id)
                ->first();

            if (!$parentComment) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Invalid parent comment.'], 422);
                }
                return back()->withErrors(['parent_id' => 'Invalid parent comment.']);
            }
        }

        $comment = $news->comments()->create([
            'user_id' => $request->user()->id,
            'parent_id' => $parentId,
            'content' => $validated['content'],
        ]);

        $actionType = $parentId ? 'news_reply_created' : 'comment_created';
        $logMessage = $parentId 
            ? "Replied to a comment on: \"{$news->title}\"" 
            : "Commented on news: \"{$news->title}\"";

        \App\Models\UserActivity::log(
            $request->user(),
            $actionType,
            $logMessage,
            [
                'news_id' => $news->id,
                'news_title' => $news->title,
                'comment_id' => $comment->id,
                'parent_id' => $parentId,
            ],
            $request
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $parentId ? 'Reply posted successfully.' : 'Comment posted successfully.',
                'comment' => [
                    'id' => $comment->id,
                    'parent_id' => $comment->parent_id,
                    'username' => $request->user()->name,
                    'is_online' => $request->user()->is_online,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at->toIso8601String(),
                ],
                'comments_count' => $news->comments()->count(),
            ]);
        }

        return back()->with('success', $parentId ? 'Reply posted successfully.' : 'Comment posted successfully.');
    }
}

