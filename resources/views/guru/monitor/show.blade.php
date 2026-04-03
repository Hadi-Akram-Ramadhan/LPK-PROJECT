@extends('layouts.guru')

@section('header')
<div class="flex items-center space-x-4">
    <a href="{{ route('guru.monitor.index') }}" class="text-slate-500 hover:text-slate-700 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <div class="flex flex-col">
        <span class="leading-tight">Monitor: {{ $ujian->judul }}</span>
        <span class="text-xs text-slate-500 font-normal mt-0.5">Total Soal Essay yang Perlu Dinilai Manual: <strong>{{ $essayCount }} Soal</strong></span>
    </div>
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

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-8">
    <div class="p-6 border-b border-slate-200 bg-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-slate-800">Daftar Peserta & Progres Ujian</h3>
            <p class="text-sm text-slate-500 mt-1">Daftar ini akan otomatis bertambah ketika Anda memberikan *assign* kelas di halaman edit ujian.</p>
        </div>
        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center space-x-1 text-xs font-medium text-slate-500">
                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                <span>Belum</span>
            </span>
            <span class="inline-flex items-center space-x-1 text-xs font-medium text-slate-500">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                <span>Sedang Mengerjakan</span>
            </span>
            <span class="inline-flex items-center space-x-1 text-xs font-medium text-slate-500">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                <span>Selesai</span>
            </span>
            <span class="inline-flex items-center space-x-1 text-xs font-medium text-slate-500">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                <span>Diblokir (Curang)</span>
            </span>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Peserta (Murid)</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Pelanggaran</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Skor Sementara</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu Selesai</th>
                    <th scope="col" class="relative px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        Aksi Penilaian Essay
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($pesertas as $peserta)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-slate-900">{{ optional($peserta->user)->name ?? 'Unknown User' }}</div>
                        <div class="text-xs text-slate-500 mt-1">{{ optional($peserta->user)->email ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if(in_array(strtolower($peserta->status), ['belum_mulai', 'belum']))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                Belum Mulai
                            </span>
                        @elseif(strtolower($peserta->status) === 'mengerjakan')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Sedang Mengerjakan
                            </span>
                        @elseif(strtolower($peserta->status) === 'selesai')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Selesai
                            </span>
                        @elseif(strtolower($peserta->status) === 'diblokir')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                                ⚠ Diblokir / Pelanggaran
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 italic">
                                {{ $peserta->status }}
                            </span>
                        @endif
                    </td>
                    {{-- Jumlah Pelanggaran --}}
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @php $cheatCount = $peserta->cheatLogs->count(); @endphp
                        @if($cheatCount > 0)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                {{ $cheatCount }}x
                            </span>
                        @else
                            <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                        <span class="font-bold text-slate-800 text-base">{{ $peserta->skor }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ $peserta->selesai_at ? \Carbon\Carbon::parse($peserta->selesai_at)->format('d M Y, H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        @if($peserta->status === 'selesai')
                            @if($essayCount > 0)
                            <a href="{{ route('guru.monitor.grade', $peserta->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-colors">
                                <svg class="-ml-1 mr-1 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Beri Nilai Essay
                            </a>
                            @else
                            <span class="text-slate-400 text-xs italic">Tidak ada soal essay</span>
                            @endif
                        @else
                            <span class="text-slate-400 text-xs italic">Ujian Belum Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="mt-2 block text-sm font-medium text-slate-900">Belum ada peserta yang ditugaskan untuk ujian ini.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pesertas->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $pesertas->links() }}
    </div>
    @endif
</div>
@endsection
