<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({ news: Array });

// Reactivity for search and filters
const searchQuery = ref('');
const filterType = ref('all'); // all, news, analysis
const selectedNews = ref(null);
const newCommentText = ref('');
const isSubmittingComment = ref(false);

// Keep selectedNews synced with updated props when comments are added
watch(() => props.news, (newNews) => {
    if (selectedNews.value && newNews) {
        const updated = newNews.find(n => n.id === selectedNews.value.id);
        if (updated) {
            selectedNews.value = updated;
        }
    }
}, { deep: true });

// Filter logic
const filteredNews = computed(() => {
    return (props.news || []).filter(item => {
        // Filter by Type
        if (filterType.value === 'news' && item.is_analysis) return false;
        if (filterType.value === 'analysis' && !item.is_analysis) return false;

        // Filter by Search Query
        if (searchQuery.value) {
            const query = searchQuery.value.toLowerCase();
            return (item.title && item.title.toLowerCase().includes(query)) || 
                   (item.description && item.description.toLowerCase().includes(query)) ||
                   (item.summary && item.summary.toLowerCase().includes(query));
        }

        return true;
    });
});

// Helper for formatting time relative to now
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

// News reader dialog trigger
function readNews(story) {
    selectedNews.value = story;
    newCommentText.value = '';
}

// Post comment to database
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
</script>

<template>
    <Head title="Market News Hub" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-white flex items-center space-x-2">
                        <span>Market News Hub</span>
                        <span class="text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full font-semibold">Real-Time Database Feed</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Live market headlines synced with community discussions and macroeconomic analysis.</p>
                </div>
            </div>
        </template>

        <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            
            <!-- Filters & Search Toolbar -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 mb-6 shadow-lg flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <button 
                        @click="filterType = 'all'"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition border"
                        :class="filterType === 'all' 
                            ? 'bg-emerald-500 text-slate-950 border-emerald-400 shadow-md shadow-emerald-500/10' 
                            : 'bg-slate-950 text-slate-400 border-slate-800 hover:text-slate-200 hover:bg-slate-900'"
                    >
                        All Feeds
                    </button>
                    <button 
                        @click="filterType = 'news'"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition border flex items-center space-x-1.5"
                        :class="filterType === 'news' 
                            ? 'bg-emerald-500 text-slate-950 border-emerald-400 shadow-md shadow-emerald-500/10' 
                            : 'bg-slate-950 text-slate-400 border-slate-800 hover:text-slate-200 hover:bg-slate-900'"
                    >
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>Latest News</span>
                    </button>
                    <button 
                        @click="filterType = 'analysis'"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition border flex items-center space-x-1.5"
                        :class="filterType === 'analysis' 
                            ? 'bg-emerald-500 text-slate-950 border-emerald-400 shadow-md shadow-emerald-500/10' 
                            : 'bg-slate-950 text-slate-400 border-slate-800 hover:text-slate-200 hover:bg-slate-900'"
                    >
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        <span>Market Analysis</span>
                    </button>
                </div>

                <div class="relative">
                    <input 
                        v-model="searchQuery"
                        type="text" 
                        placeholder="Search headlines or analysis..." 
                        class="bg-slate-950 text-xs text-slate-200 border border-slate-800 rounded-lg pl-8 pr-4 py-2 focus:outline-none focus:border-emerald-500/50 w-full md:w-64"
                    />
                    <svg class="w-4 h-4 text-slate-500 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- News Grid -->
            <div v-if="filteredNews.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="item in filteredNews" 
                    :key="'article-' + item.id" 
                    class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg flex flex-col justify-between hover:border-slate-700/80 transition-all duration-200 group cursor-pointer overflow-hidden"
                    @click="readNews(item)"
                >
                    <div>
                        <!-- Optional Thumbnail Image -->
                        <div v-if="item.image" class="w-full h-36 mb-3.5 rounded-lg overflow-hidden bg-slate-950 border border-slate-800/60">
                            <img :src="item.image" :alt="item.title" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" />
                        </div>

                        <div class="flex justify-between items-center text-[10px] font-mono text-slate-500 mb-2.5">
                            <span class="text-emerald-400 font-semibold">@{{ item.source || 'FXStreet' }}</span>
                            <span>{{ formatTimeAgo(item.published_at) }}</span>
                        </div>
                        
                        <h3 class="text-sm font-bold text-slate-100 mb-2 leading-snug group-hover:text-emerald-400 transition-colors line-clamp-2">
                            {{ item.title }}
                        </h3>
                        
                        <p class="text-xs text-slate-400 mb-4 line-clamp-3 leading-relaxed">
                            {{ item.description || item.summary }}
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-950/40 text-[10px] font-mono">
                        <div class="flex items-center space-x-2">
                            <span 
                                class="text-[8px] font-extrabold px-2 py-0.5 rounded border"
                                :class="item.is_analysis 
                                    ? 'bg-amber-500/10 text-amber-400 border-amber-500/20' 
                                    : 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20'"
                            >
                                {{ item.is_analysis ? 'ANALYSIS' : 'NEWS' }}
                            </span>
                            <span class="text-slate-450 flex items-center space-x-1">
                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                <span>{{ item.comments_count || (item.comments ? item.comments.length : 0) }}</span>
                            </span>
                        </div>
                        
                        <button class="text-emerald-400 group-hover:text-emerald-350 font-bold text-xs flex items-center space-x-1">
                            <span>Read & Discuss</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-slate-900 border border-slate-800/80 rounded-xl p-12 text-center text-slate-400 shadow-lg">
                <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 002-2V7m2 13a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                <h4 class="text-base font-bold text-white mb-1">No Articles Found</h4>
                <p class="text-xs text-slate-550 max-w-md mx-auto">There are no articles matching your filter/search or the RSS feed is currently updating.</p>
            </div>
        </div>

        <!-- ARTICLE READER & COMMENTS DIALOG -->
        <div v-if="selectedNews" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm transition-opacity duration-300" @click.self="selectedNews = null">
            <div class="bg-slate-900 border border-slate-800 w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <!-- Modal header -->
                <div class="p-5 border-b border-slate-850 bg-slate-900/90 backdrop-blur flex justify-between items-start">
                    <div>
                        <div class="flex items-center space-x-2 text-[9px] font-mono text-slate-500 uppercase tracking-wider">
                            <span class="text-emerald-400 font-bold">@{{ selectedNews.source || 'FXStreet' }}</span>
                            <span>&bull;</span>
                            <span>{{ formatTimeAgo(selectedNews.published_at) }}</span>
                        </div>
                        <h3 class="text-base font-bold text-white mt-1.5 leading-snug">{{ selectedNews.title }}</h3>
                    </div>
                    <button 
                        @click="selectedNews = null" 
                        class="text-slate-500 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition shrink-0 ml-4"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <!-- Modal body -->
                <div class="p-6 overflow-y-auto space-y-4 text-slate-300 leading-relaxed text-xs">
                    <div v-if="selectedNews.image" class="w-full h-48 rounded-xl overflow-hidden bg-slate-950 border border-slate-800">
                        <img :src="selectedNews.image" :alt="selectedNews.title" class="w-full h-full object-cover" />
                    </div>

                    <p class="font-semibold text-slate-200 bg-slate-950/60 p-4 rounded-xl border-l-2 border-emerald-500 text-xs leading-relaxed">
                        {{ selectedNews.description || selectedNews.summary }}
                    </p>

                    <!-- Comments Section -->
                    <div class="pt-4 border-t border-slate-800">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-bold text-slate-200 uppercase tracking-wider flex items-center space-x-2">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                <span>Trader Comments ({{ selectedNews.comments ? selectedNews.comments.length : (selectedNews.comments_count || 0) }})</span>
                            </h4>
                        </div>

                        <!-- Comment list -->
                        <div class="space-y-2.5 max-h-[180px] overflow-y-auto pr-1 mb-4">
                            <div 
                                v-for="c in (selectedNews.comments || [])" 
                                :key="'comment-' + c.id" 
                                class="bg-slate-950/80 border border-slate-850 p-3 rounded-lg"
                            >
                                <div class="flex justify-between items-center text-[10px] font-mono text-slate-400 mb-1">
                                    <div class="flex items-center space-x-1.5 min-w-0">
                                        <span 
                                            class="w-1.5 h-1.5 rounded-full inline-block shrink-0" 
                                            :class="c.user?.is_online ? 'bg-emerald-400 shadow-sm shadow-emerald-400 animate-pulse' : 'bg-slate-600'"
                                            :title="c.user?.is_online ? 'Online' : 'Offline'"
                                        ></span>
                                        <span class="font-bold text-emerald-400 truncate">{{ c.user ? c.user.name : 'Trader' }}</span>
                                    </div>
                                    <span class="shrink-0 ml-2">{{ formatTimeAgo(c.created_at) }}</span>
                                </div>
                                <p class="text-slate-300 text-xs">{{ c.content }}</p>
                            </div>

                            <div v-if="!selectedNews.comments || selectedNews.comments.length === 0" class="text-center py-4 text-slate-500 text-xs italic bg-slate-950/30 rounded-lg border border-slate-850">
                                No comments yet. Be the first trader to share your thoughts on this story!
                            </div>
                        </div>

                        <!-- Add comment form -->
                        <div v-if="$page.props.auth.user" class="pt-2">
                            <div class="flex space-x-2">
                                <input 
                                    v-model="newCommentText"
                                    type="text" 
                                    placeholder="Write a comment..." 
                                    class="flex-1 bg-slate-950 text-xs text-slate-200 border border-slate-800 rounded-lg px-3 py-2 focus:outline-none focus:border-emerald-500/50"
                                    @keyup.enter="postComment"
                                    :disabled="isSubmittingComment"
                                />
                                <button 
                                    @click="postComment"
                                    :disabled="isSubmittingComment || !newCommentText.trim()"
                                    class="bg-emerald-500 hover:bg-emerald-400 disabled:opacity-50 text-slate-950 font-bold text-xs px-4 py-2 rounded-lg transition"
                                >
                                    {{ isSubmittingComment ? 'Posting...' : 'Comment' }}
                                </button>
                            </div>
                        </div>
                        <div v-else class="p-3 bg-slate-950 border border-slate-850 rounded-lg text-xs text-slate-400 text-center">
                            You must <Link :href="route('login')" class="text-emerald-400 font-bold hover:underline">Log in</Link> or <Link :href="route('register')" class="text-emerald-400 font-bold hover:underline">Register</Link> to post comments on news articles.
                        </div>
                    </div>
                </div>
                
                <!-- Modal footer -->
                <div class="p-4 bg-slate-950 border-t border-slate-850 flex justify-between items-center text-xs">
                    <a 
                        v-if="selectedNews.url && selectedNews.url !== '#'"
                        :href="selectedNews.url"
                        target="_blank"
                        class="text-emerald-400 hover:text-emerald-350 font-bold transition flex items-center space-x-1"
                    >
                        <span>View on FXStreet</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                    <span v-else class="text-[10px] text-slate-500 font-mono">Original Source: FXStreet Feed</span>
                    
                    <button 
                        @click="selectedNews = null" 
                        class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-4 py-2 rounded-lg font-semibold transition"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

