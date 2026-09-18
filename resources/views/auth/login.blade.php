@extends('layouts.guest')

@section('title', 'Sign In')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold tracking-tight text-white text-center">Welcome Back</h2>
        <p class="text-xs text-slate-400 text-center mt-1">Sign in to your Pipnomics account to access live feeds and market forums.</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs text-emerald-400 font-medium">
            {{ session('status') }}
        </div>
    @endif

    <form id="auth-login-form" onsubmit="window.submitAuthLogin(event)" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Email</label>
            <input 
                id="login-email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus 
                autocomplete="username" 
                placeholder="name@company.com"
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
            <p id="login-email-error" class="text-rose-400 text-xs mt-1.5 hidden"></p>
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-emerald-400 hover:underline">Forgot password?</a>
                @endif
            </div>
            <input 
                id="login-password" 
                type="password" 
                name="password" 
                required 
                autocomplete="current-password" 
                placeholder="••••••••"
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
            <p id="login-password-error" class="text-rose-400 text-xs mt-1.5 hidden"></p>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input 
                    type="checkbox" 
                    name="remember" 
                    id="remember_me" 
                    class="rounded bg-slate-950 border-slate-800 text-emerald-500 focus:ring-0 w-4 h-4"
                />
                <span class="text-xs text-slate-400">Remember me</span>
            </label>
        </div>

        <div>
            <button 
                id="login-submit-btn"
                type="submit" 
                class="w-full bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 font-bold py-2.5 rounded-xl text-sm transition shadow-lg shadow-emerald-500/10 flex items-center justify-center space-x-2 hover:scale-[1.01]"
            >
                <span>Sign In</span>
            </button>
        </div>
    </form>

    <div class="pt-4 border-t border-slate-800 text-center">
        <p class="text-xs text-slate-400">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-emerald-400 font-bold hover:underline">Get started</a>
        </p>
    </div>
</div>
@endsection
