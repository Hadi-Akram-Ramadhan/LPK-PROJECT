<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>UBT Learning LPK URISOWON - Siswa</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    <!-- Open Graph / Meta Social Media -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="LPK URISOWON - Pelatihan Bahasa Korea">
    <meta property="og:description" content="Lembaga Pelatihan Kerja URISOWON Bangkalan. Pusat Pelatihan Bahasa Korea & Persiapan Ujian EPS-TOPIK Terpercaya di Madura.">
    <meta property="og:image" content="{{ asset('og-banner.png') }}">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="LPK URISOWON - Pelatihan Bahasa Korea">
    <meta property="twitter:description" content="LPK URISOWON Bangkalan. Pusat Pelatihan Bahasa Korea & Persiapan Ujian EPS-TOPIK Terpercaya.">
    <meta property="twitter:image" content="{{ asset('og-banner.png') }}">


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f8fafc; color: #1e293b; line-height: 1.5; font-size: 15px; }
        .wrapper { min-height: 100vh; display: flex; flex-direction: column; }

        .murid-nav { background: #111827; height: 74px; display: flex; align-items: center; padding: 0 40px; justify-content: space-between; position: sticky; top: 0; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .nav-left { display: flex; align-items: center; gap: 12px; color: #fff; text-decoration: none; }
        .nav-logo-img { width: 42px; height: 42px; object-fit: contain; }
        .nav-brand-text { font-size: 18px; font-weight: 800; letter-spacing: -0.3px; color: #fff; }

        .nav-right { display: flex; align-items: center; gap: 24px; }
        .user-pill { background: #1f2937; border: 1.5px solid #374151; padding: 10px 20px; border-radius: 40px; display: flex; align-items: center; gap: 12px; color: #fff; font-size: 14px; font-weight: 600; }
        .user-pill svg { width: 18px; height: 18px; color: #94a3b8; flex-shrink: 0; }

        .logout-btn { color: #cbd5e1; font-size: 14px; font-weight: 700; text-decoration: none; border: none; background: transparent; cursor: pointer; transition: 0.2s; padding: 8px 12px; white-space: nowrap; }
        .logout-btn:hover { color: #ef4444; }

        @media (max-width: 640px) {
            .murid-nav { padding: 0 12px; height: 60px; }
            .nav-brand-text { display: none; } /* Sembunyikan teks di mobile sesuai request */
            .nav-logo-img { width: 34px; height: 34px; }
            .nav-right { gap: 8px; }
            .user-pill { padding: 6px 10px; font-size: 11px; gap: 6px; border-radius: 30px; }
            .user-pill svg { width: 14px; height: 14px; }
            .user-pill span { display: inline; } /* Pastikan nama tetap muncul */
            .logout-btn { font-size: 11px; padding: 4px 6px; }
        }

        .main-content { flex: 1; width: 100%; max-width: 1200px; margin: 0 auto; padding: 40px 24px; box-sizing: border-box; }

        footer { background: #fff; border-top: 1px solid #e2e8f0; padding: 30px 40px; margin-top: auto; }
        .footer-inner { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: #64748b; }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Top Navbar -->
        <nav class="murid-nav">
            <a href="{{ route('murid.dashboard') }}" class="nav-left">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="nav-logo-img">
                <span class="nav-brand-text">LPK URISOWON</span>
            </a>
            <div class="nav-right">
                <div class="user-pill">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>{{ Auth::user()->name }}</span>
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
                <p>&copy; {{ date('Y') }} LPK URISOWON. All rights reserved.</p>
                <p>Support: admin@urisowon.test</p>
            </div>
        </footer>
    </div>
    
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
                        confirmButtonColor: '#3b82f6',
                        cancelButtonColor: '#ef4444',
                        confirmButtonText: 'Ya, Lanjut',
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
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText: 'Tutup'
                });
            };
        });
    </script>
</body>
</html>
