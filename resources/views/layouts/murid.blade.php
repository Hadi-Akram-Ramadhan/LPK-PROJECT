<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LPK CBT') }} - Murid</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800">
    <div class="min-h-screen flex flex-col">
        <!-- Top Navbar -->
        <nav class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-accent-500">
                                LPK CBT
                            </span>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('dashboard') }}" class="border-primary-500 text-slate-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Dashboard
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex items-center space-x-3">
                            <span class="text-sm font-medium text-slate-600">{{ Auth::user()->name }}</span>
                            <div class="h-8 w-8 rounded-full bg-gradient-to-r from-primary-400 to-accent-400 text-white flex items-center justify-center font-bold shadow-inner">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <form method="POST" action="{{ route('logout') }}" class="ml-4">
                                @csrf
                                <button type="submit" class="text-sm text-slate-500 hover:text-red-500 transition-colors">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 w-full max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8 animate-slide-up">
            @yield('content')
        </main>
        
        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 mt-auto py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center text-sm text-slate-500">
                <p>&copy; {{ date('Y') }} LPK CBT System. All rights reserved.</p>
                <p>Support: admin@lpk.test</p>
            </div>
        </footer>
    </div>
</body>
</html>
