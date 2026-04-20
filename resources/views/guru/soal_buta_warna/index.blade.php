@extends('layouts.guru')

@section('header')
<div class="flex justify-between items-center w-full">
    <span>Sistem Cerdas: Bank Soal Tes Buta Warna</span>
</div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
    <div class="flex">
        <div class="ml-3">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Upload Section -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4 items-center">
                Tambah Plat Ishihara Baru
            </h3>
            
            <form action="{{ route('guru.soal_buta_warna.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Pilih File Gambar (JPG/PNG/WEBP)</label>
                        <input type="file" name="gambar" accept="image/*" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        @error('gambar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label for="jawaban_kunci" class="block text-sm font-medium text-slate-700 mb-1">Jawaban Kunci (Angka)</label>
                        <input type="text" name="jawaban_kunci" id="jawaban_kunci" placeholder="Misal: 12" required class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-slate-300 rounded-md">
                        @error('jawaban_kunci') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                <button type="submit" class="mt-6 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Sistemkan Gambar
                </button>
            </form>
        </div>
        
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
            <h4 class="text-sm font-bold text-blue-800">Sistem Cerdas Plat Buta Warna</h4>
            <p class="text-xs text-blue-700 mt-1">Sistem ini menggunakan metode <strong>Short Answer</strong>. Pastikan jawaban Anda adalah teks atau angka murni. Jika Anda mengaktifkan mode Tes Buta Warna saat membuat Ujian, sistem akan secara acak memunculkan maksimal 5 gambar dari sini ke murid secara berurutan, setelah ujian utama diselesaikan.</p>
        </div>
    </div>
    
    <!-- File List Section -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800 w-full flex justify-between items-center">
                    <span>Semua Plat Buta Warna ({{ $soals->count() ?? 0 }})</span>
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Gambar Plat</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Jawaban Kunci</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($soals as $soal)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-20 w-20 bg-slate-100 rounded border flex items-center justify-center overflow-hidden">
                                        <img src="{{ asset('storage/' . $soal->gambar_path) }}" alt="Plat Ishihara" class="h-20 w-20 object-cover">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-slate-900 truncate max-w-[150px]">
                                            ID: #{{ $soal->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-lg text-slate-900 font-bold">
                                {{ $soal->jawaban_kunci }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="{{ route('guru.soal_buta_warna.destroy', $soal) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus soal tes ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 border border-red-200 hover:bg-red-50 rounded-md px-3 py-1 transition-colors">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-slate-500">
                                <span class="mt-2 block text-sm font-medium text-slate-900">Belum ada plat didaftarkan ke sistem cerdas.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
