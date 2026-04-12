@extends('layouts.admin')

@section('header')
<div class="flex items-center space-x-4">
    <a href="{{ route('admin.paket-soal.index') }}" class="text-slate-500 hover:text-slate-700 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <span>Tambah Soal Baru</span>
</div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-4xl mx-auto">
    {{-- Paket Info Banner --}}
    @if($paketSoal)
    <div style="background:#eff6ff;border-bottom:1px solid #bfdbfe;padding:14px 28px;display:flex;align-items:center;gap:12px;">
        <svg style="width:18px;height:18px;color:#2563eb;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <div>
            <span style="font-size:12px;color:#64748b;">Menambah soal ke paket:</span>
            <span style="font-size:13px;font-weight:700;color:#1e40af;margin-left:6px;">{{ $paketSoal->nama }}</span>
        </div>
    </div>
    @else
    <div style="background:#fefce8;border-bottom:1px solid #fde68a;padding:14px 28px;">
        <span style="font-size:13px;color:#92400e;">⚠️ Pilih paket terlebih dahulu dari <a href="{{ route('admin.paket-soal.index') }}" style="color:#b45309;font-weight:600;">Bank Soal</a>.</span>
    </div>
    @endif

    <form action="{{ route('admin.soal.store') }}" method="POST" class="p-8" enctype="multipart/form-data">
        @csrf
        @if($paketSoal)
        <input type="hidden" name="paket_soal_id" value="{{ $paketSoal->id }}">
        @endif

        @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @include('_partials.soal_form_fields', [
            'soal' => null,
            'audioFiles' => $audioFiles,
            'imageFiles' => $imageFiles,
            'baseRoute'  => 'admin',
            'uploadRoute' => 'admin.soal.uploadMedia',
        ])

        <div class="pt-10 flex justify-end border-t border-slate-100 mt-8 gap-4">
            <a href="{{ $paketSoal ? route('admin.paket-soal.show', $paketSoal) : route('admin.paket-soal.index') }}"
               class="bg-white border border-slate-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                class="ml-3 inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-accent-600 hover:bg-accent-700 transition-colors">
                Simpan Soal
            </button>
        </div>
    </form>
</div>

@include('_partials.soal_form_js', [
    'soal' => null,
    'audioFiles' => $audioFiles,
    'imageFiles' => $imageFiles,
    'uploadRoute' => 'admin.soal.uploadMedia',
])
@endsection
