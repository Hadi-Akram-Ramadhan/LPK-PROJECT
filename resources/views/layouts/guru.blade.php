<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LPK CBT') }} - Guru</title>

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
        .side-brand { display: flex; align-items: center; padding: 28px 20px 12px; }
        .side-brand-logo { width: 42px; height: 42px; object-fit: contain; margin-right: 12px; flex-shrink: 0; }
        .side-brand-text h1 { font-size: 16px; font-weight: 800; color: #fff; line-height: 1.2; letter-spacing: -0.2px; }
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

        /* Mobile Responsive Navigation */
        .sidebar-overlay { 
            display: block; position: fixed; inset: 0; background: rgba(0,0,0,0.5); 
            z-index: 40; opacity: 0; transition: opacity 0.3s; pointer-events: none; 
        }
        .sidebar-overlay.open { opacity: 1; pointer-events: auto; }
        #mobile-menu-btn { 
            display: none; background: transparent; border: none; 
            cursor: pointer; color: #1e293b; padding: 4px; margin-right: 12px; 
            flex-shrink: 0;
        }

        /* Hide header logo on desktop because sidebar already has it */
        @media (min-width: 1025px) {
            .header-brand-section { display: none !important; }
        }

        @media (max-width: 1024px) {
            .adm-side { 
                transform: translateX(-100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
                width: 260px; box-shadow: 4px 0 15px rgba(0,0,0,0.1); 
            }
            .adm-side.open { transform: translateX(0); }
            .adm-main { margin-left: 0; width: 100%; transition: margin-left 0.3s; }
            #mobile-menu-btn { display: block; }
            .adm-header { padding: 0 16px; }
            .adm-content { padding: 20px 16px; }
            
            .adm-header-title { font-size: 15px; }
            .adm-header-sub { font-size: 11px; }
        }
        @media (max-width: 640px) {
            .adm-header-clock { display: none; }
            .adm-header { height: 60px; }
            .header-brand-text { display: none; }
        }
        @media screen and (max-height: 500px) and (orientation: landscape) {
            .adm-side { transform: translateX(-100%); transition: transform 0.3s; width: 260px; z-index: 50; }
            .adm-side.open { transform: translateX(0); }
            .adm-main { margin-left: 0; width: 100%; }
            #mobile-menu-btn { display: block; }
            .adm-header { height: 50px; padding: 0 15px; }
            .adm-content { padding: 15px; }
        }
    </style>
    @yield('extra-css')
</head>
<body>
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <div class="adm-wrap">
        <aside class="adm-side">
            <div class="side-brand">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="side-brand-logo" style="width: 42px; height: 42px; object-fit: contain; margin-right: 12px; flex-shrink: 0;">
                <div class="side-brand-text">
                    <h1 style="font-size: 16px; font-weight: 800; color: #fff; line-height: 1.2; letter-spacing: -0.2px;">LPK URISOWON</h1>
                    <p>Panel Guru</p>
                </div>
            </div>

            <div class="side-profile">
                <div class="side-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                <div>
                    <div class="side-profile-name">{{ Auth::user()->name }}</div>
                    <div class="side-profile-role">Guru</div>
                </div>
            </div>

            <nav class="side-nav">
                <a href="{{ route('guru.dashboard') }}" class="side-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('guru.paket-soal.index') }}" class="side-link {{ request()->is('guru/paket-soal*', 'guru/soal*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Bank Soal
                </a>
                <a href="{{ route('guru.ujian.index') }}" class="side-link {{ request()->routeIs('guru.ujian.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"/></svg>
                    Manajemen Ujian
                </a>

                <a href="{{ route('guru.monitor.index') }}" class="side-link {{ request()->routeIs('guru.monitor.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Monitor Ujian
                </a>

                <div class="side-section-label">Media & Keamanan</div>

                <a href="{{ route('guru.audio.index') }}" class="side-link {{ request()->routeIs('guru.audio.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                    Audio Explorer
                </a>
                <a href="{{ route('guru.image.index') }}" class="side-link {{ request()->routeIs('guru.image.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Image Explorer
                </a>
                <a href="{{ route('guru.cheat-logs.index') }}" class="side-link {{ request()->routeIs('guru.cheat-logs.*') ? 'active' : '' }}">
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
                <div style="display: flex; align-items: center; gap: 16px;">
                    <button id="mobile-menu-btn">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div style="display: flex; align-items: center; gap: 10px; padding-right: 16px; border-right: 1px solid #e2e8f0;" class="header-brand-section">
                        <img src="{{ asset('logo.png') }}" alt="Logo" style="width: 32px; height: 32px; object-fit: contain;">
                        <span class="header-brand-text" style="font-size: 14px; font-weight: 800; color: #1e293b; letter-spacing: -0.3px;">LPK URISOWON</span>
                    </div>
                    <div>
                        <div class="adm-header-title">@yield('header', 'Dashboard')</div>
                        <div class="adm-header-sub">@yield('header-sub', 'Panel Guru')</div>
                    </div>
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
    
    <!-- SweetAlert2 Global Interceptor -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Intercept all forms that use inline onsubmit="return confirm(...)"
            const forms = document.querySelectorAll('form[onsubmit*="confirm"]');
            forms.forEach(form => {
                let onsubmitStr = form.getAttribute('onsubmit');
                let match = onsubmitStr.match(/confirm\(\s*['"](.*?)['"]\s*\)/);
                let message = match ? match[1] : "Apakah Anda yakin ingin melanjutkan?";
                
                form.removeAttribute('onsubmit');
                
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#0ea5e9',
                        cancelButtonColor: '#ef4444',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // 2. Override native window.alert globally for cleaner look
            window.alert = function(msg) {
                Swal.fire({
                    title: 'Informasi',
                    text: msg,
                    icon: 'info',
                    confirmButtonColor: '#0ea5e9',
                    confirmButtonText: 'Tutup'
                });
            };
        });
    </script>
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var menuBtn = document.getElementById('mobile-menu-btn');
            var overlay = document.getElementById('sidebar-overlay');
            var sidebar = document.querySelector('.adm-side');
            
            if(menuBtn && overlay && sidebar) {
                menuBtn.addEventListener('click', function() {
                    sidebar.classList.add('open');
                    overlay.classList.add('open');
                });
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('open');
                });
            }
        });
    </script>
    @yield('extra-js')
</body>
</html>
