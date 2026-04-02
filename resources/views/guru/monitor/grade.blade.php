@extends('layouts.guru')

@section('header')
<div class="flex items-center space-x-4">
    <a href="{{ route('guru.monitor.show', $ujian) }}" class="text-slate-500 hover:text-slate-700 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <div class="flex flex-col">
        <span class="leading-tight">Penilaian Essay Ujian: {{ $ujian->judul }}</span>
        <span class="text-xs text-slate-500 font-normal mt-0.5">Peserta: <strong>{{ optional($ujian_peserta->user)->name }}</strong></span>
    </div>
</div>
@endsection

@section('content')

@if(session('error'))
<div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 shadow-sm">
    <div class="flex">
        <div class="ml-3">
            <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

<div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 shadow-sm rounded-r-lg">
    <div class="flex">
        <div class="ml-3">
            <h3 class="text-sm font-semibold text-blue-800">Petunjuk Penilaian</h3>
            <p class="text-xs text-blue-700 mt-1">Isikan nilai yang didapat oleh murid pada kotak input yang disediakan di bawah setiap jawaban. Nilai maksimal untuk tiap soal tercantum di sebelah kanan pertanyaan.</p>
        </div>
    </div>
</div>

<form action="{{ route('guru.monitor.storeGrade', $ujian_peserta) }}" method="POST">
    @csrf
    
    <div class="space-y-6">
        @forelse($soalEssays as $index => $soal)
            @php
                $jawaban = $jawabans->get($soal->id);
                $jawabanTeks = $jawaban ? $jawaban->jawaban_text : '<span class="italic text-slate-400">Tidak ada jawaban.</span>';
                $poinDidapat = $jawaban ? $jawaban->poin_didapat : 0;
            @endphp
            
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-start">
                    <div class="flex gap-3">
                        <span class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-accent-100 text-accent-700 font-bold text-sm">
                            {{ $index + 1 }}
                        </span>
                        <div class="text-sm font-medium text-slate-800 pt-1">
                            {!! nl2br(strip_tags($soal->pertanyaan, '<br><b><i>')) !!}
                        </div>
                    </div>
                    <div class="ml-4 flex-shrink-0 text-sm font-bold text-slate-500 bg-white px-3 py-1 rounded border border-slate-200 shadow-sm">
                        Max: {{ $soal->poin }} Poin
                    </div>
                </div>
                
                <div class="p-6">
                    <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Jawaban Peserta:</h4>
                    <div class="bg-slate-50 border border-slate-200 p-4 rounded-lg text-sm text-slate-800 whitespace-pre-wrap min-h-[4rem]">{!! $jawabanTeks !!}</div>
                    
                    <div class="mt-6 flex items-center justify-end border-t border-slate-100 pt-4">
                        <label for="poin_{{ $soal->id }}" class="text-sm font-medium text-slate-700 mr-3">Berikan Nilai:</label>
                        <div class="relative rounded-md shadow-sm w-32">
                            <input type="number" name="poin[{{ $soal->id }}]" id="poin_{{ $soal->id }}" value="{{ old('poin.'.$soal->id, $poinDidapat) }}" max="{{ $soal->poin }}" min="0" step="0.5" class="focus:ring-accent-500 focus:border-accent-500 block w-full pl-3 pr-12 sm:text-sm border-slate-300 rounded-md font-bold text-lg text-accent-600">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-slate-400 sm:text-sm">/ {{ $soal->poin }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center text-slate-500">
                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="mt-2 block text-sm font-medium text-slate-900">Ujian ini tidak memiliki soal essay.</span>
            </div>
        @endforelse
    </div>
    
    @if(count($soalEssays) > 0)
    <div class="mt-8 bg-slate-50 px-6 py-4 border border-slate-200 rounded-xl flex items-center justify-between sticky bottom-4 shadow-lg">
        <div>
            <span class="text-sm font-medium text-slate-700">Skor Pilihan Ganda Saat Ini: <strong>{{ $skorPG }}</strong></span>
        </div>
        <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-colors">
            Simpan Nilai Essay
        </button>
    </div>
    @endif
</form>

@endsection
