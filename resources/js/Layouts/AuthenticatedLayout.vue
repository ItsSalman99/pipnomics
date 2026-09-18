<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const isDark = ref(true);

onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'light') {
        isDark.value = false;
        document.documentElement.classList.add('theme-light');
    } else {
        isDark.value = true;
        document.documentElement.classList.remove('theme-light');
    }
});

function toggleTheme() {
    isDark.value = !isDark.value;
    if (isDark.value) {
        localStorage.setItem('theme', 'dark');
        document.documentElement.classList.remove('theme-light');
    } else {
        localStorage.setItem('theme', 'light');
        document.documentElement.classList.add('theme-light');
    }
}
</script>

<template>
    <div>
        <div class="min-h-screen bg-slate-950 text-slate-100">
            <nav
                class="border-b border-slate-800 bg-slate-900"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('home')" class="flex items-center">
                                    <img src="/assets/logo.png" alt="Pipnomics" class="h-9 w-auto object-contain rounded" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('news.index')"
                                    :active="route().current('news.index')"
                                >
                                    Market News
                                </NavLink>
                                <NavLink
                                    :href="route('calendar.index')"
                                    :active="route().current('calendar.index')"
                                >
                                    Calendar
                                </NavLink>
                                <NavLink
                                    :href="route('groups.index')"
                                    :active="route().current('groups.index')"
                                >
                                    Community
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center space-x-4">
                            <!-- Theme Switcher Button -->
                            <button 
                                @click="toggleTheme" 
                                class="text-slate-400 hover:text-slate-200 p-2 rounded-lg hover:bg-slate-800 transition"
                                title="Toggle Dark/Light Mode"
                            >
                                <svg v-if="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            </button>

                            <!-- Settings Dropdown / Guest Auth Links -->
                            <div class="relative">
                                <Dropdown v-if="$page.props.auth.user" align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-slate-800 bg-slate-900 px-3 py-2 text-sm font-medium leading-4 text-slate-300 transition duration-150 ease-in-out hover:text-white hover:bg-slate-800 focus:outline-none"
                                            >
                                                <span 
                                                    class="w-2 h-2 rounded-full mr-2 transition-all"
                                                    :class="$page.props.auth.user.is_online ? 'bg-emerald-400 shadow-sm shadow-emerald-400/80 animate-pulse' : 'bg-slate-500'"
                                                    :title="$page.props.auth.user.is_online ? 'Status: Online' : 'Status: Offline'"
                                                ></span>
                                                <span>{{ $page.props.auth.user.name }}</span>

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                                <div v-else class="space-x-4 flex items-center">
                                    <Link 
                                        :href="route('login')" 
                                        class="text-sm font-medium text-slate-400 hover:text-slate-100 transition"
                                    >
                                        Log in
                                    </Link>
                                    <Link 
                                        :href="route('register')" 
                                        class="text-sm font-semibold bg-emerald-500 hover:bg-emerald-400 text-slate-950 px-3.5 py-1.5 rounded-lg transition shadow-md shadow-emerald-500/10"
                                    >
                                        Get Started
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-slate-400 transition duration-150 ease-in-out hover:bg-slate-800 hover:text-slate-200 focus:bg-slate-800 focus:text-slate-200 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <!-- Theme Toggle in Mobile -->
                        <button 
                            @click="toggleTheme" 
                            class="w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-400 hover:bg-slate-800 hover:text-white flex items-center space-x-2 transition border-b border-slate-800 mb-2 pb-3"
                        >
                            <svg v-if="isDark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"></path></svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            <span>{{ isDark ? 'Light Theme' : 'Dark Theme' }}</span>
                        </button>

                        <ResponsiveNavLink
                            :href="route('news.index')"
                            :active="route().current('news.index')"
                        >
                            Market News
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('calendar.index')"
                            :active="route().current('calendar.index')"
                        >
                            Calendar
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('groups.index')"
                            :active="route().current('groups.index')"
                        >
                            Community
                        </ResponsiveNavLink>
                    </div>

                    <div
                        v-if="$page.props.auth.user"
                        class="border-t border-slate-800 pb-1 pt-4 bg-slate-900"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-white flex items-center space-x-2"
                            >
                                <span 
                                    class="w-2 h-2 rounded-full transition-all"
                                    :class="$page.props.auth.user.is_online ? 'bg-emerald-400 shadow-sm shadow-emerald-400/80 animate-pulse' : 'bg-slate-500'"
                                ></span>
                                <span>{{ $page.props.auth.user.name }}</span>
                                <span 
                                    class="text-[10px] uppercase font-mono px-1.5 py-0.5 rounded"
                                    :class="$page.props.auth.user.is_online ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400 border border-slate-700'"
                                >
                                    {{ $page.props.auth.user.is_online ? 'Online' : 'Offline' }}
                                </span>
                            </div>
                            <div class="text-sm font-medium text-slate-400">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                    <div
                        v-else
                        class="border-t border-slate-800 pb-3 pt-4 px-4 bg-slate-900 space-y-2"
                    >
                        <ResponsiveNavLink :href="route('login')">
                            Log in
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('register')" class="bg-emerald-500 text-slate-950 font-bold hover:bg-emerald-400 text-center">
                            Get Started
                        </ResponsiveNavLink>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-slate-900 border-b border-slate-800 shadow-md shadow-black/10"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
