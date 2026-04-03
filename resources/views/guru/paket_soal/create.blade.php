@extends('layouts.guru')

@section('header')
<div style="display:flex;align-items:center;gap:12px;">
    <a href="{{ route('guru.paket-soal.index') }}" style="color:#94a3b8;text-decoration:none;display:flex;align-items:center;">
        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <span>Buat Paket Soal Baru</span>
</div>
@endsection
@section('header-sub', 'Admin / Bank Soal / Buat Paket')

@section('content')
<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:16px;border:1px solid #e2e8f0;padding:32px;">
    <form action="{{ route('guru.paket-soal.store') }}" method="POST">
        @csrf
        <div style="margin-bottom:20px;">
            <label style="display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:8px;">Nama Paket Soal *</label>
            <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Bahasa Korea Level 1, JLPT N5 Listening..."
                style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;box-sizing:border-box;"
                onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
            @error('nama') <p style="color:#ef4444;font-size:12px;margin:6px 0 0;">{{ $message }}</p> @enderror
        </div>

        <div style="margin-bottom:28px;">
            <label style="display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:8px;">Deskripsi (Opsional)</label>
            <textarea name="deskripsi" rows="4" placeholder="Deskripsi singkat tentang paket soal ini..."
                style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:10px;font-size:14px;outline:none;resize:vertical;box-sizing:border-box;"
                onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">{{ old('deskripsi') }}</textarea>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;padding-top:20px;border-top:1px solid #f1f5f9;">
            <a href="{{ route('guru.paket-soal.index') }}" style="padding:10px 20px;border-radius:10px;border:1px solid #e2e8f0;font-size:13px;font-weight:600;color:#64748b;text-decoration:none;">Batal</a>
            <button type="submit" style="padding:10px 24px;border-radius:10px;background:#2563eb;color:#fff;font-size:13px;font-weight:600;border:none;cursor:pointer;">
                Simpan Paket
            </button>
        </div>
    </form>
</div>
@endsection

