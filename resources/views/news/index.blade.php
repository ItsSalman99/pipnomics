@extends('layouts.app')

@section('title', 'Market News Hub')

@section('header')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-white flex items-center space-x-2">
            <span>Market News Hub</span>
            <span class="text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full font-semibold">Real-Time Database Feed</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">Live market headlines synced with community discussions and macroeconomic analysis.</p>
    </div>
</div>
@endsection

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
    
    <!-- Search & Filter Controls -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-lg flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4">
        
        <!-- Filter Tabs -->
        <div class="flex bg-slate-950 p-1 rounded-lg border border-slate-800 self-start">
            <button type="button" onclick="window.newsApp.setFilter('all')" id="news-tab-all" class="px-3.5 py-1.5 text-xs font-semibold rounded-md transition bg-emerald-500 text-slate-950 font-bold">All Stories</button>
            <button type="button" onclick="window.newsApp.setFilter('news')" id="news-tab-news" class="px-3.5 py-1.5 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white">News</button>
            <button type="button" onclick="window.newsApp.setFilter('analysis')" id="news-tab-analysis" class="px-3.5 py-1.5 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white">Analysis</button>
        </div>

        <!-- Search Input -->
        <div class="relative flex-1 sm:max-w-xs">
            <input 
                type="text" 
                id="news-search-input" 
                oninput="window.newsApp.handleSearch(this.value)"
                placeholder="Search headlines or analysis..." 
                class="w-full bg-slate-950 text-xs text-slate-200 border border-slate-800 rounded-lg pl-9 pr-4 py-2 focus:outline-none focus:border-emerald-500/50"
            />
            <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
    </div>

    <!-- News Grid Container -->
    <div id="news-cards-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <!-- Populated dynamically via JS from raw news items for instant filtering -->
    </div>

    <!-- Empty State -->
    <div id="news-empty-state" class="hidden bg-slate-900 border border-slate-800 rounded-xl p-12 text-center text-slate-400">
        <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 002-2V7m2 13a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
        <h4 class="text-sm font-bold text-white mb-1">No News Articles Found</h4>
        <p class="text-xs text-slate-500 max-w-sm mx-auto">Try clearing your search query or selecting a different category tab.</p>
    </div>

</div>

<script>
    window.INITIAL_NEWS_ITEMS = @json($news ?? []);
</script>
@endsection
