<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LPK CBT') }} - Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', sans-serif; background: #f0f4f8; color: #334155; font-size: 15px; }
        .adm-wrap { display: flex; min-height: 100vh; }

        .adm-side {
            width: 260px; background: #111827; display: flex; flex-direction: column;
            flex-shrink: 0; position: fixed; top: 0; left: 0; bottom: 0; z-index: 50; overflow-y: auto;
        }
        .side-brand { display: flex; align-items: center; padding: 28px 24px 12px; }
        .side-brand-icon { width: 38px; height: 38px; border-radius: 10px; background: #1e293b; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; }
        .side-brand-icon svg { width: 20px; height: 20px; color: #fff; }
        .side-brand-text h1 { font-size: 16px; font-weight: 700; color: #fff; line-height: 1.2; }
        .side-brand-text p { font-size: 10px; color: #60a5fa; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; margin-top: 2px; }

        .side-profile { display: flex; align-items: center; padding: 20px 24px; margin-bottom: 6px; }
        .side-avatar { width: 42px; height: 42px; border-radius: 50%; background: #3b82f6; color: #fff; font-weight: 700; font-size: 17px; display: flex; align-items: center; justify-content: center; margin-right: 12px; flex-shrink: 0; }
        .side-profile-name { color: #fff; font-size: 13px; font-weight: 600; }
        .side-profile-role { color: #94a3b8; font-size: 11px; margin-top: 2px; }

        .side-nav { flex: 1; padding: 0 14px; }
        .side-link { display: flex; align-items: center; padding: 11px 14px; margin-bottom: 3px; color: #94a3b8; text-decoration: none; border-radius: 10px; font-size: 13.5px; font-weight: 500; transition: background 0.15s, color 0.15s; }
        .side-link:hover { background: #1e293b; color: #e2e8f0; }
        .side-link.active { background: #2563eb; color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,0.35); }
        .side-link svg { width: 20px; height: 20px; margin-right: 14px; flex-shrink: 0; }
        .side-link .live-dot { margin-left: auto; background: #ef4444; color: #fff; font-size: 10px; padding: 2px 8px; border-radius: 10px; font-weight: 700; }

        .side-section-label { padding: 18px 14px 6px; font-size: 10px; font-weight: 700; color: #475569; letter-spacing: 1.5px; text-transform: uppercase; }

        .side-logout { padding: 20px 14px; margin-top: auto; }
        .side-logout-btn { display: flex; align-items: center; width: 100%; padding: 11px 14px; background: transparent; border: none; color: #f87171; border-radius: 10px; font-size: 13.5px; font-weight: 500; cursor: pointer; transition: background 0.15s; font-family: 'Poppins', sans-serif; }
        .side-logout-btn:hover { background: rgba(248,113,113,0.1); }
        .side-logout-btn svg { width: 20px; height: 20px; margin-right: 14px; }

        .adm-main { flex: 1; display: flex; flex-direction: column; margin-left: 260px; min-height: 100vh; }
        .adm-header { height: 72px; background: #fff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 32px; flex-shrink: 0; }
        .adm-header-title { font-size: 17px; font-weight: 700; color: #1e293b; }
        .adm-header-sub { font-size: 12px; color: #94a3b8; margin-top: 3px; }
        .adm-header-clock { display: flex; align-items: center; color: #94a3b8; font-size: 13px; font-weight: 500; }
        .adm-header-clock svg { width: 16px; height: 16px; margin-right: 6px; }
        .adm-header-clock .divider { margin: 0 10px; color: #cbd5e1; }
        .adm-content { padding: 28px 32px; flex: 1; }

        @media (max-width: 768px) {
            .adm-side { width: 220px; }
            .adm-main { margin-left: 220px; }
            .adm-content { padding: 20px 16px; }
        }

        /* ===== SHARED UTILITY CLASSES (backward compat) ===== */
        .card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .flex-between { display: flex; justify-content: space-between; align-items: center; }
        .badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-green { background: #dcfce7; color: #16a34a; }
        .badge-red { background: #fee2e2; color: #dc2626; }
        .badge-blue { background: #dbeafe; color: #2563eb; }
        .badge-yellow { background: #fef9c3; color: #a16207; }
        .badge-gray { background: #f1f5f9; color: #64748b; }

        .btn { display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; transition: 0.15s; font-family: 'Poppins', sans-serif; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-green { background: #16a34a; color: #fff; }
        .btn-green:hover { background: #15803d; }
        .btn-outline { background: #fff; color: #2563eb; border: 1.5px solid #2563eb; }
        .btn-outline:hover { background: #eff6ff; }
        .btn-danger { background: #fff; color: #dc2626; border: 1.5px solid #dc2626; }
        .btn-danger:hover { background: #fef2f2; }
        .btn svg { width: 16px; height: 16px; margin-right: 6px; }

        .tbl { width: 100%; border-collapse: collapse; }
        .tbl th { text-align: left; padding: 14px 16px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
        .tbl td { padding: 16px; font-size: 14px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .tbl tr:last-child td { border-bottom: none; }
        .tbl tr:hover td { background: #f8fafc; }

        .search-box { position: relative; }
        .search-box input { width: 100%; padding: 10px 14px 10px 40px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13px; outline: none; background: #fff; font-family: 'Poppins', sans-serif; transition: border 0.15s; }
        .search-box input:focus { border-color: #3b82f6; }
        .search-box svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #94a3b8; }

        .icon-circle { width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .icon-circle svg { width: 22px; height: 22px; }

        @media (max-width: 1024px) { .grid-4 { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .grid-4, .grid-2 { grid-template-columns: 1fr; } }
    </style>
    @yield('extra-css')
</head>
<body>
    <div class="adm-wrap">
        <aside class="adm-side">
            <div class="side-brand">
                <div class="side-brand-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                </div>
                <div class="side-brand-text">
                    <h1>LPK URISOWON</h1>
                    <p>Dashboard Admin</p>
                </div>
            </div>

            <div class="side-profile">
                <div class="side-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                <div>
                    <div class="side-profile-name">{{ Auth::user()->name }}</div>
                    <div class="side-profile-role">Administrator</div>
                </div>
            </div>

            <nav class="side-nav">
                <a href="{{ route('admin.dashboard') }}" class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.users.index') }}" class="side-link {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Manajemen User
                </a>
                <a href="{{ route('admin.kelas.index') }}" class="side-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Kelas & Jurusan
                </a>
                <a href="{{ route('admin.soal.index') }}" class="side-link {{ request()->routeIs('admin.soal.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Bank Soal
                </a>
                <a href="{{ route('admin.ujian.index') }}" class="side-link {{ request()->routeIs('admin.ujian.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"/></svg>
                    Kelola Ujian
                </a>
                <a href="{{ route('admin.monitor.index') }}" class="side-link {{ request()->routeIs('admin.monitor.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Nilai & Hasil
                </a>

                <div class="side-section-label">Media & Keamanan</div>

                <a href="{{ route('admin.audio.index') }}" class="side-link {{ request()->routeIs('admin.audio.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                    Audio Explorer
                </a>
                <a href="{{ route('admin.cheat-logs.index') }}" class="side-link {{ request()->routeIs('admin.cheat-logs.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Log Kecurangan
                    <span class="live-dot">Live</span>
                </a>
            </nav>

            <div class="side-logout">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="side-logout-btn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="adm-main">
            <header class="adm-header">
                <div>
                    <div class="adm-header-title">@yield('header', 'Dashboard')</div>
                    <div class="adm-header-sub">@yield('header-sub', 'Panel Admin')</div>
                </div>
                <div class="adm-header-clock">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span id="adm-time">00:00</span>
                    <span class="divider">|</span>
                    <span id="adm-date">01 Jan 2026</span>
                </div>
            </header>
            <div class="adm-content">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        !function(){
            function tick(){
                var n=new Date(),h=String(n.getHours()).padStart(2,'0'),m=String(n.getMinutes()).padStart(2,'0');
                document.getElementById('adm-time').textContent=h+':'+m;
                var ms=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                document.getElementById('adm-date').textContent=String(n.getDate()).padStart(2,'0')+' '+ms[n.getMonth()]+' '+n.getFullYear();
            }
            tick();setInterval(tick,1000);
        }();
    </script>
    @yield('extra-js')
</body>
</html>
