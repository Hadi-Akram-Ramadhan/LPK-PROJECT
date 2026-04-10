@extends('layouts.admin')

@section('header')
<div class="flex flex-wrap items-center justify-between gap-4 py-2">
    <div class="flex items-center">
        <a href="{{ route('admin.monitor.index') }}" class="mr-3 text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-lg md:text-xl font-bold text-slate-800 leading-tight">Detail Hasil Ujian</h2>
            <p class="text-xs md:text-sm text-slate-500">{{ $ujian->judul }}</p>
        </div>
    </div>
    <div class="flex items-center">
        <a href="{{ route('admin.monitor.export', $ujian) }}" class="btn btn-green" style="padding: 8px 16px; font-size: 13px;">
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export CSV
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] font-bold tracking-widest">
                <tr>
                    <th class="px-6 py-4 text-left">No</th>
                    <th class="px-6 py-4 text-left">Peserta</th>
                    <th class="px-6 py-4 text-left">Kelas</th>
                    <th class="px-6 py-4 text-left">Status</th>
                    <th class="px-6 py-4 text-left">Waktu</th>
                    <th class="px-6 py-4 text-center">Skor</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200 text-sm">
                @forelse($pesertas as $index => $p)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">{{ $pesertas->firstItem() + $index }}</td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-slate-800">{{ $p->user->name }}</div>
                        <div class="text-xs text-slate-400">{{ $p->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 text-slate-600">{{ $p->user->kelas->nama ?? '-' }}</td>
                    <td class="px-6 py-4">
                        @if(strtolower($p->status) === 'selesai')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">SELESAI</span>
                        @elseif(strtolower($p->status) === 'mengerjakan')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">MENGERJAKAN</span>
                        @elseif(strtolower($p->status) === 'diblokir')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200 animate-pulse">⚠ DIBLOKIR</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200 italic">BELUM MULAI</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">
                        <div>Mulai: {{ $p->mulai_at ?? '-' }}</div>
                        <div>Selesai: {{ $p->selesai_at ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-3 py-1 bg-slate-100 rounded text-slate-900 font-bold border border-slate-200 shadow-sm">
                            {{ $p->skor }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada peserta yang mengikuti ujian ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($pesertas->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 text-right">
        {{ $pesertas->links() }}
    </div>
    @endif
</div>
@endsection
