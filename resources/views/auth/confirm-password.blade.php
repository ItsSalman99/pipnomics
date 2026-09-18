@extends('layouts.guest')

@section('title', 'Confirm Password')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold tracking-tight text-white text-center">Security Check</h2>
        <p class="text-xs text-slate-400 text-center mt-1 leading-relaxed">
            This is a secure area of the application. Please confirm your password before continuing.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Password</label>
            <input 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="current-password" 
                placeholder="••••••••"
                class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
            />
            @if ($errors->has('password'))
                <p class="text-rose-400 text-xs mt-1.5">{{ $errors->first('password') }}</p>
            @endif
        </div>

        <div>
            <button 
                type="submit" 
                class="w-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold py-2.5 rounded-xl text-sm transition shadow-lg shadow-emerald-500/10"
            >
                Confirm
            </button>
        </div>
    </form>
</div>
@endsection
