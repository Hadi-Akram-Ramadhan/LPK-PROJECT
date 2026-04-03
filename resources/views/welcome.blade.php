<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                    </div>
                </div>
            </div>

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
    </script>
</body>
</html>
