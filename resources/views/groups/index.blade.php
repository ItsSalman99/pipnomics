@extends('layouts.app')

@section('title', 'Trader Communities')

@section('header')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-white flex items-center space-x-2">
            <span>Trader Communities</span>
            <span class="text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full font-semibold">Forums & Alpha</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">Join specialized trader chatrooms, copy analysis strategies, and discuss signals.</p>
    </div>
    
    <!-- Create Group Action Button -->
    <div>
        @auth
            <a 
                href="{{ route('groups.create') }}" 
                class="bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 font-bold px-4 py-2 rounded-lg text-xs transition duration-200 flex items-center space-x-1.5 shadow-md shadow-emerald-500/10 hover:scale-[1.02]"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Create Forum</span>
            </a>
        @else
            <button 
                type="button"
                onclick="window.openGuestAuthModal('create a community forum')"
                class="bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 font-bold px-4 py-2 rounded-lg text-xs transition duration-200 flex items-center space-x-1.5 shadow-md shadow-emerald-500/10 hover:scale-[1.02]"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Create Forum</span>
            </button>
        @endauth
    </div>
</div>
@endsection

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
    
    <!-- Search Bar -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-lg flex justify-between items-center">
        <div class="relative w-full max-w-md">
            <input 
                type="text" 
                id="groups-search-input" 
                oninput="window.filterGroups(this.value)"
                placeholder="Search forum topics or currency pairs..." 
                class="w-full bg-slate-950 text-xs text-slate-200 border border-slate-800 rounded-lg pl-9 pr-4 py-2 focus:outline-none focus:border-emerald-500/50"
            />
            <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <span class="text-xs font-mono text-slate-400 hidden sm:inline">
            <span id="groups-count">{{ count($groups) }}</span> forums available
        </span>
    </div>

    <!-- Groups Grid -->
    @if(isset($groups) && count($groups) > 0)
        <div id="groups-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($groups as $group)
                <div class="group-card bg-slate-900 border border-slate-800/80 hover:border-cyan-500/40 rounded-xl p-5 shadow-lg transition-all duration-200 flex flex-col justify-between hover:bg-slate-850/60 relative overflow-hidden" data-name="{{ strtolower($group->name) }}" data-desc="{{ strtolower($group->description ?? '') }}">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-2.5">
                            <h3 class="text-sm font-bold text-white group-hover:text-cyan-400 transition-colors">
                                {{ $group->name }}
                            </h3>
                            <span class="text-[10px] font-mono bg-slate-800 text-slate-400 border border-slate-700 px-2 py-0.5 rounded-full shrink-0">
                                <span class="group-members-count-{{ $group->id }}">{{ $group->members_count ?? 0 }}</span> members
                            </span>
                        </div>

                        <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed mb-4">
                            {{ $group->description ?? 'Official trader forum discussing technical chart patterns, volume analysis, and signal trading.' }}
                        </p>
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex items-center justify-between">
                        <span class="text-[10px] text-slate-500 font-mono">
                            Created {{ \Carbon\Carbon::parse($group->created_at)->diffForHumans() }}
                        </span>
                        
                        <a 
                            href="{{ route('groups.show', $group->id) }}" 
                            class="inline-flex items-center space-x-1 text-xs font-bold text-cyan-400 hover:text-cyan-300 transition"
                        >
                            <span>Open Forum</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center text-slate-400">
            <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20H22V18C22 15.7909 20.2091 14 18 14H16M17 20H7M17 20V18C17 16.9248 16.5772 15.9482 15.8906 15.2285M2 18C2 15.7909 3.79086 14 6 14H14C16.2091 14 18 15.7909 18 18V20H2M14 7C14 9.20914 12.2091 11 10 11C7.79086 11 6 9.20914 6 7C6 4.79086 7.79086 3 10 3C12.2091 3 14 4.79086 14 7Z"></path></svg>
            <h4 class="text-sm font-bold text-white mb-1">No Active Groups Yet</h4>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mb-4">Be the pioneer! Create a dedicated group to discuss trading pairs and macro ideas.</p>
            @auth
                <a href="{{ route('groups.create') }}" class="inline-flex items-center space-x-1.5 bg-gradient-to-r from-emerald-500 to-cyan-500 text-slate-950 px-4 py-2 rounded-lg text-xs font-bold hover:from-emerald-400 hover:to-cyan-400 transition">
                    <span>Create First Group</span>
                    <span>&rarr;</span>
                </a>
            @else
                <button type="button" onclick="window.openGuestAuthModal('create a community forum')" class="inline-flex items-center space-x-1.5 bg-gradient-to-r from-emerald-500 to-cyan-500 text-slate-950 px-4 py-2 rounded-lg text-xs font-bold hover:from-emerald-400 hover:to-cyan-400 transition">
                    <span>Create First Group</span>
                    <span>&rarr;</span>
                </button>
            @endauth
        </div>
    @endif

</div>
@endsection
