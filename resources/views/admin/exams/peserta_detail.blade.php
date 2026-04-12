@extends('layouts.admin')

@section('header')
<div class="flex items-center space-x-4">
    <a href="{{ route('admin.monitor.show', $ujian->id) }}" class="text-slate-500 hover:text-slate-700 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <div>
        <h2 class="text-lg md:text-xl font-bold text-slate-800 leading-tight">Detail Jawaban: {{ $ujian_peserta->user->name }}</h2>
        <p class="text-sm text-slate-500">{{ $ujian->judul }}</p>
    </div>
</div>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col justify-center items-center">
        <div class="text-sm font-medium text-slate-500 uppercase tracking-wider mb-2">Total Skor</div>
        <div class="text-4xl font-bold text-slate-800">{{ $ujian_peserta->skor }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col justify-center items-center">
        <div class="text-sm font-medium text-indigo-500 uppercase tracking-wider mb-2">Skor Listening</div>
        <div class="text-3xl font-bold text-indigo-700">{{ $skorListening }}</div>
        <div class="text-xs text-slate-400 mt-1">{{ $listening->count() }} soal dijawab</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col justify-center items-center">
        <div class="text-sm font-medium text-emerald-500 uppercase tracking-wider mb-2">Skor Reading</div>
        <div class="text-3xl font-bold text-emerald-700">{{ $skorReading }}</div>
        <div class="text-xs text-slate-400 mt-1">{{ $reading->count() }} soal dijawab</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-8">
    <div class="p-6 border-b border-slate-200 bg-slate-50">
        <h3 class="text-lg font-semibold text-slate-800">Rincian Per Soal</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">No</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe Soal & Kategori</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Jawaban Murid (Teks)</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Poin Maks</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Poin Didapat</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @php 
                    $no = 1;
                    $allJawaban = $ujian_peserta->jawabanMurids->sortBy(function($j) { return $j->soal_id; }); 
                @endphp
                @foreach($allJawaban as $jawaban)
                @php
                    $isListening = $jawaban->soal && in_array($jawaban->soal->tipe, ['audio', 'pilihan_ganda_audio']);
                    $kategoriStr = $isListening ? 'Listening' : 'Reading';
                    
                    $isBenar = $jawaban->poin_didapat == optional($jawaban->soal)->poin;
                    $isSebagian = $jawaban->poin_didapat > 0 && !$isBenar;
                    
                    $statusColor = $isBenar ? 'bg-green-100 text-green-800' : ($isSebagian ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                    $statusText = $isBenar ? 'Benar' : ($isSebagian ? 'Sebagian' : 'Salah');
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 text-center">{{ $no++ }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-800">
                        <span class="font-medium">{{ optional($jawaban->soal)->tipe }}</span>
                        <div class="text-xs text-slate-400 font-bold tracking-wide mt-1">{{ $kategoriStr }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600 max-w-sm truncate break-words" title="{{ $jawaban->jawaban_text }}">
                        @if($jawaban->jawaban_text)
                            {{ Str::limit($jawaban->jawaban_text, 50) }}
                        @else
                            <span class="italic text-slate-400">Kosong</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 text-center">
                        {{ optional($jawaban->soal)->poin ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800 text-center">
                        {{ floatval($jawaban->poin_didapat) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                            {{ $statusText }}
                        </span>
                    </td>
                </tr>
                @endforeach
                
                @if($allJawaban->isEmpty())
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500 italic block">Tidak ada data jawaban.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@endsection
