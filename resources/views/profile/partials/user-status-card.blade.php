<section>
    <header class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                <span 
                    class="w-3 h-3 rounded-full inline-block {{ Auth::user()->is_online ? 'bg-emerald-400 shadow-lg shadow-emerald-500/50 animate-pulse' : 'bg-slate-500' }}"
                ></span>
                <span>Account Status & Presence</span>
            </h2>
            <p class="mt-1 text-xs text-slate-400">
                Live session telemetry and authentication status.
            </p>
        </div>
        
        <div class="flex items-center space-x-2">
            <span 
                class="px-2.5 py-1 text-xs font-mono font-bold rounded-full border flex items-center space-x-1.5 {{ Auth::user()->is_online ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-slate-800 text-slate-400 border-slate-700' }}"
            >
                <span class="w-1.5 h-1.5 rounded-full {{ Auth::user()->is_online ? 'bg-emerald-400 animate-ping' : 'bg-slate-500' }}"></span>
                <span>{{ Auth::user()->is_online ? 'ONLINE NOW' : 'OFFLINE' }}</span>
            </span>
        </div>
    </header>

    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Online Status Card -->
        <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-medium">Current Status</span>
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728M8.464 15.536a5 5 0 010-7.072m7.072 0a5 5 0 010 7.072M12 12h.01"></path></svg>
            </div>
            <div>
                <div class="text-sm font-bold {{ Auth::user()->is_online ? 'text-emerald-400' : 'text-slate-400' }}">
                    {{ Auth::user()->is_online ? 'Active Session' : 'Disconnected' }}
                </div>
                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                    {{ Auth::user()->is_online ? 'Heartbeat verified' : 'No active socket' }}
                </div>
            </div>
        </div>

        <!-- Last Seen -->
        <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-medium">Last Seen</span>
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="text-sm font-bold text-white font-mono">
                    {{ Auth::user()->last_seen_at ? \Carbon\Carbon::parse(Auth::user()->last_seen_at)->diffForHumans() : 'Never' }}
                </div>
                <div class="text-[11px] text-slate-500 font-mono mt-0.5 truncate">
                    {{ Auth::user()->last_seen_at ? \Carbon\Carbon::parse(Auth::user()->last_seen_at)->toDayDateTimeString() : 'N/A' }}
                </div>
            </div>
        </div>

        <!-- Last Login -->
        <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-medium">Last Logged In</span>
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
            </div>
            <div>
                <div class="text-sm font-bold text-white font-mono">
                    {{ Auth::user()->last_login_at ? \Carbon\Carbon::parse(Auth::user()->last_login_at)->diffForHumans() : 'Current Session' }}
                </div>
                <div class="text-[11px] text-slate-500 font-mono mt-0.5 truncate">
                    {{ Auth::user()->last_login_at ? \Carbon\Carbon::parse(Auth::user()->last_login_at)->toDayDateTimeString() : 'Active' }}
                </div>
            </div>
        </div>

        <!-- Membership Tier -->
        <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-4 flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-medium">Plan Tier</span>
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="text-sm font-bold text-emerald-400">
                    Free Tier
                </div>
                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                    Full Platform Access
                </div>
            </div>
        </div>
    </div>
</section>
