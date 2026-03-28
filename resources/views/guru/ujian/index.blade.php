@extends('layouts.guru')

@section('header')
<div class="flex justify-between items-center w-full">
    <span>Manajemen Ujian</span>
    <a href="{{ route('guru.ujian.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-colors">
        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Buat Ujian Baru
    </a>
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

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Judul & Deskripsi</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Durasi</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Jadwal (Mulai - Selesai)</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah Soal</th>
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
                        <div class="text-xs text-slate-500 mt-1 max-w-sm truncate">{{ $ujian->deskripsi ?? 'Tidak ada deskripsi' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $ujian->durasi }} Menit
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        @if($ujian->mulai && $ujian->selesai)
                            <div class="text-green-600 font-medium">{{ \Carbon\Carbon::parse($ujian->mulai)->format('d M, H:i') }}</div>
                            <div class="text-red-500 font-medium mt-1">{{ \Carbon\Carbon::parse($ujian->selesai)->format('d M, H:i') }}</div>
                        @else
                            <span class="text-slate-400 italic">Bebas (Tidak ada batasan waktu masuk)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-slate-700">
                        {{ $ujian->soals_count }} Soal
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('guru.ujian.edit', $ujian) }}" class="text-accent-600 hover:text-accent-900 mr-3">Edit</a>
                        <form action="{{ route('guru.ujian.destroy', $ujian) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ujian ini? Logika ujian peserta juga akan terhapus.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 border-none bg-transparent cursor-pointer">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="mt-2 block text-sm font-medium text-slate-900">Belum ada kelas/ujian yang Anda jadwalkan.</span>
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
