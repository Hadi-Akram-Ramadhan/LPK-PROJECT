@extends('layouts.guru')

@section('header', 'Bank Soal')
@section('header-sub', 'Guru / Bank Soal')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:14px 20px;border-radius:12px;margin-bottom:20px;font-size:14px;display:flex;align-items:center;gap:10px;">
    <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- Header action --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;gap:16px;">
    <div style="display:flex;align-items:center;gap:16px;">
        <div>
            <h2 style="font-size:16px;font-weight:700;color:#1e293b;margin:0;">Daftar Paket Soal</h2>
            <p style="font-size:13px;color:#94a3b8;margin:4px 0 0;">{{ $pakets->total() }} paket tersedia</p>
        </div>
        <form action="{{ route('guru.paket-soal.index') }}" method="GET" style="position:relative;">
            <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama paket..." style="padding:10px 14px 10px 38px;border:1px solid #e2e8f0;border-radius:10px;font-size:13px;outline:none;width:240px;background:#fff;font-family:'Inter',sans-serif;">
        </form>
    </div>
    <a href="{{ route('guru.paket-soal.create') }}" style="display:inline-flex;align-items:center;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;background:#2563eb;color:#fff;gap:8px;flex-shrink:0;">
        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Buat Paket Baru
    </a>
</div>

@if($pakets->isEmpty())
<div style="background:#fff;border:1px dashed #e2e8f0;border-radius:16px;padding:60px;text-align:center;">
    <svg style="width:48px;height:48px;color:#cbd5e1;margin:0 auto 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    <p style="color:#94a3b8;font-size:14px;margin:0 0 16px;">Belum ada paket soal. Buat paket pertama Anda!</p>
    <a href="{{ route('guru.paket-soal.create') }}" style="display:inline-flex;align-items:center;padding:10px 24px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;background:#2563eb;color:#fff;">+ Buat Paket Soal</a>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px;">
    @foreach($pakets as $paket)
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:24px;position:relative;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 4px 20px rgba(0,0,0,.08)'" onmouseout="this.style.boxShadow='none'">
        {{-- Jumlah soal badge --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
            <span style="background:#dbeafe;color:#2563eb;font-size:12px;font-weight:700;padding:4px 12px;border-radius:20px;">{{ $paket->soals_count }} Soal</span>
            <div style="display:flex;gap:6px;">
                <form action="{{ route('guru.paket-soal.duplicate', $paket) }}" method="POST" onsubmit="return confirm('Duplikat paket soal ini beserta seluruh soalnya?')">
                    @csrf
                    <button type="submit" style="padding:6px;border-radius:8px;border:1px solid #e2e8f0;background:transparent;cursor:pointer;color:#2563eb;display:flex;align-items:center;" title="Duplikat Paket">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                    </button>
                </form>
                @if($paket->guru_id === auth()->id())
                <a href="{{ route('guru.paket-soal.edit', $paket) }}" style="padding:6px;border-radius:8px;border:1px solid #e2e8f0;color:#64748b;text-decoration:none;display:flex;align-items:center;" title="Edit Paket">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <form action="{{ route('guru.paket-soal.destroy', $paket) }}" method="POST" onsubmit="return confirm('Hapus paket ini beserta SEMUA soalnya? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf @method('DELETE')
                    <button type="submit" style="padding:6px;border-radius:8px;border:1px solid #e2e8f0;background:transparent;cursor:pointer;color:#ef4444;display:flex;align-items:center;" title="Hapus Paket">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
                @endif
            </div>
        </div>

        <a href="{{ route('guru.paket-soal.show', $paket) }}" style="text-decoration:none;">
            <h3 style="font-size:16px;font-weight:700;color:#1e293b;margin:0 0 6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $paket->nama }}">{{ $paket->nama }}</h3>
            @if($paket->deskripsi)
            <p style="font-size:13px;color:#94a3b8;margin:0 0 16px;line-height:1.5;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">{{ $paket->deskripsi }}</p>
            @else
            <p style="font-size:13px;color:#cbd5e1;margin:0 0 16px;font-style:italic;">Tanpa deskripsi</p>
            @endif

            <div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid #f1f5f9;padding-top:14px;">
                <span style="font-size:12px;color:#94a3b8;">Oleh: <strong style="color:#6366f1;">{{ $paket->guru->name ?? '-' }}</strong></span>
                <span style="font-size:12px;color:#2563eb;font-weight:600;display:flex;align-items:center;gap:4px;">
                    Lihat Soal
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            </div>
        </a>
    </div>
    @endforeach
</div>
@if($pakets->hasPages())
<div style="margin-top:24px;background:#fff;padding:16px;border-radius:12px;border:1px solid #e2e8f0;">
    {{ $pakets->appends(request()->query())->links() }}
</div>
@endif
@endif

@endsection

