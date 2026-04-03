@extends('layouts.admin')

@section('header', 'Import Data Siswa dari Excel')
@section('header-sub', 'Admin / Manajemen User / Import Siswa')

@section('extra-css')
<style>
    .import-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 24px; max-width: 600px; margin: 0 auto; }
    .import-title { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 24px; text-align: center; }
    
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 8px; }
    .file-input-wrapper {
        border: 2px dashed #e2e8f0; border-radius: 10px; padding: 32px; text-align: center;
        transition: border-color 0.15s; cursor: pointer; background: #f8fafc;
    }
    .file-input-wrapper:hover { border-color: #10b981; background: #ecfdf5; }
    .file-input-wrapper svg { width: 48px; height: 48px; color: #94a3b8; margin-bottom: 12px; }
    .file-input-text { font-size: 14px; color: #64748b; margin-bottom: 4px; }
    .file-input-sub { font-size: 12px; color: #94a3b8; }
    .help-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 16px; margin-bottom: 24px; }
    .help-title { font-size: 14px; font-weight: 700; color: #166534; margin-bottom: 8px; display: flex; align-items: center; }
    .help-title svg { width: 18px; height: 18px; margin-right: 8px; }
    .help-list { font-size: 13px; color: #14532d; line-height: 1.6; margin-left: 20px; list-style-type: disc; }

    .btn-submit { width: 100%; justify-content: center; margin-top: 8px; background: #10b981; color: #fff; display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; transition: 0.15s; }
    .btn-submit:hover { background: #059669; }
    .btn-template { background: #fff; color: #10b981; border: 1.5px solid #10b981; display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; text-decoration: none; transition: 0.15s; margin-bottom: 16px; width: 100%; justify-content: center; }
    .btn-template:hover { background: #ecfdf5; }
</style>
@endsection

@section('content')

<div class="import-card">
    <div class="import-title">Import Bulk Siswa</div>

    <div class="help-box">
        <div class="help-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Petunjuk Import
        </div>
        <ul class="help-list">
            <li>Gunakan template Excel yang sudah disediakan di bawah untuk menghindari kesalahan format.</li>
            <li><strong>Sheet Bantuan ID Kelas:</strong> Lihat sheet kedua pada Excel untuk daftar ID Kelas yang benar.</li>
            <li><strong>NIS:</strong> Nomor Induk Siswa dapat diisi secara opsional.</li>
            <li>Pastikan Email bersifat unik (belum terdaftar di sistem).</li>
            <li>Password minimal 8 karakter.</li>
        </ul>
    </div>

    @if(session('error'))
    <div style="margin-bottom: 20px; background: #fef2f2; border-left: 4px solid #f87171; padding: 14px 16px; border-radius: 8px; font-size: 14px; color: #991b1b;">
        {{ session('error') }}
    </div>
    @endif

    <a href="{{ route('admin.users.template') }}" class="btn-template">
        <svg style="width:18px;height:18px;margin-right:8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Download Template Excel (.xlsx)
    </a>

    <form action="{{ route('admin.users.storeImport') }}" method="POST" enctype="multipart/form-data" id="importForm">
        @csrf
        <div class="form-group">
            <label class="form-label" for="file_excel">Upload File Excel</label>
            <div class="file-input-wrapper" onclick="document.getElementById('file_excel').click()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19h6m-3-3v3"/></svg>
                <div class="file-input-text" id="fileName">Klik atau Drag file Excel ke sini</div>
                <div class="file-input-sub">Format: .xlsx, .xls, .csv (Max 5MB)</div>
                <input type="file" name="file_excel" id="file_excel" style="display: none" accept=".xlsx,.xls,.csv" onchange="updateFileName(this)">
            </div>
            @error('file_excel')
                <div style="color: #dc2626; font-size: 12px; margin-top: 4px;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">
            <svg style="width:18px;height:18px;margin-right:8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Mulai Import Siswa
        </button>
    </form>
</div>

<script>
    function updateFileName(input) {
        const fileName = input.files[0] ? input.files[0].name : "Klik atau Drag file Excel ke sini";
        document.getElementById('fileName').textContent = fileName;
        document.querySelector('.file-input-wrapper').style.borderColor = "#10b981";
        document.querySelector('.file-input-wrapper').style.background = "#ecfdf5";
    }
</script>

@endsection
