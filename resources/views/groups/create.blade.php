@extends('layouts.app')

@section('title', 'Create Community Forum')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold tracking-tight text-white flex items-center space-x-2">
            <span>Create Community Forum</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">Establish a specialized trading hub for specific currency pairs, commodities, or automated strategies.</p>
    </div>
    <a href="{{ route('groups.index') }}" class="text-xs text-slate-400 hover:text-white transition flex items-center space-x-1">
        <span>&larr;</span>
        <span>Back to Communities</span>
    </a>
</div>
@endsection

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl">
        <form id="create-group-form" onsubmit="window.submitCreateGroup(event)" class="space-y-6">
            @csrf

            <!-- Group Name -->
            <div>
                <label for="group-name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Forum Name <span class="text-rose-400">*</span>
                </label>
                <input 
                    type="text" 
                    id="group-name" 
                    name="name" 
                    required 
                    placeholder="e.g., Gold & Silver Swing Traders, EUR/USD Price Action" 
                    class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl px-4 py-2.5 focus:outline-none focus:border-emerald-500/50"
                />
                <p id="group-name-error" class="text-rose-400 text-xs mt-1.5 hidden"></p>
            </div>

            <!-- Description -->
            <div>
                <label for="group-description" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Description & Rules
                </label>
                <textarea 
                    id="group-description" 
                    name="description" 
                    rows="4" 
                    placeholder="Describe the objective, guidelines, indicators used, and what members should expect from this trading forum..."
                    class="w-full bg-slate-950 text-sm text-slate-100 border border-slate-800 rounded-xl p-4 focus:outline-none focus:border-emerald-500/50 resize-none leading-relaxed"
                ></textarea>
                <p id="group-description-error" class="text-rose-400 text-xs mt-1.5 hidden"></p>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                <span class="text-xs text-slate-500 font-mono">
                    <span class="text-amber-400 font-bold">PRO</span> Creator Feature
                </span>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('groups.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-400 hover:text-white transition">
                        Cancel
                    </a>
                    <button 
                        id="create-group-submit-btn"
                        type="submit" 
                        class="bg-gradient-to-r from-emerald-500 to-cyan-500 hover:from-emerald-400 hover:to-cyan-400 text-slate-950 font-bold px-6 py-2.5 rounded-xl text-xs transition duration-200 shadow-md shadow-emerald-500/10 hover:scale-[1.02] flex items-center space-x-2"
                    >
                        <span>Launch Forum</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
