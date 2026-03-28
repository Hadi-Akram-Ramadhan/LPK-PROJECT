@extends('layouts.guru')

@section('header')
<div class="flex items-center space-x-4">
    <span>Import Bank Soal (Excel)</span>
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

@if(session('error'))
<div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 shadow-sm">
    <div class="flex">
        <div class="ml-3">
            <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Panduan & Download -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                <svg class="h-5 w-5 mr-2 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Langkah 1: Unduh Template
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-sm text-slate-600">Sistem membaca data soal berdasarkan kolom-kolom spesifik. Pastikan Anda menggunakan format Excel terbaru berikut ini.</p>
            
            <a href="{{ route('guru.import.template') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 shadow-sm text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 w-full justify-center transition-colors">
                <svg class="-ml-1 mr-2 h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Unduh Format Excel (.xlsx)
            </a>

            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-sm font-bold text-blue-800 mb-2">Petunjuk Pengisian:</h4>
                <ul class="text-xs text-blue-700 space-y-2 list-disc list-inside">
                    <li>Biarkan <strong>baris ke-1</strong> (Header warna biru) tetap ada.</li>
                    <li>Satu baris Excel = Satu soal.</li>
                    <li>Untuk soal Essay, kosongkan kolom Opsi A-E dan Jawaban Benar.</li>
                    <li>Untuk Multiple Choice, pisahkan Jawaban Benar dengan koma (Contoh: <code class="bg-blue-100 px-1 rounded">A,C</code>).</li>
                    <li>Untuk soal Audio (Choukai), tuliskan <code class="bg-blue-100 px-1 rounded">NamaFile.mp3</code> pada kolom File Audio. File harus memiliki nama persis dengan yang ada di <strong>Audio Explorer</strong>.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Upload Area -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-semibold text-slate-800 flex items-center">
                <svg class="h-5 w-5 mr-2 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Langkah 2: Unggah File
            </h3>
        </div>
        <div class="p-6">
            <form action="{{ route('guru.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-md bg-slate-50 hover:bg-slate-100 transition-colors">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                        <div class="flex text-sm text-slate-600 justify-center mt-4">
                            <label for="file_excel" class="relative cursor-pointer bg-white rounded-md font-medium text-accent-600 hover:text-accent-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-accent-500 shadow-sm px-2 py-1 border border-slate-200">
                                <span>Pilih file</span>
                                <input id="file_excel" name="file_excel" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required onchange="document.getElementById('file-name').textContent = this.files[0].name">
                            </label>
                            <p class="pl-1 pt-1">atau drag and drop</p>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">XLSX, XLS up to 5MB</p>
                        <p id="file-name" class="text-sm font-semibold text-accent-700 mt-3"></p>
                    </div>
                </div>
                @error('file_excel') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

                <div class="mt-8">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-colors">
                        Mulai Proses Import
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
