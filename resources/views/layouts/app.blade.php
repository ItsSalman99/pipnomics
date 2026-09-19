<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="theme-light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pipnomics') }} - @yield('title', 'Trading Hub')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-100 min-h-screen selection:bg-emerald-500 selection:text-slate-950">
    <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col">
        <!-- Main Navigation Bar -->
        <nav class="border-b border-slate-800 bg-slate-900 sticky top-0 z-40">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <div class="flex shrink-0 items-center">
                            <a href="{{ route('home') }}" class="flex items-center group">
                                <img src="{{ asset('storage/logo.png') }}" alt="Pipnomics" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform" />
                            </a>
                        </div>

                        <!-- Desktop Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <a href="{{ route('news.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('news.*') ? 'border-emerald-500 text-white font-semibold' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700' }}">
                                Market News
                            </a>
                            <a href="{{ route('calendar.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('calendar.*') ? 'border-emerald-500 text-white font-semibold' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700' }}">
                                Calendar
                            </a>
                            <a href="{{ route('groups.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('groups.*') ? 'border-emerald-500 text-white font-semibold' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700' }}">
                                Community
                            </a>
                            <a href="{{ route('school.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out {{ request()->routeIs('school.*') ? 'border-emerald-500 text-white font-semibold' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700' }}">
                                School
                            </a>
                        </div>
                    </div>

                    <div class="hidden sm:ms-6 sm:flex sm:items-center space-x-4">
                        <!-- Settings Dropdown / Guest Auth Links -->
                        @auth
                            <div class="relative" id="user-dropdown-container">
                                <button 
                                    id="user-dropdown-btn" 
                                    type="button"
                                    class="inline-flex items-center rounded-lg border border-slate-800 bg-slate-900 px-3 py-2 text-sm font-medium leading-4 text-slate-300 transition duration-150 ease-in-out hover:text-white hover:bg-slate-800 focus:outline-none"
                                >
                                    <span 
                                        id="auth-user-status-dot"
                                        class="w-2 h-2 rounded-full mr-2 transition-all {{ Auth::user()->is_online ? 'bg-emerald-400 shadow-sm shadow-emerald-400/80 animate-pulse' : 'bg-slate-500' }}"
                                        title="{{ Auth::user()->is_online ? 'Status: Online' : 'Status: Offline' }}"
                                    ></span>
                                    <span>{{ Auth::user()->name }}</span>
                                    <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div 
                                    id="user-dropdown-menu" 
                                    class="hidden absolute right-0 mt-2 w-48 rounded-xl shadow-2xl bg-slate-900 border border-slate-800 py-1.5 z-50 transition-all"
                                >
                                    <div class="px-4 py-2 border-b border-slate-800">
                                        <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                                        <p class="text-[11px] text-slate-400 font-mono truncate">{{ Auth::user()->email }}</p>
                                    </div>
                                    <a href="{{ route('profile.edit') }}" class="block w-full px-4 py-2 text-left text-xs font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition">
                                        Profile Settings
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" id="logout-form" class="w-full">
                                        @csrf
                                        <button type="submit" class="block w-full px-4 py-2 text-left text-xs font-medium text-rose-400 hover:bg-slate-800 hover:text-rose-300 transition">
                                            Log Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="space-x-3 flex items-center">
                                <a href="{{ route('login') }}" class="text-sm font-medium text-slate-400 hover:text-slate-100 transition px-2 py-1">
                                    Log in
                                </a>
                                <a href="{{ route('register') }}" class="text-xs font-bold bg-emerald-500 hover:bg-emerald-400 text-slate-950 px-3.5 py-2 rounded-lg transition shadow-md shadow-emerald-500/10">
                                    Get Started
                                </a>
                            </div>
                        @endauth
                    </div>

                    <!-- Mobile Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            id="mobile-menu-btn"
                            type="button"
                            class="inline-flex items-center justify-center rounded-md p-2 text-slate-400 transition duration-150 ease-in-out hover:bg-slate-800 hover:text-slate-200 focus:outline-none"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path id="mobile-icon-bars" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path id="mobile-icon-close" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Drawer -->
            <div id="mobile-menu" class="hidden sm:hidden border-t border-slate-800 bg-slate-900 pb-3 pt-2">
                <div class="space-y-1 px-2">
                    <a href="{{ route('news.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('news.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        Market News
                    </a>
                    <a href="{{ route('calendar.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('calendar.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        Calendar
                    </a>
                    <a href="{{ route('groups.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('groups.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        Community
                    </a>
                    <a href="{{ route('school.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('school.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        School
                    </a>
                </div>

                @auth
                    <div class="border-t border-slate-800 pb-1 pt-4 mt-2 px-4 bg-slate-900/60">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full {{ Auth::user()->is_online ? 'bg-emerald-400 shadow-sm shadow-emerald-400/80 animate-pulse' : 'bg-slate-500' }}"></span>
                            <span class="text-base font-medium text-white">{{ Auth::user()->name }}</span>
                            <span class="text-[10px] uppercase font-mono px-1.5 py-0.5 rounded {{ Auth::user()->is_online ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                                {{ Auth::user()->is_online ? 'Online' : 'Offline' }}
                            </span>
                        </div>
                        <div class="text-xs font-mono text-slate-400 mt-0.5">{{ Auth::user()->email }}</div>

                        <div class="mt-3 space-y-1">
                            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-sm font-medium text-rose-400 hover:bg-slate-800">Log Out</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="border-t border-slate-800 pb-3 pt-4 px-4 bg-slate-900 space-y-2 mt-2">
                        <a href="{{ route('login') }}" class="block w-full text-center py-2 px-4 rounded-lg bg-slate-800 text-slate-300 text-sm font-semibold hover:bg-slate-700 transition">Log in</a>
                        <a href="{{ route('register') }}" class="block w-full text-center py-2 px-4 rounded-lg bg-emerald-500 text-slate-950 text-sm font-bold hover:bg-emerald-400 transition">Get Started</a>
                    </div>
                @endauth
            </div>
        </nav>

        <!-- Page Header Slot -->
        @hasSection('header')
            <header class="bg-slate-900 border-b border-slate-800 shadow-md shadow-black/10">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif

        <!-- Main Content Slot -->
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- Application Footer -->
        <footer class="border-t border-slate-800/80 bg-slate-900 mt-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <!-- Brand Column -->
                    <div class="space-y-3 md:col-span-1">
                        <a href="{{ route('home') }}" class="inline-block group">
                            <img src="{{ asset('storage/logo.png') }}" alt="Pipnomics" class="h-9 w-auto object-contain group-hover:scale-105 transition-transform" />
                        </a>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Real-time macroeconomic intelligence, global session telemetry, and interactive trader community forums.
                        </p>
                        <div class="flex items-center space-x-2 text-[11px] text-emerald-400 font-medium pt-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Live Market Telemetry Active</span>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-200 uppercase tracking-wider mb-3">Platform</h4>
                        <ul class="space-y-2 text-xs text-slate-400">
                            <li><a href="{{ route('news.index') }}" class="hover:text-emerald-400 transition">Market News Feed</a></li>
                            <li><a href="{{ route('calendar.index') }}" class="hover:text-emerald-400 transition">Economic Calendar</a></li>
                            <li><a href="{{ route('groups.index') }}" class="hover:text-emerald-400 transition">Community Forums</a></li>
                            <li><a href="{{ route('school.index') }}" class="hover:text-emerald-400 transition">Trading School</a></li>
                            <li><a href="{{ route('dashboard') }}" class="hover:text-emerald-400 transition">Trading Dashboard</a></li>
                        </ul>
                    </div>

                    <!-- Account & Community -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-200 uppercase tracking-wider mb-3">Community</h4>
                        <ul class="space-y-2 text-xs text-slate-400">
                            @auth
                                <li><a href="{{ route('profile.edit') }}" class="hover:text-emerald-400 transition">Profile Settings</a></li>
                                <li><a href="{{ route('groups.create') }}" class="hover:text-emerald-400 transition">Start a Community Group</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition">Sign In</a></li>
                                <li><a href="{{ route('register') }}" class="hover:text-emerald-400 transition">Create Free Account</a></li>
                                <li><a href="{{ route('groups.index') }}" class="hover:text-emerald-400 transition">Explore Public Discussions</a></li>
                            @endauth
                        </ul>
                    </div>

                    <!-- Markets & Timezones -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-200 uppercase tracking-wider mb-3">Global Hub</h4>
                        <div class="space-y-2 text-xs text-slate-400">
                            <div class="flex justify-between items-center py-0.5 border-b border-slate-800/60">
                                <span>Sydney / Tokyo</span>
                                <span class="font-mono text-[10px] text-slate-500">22:00 - 08:00 UTC</span>
                            </div>
                            <div class="flex justify-between items-center py-0.5 border-b border-slate-800/60">
                                <span>London / Europe</span>
                                <span class="font-mono text-[10px] text-slate-500">08:00 - 17:00 UTC</span>
                            </div>
                            <div class="flex justify-between items-center py-0.5 border-b border-slate-800/60">
                                <span>New York / Americas</span>
                                <span class="font-mono text-[10px] text-slate-500">13:00 - 22:00 UTC</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar & Risk Disclaimer -->
                <div class="pt-6 border-t border-slate-800/80 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-slate-500">
                    <p class="text-[11px] text-center md:text-left leading-relaxed max-w-3xl">
                        &copy; {{ date('Y') }} Pipnomics. All rights reserved. Market data, headlines, and discussion signals are provided for informational and educational purposes only.
                    </p>
                    <div class="flex items-center space-x-4 shrink-0 text-[11px]">
                        <span class="inline-flex items-center space-x-1.5 bg-slate-950 px-2.5 py-1 rounded-full border border-slate-800 text-slate-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>All Systems Operational</span>
                        </span>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Global Toast Notification Container -->
        <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col space-y-2.5 max-w-sm pointer-events-none"></div>

        <!-- Global Guest Auth Modal (Used across all pages when unauthenticated users try interactive features) -->
        <div id="guest-auth-modal" class="fixed inset-0 z-50 hidden bg-slate-950/85 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-slate-900 border border-slate-800 w-full max-w-sm rounded-2xl overflow-hidden shadow-2xl p-6 text-center space-y-4">
                <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <div>
                    <h4 class="text-md font-bold text-white">Authentication Required</h4>
                    <p id="guest-auth-modal-action-text" class="text-xs text-slate-400 mt-2">
                        You need to log in to participate in the community features.
                    </p>
                </div>
                <div class="flex flex-col space-y-2 pt-2 text-xs">
                    <a href="{{ route('login') }}" class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold py-2 rounded-lg text-center transition">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="w-full bg-slate-800 hover:bg-slate-750 text-slate-300 font-bold py-2 rounded-lg text-center border border-slate-700 transition">
                        Register
                    </a>
                    <button type="button" onclick="window.closeGuestAuthModal()" class="w-full text-slate-500 hover:text-slate-400 py-1 transition">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
