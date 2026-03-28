@extends('layouts.guru')

@section('header')
<div class="flex justify-between items-center w-full">
    <span>Bank Soal LPK</span>
    <div class="flex space-x-2">
        <a href="{{ route('guru.import.index') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-md shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-colors">
            <svg class="-ml-1 mr-2 h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19h6m-3-3v3"></path></svg>
            Import Excel
        </a>
        <a href="{{ route('guru.soal.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-colors">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Buat Soal Manual
        </a>
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

<div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-6 p-4">
    <form method="GET" action="{{ route('guru.soal.index') }}" class="flex items-center space-x-4">
        <span class="text-sm font-medium text-slate-500">Filter Tipe Soal:</span>
        <select name="tipe" onchange="this.form.submit()" class="ml-2 block w-48 pl-3 pr-10 py-2 text-base border-slate-300 focus:outline-none focus:ring-accent-500 focus:border-accent-500 sm:text-sm rounded-md">
            <option value="">Semua Filter Tipe</option>
            <option value="pilihan_ganda" {{ request('tipe') == 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda (Tunggal)</option>
            <option value="multiple_choice" {{ request('tipe') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice (Checkbox)</option>
            <option value="essay" {{ request('tipe') == 'essay' ? 'selected' : '' }}>Essay (Isian Bebas)</option>
            <option value="audio" {{ request('tipe') == 'audio' ? 'selected' : '' }}>Listening / Choukai (Audio)</option>
        </select>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Pertanyaan</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe Soal</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Poin</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tgl Dibuat</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Aksi</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($soals as $soal)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm text-slate-900 break-words line-clamp-2 max-w-md">
                            {{ strip_tags($soal->pertanyaan) }}
                        </div>
                        @if($soal->audio_path)
                        <div class="mt-1 flex items-center text-xs text-primary-600">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M8 12h.01M12 12h.01M16 12h.01M9 16H5a2 2 0 01-2-2V10a2 2 0 012-2h4l5-5v18l-5-5z"></path></svg>
                            Mendukung Audio
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($soal->tipe == 'pilihan_ganda')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Pil. Ganda</span>
                        @elseif($soal->tipe == 'multiple_choice')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Multiple</span>
                        @elseif($soal->tipe == 'essay')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Essay</span>
                        @elseif($soal->tipe == 'audio')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Choukai (Audio)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 font-medium">
                        {{ $soal->poin }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ $soal->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('guru.soal.edit', $soal) }}" class="text-accent-600 hover:text-accent-900 mr-3">Detail/Edit</a>
                        <form action="{{ route('guru.soal.destroy', $soal) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus soal ini dar bank soal?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 border-none bg-transparent cursor-pointer">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <span class="mt-2 block text-sm font-medium text-slate-900">Belum ada soal di Bank Soal Anda</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($soals->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $soals->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
