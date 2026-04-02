@extends('layouts.admin')

@section('header')
<div class="flex justify-between items-center w-full">
    <span>Pemantauan Seluruh Ujian</span>
</div>
@endsection

@section('content')

<!-- Table List Exams -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Judul Ujian</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Dibuat Oleh (Guru)</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Durasi & Waktu</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status / Peserta</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Aksi</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($ujians as $ujian)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-slate-800">{{ $ujian->judul }}</div>
                        <div class="text-xs text-slate-500 mt-1 max-w-xs truncate">{{ $ujian->deskripsi ?? 'Tidak ada deskripsi' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-slate-900">{{ optional($ujian->guru)->name ?? 'Unknown' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        <div><span class="font-medium text-slate-700">{{ $ujian->durasi }} Menit</span></div>
                        <div class="text-xs mt-1">
                            @if($ujian->mulai && $ujian->selesai)
                                {{ $ujian->mulai->format('d M H:i') }} - {{ $ujian->selesai->format('H:i') }}
                            @else
                                <span class="text-slate-400 border border-slate-200 rounded px-1">Tanpa Batas Masuk</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $ujian->pesertas_count }} Peserta Terdaftar
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.monitor.show', $ujian) }}" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded-md border border-indigo-100 transition-colors">Detail</a>
                            <a href="{{ route('admin.monitor.export', $ujian) }}" class="text-green-600 hover:text-green-900 bg-green-50 px-3 py-1 rounded-md border border-green-100 transition-colors">Export</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span class="mt-2 block text-sm font-medium text-slate-900">Belum ada ujian yang dibuat oleh Guru</span>
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
