<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';

const props = defineProps({
    groups: {
        type: Array,
        default: () => [],
    },
    newsItems: {
        type: Array,
        default: () => [],
    },
    analysisItems: {
        type: Array,
        default: () => [],
    },
    commentsItems: {
        type: Array,
        default: () => [],
    },
    onlineTraders: {
        type: Array,
        default: () => [],
    },
    onlineTradersCount: {
        type: Number,
        default: 0,
    },
});

// Reactivity for News Modal
const selectedNews = ref(null);
const likedStories = ref({});
const newCommentText = ref('');
const showAuthAlert = ref(false);
const authAlertAction = ref('');
const isSubmittingComment = ref(false);

// Keep selectedNews synced with updated props when comments are added
watch(() => [props.newsItems, props.analysisItems], ([news, analysis]) => {
    if (selectedNews.value) {
        const all = [...(news || []), ...(analysis || [])];
        const updated = all.find(n => n.id === selectedNews.value.id);
        if (updated) {
            selectedNews.value = updated;
        }
    }
}, { deep: true });

function triggerGuestAuthPrompt(action) {
    authAlertAction.value = action;
    showAuthAlert.value = true;
}

function postComment() {
    if (!newCommentText.value.trim() || !selectedNews.value || isSubmittingComment.value) return;

    isSubmittingComment.value = true;
    router.post(route('news.comments.store', selectedNews.value.id), {
        content: newCommentText.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            newCommentText.value = '';
            isSubmittingComment.value = false;
        },
        onError: () => {
            isSubmittingComment.value = false;
        }
    });
}

// Reactivity for Traders Online live polling
let pollInterval = null;

// Reactivity for global market sessions indicator
const currentUtcHour = ref(new Date().getUTCHours());
let sessionInterval = null;

// Global market sessions open/close hours (UTC)
const sessions = computed(() => [
    { name: 'Sydney', open: 22, close: 7, color: 'emerald' },
    { name: 'Tokyo', open: 23, close: 8, color: 'emerald' },
    { name: 'London', open: 8, close: 17, color: 'cyan' },
    { name: 'New York', open: 13, close: 22, color: 'blue' },
]);

const activeSessions = computed(() => {
    const hour = currentUtcHour.value;
    return sessions.value.map(s => {
        let isOpen = false;
        if (s.open < s.close) {
            isOpen = hour >= s.open && hour < s.close;
        } else {
            // Overlapping midnight (e.g. Sydney 22:00 to 07:00)
            isOpen = hour >= s.open || hour < s.close;
        }
        return { ...s, isOpen };
    });
});


// Lifecycle intervals
onMounted(() => {
    // Poll active online traders every 15 seconds to keep telemetry live
    pollInterval = setInterval(() => {
        router.reload({ only: ['onlineTraders'], preserveScroll: true, preserveState: true });
    }, 15000);

    // Clock update
    sessionInterval = setInterval(() => {
        currentUtcHour.value = new Date().getUTCHours();
    }, 60000);
});

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
    if (sessionInterval) clearInterval(sessionInterval);
});

// Toggle Premium logic via controller
function togglePremium() {
    router.post(route('profile.toggle-premium'), {}, {
        preserveScroll: true,
    });
}


// News reader dialog trigger
function readNews(story) {
    selectedNews.value = story;
    newCommentText.value = '';
}

function formatTimeAgo(dateString) {
    if (!dateString) return 'Just now';
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMins / 60);

    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
}

function openArticleByTitle(title) {
    const article = [...(props.newsItems || []), ...(props.analysisItems || [])].find(item => item.title === title);
    if (article) {
        readNews(article);
    }
}
</script>

<template>
    <Head title="Trading Hub" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white flex items-center space-x-2">
                        <span>Global Market Workspace</span>
                        <span class="text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full font-semibold">Live Feed</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Real-time terminal rates, calendar developments, and core insights aggregated.</p>
                </div>
                <!-- Session Clocks -->
                <div class="flex items-center space-x-3 bg-slate-950/80 px-4 py-2 rounded-xl border border-slate-800 shadow-inner">
                    <span class="text-xs text-slate-500 font-medium">Sessions:</span>
                    <div class="flex space-x-3">
                        <div 
                            v-for="session in activeSessions" 
                            :key="session.name"
                            class="flex items-center space-x-1.5"
                            :title="`${session.name} Session (${session.open}:00 - ${session.close}:00 UTC)`"
                        >
                            <span 
                                class="w-2 h-2 rounded-full" 
                                :class="session.isOpen ? 'bg-emerald-500 animate-pulse shadow-lg shadow-emerald-500/55' : 'bg-slate-700'"
                            ></span>
                            <span class="text-xs font-mono" :class="session.isOpen ? 'text-slate-200 font-semibold' : 'text-slate-500'">{{ session.name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </template>

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
                            <span class="text-3xl font-extrabold font-mono tracking-tight text-white transition-all duration-300">
                                {{ typeof onlineTradersCount === 'number' ? onlineTradersCount : (onlineTraders ? onlineTraders.length : 0) }}
                            </span>
                            <span class="text-xs text-emerald-400 font-semibold">
                                Active Now
                            </span>
                        </div>
                    </div>

                    <!-- Page Workspace Layout Customization -->
                    <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg">
                        <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase mb-4">Workspace Customizer</h3>
                        <div class="space-y-3">
                            <div class="p-3 bg-slate-950 rounded-lg border border-slate-800/80 flex items-center justify-between">
                                <div class="text-xs">
                                    <span class="block font-semibold text-slate-200">Terminal Sounds</span>
                                    <span class="text-[10px] text-slate-500">Audio alerts on calendar data</span>
                                </div>
                                <button class="w-8 h-4 rounded-full bg-slate-800 relative transition-colors duration-200">
                                    <span class="absolute top-0.5 left-0.5 w-3 h-3 bg-slate-400 rounded-full transition-transform duration-200"></span>
                                </button>
                            </div>
                            <div class="p-3 bg-slate-950 rounded-lg border border-slate-800/80 flex items-center justify-between">
                                <div class="text-xs">
                                    <span class="block font-semibold text-slate-200">Session Clocks</span>
                                    <span class="text-[10px] text-slate-500">Auto highlight active zones</span>
                                </div>
                                <button class="w-8 h-4 rounded-full bg-emerald-500/20 relative transition-colors duration-200">
                                    <span class="absolute top-0.5 right-0.5 w-3 h-3 bg-emerald-400 rounded-full transition-transform duration-200"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- PRO TRADING GROUPS CALLOUT (Interactive toggle of database is_premium) -->
                    <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg relative overflow-hidden group">
                        <div class="absolute -top-10 -right-10 w-28 h-28 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all duration-300"></div>
                        
                        <div v-if="$page.props.auth.user">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase">Pipfolio Membership</h3>
                                <span 
                                    class="text-[10px] font-bold px-2 py-0.5 rounded-full border" 
                                    :class="$page.props.auth.user.is_premium 
                                        ? 'bg-amber-500/10 text-amber-400 border-amber-500/30 shadow-md shadow-amber-500/5' 
                                        : 'bg-slate-800 text-slate-400 border-slate-700'"
                                >
                                    {{ $page.props.auth.user.is_premium ? 'PRO MEMBER' : 'FREE ACCOUNT' }}
                                </span>
                            </div>
                            
                            <p class="text-xs text-slate-355 leading-relaxed mb-4">
                                Premium users can form custom groups, post private signals, and access automated macroeconomic trade forecasts.
                            </p>

                            <!-- Upgrade/Downgrade Button -->
                            <button 
                                @click="togglePremium"
                                class="w-full py-2.5 px-4 rounded-lg font-bold text-xs transition duration-300 flex items-center justify-center space-x-2 border shadow-lg shadow-black/20"
                                :class="$page.props.auth.user.is_premium 
                                    ? 'bg-slate-800 hover:bg-slate-750 text-slate-300 border-slate-700 hover:text-white' 
                                    : 'bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 border-emerald-400 hover:scale-[1.02]'"
                            >
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span>{{ $page.props.auth.user.is_premium ? 'Cancel Premium Status' : 'Activate Premium Membership' }}</span>
                            </button>
                        </div>
                        <div v-else>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase">Pipfolio Pro</h3>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border bg-slate-800 text-slate-400 border-slate-700">
                                    GUEST VIEW
                                </span>
                            </div>
                            
                            <p class="text-xs text-slate-355 leading-relaxed mb-4">
                                Premium users can form custom groups, post private signals, and access automated macroeconomic trade forecasts.
                            </p>

                            <!-- Register to Join Button -->
                            <Link 
                                :href="route('register')"
                                class="w-full py-2.5 px-4 rounded-lg font-bold text-xs bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 border border-emerald-400 shadow-lg shadow-black/20 text-center block transition hover:scale-[1.02]"
                            >
                                Register to Join Pro Groups
                            </Link>
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
                                <Link 
                                    :href="route('groups.index')" 
                                    class="text-cyan-400 hover:text-cyan-300 font-bold text-xs flex items-center space-x-1 transition"
                                >
                                    <span>View All Forums</span>
                                    <span>&rarr;</span>
                                </Link>
                            </div>
                        </div>

                        <!-- Grid of Top 10 Groups -->
                        <div v-if="groups && groups.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                            <div 
                                v-for="(group, idx) in groups.slice(0, 10)" 
                                :key="'top-group-' + group.id"
                                class="bg-slate-950/60 border border-slate-800/80 hover:border-cyan-500/40 rounded-xl p-4 transition-all duration-200 hover:bg-slate-900 flex flex-col justify-between group relative overflow-hidden shadow-sm"
                            >
                                <!-- Top Rank Accent Indicator -->
                                <div 
                                    class="absolute top-0 left-0 right-0 h-0.5 transition-opacity duration-300"
                                    :class="idx === 0 ? 'bg-gradient-to-r from-amber-400 to-yellow-500' : (idx === 1 ? 'bg-gradient-to-r from-slate-300 to-slate-400' : (idx === 2 ? 'bg-gradient-to-r from-amber-700 to-amber-600' : 'bg-gradient-to-r from-cyan-500/50 to-emerald-500/50 opacity-0 group-hover:opacity-100'))"
                                ></div>

                                <div>
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="flex items-center space-x-2">
                                            <!-- Rank Badge -->
                                            <span 
                                                class="text-[10px] font-mono font-black px-1.5 py-0.5 rounded shrink-0"
                                                :class="idx === 0 
                                                    ? 'bg-amber-400/20 text-amber-300 border border-amber-400/30' 
                                                    : (idx === 1 
                                                        ? 'bg-slate-400/20 text-slate-200 border border-slate-400/30' 
                                                        : (idx === 2 
                                                            ? 'bg-amber-700/20 text-amber-400 border border-amber-700/30' 
                                                            : 'bg-slate-800/60 text-slate-400 border border-slate-700/80'))"
                                            >
                                                #{{ idx + 1 }}
                                            </span>
                                            <h4 class="text-xs font-bold text-slate-100 group-hover:text-cyan-400 transition-colors line-clamp-1">
                                                {{ group.name }}
                                            </h4>
                                        </div>
                                        <span v-if="group.owner" class="text-[10px] text-slate-500 font-mono shrink-0 truncate max-w-[110px] flex items-center space-x-1">
                                            <span 
                                                class="w-1.5 h-1.5 rounded-full inline-block shrink-0" 
                                                :class="group.owner.is_online ? 'bg-emerald-400 shadow-sm shadow-emerald-400 animate-pulse' : 'bg-slate-600'"
                                                :title="group.owner.is_online ? 'Host Online' : 'Host Offline'"
                                            ></span>
                                            <span class="truncate">@{{ group.owner.name }}</span>
                                        </span>
                                    </div>

                                    <p class="text-[11px] text-slate-400 line-clamp-2 leading-relaxed mb-3">
                                        {{ group.description || 'Active trader forum discussing market trends, currency charts, and macroeconomic signals.' }}
                                    </p>
                                </div>

                                <div class="flex items-center justify-between pt-2.5 border-t border-slate-800/80 text-[10px] font-mono">
                                    <div class="flex items-center space-x-3 text-slate-400">
                                        <!-- Members Count -->
                                        <span class="flex items-center space-x-1" title="Members">
                                            <svg class="w-3.5 h-3.5 text-cyan-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            <span class="font-semibold text-slate-300">{{ group.members_count || 0 }}</span>
                                            <span class="text-slate-500 hidden sm:inline">members</span>
                                        </span>
                                        <!-- Posts Count -->
                                        <span class="flex items-center space-x-1" title="Posts">
                                            <svg class="w-3.5 h-3.5 text-emerald-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                            <span class="font-semibold text-slate-300">{{ group.posts_count || 0 }}</span>
                                            <span class="text-slate-500 hidden sm:inline">posts</span>
                                        </span>
                                    </div>

                                    <Link 
                                        :href="route('groups.show', group.id)" 
                                        class="inline-flex items-center space-x-1 text-cyan-400 hover:text-cyan-300 font-bold group-hover:translate-x-0.5 transition-transform"
                                    >
                                        <span>Join Forum</span>
                                        <span>&rarr;</span>
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-8 text-center text-slate-400">
                            <svg class="w-10 h-10 text-slate-650 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20H22V18C22 15.7909 20.2091 14 18 14H16M17 20H7M17 20V18C17 16.9248 16.5772 15.9482 15.8906 15.2285M2 18C2 15.7909 3.79086 14 6 14H14C16.2091 14 18 15.7909 18 18V20H2M14 7C14 9.20914 12.2091 11 10 11C7.79086 11 6 9.20914 6 7C6 4.79086 7.79086 3 10 3C12.2091 3 14 4.79086 14 7Z"></path></svg>
                            <h4 class="text-sm font-bold text-white mb-1">No Active Community Forums Yet</h4>
                            <p class="text-xs text-slate-500 max-w-md mx-auto mb-3">Be the first to start a trading forum and share ideas with the community.</p>
                            <Link 
                                v-if="$page.props.auth.user && $page.props.auth.user.is_premium"
                                :href="route('groups.create')" 
                                class="inline-flex items-center space-x-1.5 bg-gradient-to-r from-emerald-500 to-cyan-500 text-slate-950 px-3.5 py-1.5 rounded-lg text-xs font-bold hover:from-emerald-400 hover:to-cyan-400 transition"
                            >
                                <span>Create First Forum</span>
                                <span>&rarr;</span>
                            </Link>
                            <Link 
                                v-else
                                :href="route('groups.index')" 
                                class="inline-flex items-center space-x-1.5 bg-slate-800 hover:bg-slate-750 text-slate-300 px-3.5 py-1.5 rounded-lg text-xs font-bold transition"
                            >
                                <span>Browse All Forums</span>
                                <span>&rarr;</span>
                            </Link>
                        </div>
                    </div>



                    <!-- TOP 5 LATEST NEWS -->
                    <div v-if="newsItems && newsItems.length > 0" class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg flex flex-col">
                        <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-800">
                            <h3 class="text-xs font-bold text-slate-200 tracking-wider uppercase flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                <span>Latest Market News</span>
                            </h3>
                            <Link 
                                :href="route('news.index')" 
                                class="text-emerald-400 hover:text-emerald-350 font-bold text-xs"
                            >
                                View More News &rarr;
                            </Link>
                        </div>
                        
                        <!-- Top 5 Feed List -->
                        <div class="divide-y divide-slate-850/60 space-y-3.5">
                            <div 
                                v-for="(item, idx) in newsItems.slice(0, 5)" 
                                :key="'news-'+idx"
                                class="group/item cursor-pointer pt-3.5 first:pt-0 pb-3 last:pb-0 hover:bg-slate-950/10 rounded-lg px-2 transition duration-150"
                                @click="readNews(item)"
                            >
                                <div class="flex justify-between items-center text-[9px] font-mono text-slate-500 mb-1.5">
                                    <span class="text-emerald-400 font-semibold">@{{ item.source }}</span>
                                    <span>{{ formatTimeAgo(item.published_at) }}</span>
                                </div>
                                <h4 class="text-xs font-bold text-slate-200 leading-snug group-hover/item:text-emerald-400 transition-colors line-clamp-1">
                                    {{ item.title }}
                                </h4>
                                <p class="text-[10px] text-slate-400 mt-1 line-clamp-2 font-normal leading-relaxed">
                                    {{ item.summary }}
                                </p>
                                <div class="mt-2 text-[9px] text-slate-555 flex items-center space-x-1.5 font-mono">
                                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    <span>{{ item.comments_count }} replies</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="bg-slate-900 border border-slate-800/80 rounded-xl p-8 text-center text-slate-400 shadow-lg">
                        <svg class="w-10 h-10 text-slate-650 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 002-2V7m2 13a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        <h4 class="text-sm font-bold text-white mb-1">Live Financial News Unavailable</h4>
                        <p class="text-xs text-slate-550 max-w-md mx-auto">Please check your internet connection or backend services. Feed aggregator is currently offline.</p>
                    </div>


                </div>

            </div>

        </div>

        <!-- POPUP MODAL FOR NEWS ARTICLE DETAILS -->
        <Transition
            enter-active-class="ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div 
                v-if="selectedNews" 
                class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4"
                @click.self="selectedNews = null"
            >
                <div class="bg-slate-900 border border-slate-800 w-full max-w-2xl rounded-2xl overflow-hidden shadow-2xl relative">
                    <div class="p-6 border-b border-slate-800/80">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[9px] font-mono font-bold px-2 py-0.5 rounded uppercase">
                                    {{ selectedNews.source || 'FXStreet' }}
                                </span>
                                <span class="text-[9px] font-mono text-slate-500 ml-2">
                                    {{ formatTimeAgo(selectedNews.published_at) }}
                                </span>
                            </div>
                            <button 
                                @click="selectedNews = null" 
                                class="text-slate-400 hover:text-white transition bg-slate-800 hover:bg-slate-700 p-1.5 rounded-lg"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <h3 class="text-lg font-extrabold text-white mt-3 leading-snug">
                            {{ selectedNews.title }}
                        </h3>
                    </div>
                    
                    <div class="p-6 space-y-4 max-h-[350px] overflow-y-auto scrollbar-thin">
                        <div v-if="selectedNews.image" class="w-full h-48 rounded-xl overflow-hidden bg-slate-950 border border-slate-800">
                            <img :src="selectedNews.image" :alt="selectedNews.title" class="w-full h-full object-cover" />
                        </div>
                        <p class="text-xs text-emerald-400/90 font-semibold bg-emerald-950/20 border border-emerald-500/10 p-3.5 rounded-lg leading-relaxed">
                            {{ selectedNews.description || selectedNews.summary }}
                        </p>
                    </div>

                    <!-- Commenting & Liking section in news details -->
                    <div class="p-6 border-t border-slate-800 bg-slate-950/40">
                        <div class="flex items-center justify-between mb-4">
                            <!-- Like button -->
                            <button 
                                @click="$page.props.auth.user ? (likedStories[selectedNews.title] = !likedStories[selectedNews.title]) : triggerGuestAuthPrompt('like this story')"
                                class="flex items-center space-x-1.5 text-xs transition px-3 py-1.5 rounded-lg border"
                                :class="likedStories[selectedNews.title] 
                                    ? 'bg-rose-500/10 text-rose-400 border-rose-500/30 shadow-md shadow-rose-500/5' 
                                    : 'bg-slate-850 hover:bg-slate-800 text-slate-400 hover:text-slate-200 border-slate-800'"
                            >
                                <svg class="w-3.5 h-3.5" :fill="likedStories[selectedNews.title] ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                <span>{{ likedStories[selectedNews.title] ? 'Liked' : 'Like Story' }}</span>
                            </button>
                            <span class="text-[10px] text-slate-500 font-mono">{{ likedStories[selectedNews.title] ? 21 : 20 }} likes</span>
                        </div>

                        <!-- Comment section -->
                        <div>
                            <span class="block text-xs font-bold text-slate-400 mb-2">Comments ({{ selectedNews.comments ? selectedNews.comments.length : (selectedNews.comments_count || 0) }})</span>
                            
                            <!-- Comment list -->
                            <div class="space-y-2.5 max-h-[160px] overflow-y-auto pr-1 mb-4 scrollbar-thin text-xs">
                                <div 
                                    v-for="c in (selectedNews.comments || [])" 
                                    :key="'comment-' + c.id" 
                                    class="bg-slate-900 border border-slate-850/80 p-2.5 rounded-lg"
                                >
                                    <div class="flex justify-between items-center font-mono text-[9px] text-slate-500 mb-1">
                                        <div class="flex items-center space-x-1.5 min-w-0">
                                            <span 
                                                class="w-1.5 h-1.5 rounded-full inline-block shrink-0" 
                                                :class="(c.user ? c.user.is_online : c.is_online) ? 'bg-emerald-400 shadow-sm shadow-emerald-400 animate-pulse' : 'bg-slate-600'"
                                                :title="(c.user ? c.user.is_online : c.is_online) ? 'Online' : 'Offline'"
                                            ></span>
                                            <span class="font-bold text-emerald-400 truncate">{{ c.user ? c.user.name : (c.username || 'Trader') }}</span>
                                        </div>
                                        <span class="shrink-0 ml-2">{{ formatTimeAgo(c.created_at || c.published_at) }}</span>
                                    </div>
                                    <p class="text-slate-300">{{ c.content || c.comment }}</p>
                                </div>
                                <div v-if="!selectedNews.comments || selectedNews.comments.length === 0" class="text-center py-3 text-slate-500 text-xs italic bg-slate-900/50 rounded-lg border border-slate-850">
                                    No comments yet. Share your thoughts on this story!
                                </div>
                            </div>

                            <!-- Comment Input -->
                            <div v-if="$page.props.auth.user">
                                <div class="flex space-x-2">
                                    <input 
                                        v-model="newCommentText"
                                        type="text" 
                                        placeholder="Add a public comment..." 
                                        class="flex-1 bg-slate-950 text-xs text-slate-200 border border-slate-800 rounded-lg px-3 py-2 focus:outline-none focus:border-emerald-500/50"
                                        @keyup.enter="postComment"
                                        :disabled="isSubmittingComment"
                                    />
                                    <button 
                                        @click="postComment"
                                        :disabled="isSubmittingComment || !newCommentText.trim()"
                                        class="bg-emerald-500 hover:bg-emerald-400 disabled:opacity-50 text-slate-950 font-bold text-xs px-4 py-2 rounded-lg transition"
                                    >
                                        {{ isSubmittingComment ? 'Posting...' : 'Post' }}
                                    </button>
                                </div>
                            </div>
                            <div v-else class="p-3 bg-slate-950 border border-slate-800/80 rounded-lg text-xs text-slate-400 text-center">
                                You must <Link :href="route('login')" class="text-emerald-400 font-bold hover:underline">Log in</Link> or <Link :href="route('register')" class="text-emerald-400 font-bold hover:underline">Register</Link> to comment or like this story.
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-950 border-t border-slate-850 flex justify-between items-center text-xs">
                        <a 
                            v-if="selectedNews.url && selectedNews.url !== '#'"
                            :href="selectedNews.url"
                            target="_blank"
                            class="text-emerald-400 hover:text-emerald-350 font-bold transition flex items-center space-x-1"
                        >
                            <span>Read Original Source</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                        <div v-else></div>
                        <button 
                            @click="selectedNews = null" 
                            class="bg-slate-800 hover:bg-slate-750 text-slate-300 px-4 py-2 rounded-lg font-bold transition"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- GUEST AUTH ALERT MODAL -->
        <Transition
            enter-active-class="ease-out duration-355"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div 
                v-if="showAuthAlert" 
                class="fixed inset-0 z-55 overflow-y-auto bg-slate-950/90 backdrop-blur-sm flex items-center justify-center p-4"
                @click.self="showAuthAlert = false"
            >
                <div class="bg-slate-900 border border-slate-800 w-full max-w-sm rounded-2xl overflow-hidden shadow-2xl p-6 text-center space-y-4">
                    <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-md font-bold text-white">Authentication Required</h4>
                        <p class="text-xs text-slate-400 mt-2">
                            You need to log in to {{ authAlertAction }} and participate in the community features.
                        </p>
                    </div>
                    <div class="flex flex-col space-y-2 pt-2 text-xs">
                        <Link 
                            :href="route('login')"
                            class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold py-2 rounded-lg text-center transition"
                        >
                            Log in
                        </Link>
                        <Link 
                            :href="route('register')"
                            class="w-full bg-slate-800 hover:bg-slate-750 text-slate-300 font-bold py-2 rounded-lg text-center border border-slate-700 transition"
                        >
                            Register
                        </Link>
                        <button 
                            @click="showAuthAlert = false"
                            class="w-full text-slate-500 hover:text-slate-400 py-1 transition"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

    </AuthenticatedLayout>
</template>

<style>
/* Custom marquee animation for top ticker tape */
@keyframes marquee {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}
.animate-marquee {
    animation: marquee 25s linear infinite;
}
.animate-marquee:hover {
    animation-play-state: paused;
}

/* Custom scrollbar styling */
.scrollbar-thin::-webkit-scrollbar {
    width: 4px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background: #334155;
    border-radius: 9999px;
}
.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background: #10b981;
}
</style>
