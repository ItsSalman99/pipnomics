<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({ 
    group: Object, 
    isMember: Boolean 
});

const joinGroup = () => { router.post(route('groups.join', props.group.id)); };
const leaveGroup = () => { router.post(route('groups.leave', props.group.id)); };

// Post creation form state
const form = useForm({
    title: '',
    content: '',
});

const submitPost = () => {
    form.post(route('groups.posts.store', props.group.id), {
        onSuccess: () => {
            form.reset();
        }
    });
};

function formatTime(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' }) + ' ' + 
           date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}
</script>

<template>
  <Head :title="group.name" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-white flex items-center space-x-2">
            <span>{{ group.name }}</span>
          </h2>
          <p class="text-xs text-slate-400 mt-1">Official community thread. Discuss technicals, share forecasts, and follow updates.</p>
        </div>
        
        <!-- Join / Leave buttons -->
        <template v-if="$page.props.auth.user">
          <button 
            v-if="!isMember" 
            @click="joinGroup" 
            class="bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 px-4 py-2 rounded-lg font-bold text-xs transition duration-200 border border-emerald-400 shadow-md shadow-emerald-500/5 hover:scale-[1.02]"
          >
            Join Group
          </button>
          <button 
            v-else 
            @click="leaveGroup" 
            class="bg-rose-600 hover:bg-rose-500 text-white px-4 py-2 rounded-lg font-bold text-xs transition border border-rose-500/30 hover:scale-[1.02]"
          >
            Leave Group
          </button>
        </template>
        <Link 
          v-else 
          :href="route('login')" 
          class="bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-955 px-4 py-2 rounded-lg font-bold text-xs transition text-center hover:scale-[1.02]"
        >
          Login to Join Group
        </Link>
      </div>
    </template>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- LEFT COLUMN: THREAD POSTS (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
          
          <!-- Forum Thread Description -->
          <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg relative overflow-hidden">
            <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase mb-2">Group Description & Guidelines</h3>
            <p class="text-xs text-slate-300 leading-relaxed">
              {{ group.description || 'Welcome to our trading forum! Join the discussion, share your technical analysis charts, and discuss macroeconomic factors.' }}
            </p>
          </div>

          <!-- Forum Posts Feed -->
          <div class="space-y-4">
            <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase mb-1 flex items-center space-x-1.5">
              <span>Discussion Board</span>
              <span class="text-[9px] bg-slate-800 text-slate-400 border border-slate-700 px-1.5 py-0.5 rounded font-mono font-bold">{{ group.posts ? group.posts.length : 0 }} posts</span>
            </h3>

            <!-- Group Posts list -->
            <div 
              v-for="(post, index) in group.posts" 
              :key="post.id" 
              class="bg-slate-900 border border-slate-800/80 rounded-xl overflow-hidden shadow-lg flex flex-col md:flex-row"
            >
              <!-- Author Profile Column -->
              <div class="md:w-32 bg-slate-950/40 p-4 border-b md:border-b-0 md:border-r border-slate-800 flex md:flex-col items-center justify-start text-center shrink-0">
                <div class="relative mr-3 md:mr-0 md:mb-2.5">
                  <div class="w-10 h-10 bg-cyan-500/10 text-cyan-400 border border-cyan-500/25 rounded-full flex items-center justify-center font-bold text-sm">
                    {{ post.user.name.charAt(0) }}
                  </div>
                  <span 
                    class="w-2.5 h-2.5 rounded-full absolute bottom-0 right-0 border-2 border-slate-950"
                    :class="post.user.is_online ? 'bg-emerald-400 animate-pulse' : 'bg-slate-600'"
                    :title="post.user.is_online ? 'Online' : 'Offline'"
                  ></span>
                </div>
                <div class="text-left md:text-center">
                  <span class="block text-xs font-bold text-slate-200 truncate max-w-[100px]">{{ post.user.name }}</span>
                  <span 
                    class="inline-block text-[8px] font-extrabold font-mono px-1 rounded mt-0.5"
                    :class="post.user.id === group.owner_id 
                        ? 'bg-amber-500/10 text-amber-400 border border-amber-500/25' 
                        : 'bg-slate-800 text-slate-400'"
                  >
                    {{ post.user.id === group.owner_id ? 'ADMIN' : 'MEMBER' }}
                  </span>
                </div>
                <span class="ml-auto md:ml-0 md:mt-4 text-[9px] font-mono text-slate-650">#{{ group.posts.length - index }}</span>
              </div>

              <!-- Post Content Column -->
              <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                  <div class="flex justify-between items-center text-[9px] font-mono text-slate-500 mb-2">
                    <span>Thread Reply</span>
                    <span>{{ formatTime(post.created_at) }}</span>
                  </div>
                  <h4 v-if="post.title" class="text-xs font-bold text-slate-100 mb-2">{{ post.title }}</h4>
                  <p class="text-xs text-slate-300 leading-relaxed whitespace-pre-line">{{ post.content }}</p>
                </div>
              </div>
            </div>

            <!-- Empty State for Discussion -->
            <div v-if="!group.posts || group.posts.length === 0" class="bg-slate-900 border border-slate-800/80 rounded-xl p-8 text-center text-slate-400 shadow-lg">
              <svg class="w-10 h-10 text-slate-650 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
              <h4 class="text-xs font-bold text-white mb-1">No posts inside this group yet</h4>
              <p class="text-xs text-slate-500 max-w-sm mx-auto">Be the first to share an analysis setup or ask a question! Complete form below.</p>
            </div>
          </div>

          <!-- POST CREATOR CARD -->
          <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg">
            <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase mb-4">Post a Reply / Analysis</h3>
            
            <form v-if="$page.props.auth.user && isMember" @submit.prevent="submitPost" class="space-y-4">
              <div>
                <input 
                  type="text" 
                  class="bg-slate-950 text-xs text-slate-200 border border-slate-800 rounded-lg px-3 py-2 w-full focus:outline-none focus:border-cyan-500/50" 
                  placeholder="Subject / Title (e.g. Bullish Double Bottom on Gold 1H Chart) - Optional"
                  v-model="form.title"
                />
                <InputError class="mt-2" :message="form.errors.title" />
              </div>
              <div>
                <textarea 
                  class="bg-slate-950 text-xs text-slate-200 border border-slate-800 rounded-lg p-3 w-full focus:outline-none focus:border-cyan-500/50 leading-relaxed" 
                  rows="5"
                  placeholder="Write your analysis thoughts, entry/exit criteria, or charts summary..."
                  v-model="form.content"
                  required
                ></textarea>
                <InputError class="mt-2" :message="form.errors.content" />
              </div>
              <div class="flex justify-end pt-2 border-t border-slate-950/30">
                <PrimaryButton 
                  :class="{ 'opacity-25': form.processing }" 
                  :disabled="form.processing"
                  class="bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-955 px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-wider transition"
                >
                  Publish Post
                </PrimaryButton>
              </div>
            </form>
            
            <!-- Message if guest or not a member -->
            <div v-else-if="$page.props.auth.user" class="bg-slate-950/40 border border-slate-800/50 rounded-lg p-4 text-center">
              <p class="text-xs text-slate-400 mb-2">You must join this community group to participate in discussions and post replies.</p>
              <button 
                @click="joinGroup" 
                class="bg-cyan-500 hover:bg-cyan-400 text-slate-950 px-4 py-1.5 rounded font-bold text-xs shadow transition duration-200 inline-block"
              >
                Join Group Now
              </button>
            </div>
            <div v-else class="bg-slate-950/40 border border-slate-800/50 rounded-lg p-4 text-center">
              <p class="text-xs text-slate-400 mb-2">Please login or register an account to join this group and share your trading ideas.</p>
              <Link 
                :href="route('login')" 
                class="bg-cyan-500 hover:bg-cyan-400 text-slate-950 px-4 py-1.5 rounded font-bold text-xs shadow transition duration-200 inline-block"
              >
                Log In to Participate
              </Link>
            </div>
          </div>

        </div>

        <!-- RIGHT COLUMN: MEMBERS SIDEBAR (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
          
          <!-- Forum Group Metadata Card -->
          <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg">
            <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase mb-3">Forum Information</h3>
            <div class="space-y-2.5 text-xs">
              <div class="flex justify-between items-center py-2 border-b border-slate-950/30">
                <span class="text-slate-500">Group Owner</span>
                <span class="font-bold text-slate-200">@{{ group.owner ? group.owner.name : 'Unknown' }}</span>
              </div>
              <div class="flex justify-between items-center py-2 border-b border-slate-950/30">
                <span class="text-slate-500">Total Members</span>
                <span class="font-bold font-mono text-slate-200">{{ group.members.length }}</span>
              </div>
              <div class="flex justify-between items-center py-2 border-b border-slate-950/30">
                <span class="text-slate-500">Total Replies</span>
                <span class="font-bold font-mono text-slate-200">{{ group.posts ? group.posts.length : 0 }}</span>
              </div>
              <div class="flex justify-between items-center py-2">
                <span class="text-slate-500">Created Date</span>
                <span class="font-mono text-slate-400">{{ new Date(group.created_at).toLocaleDateString() }}</span>
              </div>
            </div>
          </div>

          <!-- Members list Card -->
          <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg flex flex-col max-h-[350px]">
            <h3 class="text-xs font-bold text-slate-400 tracking-wider uppercase mb-4 pb-2 border-b border-slate-800">
              Active Members ({{ group.members.length }})
            </h3>
            
            <div class="space-y-3 overflow-y-auto pr-1 flex-1 scrollbar-thin">
              <div 
                v-for="member in group.members" 
                :key="member.id" 
                class="flex items-center justify-between"
              >
                <div class="flex items-center space-x-2.5">
                  <div class="relative">
                    <div class="w-7 h-7 bg-slate-950 text-slate-300 border border-slate-800 rounded-full flex items-center justify-center font-bold text-xs select-none">
                      {{ member.name.charAt(0) }}
                    </div>
                    <span 
                      class="w-2 h-2 rounded-full absolute -bottom-0.5 -right-0.5 border border-slate-900"
                      :class="member.is_online ? 'bg-emerald-400 animate-pulse' : 'bg-slate-600'"
                      :title="member.is_online ? 'Online' : 'Offline'"
                    ></span>
                  </div>
                  <div>
                    <span class="block text-xs font-bold text-slate-200 truncate max-w-[120px]">{{ member.name }}</span>
                    <span class="text-[9px] text-slate-500 font-mono" v-if="member.id === group.owner_id">Group Founder</span>
                    <span class="text-[9px] text-slate-500 font-mono" v-else>Member</span>
                  </div>
                </div>
                <!-- Status badge -->
                <span 
                  class="text-[9px] font-mono px-1.5 py-0.2 rounded uppercase"
                  :class="member.is_online ? 'text-emerald-400 bg-emerald-500/10' : 'text-slate-500 bg-slate-800/40'"
                >
                  {{ member.is_online ? 'Online' : 'Offline' }}
                </span>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
