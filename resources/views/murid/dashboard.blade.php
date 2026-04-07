@extends('layouts.murid')

@section('content')
<style>
    /* Dashboard specific styles */
    .header-section { margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; flex-wrap: wrap; }
    .page-title { font-size: 32px; font-weight: 800; color: #1e293b; margin-bottom: 8px; letter-spacing: -0.5px; }
    .page-subtitle { font-size: 15px; color: #64748b; line-height: 1.6; }

    .nav-tabs-container { margin-bottom: 30px; display: flex; flex-wrap: wrap; gap: 15px; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; align-items: center; justify-content: space-between; }
    .nav-tabs { display: flex; gap: 10px; flex-wrap: wrap; }
    .nav-tab { padding: 10px 20px; border-radius: 12px; font-size: 14px; font-weight: 700; color: #64748b; background: transparent; border: 2px solid transparent; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px; }
    .nav-tab:hover { background: #f1f5f9; color: #1e293b; }
    .nav-tab.active { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }

    .search-box { position: relative; width: 300px; }
    .search-box input { width: 100%; padding: 12px 16px 12px 40px; border-radius: 12px; border: 2px solid #e2e8f0; font-size: 14px; outline: none; transition: 0.2s; background: #fff; }
    .search-box input:focus { border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); }
    .search-box svg { position: absolute; left: 14px; top: 14px; width: 18px; height: 18px; color: #94a3b8; }

    .exam-grid { display: grid; grid-template-columns: 1fr; gap: 30px; margin-top: 20px; }
    @media (min-width: 768px) {
        .exam-grid { grid-template-columns: repeat(2, 1fr); }
    }
    .exam-card { background: #fff; border-radius: 20px; border: 1.5px solid #e2e8f0; padding: 32px 40px; transition: all 0.3s; position: relative; overflow: hidden; display: flex; flex-direction: column; }
    .exam-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.05); border-color: #2563eb44; }
    .exam-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px; background: #111827; transition: 0.3s; }
    .exam-card[data-type="tryout"]::before { background: #2563eb; }

    .card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap:10px; }
    .tag { padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid transparent; }

    .tag-category-reguler { background: #f8fafc; color: #475569; border-color: #e2e8f0; }
    .tag-category-tryout { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }

    .tag-available { background: #ecfdf5; color: #10b981; }
    .tag-finished { background: #f1f5f9; color: #475569; }
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

    /* Mobile Responsive Overrides */
    @media (max-width: 640px) {
        .page-title { font-size: 24px; }
        .page-subtitle { font-size: 14px; }

        .header-section { flex-direction: column; align-items: flex-start; gap: 15px; }
        .search-box { width: 100%; }

        .nav-tabs { width: 100%; justify-content: space-between; gap: 5px; }
        .nav-tab { flex: 1; justify-content: center; padding: 12px 10px; font-size: 12px; text-align: center; border-radius: 8px; flex-direction: column; gap: 4px; }
        .nav-tab svg { width: 20px; height: 20px; }

        .exam-card { padding: 24px 20px; }
        .exam-title { font-size: 22px; }
        .exam-desc { font-size: 14px; margin-bottom: 20px; }
        .exam-meta { padding: 14px 16px; gap: 15px; }
        .btn-action { padding: 14px; font-size: 14px; }
    }
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
            <h1 class="page-title">Halo, {{ auth()->user()->name }} 👋</h1>
            <p class="page-subtitle">
                Selamat datang di UBT Learning LPK URISOWON.<br>
                Terdapat <strong>{{ $ujianPesertas->count() }} Jadwal Ujian</strong> yang ditugaskan untuk Anda saat ini.
            </p>
        </div>
    </div>

    <!-- Navigation Tabs & Search -->
    <div class="nav-tabs-container">
        <div class="nav-tabs" id="filter-tabs">
            <button class="nav-tab active" data-filter="all">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Semua Ujian
            </button>
            <button class="nav-tab" data-filter="reguler">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Ujian Reguler
            </button>
            <button class="nav-tab" data-filter="tryout">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Ujian Try-Out
            </button>
        </div>

        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="search-input" placeholder="Cari judul ujian...">
        </div>
    </div>

    <div class="exam-grid" id="exam-grid">
        @foreach($ujianPesertas as $peserta)
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
                    $btnLabel = 'Pukul ' . \Carbon\Carbon::parse($ujian->mulai)->format('H:i');
                    $isDisabled = true;
                } else {
                    $statusTag = 'tag-available';
                    $statusLabel = 'TERSEDIA';
                }
            @endphp

            <div class="exam-card exam-card-wrapper" data-type="{{ $ujian->jenis_ujian }}">
                <div class="card-top">
                    <span class="tag {{ $ujian->jenis_ujian === 'tryout' ? 'tag-category-tryout' : 'tag-category-reguler' }}">{{ $ujian->jenis_ujian === 'tryout' ? 'TRY-OUT' : 'REGULER' }}</span>
                    <span class="tag {{ $statusTag }}">{{ $statusLabel }}</span>
                </div>

                <h3 class="exam-title exam-title-text">{{ $ujian->judul }}</h3>
                <p class="exam-desc">{{ $ujian->deskripsi ?? 'Pilih ujian ini untuk meraba kemampuan dan persiapan evaluasi akhir.' }}</p>

                <div class="exam-meta">
                    <div class="meta-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $ujian->durasi }} Menit
                    </div>
                    <div class="meta-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
        @endforeach

        @if($ujianPesertas->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                </div>
                <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 10px;">Belum ada ujian ditugaskan</h3>
                <p style="color: #64748b; font-size: 15px;">Daftar ujian yang disinkronisasi belum memuat jadwal untuk kelas Anda.</p>
            </div>
        @endif

        {{-- No Search Results --}}
        <div id="no-results" class="empty-state" style="display: none;">
            <div class="empty-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <h3 style="font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 10px;">Ujian tidak ditemukan</h3>
            <p style="color: #64748b; font-size: 15px;">Coba ubah tab filter kategori atau kurangi kata kunci pencarian Anda.</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const examCards = document.querySelectorAll('.exam-card-wrapper');
        const noResults = document.getElementById('no-results');
        const tabs = document.querySelectorAll('.nav-tab');

        let currentFilter = 'all';
        let currentSearch = '';

        function runFilter() {
            let hasResults = false;

            examCards.forEach(card => {
                const type = card.getAttribute('data-type') || 'reguler'; // fallback
                const titleElement = card.querySelector('.exam-title-text');
                const descElement = card.querySelector('.exam-desc');

                const titleMatch = titleElement && titleElement.textContent.toLowerCase().includes(currentSearch);
                const descMatch = descElement && descElement.textContent.toLowerCase().includes(currentSearch);
                const searchMatch = currentSearch === '' || titleMatch || descMatch;

                const typeMatch = currentFilter === 'all' || type === currentFilter;

                if (searchMatch && typeMatch) {
                    card.style.display = 'flex';
                    hasResults = true;
                } else {
                    card.style.display = 'none';
                }
            });

            if (noResults) {
                noResults.style.display = hasResults ? 'none' : 'block';
            }
        }

        // Search Listener
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                currentSearch = this.value.toLowerCase().trim();
                runFilter();
            });
        }

        // Tab Listeners
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Update active class
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Update filter and run
                currentFilter = this.getAttribute('data-filter');
                runFilter();
            });
        });
    });
</script>
@endsection
