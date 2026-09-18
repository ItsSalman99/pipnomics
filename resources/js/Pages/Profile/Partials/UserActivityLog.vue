<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    activities: {
        type: Array,
        default: () => [],
    },
});

const filterType = ref('all');

const getActivityBadge = (type) => {
    switch (type) {
        case 'login':
            return {
                label: 'Login',
                bg: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                icon: 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
            };
        case 'logout':
            return {
                label: 'Logout',
                bg: 'bg-slate-800 text-slate-400 border-slate-700',
                icon: 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1',
            };
        case 'register':
            return {
                label: 'Account Created',
                bg: 'bg-teal-500/10 text-teal-400 border-teal-500/30',
                icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
            };
        case 'comment_created':
            return {
                label: 'News Comment',
                bg: 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                icon: 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z',
            };
        case 'post_created':
            return {
                label: 'Community Post',
                bg: 'bg-blue-500/10 text-blue-400 border-blue-500/30',
                icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            };
        case 'group_created':
            return {
                label: 'Group Created',
                bg: 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
            };
        case 'group_joined':
            return {
                label: 'Joined Group',
                bg: 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30',
                icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            };
        case 'group_left':
            return {
                label: 'Left Group',
                bg: 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                icon: 'M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6',
            };
        case 'profile_updated':
            return {
                label: 'Profile Edit',
                bg: 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30',
                icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            };
        case 'premium_toggled':
            return {
                label: 'PRO Status',
                bg: 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30',
                icon: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
            };
        default:
            return {
                label: type,
                bg: 'bg-slate-800 text-slate-300 border-slate-700',
                icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            };
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
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
    if (!dateStr) return '';
    const date = new Date(dateStr);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);

    if (diff < 10) return 'Just now';
    if (diff < 60) return `${diff}s ago`;
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    return `${Math.floor(diff / 86400)}d ago`;
};

const filteredActivities = computed(() => {
    if (filterType.value === 'all') return props.activities;
    if (filterType.value === 'auth') {
        return props.activities.filter(a => ['login', 'logout', 'register'].includes(a.activity_type));
    }
    if (filterType.value === 'community') {
        return props.activities.filter(a => ['comment_created', 'post_created', 'group_created', 'group_joined', 'group_left'].includes(a.activity_type));
    }
    return props.activities;
});
</script>

<template>
    <section>
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span>Activity History & Audit Logs</span>
                </h2>
                <p class="mt-1 text-xs text-slate-400">
                    Comprehensive chronological record of user sessions, security events, and community activities.
                </p>
            </div>

            <!-- Filter tabs -->
            <div class="flex items-center space-x-1 bg-slate-950/80 p-1 rounded-lg border border-slate-800 text-xs font-medium">
                <button 
                    @click="filterType = 'all'"
                    class="px-3 py-1 rounded transition"
                    :class="filterType === 'all' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'text-slate-400 hover:text-white'"
                >
                    All ({{ activities.length }})
                </button>
                <button 
                    @click="filterType = 'auth'"
                    class="px-3 py-1 rounded transition"
                    :class="filterType === 'auth' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'text-slate-400 hover:text-white'"
                >
                    Auth & Security
                </button>
                <button 
                    @click="filterType = 'community'"
                    class="px-3 py-1 rounded transition"
                    :class="filterType === 'community' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'text-slate-400 hover:text-white'"
                >
                    Community
                </button>
            </div>
        </header>

        <!-- Activities List -->
        <div class="mt-6">
            <div v-if="filteredActivities.length > 0" class="overflow-hidden border border-slate-800/80 rounded-xl bg-slate-950/60 divide-y divide-slate-850">
                <div 
                    v-for="act in filteredActivities" 
                    :key="'activity-' + act.id"
                    class="p-4 hover:bg-slate-900/50 transition flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                >
                    <!-- Left: Badge & Description -->
                    <div class="flex items-start sm:items-center space-x-3 min-w-0 flex-1">
                        <!-- Icon Badge -->
                        <div 
                            class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center border"
                            :class="getActivityBadge(act.activity_type).bg"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getActivityBadge(act.activity_type).icon"></path>
                            </svg>
                        </div>

                        <!-- Text Details -->
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center space-x-2">
                                <span 
                                    class="text-[10px] font-mono uppercase font-bold px-1.5 py-0.5 rounded border"
                                    :class="getActivityBadge(act.activity_type).bg"
                                >
                                    {{ getActivityBadge(act.activity_type).label }}
                                </span>
                                <span class="text-xs font-semibold text-slate-200 truncate">
                                    {{ act.description }}
                                </span>
                            </div>
                            
                            <!-- Context/IP/User Agent -->
                            <div class="flex items-center space-x-3 mt-1 text-[11px] font-mono text-slate-500">
                                <span v-if="act.ip_address" class="flex items-center space-x-1">
                                    <svg class="w-3 h-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                    <span>{{ act.ip_address }}</span>
                                </span>
                                <span v-if="act.user_agent" class="truncate max-w-[200px] sm:max-w-[320px]" :title="act.user_agent">
                                    {{ act.user_agent }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Timestamp -->
                    <div class="text-right sm:flex-shrink-0 font-mono">
                        <div class="text-xs text-emerald-400 font-bold">
                            {{ formatTimeAgo(act.created_at) }}
                        </div>
                        <div class="text-[10px] text-slate-500 mt-0.5" :title="formatDate(act.created_at)">
                            {{ formatDate(act.created_at) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12 px-4 bg-slate-950/40 border border-slate-850 rounded-xl">
                <div class="w-12 h-12 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center mx-auto mb-3 text-slate-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-sm font-semibold text-slate-300">No activity recorded yet</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Actions such as logging in, posting comments, or joining community groups will appear in your live audit feed.
                </p>
            </div>
        </div>
    </section>
</template>
