<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="theme-light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Pipnomics') }} - @yield('title', 'Authentication')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-100 antialiased bg-slate-950 min-h-screen selection:bg-emerald-500 selection:text-slate-950">
    <div class="flex min-h-screen flex-col items-center justify-center pt-6 sm:pt-0 bg-slate-950 px-4 py-8 relative overflow-hidden">
        <!-- Subtle Ambient Glow -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="mb-6 z-10 flex justify-center">
            <a href="/" class="flex items-center group">
                <img src="{{ asset('assets/logo.png') }}" alt="Pipnomics" class="h-12 w-auto object-contain group-hover:scale-105 transition-transform" />
            </a>
        </div>

        <div class="w-full sm:max-w-md bg-slate-900 border border-slate-800 shadow-2xl px-6 py-8 sm:rounded-2xl z-10">
            @yield('content')
        </div>

        <!-- Global Toast Container -->
        <div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col space-y-2.5 max-w-sm pointer-events-none"></div>
    </div>

    @stack('scripts')
</body>
</html>
