@extends('layouts.murid')

@section('header')
<div style="display: flex; align-items: center; color: #64748b;">
    <svg style="width: 24px; height: 24px; margin-right: 8px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <span style="font-weight: 600;">Hasil Ujian Anda</span>
</div>
@endsection

@section('content')

<style>
    /* Global overrides to isolate the result card */
    .murid-nav, footer { display: none !important; }
    .main-content { 
        padding: 0 !important; 
        margin: 0 !important; 
        max-width: none !important; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        min-height: 100vh !important; 
    }

    .result-container {
        padding: 40px 20px;
        max-width: 1080px; /* Increased from 950px */
        width: 100%;
    }
    .result-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        overflow: hidden;
        display: flex;
        min-height: 580px; /* Increased from 520px */
        border: 1px solid #e2e8f0;
    }
    .result-left {
        width: 42%;
        background: #0f172a;
        color: #ffffff;
        padding: 60px 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
    }
    .result-right {
        width: 58%;
        padding: 60px 50px;
        background: white;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .score-badge {
        background: #1e293b;
        border-radius: 20px;
        padding: 30px 40px;
        margin: 24px 0;
        width: 100%;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.2);
    }
    .score-value {
        font-size: 80px;
        font-weight: 900;
        line-height: 1;
        display: block;
        margin-top: 8px;
        color: #ffffff;
    }
    .status-pill {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 2px;
        padding: 6px 16px;
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border-radius: 40px;
        text-transform: uppercase;
        margin-bottom: 20px;
    }
    .btn-dashboard {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #0f172a;
        color: white;
        text-decoration: none;
        padding: 18px 30px;
        border-radius: 16px;
        font-weight: 800;
        font-size: 14px;
        letter-spacing: 1px;
        transition: transform 0.2s, background 0.2s;
        margin-top: 20px;
    }
    .btn-dashboard:hover {
        background: #000000;
        transform: translateY(-2px);
    }
    .info-grid {
        display: flex;
        gap: 30px;
        margin-top: 30px;
    }
    .info-item {
        display: flex;
        align-items: center;
    }
    .info-icon {
        background: #f8fafc;
        padding: 10px;
        border-radius: 12px;
        margin-right: 14px;
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .alert-box {
        background: #fffbeb;
        border: 1px solid #fef3c7;
        padding: 20px;
        border-radius: 16px;
        margin-top: 30px;
        display: flex;
        gap: 14px;
    }

    .result-brand { 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        gap: 12px; 
        margin-bottom: 30px; 
    }
    .result-brand img { width: 40px; height: 40px; object-fit: contain; }
    .result-brand span { font-size: 18px; font-weight: 800; letter-spacing: -0.3px; color: #fff; }

    @media (max-width: 768px) {
        .result-container {
            padding: 20px 15px;
        }
        .result-card {
            flex-direction: column;
            border-radius: 20px;
            min-height: auto;
        }
        .result-left, .result-right {
            width: 100%;
            padding: 30px 24px;
        }
        .result-left {
            padding-bottom: 35px;
        }
        .result-brand {
            margin-bottom: 20px;
        }
        .result-brand img { width: 32px; height: 32px; }
        .result-brand span { font-size: 15px; }
        
        .result-left h2 { font-size: 20px !important; }
        .score-value { font-size: 64px; margin-top: 4px; }
        .score-badge { padding: 20px; margin: 15px 0; border-radius: 16px; }
        
        .result-right { padding-top: 25px; }
        .result-right h3 { font-size: 22px !important; margin-top: 10px !important; margin-bottom: 20px !important; }
        
        .info-grid {
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }
        .alert-box {
            margin-top: 20px;
            padding: 15px;
            border-radius: 12px;
        }
        .btn-dashboard {
            padding: 15px 20px;
            font-size: 13px;
        }
    }

    /* RESPONSIVE LANDSCAPE MOBILE */
    @media screen and (max-height: 500px) {
        body { overflow: hidden !important; }
        
        .result-container {
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 98vh; /* Prevent address bar issues somewhat */
        }
        .result-card {
            flex-direction: row !important;
            min-height: auto !important;
            height: 92vh;
            width: 96vw;
        }
        .result-left {
            width: 45% !important;
            padding: 10px 20px !important;
        }
        .result-right {
            width: 55% !important;
            padding: 10px 20px !important;
            justify-content: center;
        }
        .score-value { font-size: 36px !important; margin-top: 5px !important; }
        .score-badge { padding: 10px !important; margin: 10px 0 !important; }
        .status-pill { font-size: 8px !important; padding: 4px 10px !important; margin-bottom: 5px !important; }
        .result-left h2 { font-size: 14px !important; margin-bottom: 5px !important; line-height: 1.2 !important; }
        .result-left p { font-size: 10px !important; line-height: 1.3 !important; }
        
        .result-right h3 { font-size: 16px !important; margin: 6px 0 10px !important; }
        .result-right > div > span { font-size: 8px !important; padding: 2px 8px !important; }
        
        .info-grid { margin-top: 8px !important; gap: 10px !important; }
        .info-item p { font-size: 10px !important; margin: 0 !important; }
        .info-item > div > p:first-child { font-size: 8px !important; margin-bottom: 2px !important; }
        .info-icon { padding: 6px !important; margin-right: 10px !important; }
        .info-icon svg { width: 14px !important; height: 14px !important; }
        
        .alert-box { 
            margin-top: 10px !important; 
            padding: 10px !important; 
            gap: 10px !important;
        }
        .alert-box h4 { font-size: 10px !important; margin-bottom: 2px !important; }
        .alert-box p { font-size: 10px !important; line-height: 1.2 !important; }
        
        .result-right > div:last-of-type:not(.btn-dashboard):not(.alert-box) { /* Untuk div "skor ini final" */
            margin-top: 10px !important;
            padding: 10px !important;
            font-size: 10px !important;
        }
        
        .btn-dashboard { 
            margin-top: 12px !important; 
            padding: 10px 15px !important; 
            font-size: 11px !important; 
        }
        
        .result-brand { margin-bottom: 10px !important; gap: 8px !important; }
        .result-brand img { width: 24px !important; height: 24px !important; }
        .result-brand span { font-size: 12px !important; }
        
        .bottom-contact { display: none !important; }
        header { display: none !important; } /* Hide layout header */
    }
</style>

<div class="result-container">
    <div class="result-card">
        
        {{-- Left Area --}}
        <div class="result-left">
            <div class="result-brand">
                <img src="{{ asset('logo.png') }}" alt="Logo LPK">
                <span>LPK URISOWON</span>
            </div>
            
            <div class="status-pill">Ujian Selesai</div>
            <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 10px; line-height: 1.3;">Terima Kasih Telah Mengerjakan!</h2>
            
            <div class="score-badge">
                <span style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase;">Skor Akhir Anda</span>
                <span class="score-value">{{ $ujian_peserta->skor }}</span>
            </div>
            
            <p style="font-size: 13px; color: #94a3b8; line-height: 1.6; max-width: 240px;">Poin ini dihitung secara otomatis oleh sistem kami.</p>
        </div>

        {{-- Right Area --}}
        <div class="result-right">
            <div>
                <span style="font-size: 10px; font-weight: 800; color: #2563eb; background: #eff6ff; padding: 4px 12px; border-radius: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Ringkasan Sesi</span>
                <h3 style="font-size: 28px; font-weight: 900; color: #0f172a; margin-top: 15px; margin-bottom: 30px; line-height: 1.2;">{{ $ujian->judul }}</h3>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-icon">
                            <svg style="width: 20px; height: 20px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin: 0;">Waktu Akses</p>
                            <p style="font-size: 13px; font-weight: 700; color: #334155; margin: 0;">{{ $ujian_peserta->mulai_at ? \Carbon\Carbon::parse($ujian_peserta->mulai_at)->format('H:i') : '-' }} - {{ $ujian_peserta->selesai_at ? \Carbon\Carbon::parse($ujian_peserta->selesai_at)->format('H:i') : '-' }}</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <svg style="width: 20px; height: 20px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"></path></svg>
                        </div>
                        <div>
                            <p style="font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin: 0;">Jumlah Soal</p>
                            <p style="font-size: 13px; font-weight: 700; color: #334155; margin: 0;">{{ $ujian->soals()->count() }} Soal</p>
                        </div>
                    </div>
                </div>

                @if($adaEssay)
                <div class="alert-box">
                    <svg style="width: 20px; height: 20px; color: #f59e0b; margin-top: 2px; flex-shrink: 0;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <div>
                        <h4 style="font-size: 12px; font-weight: 800; color: #92400e; text-transform: uppercase; margin: 0 0 6px;">Informasi Nilai</h4>
                        <p style="font-size: 13px; color: #b45309; line-height: 1.5; margin: 0; font-style: italic;">Ujian ini mengandung soal Essay. Skor akhir mungkin bertambah setelah guru menilai jawaban manual Anda.</p>
                    </div>
                </div>
                @else
                <div style="margin-top: 30px; padding: 16px; background: #f1f5f9; border-radius: 12px; display: flex; align-items: center; color: #475569; font-size: 13px; font-style: italic; border: 1px solid #e2e8f0;">
                    <svg style="width: 18px; height: 18px; margin-right: 10px; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    Skor ini bersifat final berdasarkan penilaian otomatis sistem.
                </div>
                @endif
            </div>

            @if($ujian->jenis_ujian !== 'tryout')
            <a href="{{ route('murid.exam.review', $ujian_peserta) }}" style="display: flex; align-items: center; justify-content: center; background: #2563eb; color: white; text-decoration: none; padding: 16px 30px; border-radius: 16px; font-weight: 800; font-size: 14px; letter-spacing: 0.5px; transition: transform 0.2s, background 0.2s; margin-top: 12px;" onmouseover="this.style.background='#1d4ed8';this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#2563eb';this.style.transform='none'">
                LIHAT JAWABAN ANDA
                <svg style="width: 18px; height: 18px; margin-left: 10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </a>
            @endif
            <a href="{{ route('murid.dashboard') }}" class="btn-dashboard">
                KEMBALI KE DASHBOARD
                <svg style="width: 18px; height: 18px; margin-left: 10px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>

    </div>
    
    <div class="bottom-contact" style="text-align: center; margin-top: 30px;">
        <p style="font-size: 13px; color: #94a3b8;">Ada masalah dengan hasil Anda? <a href="#" style="color: #2563eb; font-weight: 700; text-decoration: none;">Hubungi Admin LPK</a></p>
    </div>
</div>

@endsection
