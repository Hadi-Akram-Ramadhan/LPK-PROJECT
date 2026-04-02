@extends('layouts.murid')

@section('content')
<style>
    /* Dashboard specific styles */
    .header-section { margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; flex-wrap: wrap; }
    .page-title { font-size: 32px; font-weight: 800; color: #1e293b; margin-bottom: 12px; letter-spacing: -0.5px; }
    .page-subtitle { font-size: 15px; color: #64748b; line-height: 1.6; }
    
    .search-box { position: relative; width: 300px; }
    .search-box input { width: 100%; padding: 12px 16px 12px 40px; border-radius: 10px; border: 1.5px solid #e2e8f0; font-size: 14px; outline: none; transition: 0.2s; }
    .search-box input:focus { border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
    .search-box svg { position: absolute; left: 14px; top: 14px; width: 16px; height: 16px; color: #94a3b8; }

    .exam-grid { display: grid; grid-template-columns: 1fr; gap: 30px; margin-top: 20px; }
    @media (min-width: 768px) {
        .exam-grid { grid-template-columns: repeat(2, 1fr); }
    }
    .exam-card { background: #fff; border-radius: 20px; border: 1.5px solid #e2e8f0; padding: 32px 40px; transition: all 0.3s; position: relative; overflow: hidden; display: flex; flex-direction: column; }
    .exam-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.05); border-color: #2563eb44; }
    .exam-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px; background: #111827; }
    
    .card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .tag { padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid transparent; }
    
    .tag-category { background: #f8fafc; color: #64748b; border-color: #e2e8f0; }
    .tag-available { background: #ecfdf5; color: #10b981; }
    .tag-finished { background: #eff6ff; color: #2563eb; }
    .tag-doing { background: #fffbeb; color: #d97706; border-color: #fef3c7; }
    .tag-blocked { background: #fef2f2; color: #ef4444; }
    .tag-time { background: #f1f5f9; color: #475569; }

    .exam-title { font-size: 28px; font-weight: 800; color: #1e293b; margin-bottom: 16px; line-height: 1.2; }
    .exam-desc { font-size: 16px; color: #64748b; line-height: 1.6; margin-bottom: 30px; flex-grow: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    
    .exam-meta { background: #f8fafc; border-radius: 14px; padding: 18px 22px; display: flex; flex-wrap: wrap; gap: 20px; align-items: center; margin-bottom: 30px; border: 1px solid #f1f5f9; }
    .meta-item { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 600; color: #475569; }
    .meta-item svg { width: 18px; height: 18px; color: #3b82f6; flex-shrink: 0; }
    
    .btn-action { width: 100%; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: 0.2s; border: none; text-align: center; text-decoration: none; display: inline-block; }
    .btn-primary { background: #111827; color: #fff; }
    .btn-primary:hover { background: #1e293b; transform: scale(1.01); }
    .btn-success { background: #10b981; color: #fff; }
    .btn-danger { background: #ef4444; color: #fff; }
    .btn-disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }

    .alert { padding: 16px 20px; border-radius: 12px; margin-bottom: 30px; font-size: 14px; font-weight: 600; }
    .alert-success { background: #ecfdf5; color: #065f46; border-left: 4px solid #10b981; }

    .empty-state { grid-column: 1 / -1; background: #fff; border-radius: 20px; padding: 80px 40px; text-align: center; border: 1.5px dashed #e2e8f0; }
    .empty-icon { width: 64px; height: 64px; background: #f8fafc; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; color: #94a3b8; }
    .empty-icon svg { width: 32px; height: 32px; }
</style>

<div class="dashboard-wrapper">
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger" style="background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; padding: 16px 20px; border-radius: 12px; margin-bottom: 30px; font-size: 14px; font-weight: 600;">
        {{ session('error') }}
    </div>
    @endif

    <div class="header-section">
        <div>
            <h1 class="page-title">Daftar Ujian Tersedia</h1>
            <p class="page-subtitle">
                Pilih paket ujian di bawah ini untuk memulai simulasi.<br>
                Pastikan koneksi internet Anda stabil sebelum memulai.
            </p>
        </div>
        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" placeholder="Cari ujian...">
        </div>
    </div>

    <div class="exam-grid">
        @forelse($ujianPesertas as $peserta)
            @php
                $ujian = $peserta->ujian;
                $now = \Carbon\Carbon::now();
                $isBelumWaktunya = $ujian->mulai && $now->lt(\Carbon\Carbon::parse($ujian->mulai));
                $isTerlambat = $ujian->selesai && $now->gt(\Carbon\Carbon::parse($ujian->selesai)) && $peserta->status == 'belum_mulai';
                
                $statusTag = '';
                $statusLabel = '';
                $btnColor = 'btn-primary';
                $btnLabel = 'Mulai Ujian Sekarang';
                $isDisabled = false;
                $route = route('murid.exam.start', $peserta);
                $isPost = true;

                if ($peserta->status === 'selesai') {
                    $statusTag = 'tag-finished';
                    $statusLabel = 'SELESAI (SKOR: '.$peserta->skor.')';
                    $btnLabel = 'Lihat Hasil & Rekap';
                    $route = route('murid.exam.result', $peserta);
                    $isPost = false;
                } elseif ($peserta->status === 'mengerjakan') {
                    $statusTag = 'tag-doing';
                    $statusLabel = 'SEDANG DIKERJAKAN';
                    $btnColor = 'btn-success';
                    $btnLabel = 'Lanjutkan Ujian';
                    $route = route('murid.exam.show', $peserta);
                    $isPost = false;
                } elseif ($peserta->status === 'diblokir') {
                    $statusTag = 'tag-blocked';
                    $statusLabel = 'TERBLOKIR';
                    $btnColor = 'btn-danger';
                    $btnLabel = 'Info Pemblokiran';
                    $route = route('murid.exam.blocked', $peserta);
                    $isPost = false;
                } elseif ($isTerlambat) {
                    $statusTag = 'tag-time';
                    $statusLabel = 'DITUTUP';
                    $btnColor = 'btn-disabled';
                    $btnLabel = 'Waktu Habis';
                    $isDisabled = true;
                } elseif ($isBelumWaktunya) {
                    $statusTag = 'tag-time';
                    $statusLabel = 'BELUM DIBUKA';
                    $btnColor = 'btn-disabled';
                    $btnLabel = 'Belum Dimulai';
                    $isDisabled = true;
                } else {
                    $statusTag = 'tag-available';
                    $statusLabel = 'TERSEDIA';
                }
            @endphp

            <div class="exam-card">
                <div class="card-top">
                    <span class="tag tag-category">{{ $ujian->kategori ?? 'UMUM' }}</span>
                    <span class="tag {{ $statusTag }}">{{ $statusLabel }}</span>
                </div>
                
                <h3 class="exam-title">{{ $ujian->judul }}</h3>
                <p class="exam-desc">{{ $ujian->deskripsi ?? 'Klik tombol di bawah untuk mengerjakan ujian ini sesuai instruksi.' }}</p>
                
                <div class="exam-meta">
                    <div class="meta-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $ujian->durasi }} Menit
                    </div>
                    <div class="meta-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $ujian->soals()->count() }} Soal
                    </div>
                </div>

                @if($isDisabled)
                    <button class="btn-action btn-disabled" disabled>{{ $btnLabel }}</button>
                @else
                    @if($isPost)
                        <form action="{{ $route }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn-action {{ $btnColor }}">{{ $btnLabel }}</button>
                        </form>
                    @else
                        <a href="{{ $route }}" class="btn-action {{ $btnColor }}">{{ $btnLabel }}</a>
                    @endif
                @endif
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                </div>
                <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 10px;">Belum ada ujian ditugaskan</h3>
                <p style="color: #64748b; font-size: 15px;">Silakan hubungi pengelola LPK jika anda merasa seharusnya memiliki jadwal ujian hari ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
