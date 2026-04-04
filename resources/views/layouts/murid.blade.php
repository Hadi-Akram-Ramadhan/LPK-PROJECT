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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f8fafc; color: #1e293b; line-height: 1.5; font-size: 15px; }
        .wrapper { min-height: 100vh; display: flex; flex-direction: column; }

        .murid-nav { background: #111827; height: 70px; display: flex; align-items: center; padding: 0 40px; justify-content: space-between; position: sticky; top: 0; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .nav-left { display: flex; align-items: center; gap: 14px; font-size: 19px; font-weight: 800; color: #fff; letter-spacing: -0.5px; }
        .logo-box { background: #2563eb; color: #fff; width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 900; }

        .nav-right { display: flex; align-items: center; gap: 24px; }
        .user-pill { background: #1f2937; border: 1.5px solid #374151; padding: 10px 20px; border-radius: 40px; display: flex; align-items: center; gap: 12px; color: #fff; font-size: 14px; font-weight: 600; }
        .user-pill svg { width: 18px; height: 18px; color: #94a3b8; flex-shrink: 0; }

        .logout-btn { color: #cbd5e1; font-size: 14px; font-weight: 700; text-decoration: none; border: none; background: transparent; cursor: pointer; transition: 0.2s; padding: 8px 12px; }
        .logout-btn:hover { color: #ef4444; }

        .main-content { flex: 1; width: 100%; max-width: 1200px; margin: 0 auto; padding: 40px 24px; box-sizing: border-box; }

        footer { background: #fff; border-top: 1px solid #e2e8f0; padding: 30px 40px; margin-top: auto; }
        .footer-inner { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: #64748b; }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Top Navbar -->
        <nav class="murid-nav">
            <div class="nav-left">
                <div class="logo-box">L</div>
                LPK CBT System
            </div>
            <div class="nav-right">
                <div class="user-pill">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    {{ Auth::user()->name }}
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer>
            <div class="footer-inner">
                <p>&copy; {{ date('Y') }} LPK CBT System. All rights reserved.</p>
                <p>Support: admin@lpk.test</p>
            </div>
        </footer>
    </div>
</body>
</html>
