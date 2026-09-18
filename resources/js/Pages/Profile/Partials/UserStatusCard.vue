<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);

const formatDate = (dateStr) => {
    if (!dateStr) return 'Never';
    const d = new Date(dateStr);
    return d.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    });
};

const formatTimeAgo = (dateStr) => {
    if (!dateStr) return 'Never';
    const date = new Date(dateStr);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);

    if (diff < 10) return 'Just now';
    if (diff < 60) return `${diff}s ago`;
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    return `${Math.floor(diff / 86400)}d ago`;
};
</script>

<template>
    <section>
        <header class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                    <span 
                        class="w-3 h-3 rounded-full inline-block"
                        :class="user.is_online ? 'bg-emerald-400 shadow-lg shadow-emerald-500/50 animate-pulse' : 'bg-slate-500'"
                    ></span>
                    <span>Account Status & Presence</span>
                </h2>
                <p class="mt-1 text-xs text-slate-400">
                    Live session telemetry and authentication status.
                </p>
            </div>
            
            <div class="flex items-center space-x-2">
                <span 
                    class="px-2.5 py-1 text-xs font-mono font-bold rounded-full border flex items-center space-x-1.5"
                    :class="user.is_online ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'bg-slate-800 text-slate-400 border-slate-700'"
                >
                    <span class="w-1.5 h-1.5 rounded-full" :class="user.is_online ? 'bg-emerald-400 animate-ping' : 'bg-slate-500'"></span>
                    <span>{{ user.is_online ? 'ONLINE NOW' : 'OFFLINE' }}</span>
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
                    <div class="text-sm font-bold" :class="user.is_online ? 'text-emerald-400' : 'text-slate-400'">
                        {{ user.is_online ? 'Active Session' : 'Disconnected' }}
                    </div>
                    <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                        {{ user.is_online ? 'Heartbeat verified' : 'No active socket' }}
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
                        {{ formatTimeAgo(user.last_seen_at) }}
                    </div>
                    <div class="text-[11px] text-slate-500 font-mono mt-0.5 truncate" :title="formatDate(user.last_seen_at)">
                        {{ formatDate(user.last_seen_at) }}
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
                        {{ formatTimeAgo(user.last_login_at) }}
                    </div>
                    <div class="text-[11px] text-slate-500 font-mono mt-0.5 truncate" :title="formatDate(user.last_login_at)">
                        {{ formatDate(user.last_login_at) }}
                    </div>
                </div>
            </div>

            <!-- Membership Plan -->
            <div class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                    <span class="font-medium">Tier Status</span>
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                </div>
                <div>
                    <div class="text-sm font-bold" :class="user.is_premium ? 'text-amber-400' : 'text-slate-300'">
                        {{ user.is_premium ? 'PRO Member' : 'Free Tier' }}
                    </div>
                    <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                        {{ user.is_premium ? 'Unlimited Access' : 'Standard Quota' }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
