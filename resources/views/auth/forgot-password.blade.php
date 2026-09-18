@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold tracking-tight text-white text-center">Reset Password</h2>
        <p class="text-xs text-slate-400 text-center mt-1 leading-relaxed">
            Forgot your password? Enter your email address and we will email you a password reset link.
        </p>
    </div>

    @if (session('status'))
        <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs text-emerald-400 font-medium">
            {{ session('status') }}
        </div>
    @endif

    <form id="forgot-password-form" onsubmit="window.submitForgotPassword(event)" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Email Address</label>
            <input 
                id="forgot-email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus 
                placeholder="name@company.com"
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
            <p id="forgot-email-error" class="text-rose-400 text-xs mt-1.5 hidden"></p>
        </div>

        <div>
            <button 
                id="forgot-submit-btn"
                type="submit" 
                class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold py-2.5 rounded-xl text-sm transition shadow-lg shadow-emerald-500/10"
            >
                Email Password Reset Link
            </button>
        </div>
    </form>

    <div class="pt-4 border-t border-slate-800 text-center">
        <a href="{{ route('login') }}" class="text-xs text-slate-400 hover:text-white transition">&larr; Back to sign in</a>
    </div>
</div>
@endsection
