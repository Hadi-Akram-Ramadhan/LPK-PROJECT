@extends('layouts.murid')

@section('header')
<div class="flex items-center">
    <svg class="h-6 w-6 text-slate-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <span>Rekap Hasil Ujian</span>
</div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 shadow-sm">
    <div class="flex">
        <div class="ml-3">
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

<div class="mt-8">
    <div class="max-w-3xl mx-auto rounded-2xl shadow-xl overflow-hidden text-center justify-center p-0 flex flex-col items-center pb-8 border border-slate-200">
        <div class="bg-gradient-to-r from-accent-600 to-accent-800 w-full py-16 text-white text-center">
            <h1 class="text-xl md:text-3xl font-black uppercase tracking-widest opacity-90">Ujian Telah Selesai</h1>
            <p class="mt-2 text-accent-100 font-medium max-w-lg mx-auto">{{ $ujian->judul }}</p>
        </div>
        
        <div class="p-8 w-full">
            
            <div class="bg-slate-50 border border-slate-200 p-6 rounded-xl mx-auto max-w-sm mb-6 -mt-16 relative z-10 shadow-md">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-widest">Skor Akhir Anda</span>
                <div class="mt-2 flex items-baseline justify-center">
                    <span class="text-7xl font-black text-slate-900 tracking-tight">{{ $ujian_peserta->skor }}</span>
                </div>
                
                @if($adaEssay)
                <div class="mt-4 bg-yellow-50 text-yellow-800 border-l-4 border-yellow-400 p-3 text-left">
                    <p class="text-xs italic">Catatan: Ujian ini mengandung soal Teks Praktik / Essay. Nilai yang Anda lihat saat ini mungkin bertambah setelah guru Anda menilai soal essay secara spesifik.</p>
                </div>
                @else
                <div class="mt-4 text-xs text-slate-500">Nilai sudah final berdasarkan penilaian otomatis sistem (PG & MC).</div>
                @endif
            </div>

            <div class="grid grid-cols-2 divide-x divide-slate-100 border-t border-b border-slate-100 mb-8 py-4 w-full">
                <div class="px-4">
                    <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Mulai Ujian</div>
                    <div class="text-sm font-medium text-slate-700">{{ $ujian_peserta->mulai_at ? \Carbon\Carbon::parse($ujian_peserta->mulai_at)->format('H:i') : '-' }} WIB</div>
                </div>
                <div class="px-4">
                    <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Selesai Ujian</div>
                    <div class="text-sm font-medium text-slate-700">{{ $ujian_peserta->selesai_at ? \Carbon\Carbon::parse($ujian_peserta->selesai_at)->format('H:i') : '-' }} WIB</div>
                </div>
            </div>
            
            <a href="{{ route('murid.dashboard') }}" class="inline-flex justify-center w-full max-w-xs items-center px-4 py-3 shadow-md border border-transparent text-sm font-medium rounded-md text-white bg-slate-800 hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-colors">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

@endsection
