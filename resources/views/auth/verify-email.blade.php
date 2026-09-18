@extends('layouts.guest')

@section('title', 'Verify Email')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold tracking-tight text-white text-center">Verify Your Email</h2>
        <p class="text-xs text-slate-400 text-center mt-2 leading-relaxed">
            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs text-emerald-400 font-medium">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="flex items-center justify-between pt-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold py-2 px-4 rounded-xl text-xs transition shadow-md shadow-emerald-500/10">
                Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-xs text-slate-400 hover:text-white underline">
                Log Out
            </button>
        </form>
    </div>
</div>
@endsection
