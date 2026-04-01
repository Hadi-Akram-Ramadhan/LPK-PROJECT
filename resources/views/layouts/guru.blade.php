<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LPK CBT') }} - Guru</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-slate-800">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-slate-800 text-white shadow-lg md:min-h-screen border-r border-slate-700">
            <div class="h-16 flex items-center justify-center border-b border-slate-700">
                <h1 class="text-xl font-bold tracking-wider text-accent-400">GURU PANEL</h1>
            </div>
            <nav class="p-4 space-y-2">
                <a href="{{ route('guru.dashboard') }}" class="block px-4 py-2 rounded transition-colors {{ request()->routeIs('guru.dashboard') ? 'bg-accent-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">Dashboard</a>
                
                <a href="{{ route('guru.soal.index') }}" class="block px-4 py-2 rounded transition-colors {{ request()->routeIs('guru.soal.*') ? 'bg-accent-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">Bank Soal</a>
                
                <a href="{{ route('guru.ujian.index') }}" class="block px-4 py-2 rounded transition-colors {{ request()->routeIs('guru.ujian.*') ? 'bg-accent-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">Manajemen Ujian</a>
                
                <a href="{{ route('guru.import.index') }}" class="block px-4 py-2 rounded transition-colors {{ request()->routeIs('guru.import.*') ? 'bg-accent-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">Import Excel</a>

                <a href="{{ route('guru.monitor.index') }}" class="block px-4 py-2 rounded transition-colors flex justify-between items-center {{ request()->routeIs('guru.monitor.*') ? 'bg-accent-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <span>Monitor Ujian</span>
                    <span class="bg-red-500 text-xs px-2 py-1 rounded-full text-white">Live</span>
                </a>

                {{-- ── Pembatas Seksi ── --}}
                <div class="pt-3 pb-1">
                    <p class="px-4 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Media & Keamanan</p>
                </div>

                <a href="{{ route('guru.audio.index') }}" class="flex items-center px-4 py-2 rounded transition-colors {{ request()->routeIs('guru.audio.*') ? 'bg-accent-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="mr-2 h-4 w-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                    </svg>
                    Audio Explorer
                </a>

                <a href="{{ route('guru.cheat-logs.index') }}" class="flex items-center px-4 py-2 rounded transition-colors {{ request()->routeIs('guru.cheat-logs.*') ? 'bg-accent-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                    <svg class="mr-2 h-4 w-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Log Kecurangan
                </a>

                <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-slate-700 mt-4">
                    @csrf
                    <button type="submit" class="w-full text-left block px-4 py-2 text-red-400 hover:bg-slate-700 rounded transition-colors">Logout</button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto w-full">
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 border-b border-slate-200">
                <div class="font-semibold text-xl text-slate-800">
                    @yield('header', 'Guru Dashboard')
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-slate-600">{{ Auth::user()->name }}</span>
                    <div class="h-8 w-8 rounded-full bg-accent-100 text-accent-700 flex items-center justify-center font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </header>
            <div class="p-6 animate-fade-in">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
