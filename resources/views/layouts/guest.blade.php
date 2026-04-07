<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'LPK Urisowon') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; font-size: 15px; }
    </style>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #fff;
            overflow: hidden;
        }

        /* ── Portrait Warning Overlay ── */
        #portrait-warning {
            display: none;
            position: fixed;
            inset: 0;
            background: #0d1b3e;
            z-index: 9999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-align: center;
            padding: 32px;
        }
        #portrait-warning.show { display: flex; }
        #portrait-warning svg {
            width: 72px;
            height: 72px;
            margin-bottom: 24px;
            color: #3b82f6;
            animation: rotate-hint 2s ease-in-out infinite;
        }
        @keyframes rotate-hint {
            0%, 100% { transform: rotate(0deg); }
            40% { transform: rotate(90deg); }
            60% { transform: rotate(90deg); }
        }
        #portrait-warning h2 { font-size: 22px; font-weight: 700; margin-bottom: 10px; }
        #portrait-warning p { font-size: 14px; color: #a0aec0; max-width: 280px; line-height: 1.6; }

        /* ── Two-Column Login Layout ── */
        .login-wrapper {
            display: flex;
            width: 100vw;
            height: 100vh;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            width: 42%;
            background: #0d1b3e;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 36px 44px;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            top: -120px;
            right: -120px;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: rgba(59,130,246,0.07);
        }
        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: rgba(59,130,246,0.05);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-logo .icon {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .brand-logo .icon svg { color: #fff; width: 22px; height: 22px; }
        .brand-logo span { font-size: 17px; font-weight: 700; color: #fff; letter-spacing: -0.3px; }

        .left-content { position: relative; z-index: 1; }
        .left-content h1 {
            font-size: clamp(28px, 3.5vw, 42px);
            font-weight: 900;
            color: #fff;
            line-height: 1.15;
            letter-spacing: -1px;
            margin-bottom: 18px;
        }
        .left-content h1 .highlight { color: #4590df; }
        .left-content p {
            font-size: 14px;
            color: #8ba3c7;
            line-height: 1.75;
            max-width: 320px;
        }

        .feature-card {
            margin-top: 36px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 20px 22px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }
        .feature-card .fc-icon {
            width: 44px;
            height: 44px;
            background: rgba(69,144,223,0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .feature-card .fc-icon svg { color: #4590df; width: 22px; height: 22px; }
        .feature-card .fc-text h4 { font-size: 15px; font-weight: 700; color: #fff; margin-bottom: 5px; }
        .feature-card .fc-text p { font-size: 13px; color: #8ba3c7; line-height: 1.6; }

        .left-footer { font-size: 12px; color: #4a607f; position: relative; z-index: 1; }

        /* ── RIGHT PANEL ── */
        .right-panel {
            width: 58%;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 48px;
            position: relative;
        }

        .login-box {
            width: 100%;
            max-width: 440px;
        }

        .login-box h2 {
            font-size: 28px;
            font-weight: 800;
            color: #0d1b3e;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .login-box .subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 32px;
        }
        .login-box .subtitle span { color: #4590df; font-weight: 500; }

        /* Form Fields */
        .field-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .field-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .field-row a {
            font-size: 12px;
            color: #4590df;
            text-decoration: none;
            font-weight: 500;
        }
        .field-row a:hover { text-decoration: underline; }

        .input-wrapper {
            position: relative;
            margin-bottom: 20px;
        }
        .input-wrapper .icon-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            width: 18px;
            height: 18px;
        }
        .input-wrapper .icon-right {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            width: 18px;
            height: 18px;
            cursor: pointer;
            background: none;
            border: none;
            display: flex;
            align-items: center;
            padding: 0;
        }
        .input-wrapper input {
            width: 100%;
            padding: 13px 46px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            color: #1e293b;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #fff;
        }
        .input-wrapper input:focus {
            border-color: #4590df;
            box-shadow: 0 0 0 3px rgba(69,144,223,0.12);
        }
        .input-wrapper input::placeholder { color: #94a3b8; }

        /* Remember */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }
        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #0d1b3e;
            cursor: pointer;
        }
        .remember-row label {
            font-size: 13px;
            color: #475569;
            cursor: pointer;
        }

        /* Submit Button */
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #0d1b3e;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.2px;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
        }
        .btn-login:hover {
            background: #162a5e;
            box-shadow: 0 4px 16px rgba(13,27,62,0.25);
        }
        .btn-login:active { transform: scale(0.98); }

        /* Contact Admin */
        .contact-row {
            text-align: center;
            margin-top: 22px;
            font-size: 13.5px;
            color: #64748b;
        }
        .contact-row a {
            color: #4590df;
            font-weight: 600;
            text-decoration: none;
        }
        .contact-row a:hover { text-decoration: underline; }

        /* Error */
        .error-msg {
            font-size: 12px;
            color: #ef4444;
            margin-top: -14px;
            margin-bottom: 14px;
        }

        /* Alert */
        .alert-status {
            background: #ecfdf5;
            border: 1px solid #6ee7b7;
            color: #047857;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        /* Help Icon */
        .help-btn {
            position: absolute;
            bottom: 24px;
            right: 24px;
            width: 36px;
            height: 36px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            cursor: pointer;
            border: none;
            font-size: 16px;
            font-weight: 700;
            transition: background 0.2s;
        }
        .help-btn:hover { background: #e2e8f0; }

        /* ── Tablet: narrow left panel ── */
        @media (max-width: 900px) {
            .left-panel { width: 38%; padding: 28px 30px; }
            .right-panel { width: 62%; padding: 30px 36px; }
        }
    </style>
</head>
<body>

    <!-- Portrait Warning Overlay -->
    <div id="portrait-warning">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M12 18.5v.5m0-14v8m-4-9h8a2 2 0 012 2v11a2 2 0 01-2 2H8a2 2 0 01-2-2V6a2 2 0 012-2z"/>
        </svg>
        <h2>Putar Perangkat Anda</h2>
        <p>Aplikasi ini dirancang untuk tampilan <strong>landscape</strong>. Silakan putar perangkat Anda secara horizontal untuk pengalaman terbaik.</p>
    </div>

    <div class="login-wrapper">

        <!-- ======== LEFT PANEL ======== -->
        <div class="left-panel">
            <!-- Brand -->
            <div class="brand-logo">
                <div class="icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </div>
                <span>LPK Urisowon</span>
            </div>

            <!-- Hero Text -->
            <div class="left-content">
                <h1>Raih Masa Depan<br><span class="highlight">Karir Profesional</span> &amp;<br>Kompeten.</h1>
                <p>Sistem ujian online terintegrasi untuk mengukur kompetensi, pantau hasil belajar, dan dapatkan sertifikasi resmi LPK Urisowon dalam satu platform modern.</p>

                <div class="feature-card">
                    <div class="fc-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="fc-text">
                        <h4>Keamanan Terjamin</h4>
                        <p>Sistem anti-cheat &amp; data peserta terenkripsi dengan standar keamanan tinggi.</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="left-footer">© {{ date('Y') }} LPK Urisowon. All rights reserved.</div>
        </div>

        <!-- ======== RIGHT PANEL ======== -->
        <div class="right-panel">
            <div class="login-box">
                <h2>Selamat Datang</h2>
                <p class="subtitle">Silakan masuk ke akun <span>admin</span> Anda.</p>

                <!-- Session Status -->
                @if(session('status'))
                    <div class="alert-status">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <label class="field-label">Email Address</label>
                    <div class="input-wrapper">
                        <svg class="icon-left" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <input type="email" name="email" id="email"
                            value="{{ old('email') }}"
                            placeholder="admin@cbt.com"
                            required autofocus autocomplete="username">
                    </div>
                    @error('email')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror

                    <!-- Password -->
                    <div class="field-row">
                        <label class="field-label" style="margin-bottom:0">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Lupa Password?</a>
                        @endif
                    </div>
                    <div class="input-wrapper">
                        <svg class="icon-left" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input type="password" name="password" id="password"
                            placeholder="••••••••"
                            required autocomplete="current-password">
                        <button type="button" class="icon-right" onclick="togglePassword()" id="eyeBtn">
                            <svg id="eyeIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror

                    <!-- Remember -->
                    <div class="remember-row">
                        <input id="remember_me" type="checkbox" name="remember">
                        <label for="remember_me">Ingat saya di perangkat ini</label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-login">Masuk Sekarang</button>
                </form>

                <div class="contact-row">
                    Belum punya akun? <a href="mailto:admin@lpk-urisowon.com">Hubungi Admin</a>
                </div>
            </div>

            <button class="help-btn" title="Bantuan">?</button>
        </div>
    </div>

    <script>
        // ── Toggle Password Visibility ──
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>`;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            }
        }

        // ── Landscape Enforcement ──
        function checkOrientation() {
            const warning = document.getElementById('portrait-warning');
            // Only enforce on touch/mobile devices
            const isMobile = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            if (isMobile && window.innerHeight > window.innerWidth) {
                warning.classList.add('show');
            } else {
                warning.classList.remove('show');
            }
        }

        window.addEventListener('resize', checkOrientation);
        window.addEventListener('orientationchange', checkOrientation);
        document.addEventListener('DOMContentLoaded', checkOrientation);
    </script>
</body>
</html>
