<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LPK CBT') }} - Admin</title>

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
        <aside class="w-full md:w-64 bg-surface-dark text-white shadow-lg md:min-h-screen border-r border-slate-700">
            <div class="h-16 flex items-center justify-center border-b border-slate-700">
                <h1 class="text-xl font-bold tracking-wider text-primary-400">ADMIN PANEL</h1>
            </div>
            <nav class="p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-primary-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Dashboard</a>
                
                <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 rounded transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-primary-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Manajemen Pengguna</a>
                
                <a href="{{ route('admin.kelas.index') }}" class="block px-4 py-2 rounded transition-colors {{ request()->routeIs('admin.kelas.*') ? 'bg-primary-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Kelola Kelas</a>
                
                <a href="{{ route('admin.exams.index') }}" class="block px-4 py-2 rounded transition-colors {{ request()->routeIs('admin.exams.*') ? 'bg-primary-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Monitor Ujian</a>

                <a href="{{ route('admin.audio.index') }}" class="block px-4 py-2 rounded transition-colors {{ request()->routeIs('admin.audio.*') ? 'bg-primary-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Audio Explorer</a>

                <a href="{{ route('admin.cheat-logs.index') }}" class="block px-4 py-2 rounded transition-colors flex justify-between items-center {{ request()->routeIs('admin.cheat-logs.*') ? 'bg-primary-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span>Anti-Cheat Logs</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="pt-4 border-t border-slate-700 mt-4">
                    @csrf
                    <button type="submit" class="w-full text-left block px-4 py-2 text-red-400 hover:bg-slate-800 rounded transition-colors">Logout</button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto w-full">
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6 border-b border-slate-200">
                <div class="font-semibold text-xl text-slate-800">
                    @yield('header', 'Admin Dashboard')
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm font-medium text-slate-600">{{ Auth::user()->name }}</span>
                    <div class="h-8 w-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-bold">
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
