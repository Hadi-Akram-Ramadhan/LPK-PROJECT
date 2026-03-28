@extends('layouts.admin')

@section('header', 'Dashboard Admin')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Stat Cards -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center space-x-4 hover:shadow-md transition-shadow">
        <div class="h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium">Total Murid</p>
            <p class="text-2xl font-bold text-slate-800">124</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center space-x-4 hover:shadow-md transition-shadow">
        <div class="h-12 w-12 rounded-full bg-accent-100 flex items-center justify-center text-accent-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium">Ujian Aktif</p>
            <p class="text-2xl font-bold text-slate-800">3</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center space-x-4 hover:shadow-md transition-shadow">
        <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center text-red-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium">Peringatan Kecurangan</p>
            <p class="text-2xl font-bold text-red-600">5</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-slate-800">Aktivitas Terkini</h2>
        <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-700">Lihat Semua</a>
    </div>
    <div class="p-6 text-slate-500 text-center py-12">
        Belum ada aktivitas yang tercatat.
    </div>
</div>
@endsection
