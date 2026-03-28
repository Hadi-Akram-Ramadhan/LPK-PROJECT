@extends('layouts.murid')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-800">Konnichiwa, {{ Auth::user()->name }}! 🎓</h1>
    <p class="text-slate-500 mt-2">Selamat datang di portal ujian LPK. Berikut adalah daftar ujian yang tersedia untuk Anda.</p>
</div>

<!-- Available Exams Section -->
<div class="mb-10">
    <h2 class="text-xl font-semibold text-slate-800 mb-4 flex items-center">
        <svg class="w-6 h-6 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Ujian Tersedia
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Mock Exam Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition-all transform hover:-translate-y-1 duration-300">
            <div class="h-2 bg-gradient-to-r from-primary-400 to-primary-600"></div>
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        N4 Level
                    </span>
                    <span class="text-slate-500 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        90 Menit
                    </span>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Simulasi JLPT N4 - Choukai & Dokkai</h3>
                <p class="text-slate-500 text-sm mb-6 line-clamp-2">Latihan simulasi ujian lengkap meliputi pemahaman mendengar (Choukai) dan membaca (Dokkai).</p>
                
                <a href="#" class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Mulai Ujian
                </a>
            </div>
        </div>

        <!-- Another Exam (Essay Focus) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-lg transition-all transform hover:-translate-y-1 duration-300 opacity-75">
            <div class="h-2 bg-gradient-to-r from-accent-400 to-accent-600"></div>
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        Sakubun
                    </span>
                    <span class="text-slate-500 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        45 Menit
                    </span>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Latihan Menulis Sakubun</h3>
                <p class="text-slate-500 text-sm mb-6 line-clamp-2">Ujian essay berfokus pada kemampuan tata bahasa dan struktur kalimat dalam membuat paragraf pendek.</p>
                
                <button disabled title="Belum waktunya" class="w-full flex justify-center items-center px-4 py-2 border border-slate-200 rounded-lg shadow-sm text-sm font-medium text-slate-400 bg-slate-50 cursor-not-allowed">
                    Ujian Belum Dimulai
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Exam Results Section -->
<div>
    <h2 class="text-xl font-semibold text-slate-800 mb-4 flex items-center">
        <svg class="w-6 h-6 mr-2 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        Riwayat Ujian & Hasil
    </h2>
    <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Nama Ujian</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Skor Total</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-slate-900">Quiz Kanji N5</div>
                        <div class="text-sm text-slate-500">Selesai pada 25 Mar 2026</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-slate-900 font-bold">85 / 100</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="#" class="text-primary-600 hover:text-primary-900">Lihat Detail</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
