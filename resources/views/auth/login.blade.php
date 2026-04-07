<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<<<<<<< HEAD

    <title>Login - LPK Urisowon</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-white" style="font-family: 'Inter', sans-serif;">
    <div class="flex min-h-screen">

        <!-- Left Panel -->
        <div class="hidden lg:flex lg:w-[50%] bg-[#0B1528] text-white p-12 xl:p-16 flex-col justify-between relative overflow-hidden">
            <!-- Background Glows -->
            <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
                <div class="absolute top-[-15%] right-[-10%] w-[60%] h-[60%] rounded-full bg-[#1e3a8a] opacity-30 blur-[120px]"></div>
                <div class="absolute bottom-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-[#1e40af] opacity-20 blur-[100px]"></div>
            </div>

            <!-- Header / Logo -->
            <div class="z-10 flex items-center space-x-3">
                <div class="w-9 h-9 rounded-md bg-white/10 flex items-center justify-center border border-white/5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-400"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                </div>
                <span class="font-semibold text-[15px] tracking-wide">LPK Urisowon</span>
            </div>

            <!-- Main Content -->
            <div class="z-10 max-w-[500px] mt-10">
                <h1 class="text-[3.25rem] font-bold leading-[1.15] mb-6 tracking-tight">
                    Raih Masa Depan <br>
                    <span class="text-[#4C8DF5]">Karir Profesional</span> & <br>
                    Kompeten.
                </h1>
                <p class="text-[#8E9BB0] text-[15px] leading-relaxed mb-12 pr-4">
                    Sistem ujian online terintegrasi untuk mengukur kompetensi, pantau hasil belajar, dan dapatkan sertifikasi resmi LPK Urisowon dalam satu platform modern.
                </p>

                <!-- Feature Box -->
                <div class="bg-[#151F34] border border-white/5 rounded-xl p-5 flex items-start space-x-4 max-w-[420px]">
                    <div class="flex-shrink-0 w-11 h-11 rounded-lg bg-[#2A3A5A] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-400"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold text-[15px] mb-1">Keamanan Terjamin</h3>
                        <p class="text-[13px] text-[#8E9BB0] leading-relaxed">Sistem anti-cheat & data peserta terenkripsi dengan standar keamanan tinggi.</p>
=======
    <title>Login - {{ config('app.name', 'LPK Urisowon') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
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
        /* Decorative circles */
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

        /* ─── INPUT STYLES ─── */
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
            font-family: 'Poppins', sans-serif;
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

        /* ─── REMEMBER ─── */
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

        /* ─── SUBMIT ─── */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #0d1b3e;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
        }
        .btn-submit:hover { background: #162a5e; box-shadow: 0 6px 18px rgba(13,27,62,0.3); }
        .btn-submit:active { transform: scale(0.985); }

        /* ─── CONTACT ADMIN ─── */
        .contact-text {
            text-align: center;
            margin-top: 22px;
            font-size: 13.5px;
            color: #64748b;
        }
        .contact-text a { color: #4590df; font-weight: 600; text-decoration: none; }
        .contact-text a:hover { text-decoration: underline; }



        /* ─── ALERT ─── */
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
>>>>>>> 00b61a58f2126803ab00ab80907d0636f1e84ce8
                    </div>
                </div>
            </div>

<<<<<<< HEAD
            <!-- Footer text -->
            <div class="z-10 text-[13px] text-[#5C6B89]">
                &copy; 2026 LPK Urisowon. All rights reserved.
            </div>
        </div>

    <style>
        /* Mobile styling SAFELY applied without Tailwind recompilation */
        .mobile-only { display: flex; flex-direction: column; align-items: center; justify-content: center; margin-bottom: 2rem; }
        .mobile-card {
            background-color: white;
            border-radius: 1.25rem;
            padding: 2.25rem 1.75rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            border: 1px solid #f1f5f9;
            width: 100%;
        }
        .right-panel-mobile-bg { background-color: #F8FAFC; align-items: center; justify-content: center; }

        /* Desktop resets */
        @media (min-width: 1024px) {
            .mobile-only { display: none !important; }
            .mobile-card {
                background-color: transparent !important;
                border-radius: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
            .right-panel-mobile-bg { background-color: transparent !important; align-items: center !important; justify-content: center !important; }
        }
    </style>

        <!-- Right Panel (Form) -->
        <div class="w-full lg:w-[50%] flex justify-center p-6 sm:p-12 relative right-panel-mobile-bg">
            <div class="w-full max-w-[400px] mobile-card">
                
                <!-- Logo Mobile Only -->
                <div class="mobile-only">
                    <div class="w-12 h-12 rounded-xl bg-[#0B1528] flex items-center justify-center mb-3 shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-400"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    </div>
                    <h1 class="text-[20px] font-bold text-[#0B1528] tracking-wide">LPK <span class="text-[#4C8DF5]">Urisowon</span></h1>
                </div>

                <div class="mb-10 text-center lg:text-left">
                    <h2 class="text-[28px] font-bold text-[#0B1528] mb-2">Selamat Datang</h2>
                    <p class="text-[#64748B] text-[15px]">Silakan masuk ke akun peserta ujian Anda.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-[13px] font-semibold text-[#334155] mb-1.5">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="siswa@urisowon.com" class="pl-[42px] block w-full rounded-lg border-gray-200 bg-[#F8FAFC] text-[14px] text-gray-900 focus:ring-blue-500 focus:border-blue-500 transition-colors py-[10px] placeholder-gray-400">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-[13px] font-semibold text-[#334155]">Password</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-[13px] font-bold text-[#0B1528] hover:text-blue-600 transition-colors">Lupa Password?</a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </div>
                            <input id="password" type="password" name="password" required placeholder="••••••••" class="pl-[42px] pr-10 block w-full rounded-lg border-gray-200 bg-[#F8FAFC] text-[14px] text-gray-900 focus:ring-blue-500 focus:border-blue-500 transition-colors py-[10px] placeholder-gray-400 tracking-wider">
                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center cursor-pointer" id="toggle-password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 hover:text-gray-600 transition-colors"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </div>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center pt-1">
                        <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-[#0B1528] focus:ring-[#0B1528] bg-gray-50 cursor-pointer">
                        <label for="remember_me" class="ml-2.5 block text-[13px] text-[#475569] cursor-pointer font-medium">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-[15px] font-bold text-white bg-[#0B1528] hover:bg-[#1a2b53] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0B1528] transition-colors">
                            Masuk Sekarang
                        </button>
                    </div>
                </form>

                <!-- Footer Links -->
                <div class="mt-8 text-center text-[13px] text-[#64748B]">
                    Belum punya akun? <a href="#" class="font-bold text-[#0B1528] hover:underline">Hubungi Admin</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Script for password toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('toggle-password');

            if (toggleButton && passwordInput) {
                toggleButton.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Toggle SVG (Eye to Eye-off)
                    if (type === 'text') {
                        toggleButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 hover:text-gray-600 transition-colors"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>';
                    } else {
                        toggleButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 hover:text-gray-600 transition-colors"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>';
                    }
                });
            }
        });
=======
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
                <p class="form-subtitle">Silakan masuk ke akun <span class="accent">admin</span> Anda.</p>

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
                            placeholder="admin@cbt.com"
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
        // Toggle password visibility
        function togglePassword() {
            const pwd = document.getElementById('password');
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


>>>>>>> 00b61a58f2126803ab00ab80907d0636f1e84ce8
    </script>
</body>
</html>
