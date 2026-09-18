@extends('layouts.app')

@section('title', $group->name)

@section('header')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-white flex items-center space-x-2">
            <span>{{ $group->name }}</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">Official community thread. Discuss technicals, share forecasts, and follow updates.</p>
    </div>
    
    <!-- Join / Leave buttons via AJAX -->
    <div id="group-action-container">
        @auth
            <button 
                id="group-membership-btn"
                type="button" 
                onclick="window.toggleGroupMembership({{ $group->id }}, {{ $isMember ? 'true' : 'false' }})" 
                class="{{ $isMember ? 'bg-rose-600 hover:bg-rose-500 text-white border-rose-500/30' : 'bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 border-emerald-400 shadow-md shadow-emerald-500/5' }} px-4 py-2 rounded-lg font-bold text-xs transition duration-200 border hover:scale-[1.02]"
            >
                {{ $isMember ? 'Leave Group' : 'Join Group' }}
            </button>
        @else
            <button 
                type="button"
                onclick="window.openGuestAuthModal('join this group and participate in discussions')"
                class="bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 px-4 py-2 rounded-lg font-bold text-xs transition text-center hover:scale-[1.02]"
            >
                Join Group
            </button>
        @endauth
    </div>
</div>
@endsection

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- LEFT COLUMN: THREAD POSTS (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Forum Thread Description -->
            <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg relative overflow-hidden">
                <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase mb-2">Group Description & Guidelines</h3>
                <p class="text-xs text-slate-300 leading-relaxed">
                    {{ $group->description ?? 'Welcome to our trading forum! Join the discussion, share your technical analysis charts, and discuss macroeconomic factors.' }}
                </p>
            </div>

            <!-- Create Post Box (For Members) -->
            <div id="create-post-box" class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg {{ $isMember ? '' : 'hidden' }}">
                <h3 class="text-xs font-bold text-slate-300 tracking-wider uppercase mb-3 flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Start a Discussion / Post Signal</span>
                </h3>
                <form id="create-post-form" onsubmit="window.submitGroupPost(event, {{ $group->id }})" class="space-y-3">
                    @csrf
                    <input 
                        type="text" 
                        id="post-title-input" 
                        name="title" 
                        placeholder="Subject / Currency Pair (optional)..." 
                        class="w-full bg-slate-950 text-xs text-slate-200 border border-slate-800 rounded-lg px-3 py-2 focus:outline-none focus:border-emerald-500/50"
                    />
                    <textarea 
                        id="post-content-input" 
                        name="content" 
                        required 
                        rows="3" 
                        placeholder="Share your chart analysis, trading thesis, stop-loss / take-profit targets..." 
                        class="w-full bg-slate-950 text-xs text-slate-200 border border-slate-800 rounded-lg p-3 focus:outline-none focus:border-emerald-500/50 resize-none leading-relaxed"
                    ></textarea>
                    
                    <div class="flex justify-between items-center pt-1">
                        <span class="text-[11px] text-slate-500">Keep discussions professional and data-driven.</span>
                        <button 
                            id="post-submit-btn" 
                            type="submit" 
                            class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs px-4 py-2 rounded-lg transition"
                        >
                            Publish Post
                        </button>
                    </div>
                </form>
            </div>

            <!-- Forum Posts Feed -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase flex items-center space-x-1.5">
                        <span>Discussion Board</span>
                        <span id="group-posts-badge" class="text-[9px] bg-slate-800 text-slate-400 border border-slate-700 px-1.5 py-0.5 rounded font-mono font-bold">{{ count($group->posts) }} posts</span>
                    </h3>
                </div>

                <!-- Group Posts list -->
                <div id="group-posts-list" class="space-y-3.5">
                    @forelse($group->posts->sortByDesc('created_at') as $post)
                        <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg space-y-2.5 hover:border-slate-700 transition">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-bold text-emerald-400 font-mono">
                                        {{ substr($post->user->name ?? 'T', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-1.5">
                                            <span class="text-xs font-bold text-white">{{ $post->user->name ?? 'Anonymous Trader' }}</span>
                                            <span class="w-1.5 h-1.5 rounded-full {{ ($post->user && $post->user->is_online) ? 'bg-emerald-400 shadow-sm shadow-emerald-400 animate-pulse' : 'bg-slate-600' }}"></span>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-500 font-mono">{{ \Carbon\Carbon::parse($post->created_at)->diffForHumans() }}</span>
                            </div>

                            @if($post->title)
                                <h4 class="text-xs font-bold text-emerald-400 mt-1">{{ $post->title }}</h4>
                            @endif

                            <p class="text-xs text-slate-300 leading-relaxed whitespace-pre-line">{{ $post->content }}</p>
                        </div>
                    @empty
                        <div id="posts-empty-state" class="bg-slate-900 border border-slate-800 rounded-xl p-8 text-center text-slate-400">
                            <p class="text-xs text-slate-500">No posts in this forum yet. Join the group and start the conversation!</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: SIDEBAR (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Host Card -->
            <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg">
                <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase mb-3">Forum Host</h3>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center font-bold text-emerald-400 text-sm">
                        {{ substr($group->owner->name ?? 'H', 0, 1) }}
                    </div>
                    <div>
                        <div class="flex items-center space-x-1.5">
                            <span class="text-sm font-bold text-white">{{ $group->owner->name ?? 'Host' }}</span>
                            <span class="w-1.5 h-1.5 rounded-full {{ ($group->owner && $group->owner->is_online) ? 'bg-emerald-400 shadow-sm shadow-emerald-400 animate-pulse' : 'bg-slate-600' }}"></span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-mono">Forum Founder</span>
                    </div>
                </div>
            </div>

            <!-- Members List -->
            <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg space-y-3">
                <div class="flex justify-between items-center pb-2 border-b border-slate-800">
                    <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase">Members</h3>
                    <span id="group-sidebar-members-count" class="text-xs font-mono font-bold text-emerald-400">{{ count($group->members) }} Active</span>
                </div>

                <div class="space-y-2 max-h-60 overflow-y-auto pr-1 scrollbar-thin">
                    @foreach($group->members as $member)
                        <div class="flex items-center justify-between text-xs py-1">
                            <div class="flex items-center space-x-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ $member->is_online ? 'bg-emerald-400 shadow-sm shadow-emerald-400 animate-pulse' : 'bg-slate-600' }}"></span>
                                <span class="text-slate-200 font-medium">{{ $member->name }}</span>
                            </div>
                            @if($member->id === $group->user_id)
                                <span class="text-[9px] bg-amber-500/10 text-amber-400 border border-amber-500/20 px-1.5 py-0.5 rounded font-mono font-bold">HOST</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
