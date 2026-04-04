@extends('layouts.guru')

@section('header', 'Dashboard Guru')
@section('header-sub', 'Selamat datang di panel pengajar')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Stat Cards -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
        <p class="text-sm text-slate-500 font-medium mb-1">Total Soal</p>
        <p class="text-3xl font-bold text-slate-800">{{ $totalSoal }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
        <p class="text-sm text-slate-500 font-medium mb-1">Ujian Dibuat</p>
        <p class="text-3xl font-bold text-slate-800">{{ $totalUjian }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
        <p class="text-sm text-slate-500 font-medium mb-1">Perlu Dinilai</p>
        <p class="text-3xl font-bold text-slate-800">{{ $perluDinilai }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
        <p class="text-sm text-slate-500 font-medium mb-1">Sedang Ujian</p>
        <p class="text-3xl font-bold text-primary-600 flex items-center">
            {{ $sedangUjianCount }}
            <span class="ml-2 inline-flex h-3 w-3 rounded-full bg-primary-500 animate-pulse"></span>
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Ujian yang Akan Datang --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-800">Jadwal Ujian Mendatang</h2>
            </div>
            <div class="p-6">
                @forelse($upcomingExams as $ujian)
                <div class="bg-blue-50/50 rounded-lg p-4 border border-blue-100 flex justify-between items-center mb-4 last:mb-0">
                    <div>
                        <h3 class="font-semibold text-slate-800 text-lg">{{ $ujian->judul }}</h3>
                        <p class="text-blue-600 text-sm mt-1 flex items-center font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ \Carbon\Carbon::parse($ujian->mulai)->format('d M Y, H:i') }} ({{ $ujian->durasi }} Menit)
                        </p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">Mendatang</span>
                </div>
                @empty
                <div class="text-center py-6 border-2 border-dashed border-slate-200 rounded-lg text-slate-400 font-medium">
                    Belum ada ujian mendatang
                </div>
                @endforelse
                
                <a href="{{ route('guru.ujian.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 w-full mt-4 transition-colors">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Buat Ujian Baru
                </a>
            </div>
        </div>

        {{-- Ujian Terbaru --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-slate-800">Ujian Terbaru (Baru Dibuat)</h2>
                <a href="{{ route('guru.ujian.index') }}" class="text-xs font-bold text-accent-600 uppercase tracking-widest hover:underline">Kelola</a>
            </div>
            <div class="p-6">
                @forelse($latestExams as $ujian)
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-100 flex justify-between items-center mb-4 last:mb-0">
                    <div>
                        <h3 class="font-semibold text-slate-800 text-lg">{{ $ujian->judul }}</h3>
                        <p class="text-slate-500 text-sm mt-1 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Dibuat {{ $ujian->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center py-6 border-2 border-dashed border-slate-200 rounded-lg text-slate-400 font-medium">
                    Belum ada ujian terbaru
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Aksi Cepat</h2>
        </div>
        <div class="p-4 space-y-2">
            <a href="{{ route('guru.soal.index') }}" class="block px-4 py-3 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Bank Soal
            </a>
            <a href="#" class="block px-4 py-3 bg-slate-50 hover:bg-orange-50 hover:text-orange-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center group">
                <svg class="h-5 w-5 mr-3 text-slate-400 group-hover:text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Upload Gambar Soal
                <span class="ml-auto text-[10px] bg-orange-100 text-orange-600 px-2 py-0.5 rounded font-bold uppercase">Soon</span>
            </a>
            <a href="{{ route('guru.cheat-logs.index') }}" class="block px-4 py-3 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                Monitor Kecurangan
            </a>
            <a href="{{ route('guru.audio.index') }}" class="block px-4 py-3 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center text-slate-600">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                Upload Audio Soal
            </a>
        </div>
    </div>
</div>
@endsection
