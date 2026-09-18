@extends('layouts.guest')

@section('title', 'Set New Password')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold tracking-tight text-white text-center">Set New Password</h2>
        <p class="text-xs text-slate-400 text-center mt-1">Please enter your new password to complete the account recovery.</p>
    </div>

    <form id="reset-password-form" onsubmit="window.submitResetPassword(event)" class="space-y-4">
        @csrf
        <input type="hidden" name="token" id="reset-token" value="{{ $token }}">

        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Email Address</label>
            <input 
                id="reset-email" 
                type="email" 
                name="email" 
                value="{{ old('email', $email) }}" 
                required 
                autofocus 
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
            <p id="reset-email-error" class="text-rose-400 text-xs mt-1.5 hidden"></p>
        </div>

        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">New Password</label>
            <input 
                id="reset-password" 
                type="password" 
                name="password" 
                required 
                autocomplete="new-password" 
                placeholder="••••••••"
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
            <p id="reset-password-error" class="text-rose-400 text-xs mt-1.5 hidden"></p>
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Confirm Password</label>
            <input 
                id="reset-password-confirmation" 
                type="password" 
                name="password_confirmation" 
                required 
                autocomplete="new-password" 
                placeholder="••••••••"
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
        </div>

        <div>
            <button 
                id="reset-submit-btn"
                type="submit" 
                class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold py-2.5 rounded-xl text-sm transition shadow-lg shadow-emerald-500/10"
            >
                Reset Password
            </button>
        </div>
    </form>
</div>
@endsection
