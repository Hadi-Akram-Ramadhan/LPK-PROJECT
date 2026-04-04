@extends('layouts.guru')

@section('header', 'Dashboard Guru')

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
        <p class="text-sm text-slate-500 font-medium mb-1">Perlu Dinilai (Selesai)</p>
        <p class="text-3xl font-bold text-accent-600">{{ $perluDinilai }}</p>
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
            <a href="{{ route('guru.soal.create') }}" class="block px-4 py-3 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Tambah Soal Baru
            </a>
            <a href="{{ route('guru.import.index') }}" class="block px-4 py-3 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Soal Excel
            </a>
            <a href="{{ route('guru.audio.index') }}" class="block px-4 py-3 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                Upload Audio Soal
            </a>
        </div>
    </div>
</div>
@endsection
