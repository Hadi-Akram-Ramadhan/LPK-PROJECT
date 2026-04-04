<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'LPK Urisowon') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #fff;
            overflow: hidden;
        }

        /* ─── SPLIT LAYOUT ─── */
        .login-wrapper {
            display: flex;
            width: 100vw;
            height: 100vh;
        }

        /* ─── LEFT DARK PANEL ─── */
        .left-panel {
            width: 50%;
            background: #0d1b3e; /* Very dark blue */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 60px 80px; /* Increased to match exact padding in image */
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            top: -130px;
            right: -100px;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: rgba(69,144,223,0.07);
            pointer-events: none;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -100px;
            left: -100px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(69,144,223,0.05);
            pointer-events: none;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px; /* Gap between logo and text */
            position: relative;
            z-index: 1;
        }
        .brand-logo {
            height: 48px; /* Smaller logo */
            width: auto;
            max-width: 100%;
            object-fit: contain;
            border-radius: 8px; /* Add slight rounding */
        }
        .brand-name { 
            font-size: 19px; 
            font-weight: 800; 
            color: #fff; 
            letter-spacing: -0.2px; 
        }

        .hero { position: relative; z-index: 1; }
        .hero h1 {
            font-size: clamp(34px, 4vw, 48px); /* Much larger as seen in the image */
            font-weight: 900;
            color: #fff;
            line-height: 1.15;
            letter-spacing: -1px;
            margin-bottom: 24px;
        }
        .hero h1 .accent { color: #4590df; }
        .hero p {
            font-size: 16px; /* Matched to image size */
            color: #8ba3c7;
            line-height: 1.7;
            max-width: 480px; /* Extended to allow wide text wrapping */
        }

        .feature-box {
            margin-top: 40px; /* Pushed down a bit more */
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 14px;
            padding: 22px 24px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            width: 100%;
            max-width: 480px; /* Spans full width of paragraph block */
        }
        .fb-icon {
            width: 44px;
            height: 44px;
            background: rgba(69,144,223,0.15);
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .fb-icon svg { color: #4590df; width: 22px; height: 22px; }
        .fb-title { font-size: 14.5px; font-weight: 700; color: #fff; margin-bottom: 5px; }
        .fb-desc { font-size: 12.5px; color: #8ba3c7; line-height: 1.65; }

        .left-footer {
            font-size: 12px;
            color: #3d5070;
            position: relative;
            z-index: 1;
        }

        /* ─── RIGHT WHITE PANEL ─── */
        .right-panel {
            flex: 1;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 56px;
            position: relative;
        }

        .form-container {
            width: 100%;
            max-width: 440px;
        }

        .form-title {
            font-size: 32px;
            font-weight: 800;
            color: #0b1426;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }
        .form-subtitle {
            font-size: 15px;
            color: #64748b;
            margin-bottom: 40px;
        }
        .form-subtitle .accent { color: #4590df; font-weight: 500; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .label-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 8px;
        }
        .label-row .form-label { margin-bottom: 0; }
        .forgot-link {
            font-size: 12px;
            color: #4590df;
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-link:hover { text-decoration: underline; }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }
        .input-group .i-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
            width: 18px; height: 18px;
        }
        .input-group .i-right {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px; height: 20px;
        }
        .input-group input {
            width: 100%;
            padding: 13px 46px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            color: #0f172a;
            font-family: 'Inter', sans-serif;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-group input::placeholder { color: #c0cfe0; }
        .input-group input:focus {
            border-color: #4590df;
            box-shadow: 0 0 0 3px rgba(69,144,223,0.13);
        }

        .form-error {
            font-size: 12px;
            color: #ef4444;
            margin-top: -14px;
            margin-bottom: 14px;
        }

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
        .remember-row label { font-size: 13px; color: #475569; cursor: pointer; }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #0d1b3e;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
        }
        .btn-submit:hover { background: #162a5e; box-shadow: 0 6px 18px rgba(13,27,62,0.3); }
        .btn-submit:active { transform: scale(0.985); }

        .contact-text {
            text-align: center;
            margin-top: 22px;
            font-size: 13.5px;
            color: #64748b;
        }
        .contact-text a { color: #4590df; font-weight: 600; text-decoration: none; }
        .contact-text a:hover { text-decoration: underline; }

        .status-alert {
            background: #ecfdf5;
            border: 1px solid #6ee7b7;
            color: #047857;
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        /* ─── MOBILE RESPONSIVENESS ─── */
        .mobile-brand { display: none; }

        @media (max-width: 900px) {
            body { 
                background: #f8fafc; 
                overflow: auto !important; 
            }
            .login-wrapper {
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100vh;
                padding: 24px;
            }
            .left-panel { display: none; }
            .right-panel {
                flex: none;
                width: 100%;
                background: transparent;
                padding: 0;
            }
            .form-container {
                background: #fff;
                padding: 40px 32px;
                border-radius: 20px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.02);
                margin: 0 auto;
                width: 100%;
                max-width: 420px;
            }
            .mobile-brand {
                display: flex;
                flex-direction: row; /* side-by-side */
                align-items: center;
                justify-content: center;
                gap: 12px;
                margin-bottom: 28px;
            }
            .mobile-brand .brand-logo {
                height: 52px; /* Smaller mobile logo */
                width: auto;
                border-radius: 8px;
            }
            .mobile-brand .brand-name {
                font-size: 20px;
                font-weight: 800;
                color: #0b1426;
                letter-spacing: -0.3px;
            }
            .form-title {
                text-align: center;
                font-size: 24px;
                margin-bottom: 4px;
            }
            .form-subtitle {
                text-align: center;
                margin-bottom: 28px;
                font-size: 13.5px;
            }
        }
    </style>
</head>
<body>



    <div class="login-wrapper">

        <!-- ════ LEFT PANEL ════ -->
        <div class="left-panel">
            <div class="brand">
                <img src="{{ asset('logo.png') }}" alt="Logo LPK Urisowon" class="brand-logo">
                <span class="brand-name">LPK URISOWON</span>
            </div>

            <div class="hero">
                <h1>
                    Raih Masa Depan<br>
                    <span class="accent">Karir Profesional</span> &amp;<br>
                    Kompeten.
                </h1>
                <p>Sistem ujian online terintegrasi untuk mengukur kompetensi, pantau hasil belajar, dan dapatkan sertifikasi resmi LPK Urisowon dalam satu platform modern.</p>

                <div class="feature-box">
                    <div class="fb-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="fb-title">Keamanan Terjamin</div>
                        <div class="fb-desc">Sistem anti-cheat &amp; data peserta terenkripsi dengan standar keamanan tinggi.</div>
                    </div>
                </div>
            </div>

            <div class="left-footer">© {{ date('Y') }} LPK Urisowon. All rights reserved.</div>
        </div>

        <!-- ════ RIGHT PANEL ════ -->
        <div class="right-panel">
            <div class="form-container">

                <!-- Mobile Only Brand -->
                <div class="mobile-brand">
                    <img src="{{ asset('logo.png') }}" alt="Logo LPK Urisowon" class="brand-logo">
                    <span class="brand-name">LPK URISOWON</span>
                </div>

                <h1 class="form-title">Selamat Datang</h1>
                <p class="form-subtitle">Silakan masuk ke akun <span class="accent">peserta ujian</span> Anda.</p>

                @if(session('status'))
                    <div class="status-alert">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <label class="form-label" for="email">Email Address</label>
                    <div class="input-group">
                        <svg class="i-left" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <input type="email" id="email" name="email"
                            value="{{ old('email') }}"
                            placeholder="siswa@urisowon.com"
                            required autofocus autocomplete="username">
                    </div>
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror

                    <!-- Password -->
                    <div class="label-row">
                        <label class="form-label" for="password">Password</label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">Lupa Password?</a>
                        @endif
                    </div>
                    <div class="input-group">
                        <svg class="i-left" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input type="password" id="password" name="password"
                            placeholder="••••••••"
                            required autocomplete="current-password">
                        <button type="button" class="i-right" onclick="togglePassword()" aria-label="Toggle password">
                            <svg id="eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror

                    <!-- Remember -->
                    <div class="remember-row">
                        <input id="remember_me" type="checkbox" name="remember">
                        <label for="remember_me">Ingat saya di perangkat ini</label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-submit">Masuk Sekarang</button>
                </form>

                <p class="contact-text">
                    Belum punya akun? <a href="mailto:admin@lpk-urisowon.com">Hubungi Admin</a>
                </p>
            </div>

        </div>
    </div>

    <script>
        function togglePassword() {
            const pwd  = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7
                    a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242
                    M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>`;
            } else {
                pwd.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                        -1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            }
        }


    </script>
</body>
</html>
