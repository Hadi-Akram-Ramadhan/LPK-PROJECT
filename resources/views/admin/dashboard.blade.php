@extends('layouts.admin')

@section('header', 'Dashboard')
@section('header-sub', 'Selamat datang di panel admin')

@section('content')
<style>
    /* Stat Cards */
    .stat-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px; }
    .stat-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 24px; display: flex; align-items: center; gap: 16px; }
    .stat-icon { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-icon svg { width: 24px; height: 24px; }
    .stat-icon.blue { background: #dbeafe; color: #2563eb; }
    .stat-icon.green { background: #dcfce7; color: #16a34a; }
    .stat-icon.purple { background: #ede9fe; color: #7c3aed; }
    .stat-icon.orange { background: #ffedd5; color: #ea580c; }
    .stat-val { font-size: 28px; font-weight: 800; color: #1e293b; }
    .stat-label { font-size: 13px; color: #94a3b8; margin-top: 2px; }

    /* Bottom sections */
    .bottom-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .section-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden; }
    .section-head { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #f1f5f9; }
    .section-title { font-size: 16px; font-weight: 700; color: #1e293b; }
    .section-link { font-size: 13px; color: #2563eb; text-decoration: none; font-weight: 500; }
    .section-link:hover { text-decoration: underline; }

    /* Exam list items */
    .exam-item { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .exam-item:last-child { border-bottom: none; }
    .exam-name { font-size: 14px; font-weight: 600; color: #1e293b; }
    .exam-meta { font-size: 12px; color: #94a3b8; margin-top: 3px; }
    .exam-badge { display: inline-block; padding: 4px 16px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .exam-badge.aktif { background: #dcfce7; color: #16a34a; }
    .exam-badge.selesai { background: #f1f5f9; color: #64748b; }

    /* Result items */
    .result-item { padding: 16px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .result-item:last-child { border-bottom: none; }
    .result-name { font-size: 14px; font-weight: 600; color: #1e293b; }
    .result-sub { font-size: 12px; color: #94a3b8; margin-top: 2px; }
    .result-score { font-size: 16px; font-weight: 700; color: #2563eb; text-align: right; }
    .result-grade { font-size: 12px; color: #94a3b8; margin-top: 2px; text-align: right; }

    @media (max-width: 1024px) {
        .stat-cards { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 768px) {
        .stat-cards { grid-template-columns: 1fr; }
        .bottom-grid { grid-template-columns: 1fr; }
    }
</style>

<!-- Stat Cards -->
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon blue">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div>
            <div class="stat-val">3</div>
            <div class="stat-label">Total Siswa</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <div>
            <div class="stat-val">2</div>
            <div class="stat-label">Ujian Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="stat-val">13</div>
            <div class="stat-label">Total Soal</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
        <div>
            <div class="stat-val">87.5%</div>
            <div class="stat-label">Rata-rata Nilai</div>
        </div>
    </div>
</div>

<!-- Bottom Sections -->
<div class="bottom-grid">
    <!-- Ujian Terbaru -->
    <div class="section-card">
        <div class="section-head">
            <div class="section-title">Ujian Terbaru</div>
            <a href="#" class="section-link">Lihat semua</a>
        </div>
        <div class="exam-item">
            <div>
                <div class="exam-name">tes ngaji</div>
                <div class="exam-meta">10 soal · 1 peserta · 0 selesai</div>
            </div>
            <span class="exam-badge aktif">Aktif</span>
        </div>
        <div class="exam-item">
            <div>
                <div class="exam-name">tes</div>
                <div class="exam-meta">3 soal · 2 peserta · 1 selesai</div>
            </div>
            <span class="exam-badge aktif">Aktif</span>
        </div>
    </div>

    <!-- Hasil Ujian Terbaru -->
    <div class="section-card">
        <div class="section-head">
            <div class="section-title">Hasil Ujian Terbaru</div>
            <a href="#" class="section-link">Lihat semua</a>
        </div>
        <div class="result-item">
            <div>
                <div class="result-name">Budi Santoso</div>
                <div class="result-sub">tes</div>
            </div>
            <div>
                <div class="result-score">87.50%</div>
                <div class="result-grade">Grade: A</div>
            </div>
        </div>
    </div>
</div>
@endsection
