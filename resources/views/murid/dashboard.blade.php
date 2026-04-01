@extends('layouts.murid')

@section('header')
<div class="flex items-center">
    <svg class="h-6 w-6 text-slate-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
    <span>Daftar Ujian Anda</span>
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

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($ujianPesertas as $peserta)
        @php
            $ujian = $peserta->ujian;
            $now = \Carbon\Carbon::now();
            
            $isBelumWaktunya = $ujian->mulai && $now->lt(\Carbon\Carbon::parse($ujian->mulai));
            $isTerlambat = $ujian->selesai && $now->gt(\Carbon\Carbon::parse($ujian->selesai)) && $peserta->status == 'belum_mulai';
        @endphp

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col transition-all hover:shadow-md">
            <div class="p-6 flex-1">
                <div class="flex justify-between items-start mb-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        {{ $peserta->status === 'selesai' ? 'bg-green-100 text-green-800' : 
                           ($peserta->status === 'mengerjakan' ? 'bg-blue-100 text-blue-800 animate-pulse' : 
                           ($peserta->status === 'diblokir' ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-800')) }}">
                        @if($peserta->status === 'selesai') Selesai
                        @elseif($peserta->status === 'mengerjakan') Sedang Mengerjakan
                        @elseif($peserta->status === 'diblokir') Terblokir
                        @else Belum Dikerjakan
                        @endif
                    </span>
                    <span class="text-sm font-medium text-slate-500 flex items-center">
                        <svg class="h-4 w-4 mr-1 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $ujian->durasi }} Menit
                    </span>
                </div>
                
                <h3 class="text-lg font-bold text-slate-900 mb-1 line-clamp-2">{{ $ujian->judul }}</h3>
                <p class="text-sm text-slate-500 mb-4 line-clamp-2">{{ $ujian->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                
                <div class="space-y-2 text-xs text-slate-600">
                    @if($ujian->mulai)
                    <div class="flex items-center">
                        <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Mulai: {{ \Carbon\Carbon::parse($ujian->mulai)->format('d M Y, H:i') }}
                    </div>
                    @endif
                    
                    @if($ujian->selesai)
                    <div class="flex items-center">
                        <svg class="h-4 w-4 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Batas: {{ \Carbon\Carbon::parse($ujian->selesai)->format('d M Y, H:i') }}
                    </div>
                    @endif
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <div>
                    @if($peserta->status === 'selesai')
                        <span class="text-sm font-bold text-slate-800">Skor: {{ $peserta->skor }}</span>
                    @endif
                </div>

                @if($peserta->status === 'selesai')
                    <a href="{{ route('murid.exam.result', $peserta) }}" class="text-sm font-medium text-accent-600 hover:text-accent-500">Lihat Rekap &rarr;</a>
                @elseif($peserta->status === 'diblokir')
                    <a href="{{ route('murid.exam.blocked', $peserta) }}" class="text-sm font-medium text-red-600 hover:text-red-500">Info Blokir &rarr;</a>
                @elseif($isTerlambat && $peserta->status === 'belum_mulai')
                    <span class="text-sm font-medium text-red-500">Terlambat</span>
                @elseif($isBelumWaktunya)
                    <span class="text-sm font-medium text-slate-400">Belum Waktunya</span>
                @else
                    <form action="{{ route('murid.exam.start', $peserta) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white {{ $peserta->status === 'mengerjakan' ? 'bg-orange-500 hover:bg-orange-600' : 'bg-accent-600 hover:bg-accent-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-colors">
                            {{ $peserta->status === 'mengerjakan' ? 'Lanjutkan' : 'Mulai Kerjakan' }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center text-slate-500">
            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <span class="mt-2 block text-sm font-medium text-slate-900">Belum ada ujian.</span>
            <p class="mt-1 text-sm text-slate-500">Tidak ada ujian yang ditugaskan ke kelas Anda saat ini.</p>
        </div>
    @endforelse
</div>
@endsection
