@extends('layouts.app')

@section('title', 'Account Profile & Security')

@section('header')
<div class="flex items-center justify-between">
    <div>
        <h2 class="text-xl font-extrabold tracking-tight text-white flex items-center space-x-2">
            <span>Account & Security Settings</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">Manage profile credentials, presence status, and review security activities.</p>
    </div>
</div>
@endsection

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
        <!-- User Presence & Telemetry Card -->
        <div class="bg-slate-900 border border-slate-800 p-6 shadow-xl sm:rounded-2xl">
            @include('profile.partials.user-status-card')
        </div>

        <!-- User Activity History & Audit Logs -->
        <div class="bg-slate-900 border border-slate-800 p-6 shadow-xl sm:rounded-2xl">
            @include('profile.partials.user-activity-log', ['activities' => $activities])
        </div>

        <!-- Profile Information -->
        <div class="bg-slate-900 border border-slate-800 p-6 shadow-xl sm:rounded-2xl">
            <div class="max-w-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password -->
        <div class="bg-slate-900 border border-slate-800 p-6 shadow-xl sm:rounded-2xl">
            <div class="max-w-2xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete Account -->
        <div class="bg-slate-900 border border-slate-800 p-6 shadow-xl sm:rounded-2xl">
            <div class="max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
