@extends('layouts.guru')

@section('header')
<div class="flex items-center space-x-4">
    <span>Pilih Ujian Untuk Dimonitor / Dinilai</span>
</div>
@endsection

@section('content')

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Judul Ujian</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Durasi</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Peserta/Murid</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Aksi</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($ujians as $ujian)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-slate-900 break-words max-w-sm">{{ $ujian->judul }}</div>
                        <div class="text-xs text-slate-500 mt-1">
                            @if($ujian->mulai && $ujian->selesai)
                                Dijadwalkan: {{ \Carbon\Carbon::parse($ujian->mulai)->format('d M H:i') }} - {{ \Carbon\Carbon::parse($ujian->selesai)->format('d M H:i') }}
                            @else
                                <span class="italic">Tanpa Jadwal Spesifik</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $ujian->durasi }} Menit
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $ujian->pesertas_count }} Terdaftar
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('guru.monitor.show', $ujian) }}" class="inline-flex items-center px-3 py-1 bg-accent-100 text-accent-700 text-xs font-semibold rounded-md hover:bg-accent-200 transition-colors">
                                    Monitor
                                </a>
                                <a href="{{ route('guru.monitor.export', $ujian) }}" class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-md hover:bg-green-200 transition-colors">
                                    Export
                                </a>
                            </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <span class="mt-2 block text-sm font-medium text-slate-900">Belum ada ujian. Buat ujian terlebih dahulu di Manajemen Ujian.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($ujians->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $ujians->links() }}
    </div>
    @endif
</div>
@endsection
