@extends('layouts.guru')

@section('header', 'Dashboard Guru')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Stat Cards -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
        <p class="text-sm text-slate-500 font-medium mb-1">Total Soal</p>
        <p class="text-3xl font-bold text-slate-800">45</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
        <p class="text-sm text-slate-500 font-medium mb-1">Ujian Dibuat</p>
        <p class="text-3xl font-bold text-slate-800">4</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
        <p class="text-sm text-slate-500 font-medium mb-1">Perlu Dinilai (Essay)</p>
        <p class="text-3xl font-bold text-accent-600">12</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow">
        <p class="text-sm text-slate-500 font-medium mb-1">Sedang Ujian</p>
        <p class="text-3xl font-bold text-primary-600 flex items-center">
            28
            <span class="ml-2 inline-flex h-3 w-3 rounded-full bg-primary-500 animate-pulse"></span>
        </p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Jadwal Ujian Mendatang</h2>
        </div>
        <div class="p-6">
            <div class="bg-slate-50 rounded-lg p-4 border border-slate-100 flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-semibold text-slate-800 text-lg">Ujian N4 - Choukai (Mendengar)</h3>
                    <p class="text-slate-500 text-sm mt-1 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Besok, 08:00 - 09:30 (90 Menit)
                    </p>
                </div>
                <button class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 hover:text-slate-900 px-4 py-2 rounded shadow-sm text-sm font-medium transition-colors">
                    Edit
                </button>
            </div>
            
            <a href="#" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 w-full mt-2 transition-colors">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Buat Ujian Baru
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-lg font-semibold text-slate-800">Aksi Cepat</h2>
        </div>
        <div class="p-4 space-y-2">
            <a href="#" class="block px-4 py-3 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Tambah Soal Baru
            </a>
            <a href="#" class="block px-4 py-3 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Soal Excel
            </a>
            <a href="#" class="block px-4 py-3 bg-slate-50 hover:bg-primary-50 hover:text-primary-700 rounded-lg border border-slate-100 transition-colors text-slate-700 font-medium flex items-center">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                Upload Audio Soal
            </a>
        </div>
    </div>
</div>
@endsection
