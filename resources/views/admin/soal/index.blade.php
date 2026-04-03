@extends('layouts.admin')

@section('header', 'Bank Soal')
@section('header-sub', 'Admin / Soal')

@section('extra-css')
<style>
    .page-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .toolbar-search { position: relative; }
    .toolbar-search input { padding: 10px 14px 10px 40px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13px; outline: none; background: #fff; font-family: 'Inter', sans-serif; width: 280px; }
    .toolbar-search input:focus { border-color: #3b82f6; }
    .toolbar-search svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #94a3b8; }

    .soal-table { width: 100%; border-collapse: collapse; }
    .soal-table th { text-align: left; padding: 14px 20px; font-size: 11px; font-weight: 700; color: #2563eb; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
    .soal-table td { padding: 18px 20px; font-size: 14px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .soal-table tr:hover td { background: #f8fafc; }
    .sbadge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .sbadge-blue { background: #dbeafe; color: #2563eb; }
    .sbadge-purple { background: #ede9fe; color: #7c3aed; }
    .sbadge-green { background: #dcfce7; color: #16a34a; }
    .sbadge-orange { background: #ffedd5; color: #ea580c; }
    .stat-row { display: flex; gap: 16px; margin-bottom: 24px; }
    .stat-pill { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 20px 28px; flex: 1; text-align: center; }
    .stat-pill-val { font-size: 28px; font-weight: 800; color: #1e293b; }
    .stat-pill-label { font-size: 13px; color: #94a3b8; margin-top: 4px; }
</style>
@endsection

@section('content')

<div class="page-toolbar">
    <div class="toolbar-search">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" placeholder="Cari soal...">
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('admin.soal.import') }}" style="display:inline-flex;align-items:center;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;background:#fff;color:#2563eb;border:1.5px solid #2563eb;">
            <svg style="width:16px;height:16px;margin-right:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19h6m-3-3v3"/></svg>
            Import Excel
        </a>
        <a href="{{ route('admin.soal.create') }}" style="display:inline-flex;align-items:center;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;background:#16a34a;color:#fff;border:none;">
            <svg style="width:16px;height:16px;margin-right:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            + Buat Soal Manual
        </a>
    </div>
</div>

<!-- Filter Tipe -->
<div style="display: flex; gap: 10px; margin-bottom: 24px;">
    <a href="{{ route('admin.soal.index') }}" style="display:inline-flex;align-items:center;padding:8px 18px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;{{ !request('tipe') ? 'background:#2563eb;color:#fff;' : 'background:#fff;color:#64748b;border:1.5px solid #e2e8f0;' }}">Semua</a>
    <a href="{{ route('admin.soal.index', ['tipe' => 'pilihan_ganda']) }}" style="display:inline-flex;align-items:center;padding:8px 18px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;{{ request('tipe') == 'pilihan_ganda' ? 'background:#2563eb;color:#fff;' : 'background:#fff;color:#64748b;border:1.5px solid #e2e8f0;' }}">Pilihan Ganda</a>
    <a href="{{ route('admin.soal.index', ['tipe' => 'audio']) }}" style="display:inline-flex;align-items:center;padding:8px 18px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;{{ request('tipe') == 'audio' ? 'background:#2563eb;color:#fff;' : 'background:#fff;color:#64748b;border:1.5px solid #e2e8f0;' }}">Audio</a>
    <a href="{{ route('admin.soal.index', ['tipe' => 'essay']) }}" style="display:inline-flex;align-items:center;padding:8px 18px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;{{ request('tipe') == 'essay' ? 'background:#2563eb;color:#fff;' : 'background:#fff;color:#64748b;border:1.5px solid #e2e8f0;' }}">Essay</a>
</div>

<div class="stat-row">
    <div class="stat-pill">
        <div class="stat-pill-val">{{ $totalSoal }}</div>
        <div class="stat-pill-label">Total Soal</div>
    </div>
    <div class="stat-pill">
        <div class="stat-pill-val">{{ $soals->where('tipe', 'pilihan_ganda')->count() }}</div>
        <div class="stat-pill-label">Pilihan Ganda</div>
    </div>
    <div class="stat-pill">
        <div class="stat-pill-val">{{ $soals->where('tipe', 'audio')->count() }}</div>
        <div class="stat-pill-label">Audio</div>
    </div>
    <div class="stat-pill">
        <div class="stat-pill-val">{{ $soals->where('tipe', 'essay')->count() }}</div>
        <div class="stat-pill-label">Essay</div>
    </div>
</div>

<div style="background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden;">
    <table class="soal-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Pertanyaan</th>
                <th>Tipe</th>
                <th>Poin</th>
                <th>Dibuat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($soals as $index => $soal)
            <tr>
                <td>{{ $soals->firstItem() + $index }}</td>
                <td style="max-width: 400px;">
                    <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {!! strip_tags($soal->pertanyaan) !!}
                    </div>
                </td>
                <td>
                    @if($soal->tipe === 'pilihan_ganda')
                        <span class="sbadge sbadge-blue">Pilihan Ganda</span>
                    @elseif($soal->tipe === 'audio')
                        <span class="sbadge sbadge-orange">Audio</span>
                    @elseif($soal->tipe === 'essay')
                        <span class="sbadge sbadge-green">Essay</span>
                    @elseif($soal->tipe === 'multiple_choice')
                        <span class="sbadge sbadge-purple">Multiple Choice</span>
                    @endif
                </td>
                <td>{{ $soal->poin }}</td>
                <td style="color: #94a3b8;">{{ $soal->guru->name ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 48px; color: #94a3b8;">Belum ada soal</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding: 16px 20px; font-size: 13px; color: #94a3b8;">Total {{ $totalSoal }} soal</div>
    @if($soals->hasPages())
    <div style="padding: 12px 20px; border-top: 1px solid #e2e8f0;">
        {{ $soals->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
