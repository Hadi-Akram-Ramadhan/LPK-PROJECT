@extends('layouts.guru')

@section('header', 'Bank Soal')
@section('header-sub', 'Panel Guru / Bank Soal')

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
</style>
@endsection

@section('content')

@if(session('success'))
<div style="margin-bottom: 20px; background: #f0fdf4; border-left: 4px solid #4ade80; padding: 14px 18px; border-radius: 8px;">
    <p style="font-size: 14px; color: #15803d; font-weight: 500; margin: 0;">{{ session('success') }}</p>
</div>
@endif

<div class="page-toolbar">
    <div class="toolbar-search">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" placeholder="Cari soal...">
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('guru.import.index') }}" style="display:inline-flex;align-items:center;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;background:#fff;color:#2563eb;border:1.5px solid #2563eb;">
            <svg style="width:16px;height:16px;margin-right:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19h6m-3-3v3"/></svg>
            Import Excel
        </a>
        <a href="{{ route('guru.soal.create') }}" style="display:inline-flex;align-items:center;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;background:#16a34a;color:#fff;border:none;">
            <svg style="width:16px;height:16px;margin-right:6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            + Buat Soal Manual
        </a>
    </div>
</div>

<!-- Filter Tipe -->
<div style="display: flex; gap: 10px; margin-bottom: 24px; align-items: center;">
    <span style="font-size: 13px; font-weight: 600; color: #64748b; margin-right: 6px;">Filter Tipe:</span>
    <a href="{{ route('guru.soal.index') }}" style="display:inline-flex;align-items:center;padding:8px 18px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;{{ !request('tipe') ? 'background:#2563eb;color:#fff;' : 'background:#fff;color:#64748b;border:1.5px solid #e2e8f0;' }}">Semua</a>
    <a href="{{ route('guru.soal.index', ['tipe' => 'pilihan_ganda']) }}" style="display:inline-flex;align-items:center;padding:8px 18px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;{{ request('tipe') == 'pilihan_ganda' ? 'background:#2563eb;color:#fff;' : 'background:#fff;color:#64748b;border:1.5px solid #e2e8f0;' }}">Pilihan Ganda</a>
    <a href="{{ route('guru.soal.index', ['tipe' => 'multiple_choice']) }}" style="display:inline-flex;align-items:center;padding:8px 18px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;{{ request('tipe') == 'multiple_choice' ? 'background:#2563eb;color:#fff;' : 'background:#fff;color:#64748b;border:1.5px solid #e2e8f0;' }}">Multiple Choice</a>
    <a href="{{ route('guru.soal.index', ['tipe' => 'essay']) }}" style="display:inline-flex;align-items:center;padding:8px 18px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;{{ request('tipe') == 'essay' ? 'background:#2563eb;color:#fff;' : 'background:#fff;color:#64748b;border:1.5px solid #e2e8f0;' }}">Essay</a>
    <a href="{{ route('guru.soal.index', ['tipe' => 'audio']) }}" style="display:inline-flex;align-items:center;padding:8px 18px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;{{ request('tipe') == 'audio' ? 'background:#2563eb;color:#fff;' : 'background:#fff;color:#64748b;border:1.5px solid #e2e8f0;' }}">Audio</a>
</div>

<div style="background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden;">
    <table class="soal-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Pertanyaan</th>
                <th>Tipe Soal</th>
                <th>Poin</th>
                <th>Tgl Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($soals as $index => $soal)
            <tr>
                <td>{{ $soals->firstItem() + $index }}</td>
                <td style="max-width: 400px;">
                    <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ strip_tags($soal->pertanyaan) }}
                    </div>
                    @if($soal->audio_path)
                    <div style="margin-top: 4px; display: flex; align-items: center; font-size: 11px; color: #2563eb;">
                        <svg style="width:12px;height:12px;margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M9 16H5a2 2 0 01-2-2V10a2 2 0 012-2h4l5-5v18l-5-5z"></path></svg>
                        Audio
                    </div>
                    @endif
                </td>
                <td>
                    @if($soal->tipe == 'pilihan_ganda')
                        <span class="sbadge sbadge-blue">Pil. Ganda</span>
                    @elseif($soal->tipe == 'multiple_choice')
                        <span class="sbadge sbadge-purple">Multiple</span>
                    @elseif($soal->tipe == 'essay')
                        <span class="sbadge sbadge-green">Essay</span>
                    @elseif($soal->tipe == 'audio')
                        <span class="sbadge sbadge-orange">Audio</span>
                    @endif
                </td>
                <td>{{ $soal->poin }}</td>
                <td style="color: #94a3b8;">{{ $soal->created_at->format('d M Y') }}</td>
                <td>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <a href="{{ route('guru.soal.edit', $soal) }}" style="color:#2563eb;text-decoration:none;font-size:13px;font-weight:600;">Edit</a>
                        <form action="{{ route('guru.soal.destroy', $soal) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus soal ini dari bank soal?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color:#dc2626;background:transparent;border:none;cursor:pointer;font-size:13px;font-weight:600;font-family:'Inter',sans-serif;">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 48px; color: #94a3b8;">Belum ada soal di Bank Soal Anda</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding: 16px 20px; font-size: 13px; color: #94a3b8;">Total {{ $soals->total() }} soal</div>
    @if($soals->hasPages())
    <div style="padding: 12px 20px; border-top: 1px solid #e2e8f0;">
        {{ $soals->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection

