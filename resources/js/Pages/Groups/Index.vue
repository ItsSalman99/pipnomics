<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({ groups: Array });
</script>

<template>
  <Head title="Community Forums" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div>
          <h2 class="text-2xl font-bold tracking-tight text-white flex items-center space-x-2">
            <span>Community Forums</span>
            <span class="text-xs bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 px-2 py-0.5 rounded-full font-semibold">Interactive</span>
          </h2>
          <p class="text-xs text-slate-400 mt-1">Discuss setups, post news analysis, and collaborate with other traders worldwide.</p>
        </div>
        <Link 
          v-if="$page.props.auth.user && $page.props.auth.user.is_premium" 
          :href="route('groups.create')" 
          class="bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 px-4 py-2 rounded-lg font-bold text-xs transition duration-200 border border-emerald-400 shadow-md shadow-emerald-500/5 hover:scale-[1.02]"
        >
          Create New Group
        </Link>
      </div>
    </template>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div 
          v-for="group in groups" 
          :key="group.id" 
          class="bg-slate-900 border border-slate-800/80 rounded-xl p-5 shadow-lg flex flex-col justify-between hover:border-slate-700/80 transition-all duration-200 group"
        >
          <div>
            <h3 class="text-base font-extrabold text-slate-100 mb-2 leading-snug group-hover:text-cyan-400 transition-colors">
              {{ group.name }}
            </h3>
            <p class="text-xs text-slate-400 mb-4 line-clamp-3 leading-relaxed">
              {{ group.description || 'No description provided.' }}
            </p>
          </div>
          
          <div class="flex justify-between items-center border-t border-slate-950/30 pt-4 mt-2">
            <span class="text-[10px] font-mono text-slate-500 flex items-center space-x-1.5">
              <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
              <span>{{ group.members_count }} Traders</span>
            </span>
            <Link 
              :href="route('groups.show', group.id)" 
              class="text-cyan-400 hover:text-cyan-300 font-bold text-xs flex items-center space-x-1"
            >
              <span>Enter Forum</span>
              <span>&rarr;</span>
            </Link>
          </div>
        </div>

        <div v-if="groups.length === 0" class="col-span-3 bg-slate-900 border border-slate-800/80 rounded-xl p-12 text-center text-slate-400 shadow-lg">
          <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20H22V18C22 15.7909 20.2091 14 18 14H16M17 20H7M17 20V18C17 16.9248 16.5772 15.9482 15.8906 15.2285M2 18C2 15.7909 3.79086 14 6 14H14C16.2091 14 18 15.7909 18 18V20H2M14 7C14 9.20914 12.2091 11 10 11C7.79086 11 6 9.20914 6 7C6 4.79086 7.79086 3 10 3C12.2091 3 14 4.79086 14 7Z"></path></svg>
          <h4 class="text-base font-bold text-white mb-1">No forums found</h4>
          <p class="text-xs text-slate-550 max-w-md mx-auto">Click "Create New Group" in the header to start a new discussion community forum.</p>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
