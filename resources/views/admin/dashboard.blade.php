@extends('layouts.admin')

@section('header', 'Dashboard')
@section('header-sub', 'Selamat datang di panel admin')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Stat Cards -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
        <p class="text-sm text-slate-500 font-medium mb-1">Total Siswa</p>
        <p class="text-3xl font-bold text-slate-800">{{ $totalSiswa }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
        <p class="text-sm text-slate-500 font-medium mb-1">Total Ujian</p>
        <p class="text-3xl font-bold text-slate-800">{{ $totalUjian }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
        <p class="text-sm text-slate-500 font-medium mb-1">Total Soal</p>
        <p class="text-3xl font-bold text-slate-800">{{ $totalSoal }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
        <p class="text-sm text-slate-500 font-medium mb-1">Rata-rata Nilai</p>
        <p class="text-3xl font-bold text-primary-600 flex items-center">
            {{ number_format($avgScore, 1) }}
            <span class="ml-2 inline-flex h-3 w-3 rounded-full bg-primary-500 animate-pulse"></span>
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Ujian Terbaru</h2>
        </div>
        <div class="p-6">
            @forelse($ujianTerbaru as $ujian)
            <div class="bg-slate-50 rounded-lg p-4 border border-slate-100 flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-semibold text-slate-800 text-lg">{{ $ujian->judul }}</h3>
                    <p class="text-slate-500 text-sm mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $ujian->soals()->count() }} soal · {{ $ujian->pesertas()->count() }} peserta · {{ $ujian->pesertas()->where('status', 'selesai')->count() }} selesai
                    </p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $ujian->jenis_ujian === 'tryout' ? 'bg-blue-100 text-blue-800 border-blue-200' : 'bg-purple-100 text-purple-800 border-purple-200' }} border uppercase">
                        {{ $ujian->jenis_ujian }}
                    </span>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-slate-500">
                <p>Belum ada ujian yang dibuat.</p>
            </div>
            @endforelse

            <a href="{{ route('admin.ujian.create') }}" 
               class="flex items-center justify-center gap-2 mt-4 text-sm font-bold py-3 px-4 text-white rounded-xl transition-all shadow-sm hover:shadow-md font-['Poppins'] hover:scale-[1.01] active:scale-[0.99]"
               style="background-color: #9333ea; color: white !important;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                Buat Ujian Baru
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Aksi Cepat</h2>
        </div>
        <div class="p-4 space-y-2">
            <a href="{{ route('admin.users.index') }}" class="block px-4 py-3 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Kelola Siswa
            </a>
            <a href="{{ route('admin.soal.index') }}" class="block px-4 py-3 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Bank Soal
            </a>
            <a href="#" class="block px-4 py-3 bg-slate-50 hover:bg-orange-50 hover:text-orange-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center group">
                <svg class="h-5 w-5 mr-3 text-slate-400 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Upload Gambar Soal
                <span class="ml-auto text-[10px] bg-orange-100 text-orange-600 px-2 py-0.5 rounded font-bold uppercase">Soon</span>
            </a>
            <a href="{{ route('admin.cheat-logs.index') }}" class="block px-4 py-3 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                Monitor Kecurangan
            </a>
            <a href="{{ route('admin.audio.index') }}" class="block px-4 py-3 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center text-slate-600">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                Upload Audio Soal
            </a>
        </div>
    </div>
</div>
@endsection
