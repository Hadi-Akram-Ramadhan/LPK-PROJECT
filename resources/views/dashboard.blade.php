@extends('layouts.murid')

@section('content')
<style>
    .dashboard-container { max-width: 1100px; margin: 0 auto; padding: 40px 20px; }
    .header-section { margin-bottom: 40px; }
    .page-title { font-size: 32px; font-weight: 800; color: #1e293b; margin-bottom: 12px; letter-spacing: -0.5px; }
    .page-subtitle { font-size: 15px; color: #64748b; line-height: 1.6; }
    
    .exam-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 24px; }
    .exam-card { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; padding: 32px; transition: all 0.3s; position: relative; overflow: hidden; display: flex; flex-direction: column; }
    .exam-card:hover { transform: translateY(-4px); shadow: 0 12px 24px rgba(0,0,0,0.05); border-color: #cbd5e1; }
    .exam-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px; background: #111827; }
    
    .card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .tag { padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
    .tag-blue { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
    .tag-green { background: #ecfdf5; color: #10b981; }
    .tag-gray { background: #f1f5f9; color: #94a3b8; }
    
    .exam-title { font-size: 22px; font-weight: 800; color: #1e293b; margin-bottom: 12px; line-height: 1.2; }
    .exam-desc { font-size: 14px; color: #64748b; line-height: 1.6; margin-bottom: 24px; flex-grow: 1; }
    
    .exam-meta { background: #f8fafc; border-radius: 12px; padding: 14px 20px; display: flex; gap: 20px; align-items: center; margin-bottom: 28px; border: 1px solid #f1f5f9; }
    .meta-item { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: #475569; }
    .meta-item svg { width: 16px; height: 16px; color: #3b82f6; }
    
    .btn-start { width: 100%; padding: 14px; border-radius: 12px; background: #111827; color: #fff; border: none; font-size: 15px; font-weight: 700; cursor: pointer; transition: 0.2s; }
    .btn-start:hover { background: #1e293b; transform: scale(1.02); }
    .btn-disabled { width: 100%; padding: 14px; border-radius: 12px; background: #f1f5f9; color: #94a3b8; border: none; font-size: 15px; font-weight: 700; cursor: not-allowed; }
</style>

<div class="dashboard-container">
    <div class="header-section">
        <h1 class="page-title">Daftar Ujian Tersedia</h1>
        <p class="page-subtitle">
            Pilih paket ujian di bawah ini untuk memulai simulasi.<br>
            Pastikan koneksi internet Anda stabil sebelum memulai ujian.
        </p>
    </div>

    <div class="exam-grid">
        <!-- Card 1 -->
        <div class="exam-card">
            <div class="card-top">
                <span class="tag tag-blue">EPS-TOPIK</span>
                <span class="tag tag-green">Tersedia</span>
            </div>
            <h3 class="exam-title">EPS TOPIK - Reading</h3>
            <p class="exam-desc">Tes kemampuan membaca bahasa Korea untuk persiapan ujian EPS TOPIK resmi.</p>
            <div class="exam-meta">
                <div class="meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    60 Menit
                </div>
                <div class="meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    40 Soal
                </div>
            </div>
            <button class="btn-start">Mulai Ujian Sekarang</button>
        </div>

        <!-- Card 2 -->
        <div class="exam-card">
            <div class="card-top">
                <span class="tag tag-blue">EPS-TOPIK</span>
                <span class="tag tag-gray">Belum Tersedia</span>
            </div>
            <h3 class="exam-title">EPS TOPIK - Listening</h3>
            <p class="exam-desc">Tes kemampuan mendengar bahasa Korea (Choukai). Paket audio masih dipersiapkan.</p>
            <div class="exam-meta">
                <div class="meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    40 Menit
                </div>
                <div class="meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    20 Soal
                </div>
            </div>
            <button class="btn-disabled">Tidak Tersedia</button>
        </div>
    </div>

    <!-- Footer -->
    <div style="margin-top: 80px; padding: 24px 0; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; color: #94a3b8; font-size: 13px;">
        <div>&copy; 2025 Lembaga Pelatihan Kerja (LPK). All rights reserved.</div>
        <div style="display: flex; gap: 20px;">
            <a href="#" style="color: inherit; text-decoration: none;">Bantuan</a>
            <a href="#" style="color: inherit; text-decoration: none;">Privasi</a>
        </div>
    </div>
</div>
@endsection
