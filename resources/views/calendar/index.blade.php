@extends('layouts.app')

@section('title', 'Economic Calendar')

@section('header')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-white flex items-center space-x-2">
            <span>Economic Calendar</span>
            <span class="text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full font-semibold">Live Macro Feed</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">Real-time macroeconomic calendar events, interest rate decisions, CPI prints, and market-moving metrics.</p>
    </div>
</div>
@endsection

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
    
    <!-- Top Navigation & Control Bar -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-lg flex flex-col lg:flex-row justify-between items-stretch lg:items-center gap-4">
        
        <!-- View Mode & Date Range Controls -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- View Mode Switcher -->
            <div class="flex bg-slate-950 p-1 rounded-lg border border-slate-800">
                <button type="button" onclick="window.calendarApp.setViewMode('day')" id="cal-mode-day" class="px-3 py-1 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white">Day</button>
                <button type="button" onclick="window.calendarApp.setViewMode('week')" id="cal-mode-week" class="px-3 py-1 text-xs font-semibold rounded-md transition bg-emerald-500 text-slate-950 font-bold">Week</button>
                <button type="button" onclick="window.calendarApp.setViewMode('month')" id="cal-mode-month" class="px-3 py-1 text-xs font-semibold rounded-md transition text-slate-400 hover:text-white">Month</button>
            </div>

            <!-- Previous / Today / Next -->
            <div class="flex items-center space-x-1.5">
                <button type="button" onclick="window.calendarApp.prevPeriod()" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-750 text-slate-300 transition" title="Previous Period">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button type="button" onclick="window.calendarApp.goToToday()" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-750 text-slate-200 text-xs font-bold transition">
                    Today
                </button>
                <button type="button" onclick="window.calendarApp.nextPeriod()" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-750 text-slate-300 transition" title="Next Period">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>

            <!-- Active Range Label -->
            <span id="cal-active-range-label" class="text-sm font-extrabold text-white font-mono px-2 py-1 bg-slate-950/70 border border-slate-800 rounded-lg"></span>
        </div>

        <!-- Timezone & Time Format Controls -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Search Box -->
            <div class="relative flex-1 sm:flex-initial">
                <input 
                    type="text" 
                    id="cal-search-input" 
                    oninput="window.calendarApp.handleSearch(this.value)"
                    placeholder="Search events..." 
                    class="bg-slate-950 text-xs text-slate-200 border border-slate-800 rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:border-emerald-500/50 w-full sm:w-44"
                />
                <svg class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Timezone Selector -->
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <select 
                    id="cal-timezone-select" 
                    onchange="window.calendarApp.setTimezone(this.value)"
                    class="bg-slate-950 text-xs font-mono text-slate-300 border border-slate-800 rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-emerald-500/50"
                >
                    <option value="LOCAL">Local Timezone</option>
                    <option value="UTC">UTC / GMT</option>
                    <option value="America/New_York">US Eastern (EST/EDT)</option>
                    <option value="Europe/London">London (GMT/BST)</option>
                    <option value="Asia/Tokyo">Tokyo (JST)</option>
                    <option value="Australia/Sydney">Sydney (AEST/AEDT)</option>
                </select>
            </div>

            <!-- 12h / 24h Toggle -->
            <button 
                id="cal-timeformat-toggle"
                type="button" 
                onclick="window.calendarApp.toggleTimeFormat()"
                class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-750 text-xs font-mono font-bold text-slate-300 transition"
                title="Toggle 12h / 24h Time Format"
            >
                <span id="cal-timeformat-label">12H</span>
            </button>
        </div>
    </div>

    <!-- Filters Bar: Currencies & Impacts -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 shadow-lg space-y-4">
        <!-- Currencies Selection -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center space-x-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Currencies:</span>
                <div class="flex flex-wrap gap-1.5" id="cal-currency-pills">
                    <!-- USD, EUR, GBP, AUD, CAD, CHF, JPY, NZD, CNY -->
                </div>
            </div>
            <div class="flex items-center space-x-2 text-[11px]">
                <button type="button" onclick="window.calendarApp.selectAllCurrencies()" class="text-emerald-400 hover:underline">Select All</button>
                <span class="text-slate-600">|</span>
                <button type="button" onclick="window.calendarApp.clearAllCurrencies()" class="text-slate-400 hover:underline">Clear</button>
            </div>
        </div>

        <!-- Impact Selection -->
        <div class="flex flex-wrap items-center gap-3 pt-3 border-t border-slate-800/80">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Impact:</span>
            
            <label class="inline-flex items-center space-x-1.5 cursor-pointer bg-rose-500/10 border border-rose-500/20 px-2.5 py-1 rounded-lg">
                <input type="checkbox" id="impact-high" checked onchange="window.calendarApp.toggleImpact('High', this.checked)" class="rounded bg-slate-950 border-rose-500/50 text-rose-500 focus:ring-0 w-3.5 h-3.5">
                <span class="text-xs font-bold text-rose-400">High Impact</span>
            </label>

            <label class="inline-flex items-center space-x-1.5 cursor-pointer bg-amber-500/10 border border-amber-500/20 px-2.5 py-1 rounded-lg">
                <input type="checkbox" id="impact-med" checked onchange="window.calendarApp.toggleImpact('Medium', this.checked)" class="rounded bg-slate-950 border-amber-500/50 text-amber-500 focus:ring-0 w-3.5 h-3.5">
                <span class="text-xs font-bold text-amber-400">Medium Impact</span>
            </label>

            <label class="inline-flex items-center space-x-1.5 cursor-pointer bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-1 rounded-lg">
                <input type="checkbox" id="impact-low" checked onchange="window.calendarApp.toggleImpact('Low', this.checked)" class="rounded bg-slate-950 border-emerald-500/50 text-emerald-500 focus:ring-0 w-3.5 h-3.5">
                <span class="text-xs font-bold text-emerald-400">Low Impact</span>
            </label>

            <label class="inline-flex items-center space-x-1.5 cursor-pointer bg-slate-800/60 border border-slate-700 px-2.5 py-1 rounded-lg">
                <input type="checkbox" id="impact-hol" checked onchange="window.calendarApp.toggleImpact('Holiday', this.checked)" class="rounded bg-slate-950 border-slate-600 text-slate-400 focus:ring-0 w-3.5 h-3.5">
                <span class="text-xs font-bold text-slate-400">Holiday</span>
            </label>
        </div>
    </div>

    <!-- Calendar Events Table Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/80 text-slate-400 uppercase font-mono text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4 w-24">Time</th>
                        <th class="py-3 px-4 w-20">Currency</th>
                        <th class="py-3 px-4 w-24">Impact</th>
                        <th class="py-3 px-4">Event Detail</th>
                        <th class="py-3 px-4 text-right w-24">Actual</th>
                        <th class="py-3 px-4 text-right w-24">Forecast</th>
                        <th class="py-3 px-4 text-right w-24">Previous</th>
                    </tr>
                </thead>
                <tbody id="cal-events-tbody" class="divide-y divide-slate-850/60">
                    <!-- Rendered dynamically via JS -->
                </tbody>
            </table>
        </div>

        <!-- Empty state container -->
        <div id="cal-empty-state" class="hidden p-12 text-center text-slate-400">
            <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <h4 class="text-sm font-bold text-white mb-1">No Economic Releases Found</h4>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">Try adjusting your active currency filters, impact levels, or navigate to a different time period.</p>
        </div>
    </div>

</div>

<!-- Pass initial data to JavaScript -->
<script>
    window.INITIAL_CALENDAR_EVENTS = @json($events ?? []);
</script>
@endsection
