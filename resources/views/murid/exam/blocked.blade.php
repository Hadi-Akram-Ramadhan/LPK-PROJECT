<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>UJIAN DIBLOKIR</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Set up long-polling / regular refresh as fallback
        let fallbackTimer = setInterval(function() {
            window.location.reload();
        }, 15000); // refresh every 15 seconds
        
        document.addEventListener('DOMContentLoaded', function() {
            // Check if Echo is loaded
            if (window.Echo) {
                console.log("Listening to unlock channel...");
                window.Echo.private(`exam.blocked.{{ auth()->id() }}`)
                    .listen('.CheatLogApprovedEvent', (e) => {
                        console.log("Unlock event received:", e);
                        clearInterval(fallbackTimer);
                        
                        // Give a nice UI transition
                        document.body.innerHTML = `
                            <div style="height:100vh;display:flex;align-items:center;justify-content:center;background:#10b981;color:white;font-family:sans-serif;">
                                <div style="text-align:center;">
                                    <h1 style="font-size:2rem;font-weight:bold;margin-bottom:1rem;">Ujian Dibuka Kembali!</h1>
                                    <p>Mengembalikan Anda ke halaman ujian dalam sekejap...</p>
                                </div>
                            </div>
                        `;
                        
                        setTimeout(() => window.location.href = "{{ route('murid.exam.show', $ujian_peserta) }}", 2000);
                    });
            }
        });
    </script>
</head>
<body class="h-full font-sans antialiased flex flex-col items-center justify-center p-4">

    <div class="max-w-md w-full bg-red-600 rounded-2xl shadow-2xl overflow-hidden animate-pulse-slow relative">
        <div class="p-8 pb-6 text-center">
            <svg class="mx-auto h-20 w-20 text-white mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            <h1 class="text-3xl font-black text-white uppercase tracking-wider mb-2">UJIAN DIBEKUKAN</h1>
            <p class="text-red-100 font-medium text-sm">Sistem CBT mendeteksi adanya aktivitas mencurigakan pada perangkat Anda.</p>
        </div>
        
        <div class="bg-white p-6 rounded-b-xl border-t-4 border-red-700">
            <div class="flex items-start space-x-3 mb-4">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Alasan Pemblokiran:</h3>
                    <p class="mt-1 text-sm text-slate-600">Anda meninggalkan halaman ujian, berpindah tab, membuka jendela baru, atau menutup browser sementara waktu.</p>
                </div>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-6">
                <div class="flex">
                    <div class="ml-3">
                        <h4 class="text-sm font-bold text-yellow-800">Menunggu Persetujuan Guru</h4>
                        <p class="mt-1 text-xs text-yellow-700">Waktu ujian Anda akan terus berjalan. Segera lapor ke pengawas atau guru pengampu Anda untuk membuka gembok ujian ini dari layar mereka.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-[11px] text-slate-400">Halaman ini akan me-refresh otomatis setiap 10 detik untuk mengecek status persetujuan.</p>
            </div>
            
            <div class="mt-8">
                <form action="{{ route('murid.exam.finish', $ujian_peserta) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyerah dan mengakhiri ujian dengan skor seadanya?');">
                    @csrf
                    <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-slate-300 rounded-md shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-red-50 hover:text-red-700 hover:border-red-300 focus:outline-none transition-colors">
                        Akhiri Ujian Permanen (Nyerah)
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
