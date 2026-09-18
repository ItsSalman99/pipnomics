@extends('layouts.app')

@section('title', 'Trading Hub')

@section('header')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-white flex items-center space-x-2">
            <span>Global Market Workspace</span>
            <span class="text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full font-semibold">Live Feed</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">Real-time terminal rates, calendar developments, and core insights aggregated.</p>
    </div>
</div>
@endsection

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    <!-- Main Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- LEFT COLUMN: SIDEBAR WIDGETS (3 Cols) -->
        <div class="lg:col-span-3 space-y-6">
            
            <!-- Traders Online -->
            <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-emerald-500/5 to-transparent rounded-full pointer-events-none"></div>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-ping"></span>
                        <span>Traders Online</span>
                    </h3>
                    <span class="text-[9px] font-mono font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full flex items-center space-x-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>LIVE TELEMETRY</span>
                    </span>
                </div>

                <div class="flex items-baseline space-x-2">
                    <span id="online-traders-count" class="text-3xl font-extrabold font-mono tracking-tight text-white transition-all duration-300">
                        {{ $onlineTradersCount }}
                    </span>
                    <span class="text-xs text-emerald-400 font-semibold">
                        Active Now
                    </span>
                </div>
            </div>

            <!-- Market Trading Sessions Widget -->
            <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg relative overflow-hidden">
                <div class="flex justify-between items-center mb-3.5 pb-2.5 border-b border-slate-800">
                    <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase flex items-center space-x-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Market Sessions</span>
                    </h3>
                    <span id="live-utc-clock" class="text-[10px] font-mono font-bold text-slate-300 bg-slate-950 px-2 py-0.5 rounded-md border border-slate-800">
                        --:-- UTC
                    </span>
                </div>

                <div class="space-y-2" id="market-sessions-list">
                    <!-- Sydney -->
                    <div id="session-sydney" class="session-card p-2.5 rounded-lg border border-slate-800/70 bg-slate-950 flex items-center justify-between transition-all duration-200" title="Sydney Session: 22:00 - 07:00 UTC">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-2 h-2 rounded-full bg-slate-700 session-dot shrink-0"></span>
                            <div>
                                <span class="text-xs font-bold text-slate-200 block session-title">Sydney</span>
                                <span class="text-[10px] text-slate-500 font-mono">22:00 - 07:00 UTC</span>
                            </div>
                        </div>
                        <span class="session-badge text-[9px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-850 text-slate-500 border border-slate-800">
                            CLOSED
                        </span>
                    </div>

                    <!-- Tokyo -->
                    <div id="session-tokyo" class="session-card p-2.5 rounded-lg border border-slate-800/70 bg-slate-950 flex items-center justify-between transition-all duration-200" title="Tokyo Session: 23:00 - 08:00 UTC">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-2 h-2 rounded-full bg-slate-700 session-dot shrink-0"></span>
                            <div>
                                <span class="text-xs font-bold text-slate-200 block session-title">Tokyo</span>
                                <span class="text-[10px] text-slate-500 font-mono">23:00 - 08:00 UTC</span>
                            </div>
                        </div>
                        <span class="session-badge text-[9px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-850 text-slate-500 border border-slate-800">
                            CLOSED
                        </span>
                    </div>

                    <!-- London -->
                    <div id="session-london" class="session-card p-2.5 rounded-lg border border-slate-800/70 bg-slate-950 flex items-center justify-between transition-all duration-200" title="London Session: 08:00 - 17:00 UTC">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-2 h-2 rounded-full bg-slate-700 session-dot shrink-0"></span>
                            <div>
                                <span class="text-xs font-bold text-slate-200 block session-title">London</span>
                                <span class="text-[10px] text-slate-500 font-mono">08:00 - 17:00 UTC</span>
                            </div>
                        </div>
                        <span class="session-badge text-[9px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-850 text-slate-500 border border-slate-800">
                            CLOSED
                        </span>
                    </div>

                    <!-- New York -->
                    <div id="session-newyork" class="session-card p-2.5 rounded-lg border border-slate-800/70 bg-slate-950 flex items-center justify-between transition-all duration-200" title="New York Session: 13:00 - 22:00 UTC">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-2 h-2 rounded-full bg-slate-700 session-dot shrink-0"></span>
                            <div>
                                <span class="text-xs font-bold text-slate-200 block session-title">New York</span>
                                <span class="text-[10px] text-slate-500 font-mono">13:00 - 22:00 UTC</span>
                            </div>
                        </div>
                        <span class="session-badge text-[9px] font-mono font-bold px-2 py-0.5 rounded-full bg-slate-850 text-slate-500 border border-slate-800">
                            CLOSED
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: MAIN WORKSPACE widgets (9 Cols) -->
        <div class="lg:col-span-9 space-y-6">

            <!-- TOP 10 COMMUNITY FORUMS WIDGET -->
            <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg flex flex-col">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-3.5 mb-4 border-b border-slate-800 gap-2 sm:gap-0">
                    <div>
                        <h3 class="text-xs font-bold text-slate-200 tracking-wider uppercase flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 bg-cyan-400 rounded-full animate-pulse"></span>
                            <span>Top Community Forums</span>
                            <span class="text-[10px] bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 px-2 py-0.5 rounded-full font-mono font-semibold normal-case">Top 10 by Activity</span>
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Most active trader communities ranked by members and discussions.</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a 
                            href="{{ route('groups.index') }}" 
                            class="text-cyan-400 hover:text-cyan-300 font-bold text-xs flex items-center space-x-1 transition"
                        >
                            <span>View All Forums</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- Grid of Top 10 Groups -->
                @if(isset($groups) && count($groups) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        @foreach($groups->slice(0, 10) as $idx => $group)
                            <div class="bg-slate-950/60 border border-slate-800/80 hover:border-cyan-500/40 rounded-xl p-4 transition-all duration-200 hover:bg-slate-900 flex flex-col justify-between group relative overflow-hidden shadow-sm">
                                <!-- Top Rank Accent Indicator -->
                                <div 
                                    class="absolute top-0 left-0 right-0 h-0.5 transition-opacity duration-300 {{ $idx === 0 ? 'bg-gradient-to-r from-amber-400 to-yellow-500' : ($idx === 1 ? 'bg-gradient-to-r from-slate-300 to-slate-400' : ($idx === 2 ? 'bg-gradient-to-r from-amber-700 to-amber-600' : 'bg-gradient-to-r from-cyan-500/50 to-emerald-500/50 opacity-0 group-hover:opacity-100')) }}"
                                ></div>

                                <div>
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="flex items-center space-x-2">
                                            <!-- Rank Badge -->
                                            <span 
                                                class="text-[10px] font-mono font-black px-1.5 py-0.5 rounded shrink-0 {{ $idx === 0 ? 'bg-amber-400/20 text-amber-300 border border-amber-400/30' : ($idx === 1 ? 'bg-slate-400/20 text-slate-200 border border-slate-400/30' : ($idx === 2 ? 'bg-amber-700/20 text-amber-400 border border-amber-700/30' : 'bg-slate-800/60 text-slate-400 border border-slate-700/80')) }}"
                                            >
                                                #{{ $idx + 1 }}
                                            </span>
                                            <h4 class="text-xs font-bold text-slate-100 group-hover:text-cyan-400 transition-colors line-clamp-1">
                                                {{ $group->name }}
                                            </h4>
                                        </div>
                                        @if($group->owner)
                                            <span class="text-[10px] text-slate-500 font-mono shrink-0 truncate max-w-[110px] flex items-center space-x-1">
                                                <span 
                                                    class="w-1.5 h-1.5 rounded-full inline-block shrink-0 {{ $group->owner->is_online ? 'bg-emerald-400 shadow-sm shadow-emerald-400 animate-pulse' : 'bg-slate-600' }}"
                                                    title="{{ $group->owner->is_online ? 'Host Online' : 'Host Offline' }}"
                                                ></span>
                                                <span class="truncate">{{ '@' . $group->owner->name }}</span>
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-[11px] text-slate-400 line-clamp-2 leading-relaxed mb-3">
                                        {{ $group->description ?? 'Active trader forum discussing market trends, currency charts, and macroeconomic signals.' }}
                                    </p>
                                </div>

                                <div class="flex items-center justify-between pt-2.5 border-t border-slate-800/80 text-[10px] font-mono">
                                    <div class="flex items-center space-x-3 text-slate-400">
                                        <!-- Members Count -->
                                        <span class="flex items-center space-x-1" title="Members">
                                            <svg class="w-3.5 h-3.5 text-cyan-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            <span class="font-semibold text-slate-300">{{ $group->members_count ?? 0 }}</span>
                                            <span class="text-slate-500 hidden sm:inline">members</span>
                                        </span>
                                        <!-- Posts Count -->
                                        <span class="flex items-center space-x-1" title="Posts">
                                            <svg class="w-3.5 h-3.5 text-emerald-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                            <span class="font-semibold text-slate-300">{{ $group->posts_count ?? 0 }}</span>
                                            <span class="text-slate-500 hidden sm:inline">posts</span>
                                        </span>
                                    </div>

                                    <a 
                                        href="{{ route('groups.show', $group->id) }}" 
                                        class="inline-flex items-center space-x-1 text-cyan-400 hover:text-cyan-300 font-bold group-hover:translate-x-0.5 transition-transform"
                                    >
                                        <span>Join Forum</span>
                                        <span>&rarr;</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-8 text-center text-slate-400">
                        <svg class="w-10 h-10 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20H22V18C22 15.7909 20.2091 14 18 14H16M17 20H7M17 20V18C17 16.9248 16.5772 15.9482 15.8906 15.2285M2 18C2 15.7909 3.79086 14 6 14H14C16.2091 14 18 15.7909 18 18V20H2M14 7C14 9.20914 12.2091 11 10 11C7.79086 11 6 9.20914 6 7C6 4.79086 7.79086 3 10 3C12.2091 3 14 4.79086 14 7Z"></path></svg>
                        <h4 class="text-sm font-bold text-white mb-1">No Active Community Forums Yet</h4>
                        <p class="text-xs text-slate-500 max-w-md mx-auto mb-3">Be the first to start a trading forum and share ideas with the community.</p>
                        @auth
                            <a href="{{ route('groups.create') }}" class="inline-flex items-center space-x-1.5 bg-gradient-to-r from-emerald-500 to-cyan-500 text-slate-950 px-3.5 py-1.5 rounded-lg text-xs font-bold hover:from-emerald-400 hover:to-cyan-400 transition">
                                <span>Create First Forum</span>
                                <span>&rarr;</span>
                            </a>
                        @else
                            <a href="{{ route('groups.index') }}" class="inline-flex items-center space-x-1.5 bg-slate-800 hover:bg-slate-750 text-slate-300 px-3.5 py-1.5 rounded-lg text-xs font-bold transition">
                                <span>Browse All Forums</span>
                                <span>&rarr;</span>
                            </a>
                        @endauth
                    </div>
                @endif
            </div>

            <!-- TOP 5 LATEST NEWS WIDGET -->
            @if(isset($newsItems) && count($newsItems) > 0)
                <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg flex flex-col">
                    <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-800">
                        <h3 class="text-xs font-bold text-slate-200 tracking-wider uppercase flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                            <span>Latest Market News</span>
                        </h3>
                        <a 
                            href="{{ route('news.index') }}" 
                            class="text-emerald-400 hover:text-emerald-300 font-bold text-xs"
                        >
                            View More News &rarr;
                        </a>
                    </div>
                    
                    <!-- Top 5 Feed List -->
                    <div class="divide-y divide-slate-850/60 space-y-3.5">
                        @foreach($newsItems->slice(0, 5) as $idx => $item)
                            <a 
                                href="{{ route('news.show', $item->id) }}"
                                class="group/item block pt-3.5 first:pt-0 pb-3 last:pb-0 hover:bg-slate-950/20 rounded-lg px-2 transition duration-150 news-card-item"
                            >
                                <div class="flex justify-between items-center text-[9px] font-mono text-slate-500 mb-1.5">
                                    <span class="text-emerald-400 font-semibold">{{ '@' . ($item->source ?? 'FXStreet') }}</span>
                                    <span>{{ \Carbon\Carbon::parse($item->published_at)->diffForHumans() }}</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-200 leading-snug group-hover/item:text-emerald-400 transition-colors line-clamp-1">
                                    {{ $item->title }}
                                </h4>
                                <p class="text-[10px] text-slate-400 mt-1 line-clamp-2 font-normal leading-relaxed">
                                    {{ $item->summary }}
                                </p>
                                <div class="mt-2 text-[9px] text-slate-500 flex items-center space-x-1.5 font-mono">
                                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    <span>{{ $item->comments_count ?? 0 }} replies</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-8 text-center text-slate-400 shadow-lg">
                    <svg class="w-10 h-10 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 002-2V7m2 13a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    <h4 class="text-sm font-bold text-white mb-1">Live Financial News Unavailable</h4>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">Please check your internet connection or backend services. Feed aggregator is currently offline.</p>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
