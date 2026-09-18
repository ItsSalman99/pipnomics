@extends('layouts.app')

@section('title', $news->title . ' - Market News')

@section('header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div class="flex items-center space-x-3">
        <a 
            href="{{ route('news.index') }}" 
            class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-400 hover:text-white bg-slate-900 border border-slate-800 hover:border-slate-700 px-3 py-1.5 rounded-lg transition shadow-sm"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Back to News Hub</span>
        </a>
        <div class="h-4 w-px bg-slate-800 hidden sm:block"></div>
        <span class="text-xs text-slate-400 hidden sm:inline">Article Discussion & Wire Details</span>
    </div>

    <!-- Quick action links -->
    <div class="flex items-center space-x-2">
        <button 
            type="button" 
            onclick="window.copyArticleLink()" 
            class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-300 hover:text-white bg-slate-850 hover:bg-slate-800 border border-slate-750 px-3 py-1.5 rounded-lg transition"
            id="share-article-btn"
        >
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
            <span>Share</span>
        </button>

        @if($news->url && $news->url !== '#')
            <a 
                href="{{ $news->url }}" 
                target="_blank" 
                rel="noopener noreferrer"
                class="inline-flex items-center space-x-1.5 text-xs font-semibold text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 rounded-lg transition"
            >
                <span>Wire Feed</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
        @endif
    </div>
</div>
@endsection

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- MAIN ARTICLE COLUMN (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Article Card -->
            <article class="bg-slate-900 border border-slate-800/90 rounded-2xl overflow-hidden shadow-xl">
                
                <!-- Article Header Meta -->
                <div class="p-6 md:p-8 border-b border-slate-800">
                    <div class="flex flex-wrap items-center gap-2.5 text-xs mb-4">
                        <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-2.5 py-1 rounded-md font-mono font-bold uppercase tracking-wider text-[10px]">
                            {{ $news->source ?? 'FXStreet' }}
                        </span>

                        @if($news->is_analysis)
                            <span class="bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 px-2.5 py-1 rounded-md font-semibold text-[10px]">
                                Technical & Macro Analysis
                            </span>
                        @else
                            <span class="bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 px-2.5 py-1 rounded-md font-semibold text-[10px]">
                                Market News Flash
                            </span>
                        @endif

                        <span class="text-slate-500 font-mono text-[11px] flex items-center space-x-1 ml-auto">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ $news->published_at ? \Carbon\Carbon::parse($news->published_at)->diffForHumans() : 'Just now' }}</span>
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-white leading-tight tracking-tight">
                        {{ $news->title }}
                    </h1>

                    <div class="flex items-center text-xs text-slate-400 mt-4 pt-4 border-t border-slate-850 gap-4 flex-wrap">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-[10px] font-bold text-emerald-400">
                                FX
                            </div>
                            <span class="font-medium text-slate-300">{{ $news->source ?? 'FXStreet Research' }}</span>
                        </div>
                        <span class="text-slate-600">&bull;</span>
                        <span class="font-mono text-slate-400 text-[11px]">
                            {{ $news->published_at ? \Carbon\Carbon::parse($news->published_at)->format('M d, Y · H:i T') : '' }}
                        </span>
                        <span class="text-slate-600">&bull;</span>
                        <span class="text-slate-400 flex items-center space-x-1">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            <span>~2 min read</span>
                        </span>
                    </div>
                </div>

                <!-- Featured Image -->
                @if($news->image)
                    <div class="relative w-full max-h-[420px] bg-slate-950 overflow-hidden border-b border-slate-800">
                        <img 
                            src="{{ $news->image }}" 
                            alt="{{ $news->title }}" 
                            class="w-full h-full object-cover max-h-[420px]"
                            onerror="this.parentElement.classList.add('hidden')"
                        />
                    </div>
                @endif

                <!-- Article Content Body -->
                <div class="p-6 md:p-8 space-y-6">
                    
                    <!-- Lead Highlight -->
                    @if($news->description || $news->summary)
                        <div class="bg-emerald-950/20 border border-emerald-500/20 rounded-xl p-5 relative overflow-hidden">
                            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl pointer-events-none"></div>
                            <h3 class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2 flex items-center space-x-1.5 font-mono">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Executive Summary & Lead</span>
                            </h3>
                            <p class="text-sm md:text-base text-slate-200 leading-relaxed font-medium">
                                {{ $news->description ?? $news->summary }}
                            </p>
                        </div>
                    @endif

                    <!-- Insights & Fundamental Breakdown -->
                    <div class="space-y-4 text-slate-300 text-sm md:text-base leading-relaxed">
                        <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-5 space-y-3">
                            <h4 class="text-xs font-bold text-slate-200 uppercase tracking-wider flex items-center space-x-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Key Trading & Market Takeaways</span>
                            </h4>
                            <ul class="space-y-2 text-xs text-slate-300 font-normal">
                                <li class="flex items-start space-x-2">
                                    <span class="text-emerald-400 font-bold mt-0.5">&bull;</span>
                                    <span>Live institutional market sentiment synced from global financial desks.</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <span class="text-emerald-400 font-bold mt-0.5">&bull;</span>
                                    <span>Traders are advised to monitor macroeconomic releases and key technical pivot levels.</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <span class="text-emerald-400 font-bold mt-0.5">&bull;</span>
                                    <span>Join the community discussion below to share your trade setup or ask questions.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- External Source CTA -->
                    @if($news->url && $news->url !== '#')
                        <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div>
                                <h4 class="text-xs font-bold text-white">Full Press Release & Institutional Data</h4>
                                <p class="text-[11px] text-slate-400 mt-0.5">Read original source directly on {{ $news->source ?? 'FXStreet' }}.</p>
                            </div>
                            <a 
                                href="{{ $news->url }}" 
                                target="_blank" 
                                rel="noopener noreferrer"
                                class="inline-flex items-center space-x-1.5 text-xs font-bold bg-slate-800 hover:bg-slate-750 text-slate-200 hover:text-white px-4 py-2 rounded-lg border border-slate-700 transition shrink-0"
                            >
                                <span>Open Raw Wire</span>
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                        </div>
                    @endif

                </div>

                <!-- Interactive Engagement Toolbar -->
                <div class="p-6 border-t border-slate-800 bg-slate-950/40 flex flex-wrap items-center justify-between gap-4">
                    
                    <div class="flex items-center space-x-3">
                        <!-- Like Button (AJAX) -->
                        @auth
                            <button 
                                id="article-like-btn"
                                type="button"
                                onclick="window.newsShowApp.toggleLike({{ $news->id }})"
                                class="inline-flex items-center space-x-2 text-xs font-bold px-4 py-2 rounded-xl border transition {{ $isLiked ? 'bg-rose-500/15 text-rose-400 border-rose-500/30 shadow-md shadow-rose-500/10' : 'bg-slate-800 hover:bg-slate-750 text-slate-300 hover:text-white border-slate-700' }}"
                            >
                                <svg id="article-like-icon" class="w-4 h-4 {{ $isLiked ? 'fill-rose-500 text-rose-500 animate-pulse' : 'text-slate-400' }}" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span id="article-like-btn-text">{{ $isLiked ? 'Liked' : 'Like Article' }}</span>
                                <span id="article-like-badge" class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isLiked ? 'bg-rose-500/20 text-rose-300' : 'bg-slate-900 text-slate-400' }}">
                                    {{ $news->likes_count ?? 0 }}
                                </span>
                            </button>
                        @else
                            <a 
                                href="{{ route('login') }}"
                                class="inline-flex items-center space-x-2 text-xs font-bold px-4 py-2 rounded-xl border bg-slate-800 hover:bg-slate-750 text-slate-300 hover:text-white border-slate-700 transition"
                            >
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span>Like</span>
                                <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-mono bg-slate-900 text-slate-400">
                                    {{ $news->likes_count ?? 0 }}
                                </span>
                            </a>
                        @endauth

                        <!-- Jump to comment button -->
                        <a 
                            href="#discussion-section"
                            class="inline-flex items-center space-x-2 text-xs font-bold px-4 py-2 rounded-xl border bg-slate-800 hover:bg-slate-750 text-slate-300 hover:text-white border-slate-700 transition"
                        >
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <span>Replies</span>
                            <span id="article-comment-counter-badge" class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] font-mono bg-slate-900 text-slate-400">
                                {{ $news->comments_count ?? 0 }}
                            </span>
                        </a>
                    </div>

                    <!-- Share action -->
                    <button 
                        type="button" 
                        onclick="window.copyArticleLink()"
                        class="text-xs text-slate-400 hover:text-slate-200 flex items-center space-x-1.5 font-medium transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                        <span>Copy Story Link</span>
                    </button>

                </div>

            </article>

            <!-- DISCUSSION & REPLIES SECTION -->
            <section id="discussion-section" class="bg-slate-900 border border-slate-800/90 rounded-2xl p-6 md:p-8 shadow-xl space-y-6">
                
                <div class="flex justify-between items-center pb-4 border-b border-slate-800">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-white flex items-center space-x-2">
                            <span>Community Discussion</span>
                            <span id="comments-count-header" class="text-xs bg-emerald-500/15 text-emerald-400 border border-emerald-500/25 px-2 py-0.5 rounded-full font-mono font-bold">
                                {{ $news->comments_count ?? 0 }}
                            </span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Share your market outlook, setup, or reply to fellow traders.</p>
                    </div>
                </div>

                <!-- Comment Input Area -->
                @auth
                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 shadow-inner">
                        <form id="top-comment-form" onsubmit="window.newsShowApp.submitComment(event, null)" class="space-y-3">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="w-2 h-2 rounded-full {{ auth()->user()->is_online ? 'bg-emerald-400 shadow-sm shadow-emerald-400 animate-pulse' : 'bg-slate-600' }}"></span>
                                <span class="text-xs font-bold text-slate-200">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] text-slate-500 font-mono">Posting as authenticated trader</span>
                            </div>

                            <textarea 
                                id="top-comment-input"
                                rows="3" 
                                placeholder="What's your take on this market development? Share key levels or fundamentals..." 
                                class="w-full bg-slate-900 text-xs text-slate-200 border border-slate-800 rounded-lg p-3 focus:outline-none focus:border-emerald-500/50 resize-none"
                                required
                            ></textarea>

                            <div class="flex justify-between items-center pt-1">
                                <span class="text-[10px] text-slate-500 font-mono">Max 1500 chars</span>
                                <button 
                                    id="top-comment-submit-btn"
                                    type="submit"
                                    class="bg-emerald-500 hover:bg-emerald-400 disabled:opacity-50 text-slate-950 font-bold text-xs px-5 py-2 rounded-lg transition shadow-md shadow-emerald-500/10 flex items-center space-x-1.5"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    <span>Post Comment</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="bg-slate-950 border border-slate-800/80 rounded-xl p-5 text-center space-y-3">
                        <div class="w-10 h-10 bg-emerald-500/10 text-emerald-400 rounded-full flex items-center justify-center mx-auto border border-emerald-500/20">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">Join the Pipfolio Trader Conversation</h4>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">Sign in to like stories, post market opinions, and reply to community analysis.</p>
                        </div>
                        <div class="flex justify-center items-center space-x-3 pt-2">
                            <a href="{{ route('login') }}" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs px-4 py-2 rounded-lg transition shadow">Log In</a>
                            <a href="{{ route('register') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs px-4 py-2 rounded-lg border border-slate-700 transition">Create Account</a>
                        </div>
                    </div>
                @endauth

                <!-- COMMENTS LIST CONTAINER -->
                <div id="comments-container" class="space-y-4 pt-2">
                    @forelse($news->topLevelComments as $comment)
                        <div id="comment-node-{{ $comment->id }}" class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-4.5 space-y-3 transition duration-150">
                            
                            <!-- Comment Header -->
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs text-emerald-400">
                                        {{ strtoupper(substr($comment->user->name ?? 'T', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-1.5">
                                            <span class="text-xs font-bold text-slate-200">{{ $comment->user->name ?? 'Trader' }}</span>
                                            <span class="w-1.5 h-1.5 rounded-full inline-block {{ ($comment->user->is_online ?? false) ? 'bg-emerald-400 shadow-sm shadow-emerald-400 animate-pulse' : 'bg-slate-600' }}"></span>
                                        </div>
                                        <span class="text-[10px] font-mono text-slate-500">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                @auth
                                    <button 
                                        type="button" 
                                        onclick="window.newsShowApp.toggleReplyBox({{ $comment->id }})" 
                                        class="text-xs font-semibold text-slate-400 hover:text-emerald-400 transition flex items-center space-x-1 bg-slate-900 hover:bg-slate-850 px-2.5 py-1 rounded-md border border-slate-800"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                        <span>Reply</span>
                                    </button>
                                @endauth
                            </div>

                            <!-- Comment Content -->
                            <p class="text-xs text-slate-300 leading-relaxed pl-9">
                                {{ $comment->content }}
                            </p>

                            <!-- Inline Reply Form (Hidden by default) -->
                            @auth
                                <div id="reply-box-{{ $comment->id }}" class="hidden pl-9 pt-2">
                                    <form onsubmit="window.newsShowApp.submitComment(event, {{ $comment->id }})" class="space-y-2 bg-slate-900 p-3 rounded-lg border border-slate-800">
                                        <div class="text-[10px] text-emerald-400 font-mono font-semibold">Replying to {{ $comment->user->name ?? 'Trader' }}:</div>
                                        <textarea 
                                            id="reply-input-{{ $comment->id }}"
                                            rows="2" 
                                            placeholder="Write your reply..." 
                                            class="w-full bg-slate-950 text-xs text-slate-200 border border-slate-800 rounded p-2 focus:outline-none focus:border-emerald-500/50 resize-none"
                                            required
                                        ></textarea>
                                        <div class="flex justify-end space-x-2">
                                            <button 
                                                type="button" 
                                                onclick="window.newsShowApp.toggleReplyBox({{ $comment->id }})" 
                                                class="px-3 py-1 text-xs text-slate-400 hover:text-white"
                                            >
                                                Cancel
                                            </button>
                                            <button 
                                                id="reply-submit-btn-{{ $comment->id }}"
                                                type="submit" 
                                                class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs px-3 py-1 rounded transition"
                                            >
                                                Post Reply
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endauth

                            <!-- Nested Replies Container -->
                            <div id="replies-list-{{ $comment->id }}" class="space-y-2.5 pl-9 pt-1 {{ $comment->replies->count() > 0 ? '' : 'hidden' }}">
                                @foreach($comment->replies as $reply)
                                    <div class="bg-slate-900/90 border-l-2 border-emerald-500/50 pl-3 py-2 pr-3 rounded-r-lg border-y border-r border-slate-850">
                                        <div class="flex justify-between items-center mb-1">
                                            <div class="flex items-center space-x-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full inline-block {{ ($reply->user->is_online ?? false) ? 'bg-emerald-400 animate-pulse' : 'bg-slate-600' }}"></span>
                                                <span class="text-xs font-bold text-emerald-400">{{ $reply->user->name ?? 'Trader' }}</span>
                                            </div>
                                            <span class="text-[9px] font-mono text-slate-500">{{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-xs text-slate-300">{{ $reply->content }}</p>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @empty
                        <div id="no-comments-placeholder" class="text-center py-8 text-slate-500 text-xs italic bg-slate-950/40 rounded-xl border border-slate-850">
                            <svg class="w-8 h-8 text-slate-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <span>No comments yet. Be the first trader to share your thoughts on this story!</span>
                        </div>
                    @endforelse
                </div>

            </section>

        </div>

        <!-- SIDEBAR COLUMN (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Other Latest Market Stories Widget -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
                <div class="flex justify-between items-center pb-3 border-b border-slate-800">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>More Market Stories</span>
                    </h3>
                    <a href="{{ route('news.index') }}" class="text-[11px] font-bold text-emerald-400 hover:text-emerald-300 transition">
                        View All &rarr;
                    </a>
                </div>

                <div class="space-y-3 divide-y divide-slate-850/60">
                    @forelse($relatedNews as $related)
                        <a 
                            href="{{ route('news.show', $related->id) }}"
                            class="group block pt-3 first:pt-0 hover:bg-slate-850/40 p-2 rounded-lg transition"
                        >
                            <div class="flex justify-between items-center text-[9px] font-mono text-slate-500 mb-1">
                                <span class="text-emerald-400 font-semibold uppercase">{{ $related->source ?? 'FXStreet' }}</span>
                                <span>{{ $related->published_at ? \Carbon\Carbon::parse($related->published_at)->diffForHumans() : '' }}</span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-200 group-hover:text-emerald-400 transition-colors line-clamp-2 leading-snug">
                                {{ $related->title }}
                            </h4>
                            <div class="mt-1.5 text-[9px] text-slate-500 flex items-center space-x-2 font-mono">
                                <span class="flex items-center space-x-1">
                                    <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    <span>{{ $related->comments_count ?? 0 }} replies</span>
                                </span>
                            </div>
                        </a>
                    @empty
                        <p class="text-xs text-slate-500 italic py-2">No other stories available at this time.</p>
                    @endforelse
                </div>
            </div>

            <!-- Economic Pulse & School Promo Banner -->
            <div class="bg-gradient-to-br from-emerald-950/40 to-slate-900 border border-emerald-500/20 rounded-2xl p-5 shadow-xl space-y-3">
                <span class="text-[9px] bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded font-mono font-bold uppercase">
                    Pipnomics Community
                </span>
                <h3 class="text-sm font-bold text-white">Master Fundamental Trading</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Learn how macro releases, central bank meetings, and breaking wire news influence liquidity and volatility across major pairs.
                </p>
                <div class="pt-2 flex flex-col space-y-2">
                    <a 
                        href="{{ route('calendar.index') }}" 
                        class="w-full text-center bg-slate-800 hover:bg-slate-750 text-slate-200 hover:text-white font-bold text-xs py-2 rounded-lg border border-slate-700 transition"
                    >
                        Check Economic Calendar &rarr;
                    </a>
                    <a 
                        href="{{ route('school.index') }}" 
                        class="w-full text-center bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs py-2 rounded-lg transition shadow-md shadow-emerald-500/10"
                    >
                        Apply for Pipnomics School &rarr;
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    window.CURRENT_ARTICLE_ID = {{ $news->id }};
    window.copyArticleLink = function() {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(window.location.href).then(() => {
                window.showToast('Story link copied to clipboard!', 'success');
            }).catch(() => {
                window.showToast('Failed to copy link.', 'error');
            });
        } else {
            window.showToast('Clipboard not supported in this browser.', 'warning');
        }
    };
</script>
@endsection
