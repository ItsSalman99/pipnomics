@extends('layouts.app')

@section('title', 'Trading School & Academy')

@section('content')
<div class="min-h-[calc(100vh-16rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-2xl">

        <!-- Form Card Container -->
        <div id="school-card" class="bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-6 sm:p-10 relative overflow-hidden">
            <!-- Subtle glow background -->
            <div class="absolute -top-20 -right-20 w-60 h-60 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 -left-20 w-60 h-60 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Header Section -->
            <div class="text-center mb-8 relative z-10">
                <div class="inline-flex items-center space-x-2 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Pipnomics Academy</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                    Join Our Trading School
                </h1>
                <p class="text-xs sm:text-sm text-slate-400 mt-2 max-w-lg mx-auto leading-relaxed">
                    Master financial markets, macroeconomic data, price action strategies, and disciplined risk management with institutional traders.
                </p>
            </div>

            <!-- Success Alert Banner (Shown after AJAX submission) -->
            <div id="school-success-message" class="hidden p-6 bg-emerald-950/80 border border-emerald-500/40 rounded-xl text-center space-y-3 relative z-10 animate-fade-in">
                <div class="w-12 h-12 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="text-base font-bold text-white">Application Submitted Successfully!</h3>
                <p id="school-success-detail" class="text-xs text-slate-300 leading-relaxed max-w-md mx-auto">
                    Thank you for applying. One of our senior trading instructors will reach out to you via email and phone/WhatsApp within 24 hours.
                </p>
                <div class="pt-3">
                    <button 
                        type="button" 
                        onclick="window.resetSchoolForm()" 
                        class="text-xs text-emerald-400 hover:text-emerald-300 underline font-medium"
                    >
                        Submit Another Application
                    </button>
                </div>
            </div>

            <!-- Registration Form -->
            <form id="school-registration-form" onsubmit="window.submitSchoolForm(event)" class="space-y-5 relative z-10">
                @csrf

                <!-- Name & Email Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="school-name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Full Name <span class="text-rose-400">*</span>
                        </label>
                        <input 
                            id="school-name" 
                            name="name" 
                            type="text" 
                            value="{{ Auth::user()->name ?? old('name') }}" 
                            required 
                            placeholder="e.g. John Doe"
                            class="w-full bg-slate-950 text-xs text-slate-100 border border-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-500/60 transition"
                        />
                    </div>

                    <div>
                        <label for="school-email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Email Address <span class="text-rose-400">*</span>
                        </label>
                        <input 
                            id="school-email" 
                            name="email" 
                            type="email" 
                            value="{{ Auth::user()->email ?? old('email') }}" 
                            required 
                            placeholder="e.g. trader@example.com"
                            class="w-full bg-slate-950 text-xs text-slate-100 border border-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-500/60 transition"
                        />
                    </div>
                </div>

                <!-- Phone / WhatsApp -->
                <div>
                    <label for="school-phone" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Phone / WhatsApp Number <span class="text-rose-400">*</span>
                    </label>
                    <input 
                        id="school-phone" 
                        name="phone" 
                        type="text" 
                        required 
                        placeholder="e.g. +1 (555) 019-2834 or +44 7700 900077"
                        class="w-full bg-slate-950 text-xs text-slate-100 border border-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-500/60 transition"
                    />
                </div>

                <!-- Experience Level -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Trading Experience Level <span class="text-rose-400">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="relative flex flex-col p-3.5 bg-slate-950 border border-slate-800 rounded-xl cursor-pointer hover:border-emerald-500/40 transition">
                            <input type="radio" name="experience_level" value="beginner" checked class="peer sr-only">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-200 peer-checked:text-emerald-400">Beginner</span>
                                <span class="w-3.5 h-3.5 rounded-full border border-slate-600 peer-checked:border-emerald-400 peer-checked:bg-emerald-500"></span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1">Starting from zero basics</span>
                        </label>

                        <label class="relative flex flex-col p-3.5 bg-slate-950 border border-slate-800 rounded-xl cursor-pointer hover:border-emerald-500/40 transition">
                            <input type="radio" name="experience_level" value="intermediate" class="peer sr-only">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-200 peer-checked:text-emerald-400">Intermediate</span>
                                <span class="w-3.5 h-3.5 rounded-full border border-slate-600 peer-checked:border-emerald-400 peer-checked:bg-emerald-500"></span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1">1-2 years market experience</span>
                        </label>

                        <label class="relative flex flex-col p-3.5 bg-slate-950 border border-slate-800 rounded-xl cursor-pointer hover:border-emerald-500/40 transition">
                            <input type="radio" name="experience_level" value="advanced" class="peer sr-only">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-200 peer-checked:text-emerald-400">Advanced</span>
                                <span class="w-3.5 h-3.5 rounded-full border border-slate-600 peer-checked:border-emerald-400 peer-checked:bg-emerald-500"></span>
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1">Refining institutional edge</span>
                        </label>
                    </div>
                </div>

                <!-- Course Track & Preferred Schedule Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="school-track" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Preferred Course Track <span class="text-rose-400">*</span>
                        </label>
                        <select 
                            id="school-track" 
                            name="preferred_track" 
                            required
                            class="w-full bg-slate-950 text-xs text-slate-100 border border-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-500/60 transition"
                        >
                            <option value="technical_analysis">Price Action & Technical Analysis</option>
                            <option value="macro_fundamentals">Macroeconomics & Fundamental News</option>
                            <option value="forex_commodities">Forex & Commodities Mastery</option>
                            <option value="crypto_risk">Crypto Markets & Risk Systems</option>
                        </select>
                    </div>

                    <div>
                        <label for="school-schedule" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Preferred Schedule <span class="text-rose-400">*</span>
                        </label>
                        <select 
                            id="school-schedule" 
                            name="schedule" 
                            required
                            class="w-full bg-slate-950 text-xs text-slate-100 border border-slate-800 rounded-xl px-4 py-3 focus:outline-none focus:border-emerald-500/60 transition"
                        >
                            <option value="weekends">Weekend Live Batches (Sat - Sun)</option>
                            <option value="weekday_evenings">Weekday Evenings (Mon - Thu)</option>
                            <option value="mentorship">1-on-1 Personalized Mentorship</option>
                        </select>
                    </div>
                </div>

                <!-- Learning Goals / Questions -->
                <div>
                    <label for="school-goals" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Your Trading Goals / Topics of Interest <span class="text-slate-500 text-[10px] normal-case">(Optional)</span>
                    </label>
                    <textarea 
                        id="school-goals" 
                        name="goals" 
                        rows="3" 
                        placeholder="Tell us what you want to achieve or specific pairs/instruments you focus on..."
                        class="w-full bg-slate-950 text-xs text-slate-100 border border-slate-800 rounded-xl p-4 focus:outline-none focus:border-emerald-500/60 transition resize-none leading-relaxed"
                    ></textarea>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button 
                        id="school-submit-btn" 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 font-extrabold text-sm py-3.5 px-6 rounded-xl transition duration-200 flex items-center justify-center space-x-2 shadow-lg shadow-emerald-500/10 hover:scale-[1.01]"
                    >
                        <span id="school-submit-text">Enroll & Join Classes</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>

                <p class="text-[11px] text-slate-500 text-center">
                    By submitting, you agree to receive curriculum details and admission schedules from Pipnomics Academy.
                </p>
            </form>

        </div>
    </div>
</div>
@endsection
