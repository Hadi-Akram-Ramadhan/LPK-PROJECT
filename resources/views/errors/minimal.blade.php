<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - LPK URISOWON</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Poppins', sans-serif; background: #f0f4f8; }
        .gradient-text {
            background: linear-gradient(to right, #2563eb, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center p-4">

    <div class="max-w-2xl w-full bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row relative">
        
        <!-- Left Side Image/Branding -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 p-10 md:w-5/12 flex items-center justify-center flex-col relative overflow-hidden">
            <!-- Decorative rings -->
            <div class="absolute w-40 h-40 bg-white/5 rounded-full -top-10 -left-10"></div>
            <div class="absolute w-24 h-24 bg-white/5 rounded-full bottom-10 -right-5"></div>
            
            <img src="{{ asset('images/logo.png') }}" alt="LPK Logo" class="w-24 h-24 object-contain mb-4 relative z-10 filter drop-shadow-lg" onerror="this.src='https://ui-avatars.com/api/?name=LPK&background=2563eb&color=fff&rounded=true'">
            
            <div class="text-center relative z-10">
                <h2 class="text-xl font-bold text-white tracking-tight">LPK URISOWON</h2>
                <p class="text-[11px] font-semibold text-blue-400 tracking-[0.15em] uppercase mt-1">Sistem Ujian Terpadu</p>
            </div>
        </div>

        <!-- Right Side Error Details -->
        <div class="p-10 md:w-7/12 flex flex-col justify-center text-center md:text-left">
            <div class="text-5xl font-black text-slate-200 tracking-tighter mb-2">
                @yield('code')
            </div>
            
            <h1 class="text-2xl font-bold text-slate-800 mb-4 tracking-tight">
                @yield('message')
            </h1>
            
            <p class="text-slate-500 text-sm mb-6 leading-relaxed">
                Mohon maaf, halaman yang Anda cari tidak ditemukan atau terjadi kesalahan pada sistem kami.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button onclick="window.history.back()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-all shadow-sm focus:ring-2 focus:ring-slate-400 focus:outline-none flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </button>
                <a href="{{ url('/') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md transition-all focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 focus:outline-none flex items-center justify-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Halaman Utama
                </a>
            </div>
        </div>

    </div>
</body>
</html>
