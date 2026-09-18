<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean, },
    canRegister: { type: Boolean, },
});
</script>

<template>
    <Head title="Pipnomics - Financial Market Hub" />
    <div class="min-h-screen bg-slate-900 text-slate-100 font-sans selection:bg-emerald-500 selection:text-white">
        <!-- Ticker Tape -->
        <div class="w-full bg-slate-950 border-b border-slate-800 overflow-hidden py-2">
            <div class="flex space-x-8 animate-pulse text-sm font-mono whitespace-nowrap px-4">
                <span class="text-emerald-400">EUR/USD 1.0942 (+0.12%)</span>
                <span class="text-red-400">GBP/USD 1.2631 (-0.05%)</span>
                <span class="text-emerald-400">USD/JPY 149.20 (+0.34%)</span>
                <span class="text-emerald-400">BTC/USD 64,230 (+1.20%)</span>
                <span class="text-red-400">GOLD 2,340.50 (-0.10%)</span>
                <span class="text-emerald-400">S&P 500 5,120.25 (+0.80%)</span>
            </div>
        </div>

        <!-- Navbar -->
        <nav class="w-full px-6 py-4 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center">
                <img src="/assets/logo.png" alt="Pipnomics" class="h-9 w-auto object-contain rounded" />
            </div>
            <div v-if="canLogin" class="space-x-4 font-semibold text-sm">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-slate-300 hover:text-white">Dashboard</Link>
                <template v-else>
                    <Link :href="route('login')" class="text-slate-300 hover:text-white transition">Log in</Link>
                    <Link v-if="canRegister" :href="route('register')" class="bg-emerald-500 text-slate-900 px-4 py-2 rounded hover:bg-emerald-400 transition shadow-lg shadow-emerald-500/20">Get Started</Link>
                </template>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="max-w-7xl mx-auto px-6 py-24 flex flex-col items-center text-center">
            <h1 class="text-5xl md:text-7xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-cyan-400 tracking-tight mb-6">
                Master the Markets
            </h1>
            <p class="text-lg md:text-xl text-slate-400 max-w-2xl mb-10">
                Join a community of elite traders. Get real-time financial news, track the economic calendar, and form premium trading groups to stay ahead of the curve.
            </p>
            <div class="flex space-x-4">
                <Link :href="route('register')" class="bg-emerald-500 text-slate-900 px-8 py-3 rounded-lg font-bold text-lg hover:bg-emerald-400 transition shadow-lg shadow-emerald-500/20">Start Trading Now</Link>
                <a href="#features" class="bg-slate-800 text-white border border-slate-700 px-8 py-3 rounded-lg font-bold text-lg hover:bg-slate-700 transition">Explore Features</a>
            </div>
        </main>

        <!-- Features Section -->
        <section id="features" class="bg-slate-950 py-24">
            <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="p-8 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/50 transition">
                    <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Live Market News</h3>
                    <p class="text-slate-400">Stay informed with up-to-the-minute breaking news from global financial markets affecting your portfolio.</p>
                </div>
                <!-- Feature 2 -->
                <div class="p-8 rounded-2xl bg-slate-900 border border-slate-800 hover:border-cyan-500/50 transition">
                    <div class="w-12 h-12 bg-cyan-500/10 text-cyan-400 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Economic Calendar</h3>
                    <p class="text-slate-400">Track high-impact macroeconomic events, interest rate decisions, and crucial data releases in real-time.</p>
                </div>
                <!-- Feature 3 -->
                <div class="p-8 rounded-2xl bg-slate-900 border border-slate-800 hover:border-purple-500/50 transition">
                    <div class="w-12 h-12 bg-purple-500/10 text-purple-400 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-white">Pro Trading Groups</h3>
                    <p class="text-slate-400">Create or join exclusive trading circles. Premium members can host groups and share private strategies.</p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-slate-800 py-8 text-center text-slate-500 text-sm">
            &copy; 2026 Pipnomics. All rights reserved. Data provided for informational purposes.
        </footer>
    </div>
</template>
