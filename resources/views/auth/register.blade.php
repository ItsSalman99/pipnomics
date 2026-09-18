@extends('layouts.guest')

@section('title', 'Create Account')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold tracking-tight text-white text-center">Create Your Account</h2>
        <p class="text-xs text-slate-400 text-center mt-1">Join Pipnomics to participate in financial discussions and access real-time data.</p>
    </div>

    <form id="auth-register-form" onsubmit="window.submitAuthRegister(event)" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Full Name</label>
            <input 
                id="register-name" 
                type="text" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                autofocus 
                autocomplete="name" 
                placeholder="e.g. Alex Morgan"
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
            <p id="register-name-error" class="text-rose-400 text-xs mt-1.5 hidden"></p>
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Email Address</label>
            <input 
                id="register-email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autocomplete="username" 
                placeholder="name@company.com"
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
            <p id="register-email-error" class="text-rose-400 text-xs mt-1.5 hidden"></p>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Password</label>
            <input 
                id="register-password" 
                type="password" 
                name="password" 
                required 
                autocomplete="new-password" 
                placeholder="••••••••"
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
            <p id="register-password-error" class="text-rose-400 text-xs mt-1.5 hidden"></p>
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Confirm Password</label>
            <input 
                id="register-password-confirmation" 
                type="password" 
                name="password_confirmation" 
                required 
                autocomplete="new-password" 
                placeholder="••••••••"
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
        </div>

        <div class="pt-2">
            <button 
                id="register-submit-btn"
                type="submit" 
                class="w-full bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 font-bold py-2.5 rounded-xl text-sm transition shadow-lg shadow-emerald-500/10 flex items-center justify-center space-x-2 hover:scale-[1.01]"
            >
                <span>Complete Registration</span>
            </button>
        </div>
    </form>

    <div class="pt-4 border-t border-slate-800 text-center">
        <p class="text-xs text-slate-400">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-emerald-400 font-bold hover:underline">Sign in</a>
        </p>
    </div>
</div>
@endsection
