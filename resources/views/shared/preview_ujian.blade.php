<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PREVIEW: {{ $ujian->judul }} - LPK URISOWON</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">

    <style>
        /* RESET & BASE - MIRRORING STUDENT EXAM */
        * { box-sizing: border-box; }
        body {
            background-color: #f3f4f6;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-weight: normal;
        }

        /* PREVIEW BANNER */
        .preview-banner {
            width: 100%;
            background: #065f46;
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            position: fixed;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 24px;
        }

        /* MAIN CONTAINER */
        .cbt-container {
            width: 95vw;
            max-width: 1280px;
            height: 82vh;
            min-height: 550px;
            background-color: #fff;
            border: 1px solid #000;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            position: relative;
            z-index: 10;
            margin-top: 40px;
        }

        /* HEADER */
        .cbt-header {
            display: flex;
            height: 60px;
            border-bottom: 1px solid #000;
            flex-shrink: 0;
        }
        .hdr-col {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #000;
        }
        .hdr-col:last-child {
            border-right: none;
        }
        .hdr-label {
            color: #6b7280;
            font-size: 13px;
            text-transform: uppercase;
            margin-right: 8px;
        }
        .hdr-val {
            font-size: 18px;
            color: #000;
        }

        /* MAIN CONTENT AREA */
        .cbt-main {
            display: flex;
            flex: 1;
            overflow: hidden;
            background-color: #fff;
        }

        /* LEFT: QUESTION PANEL */
        .cbt-left {
            flex: 1.4;
            border-right: 1px solid #000;
            padding: 35px;
            overflow-y: auto;
        }
        .question-flex {
            display: flex;
            font-size: 20px;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #111;
        }
        .question-number {
            margin-right: 12px;
        }
        .question-text {
            flex: 1;
            overflow-wrap: break-word;
            word-break: break-word;
            hyphens: auto;
        }

        /* Audio Player Styling */
        .audio-wrapper {
            margin-top: 20px;
            display: flex;
            justify-content: center;
        }
        .audio-player {
            width: 100%;
            max-width: 300px;
            height: 40px;
            border-radius: 20px;
            outline: none;
        }

        /* RIGHT: OPTIONS PANEL */
        .cbt-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .opt-label {
            flex: 1;
            display: flex;
            margin: 0;
            padding: 0;
            border-bottom: 1px solid #e5e7eb;
            position: relative;
        }
        .opt-label:last-child {
            border-bottom: none;
        }
        .opt-body {
            display: flex;
            align-items: center;
            width: 100%;
            height: 100%;
            padding: 0 35px;
            background-color: #fff;
        }
        .opt-circle {
            width: 42px;
            height: 42px;
            border: 1px solid #9ca3af;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #374151;
            margin-right: 25px;
            flex-shrink: 0;
        }
        .opt-text {
            font-size: 20px;
            color: #1f2937;
            overflow-wrap: break-word;
            word-break: break-word;
            hyphens: auto;
        }

        /* CORRECT ANSWER HIGHLIGHTING (PREVIEW SPECIAL) */
        .is-correct-key .opt-body {
            background-color: #ecfdf5 !important;
            border-left: 6px solid #10b981;
        }
        .is-correct-key .opt-circle {
            background-color: #10b981 !important;
            border-color: #10b981 !important;
            color: #fff !important;
        }
        .correct-badge {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: #10b981;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        /* FOOTER */
        .cbt-footer {
            display: flex;
            height: 60px;
            border-top: 1px solid #000;
            flex-shrink: 0;
        }
        .ftr-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            background: transparent;
            border: none;
            font-size: 15px;
            cursor: pointer;
            color: #111;
        }
        .ftr-btn:hover { background-color: #f8fafc; }
        .ftr-prev {
            width: 18%;
            border-right: 1px solid #000;
        }
        .ftr-mid {
            flex: 1;
            color: #1d4ed8;
            font-weight: 700;
        }
        .ftr-next {
            width: 18%;
            border-left: 1px solid #000;
        }

        /* MODAL SHOW ALL QUESTIONS */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            padding: 20px;
        }
        .modal-box {
            background-color: #fff;
            width: 100%;
            max-width: 1280px;
            height: 85vh;
            border: 1px solid #000;
            display: flex;
            flex-direction: column;
        }
        .modal-header {
            padding: 15px 25px;
            border-bottom: 2px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-body {
            display: flex;
            flex: 1;
            overflow: hidden;
        }
        .modal-section {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
        }
        .modal-section:first-child {
            border-right: 4px solid #f3f4f6;
        }
        .grid-nums {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        .grid-btn {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d1d5db;
            border-radius: 50%;
            text-decoration: none;
            color: #374151;
            font-size: 18px;
        }
        .grid-btn:hover { background-color: #f3f4f6; }
        .grid-btn.current {
            border: 3px solid #111;
            background: #f8fafc;
        }

        /* WATERMARK */
        .watermark-container {
            position: relative;
            display: inline-block;
            max-width: 100%;
        }
        .watermark-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            pointer-events: none;
            color: rgba(255,255,255,0.3);
            text-shadow: 1px 1px 2px rgba(0,0,0,0.4);
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            transform: rotate(-20deg);
            white-space: nowrap;
            overflow: hidden;
            z-index: 10;
        }

        @media screen and (max-height: 500px) {
            .cbt-container {
                height: 85vh !important;
                margin-top: 35px;
            }
            .cbt-header, .cbt-footer { height: 45px !important; }
            .hdr-val { font-size: 14px !important; }
            .opt-text, .question-flex { font-size: 16px !important; }
        }
    </style>
</head>
<body>

    <div class="preview-banner">
        <div>MODE PREVIEW UJIAN (GURU/ADMIN)</div>
        @php
            $back_url = request()->routeIs('admin.*') ? route('admin.ujian.index') : route('guru.ujian.index');
        @endphp
        <a href="{{ $back_url }}" style="color:white; text-decoration:none; background:rgba(255,255,255,0.1); padding:4px 12px; border-radius:4px; font-size:11px;">KEMBALI KE PANEL</a>
    </div>


    <div class="cbt-container">
        <!-- HEADER -->
        <header class="cbt-header">
            <div class="hdr-col" style="flex: 1.5;">
                <span class="hdr-label">UJIAN:</span>
                <span class="hdr-val" style="font-weight:700;">{{ $ujian->judul }}</span>
            </div>
            <div class="hdr-col">
                <span class="hdr-label">SOAL KE:</span>
                <span class="hdr-val">{{ $page }}</span>
            </div>
            <div class="hdr-col">
                <span class="hdr-label">TOTAL SOAL:</span>
                <span class="hdr-val">{{ $totalSoal }}</span>
            </div>
        </header>

        <!-- MAIN -->
        <main class="cbt-main">
            <!-- QUESTION (LEFT) -->
            <div class="cbt-left">
                <div class="question-flex">
                    <div class="question-number">{{ $page }}.</div>
                    <div class="question-text">
                        {!! \App\Helpers\HtmlSanitizer::clean($currentSoal->pertanyaan) !!}
                    </div>
                </div>

                @if($currentSoal->audio_path)
                    <div class="audio-wrapper">
                        <audio controls class="audio-player">
                            <source src="{{ asset('storage/' . $currentSoal->audio_path) }}" type="audio/mpeg">
                        </audio>
                    </div>
                @endif

                @if($currentSoal->gambar_path)
                    <div style="text-align: center; margin-top: 20px;">
                        <div class="watermark-container">
                            <img src="{{ asset('storage/' . $currentSoal->gambar_path) }}" style="max-width: 100%; border: 1px solid #d1d5db; padding: 4px; border-radius: 8px;">
                            <div class="watermark-overlay">PREVIEW MODE</div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- OPTIONS / KEY (RIGHT) -->
            <div class="cbt-right">
                @if(in_array($currentSoal->tipe, ['pilihan_ganda', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar', 'multiple_choice']))
                    @foreach($currentSoal->pilihanJawabans as $index => $opsi)
                    <div class="opt-label {{ $opsi->is_benar ? 'is-correct-key' : '' }}">
                        <div class="opt-body" style="padding-top: 15px; padding-bottom: 15px;">
                            <div class="opt-circle">{{ chr(65 + $index) }}</div>
                            <div class="opt-text" style="display:flex; flex-direction:column; gap:8px;">
                                @if($opsi->teks)
                                    <span>{{ $opsi->teks }}</span>
                                @endif

                                @if($opsi->media_tipe === 'audio' && $opsi->media_path)
                                    <audio controls style="height: 40px; max-width: 220px; outline:none;">
                                        <source src="{{ asset('storage/' . $opsi->media_path) }}" type="audio/mpeg">
                                    </audio>
                                @elseif($opsi->media_tipe === 'gambar' && $opsi->media_path)
                                    <div class="watermark-container">
                                        <img src="{{ asset('storage/' . $opsi->media_path) }}" style="max-height: 120px; border-radius: 6px; border: 1px solid #e5e7eb; padding: 2px; max-width: 100%; object-fit: contain;">
                                    </div>
                                @endif
                            </div>
                            @if($opsi->is_benar)
                                <div class="correct-badge">Kunci Jawaban</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @elseif($currentSoal->tipe === 'essay')
                    <div style="padding: 35px;">
                        <p style="font-size: 18px; color: #111; font-weight:700;">Reference / Answer Guide:</p>
                        <div style="background: #f8fafc; border: 1px solid #cbd5e1; padding: 20px; border-radius: 8px; font-size: 16px; color: #475569; line-height: 1.6;">
                            <em>(Halaman ini adalah preview. Guru dapat menjelaskan kriteria penilaian kepada murid di sini.)</em>
                        </div>
                    </div>
                @elseif($currentSoal->tipe === 'short_answer')
                    <div style="padding: 35px;">
                        <p style="font-size: 16px; color: #374151; font-weight: 700;">✏️ Kunci Jawaban Singkat:</p>
                        <div style="background: #ecfdf5; border: 2px solid #10b981; padding: 20px; border-radius: 12px; margin-top: 10px;">
                            <span style="font-size: 24px; color: #065f46; font-weight: 800;">{{ $currentSoal->jawaban_kunci }}</span>
                            <p style="font-size: 12px; color: #047857; margin-top: 8px;">*Sistem secara otomatis mengabaikan besar/kecil huruf dan typo ringan.</p>
                        </div>
                    </div>
                @endif
            </div>
        </main>

        <!-- FOOTER -->
        <footer class="cbt-footer">
            @php
                $route_name = request()->routeIs('admin.*') ? 'admin.ujian.preview' : 'guru.ujian.preview';
            @endphp

            @if($page > 1)
                <a href="{{ route($route_name, ['ujian' => $ujian->id, 'page' => $page - 1]) }}" class="ftr-btn ftr-prev">&lt; SEBELUMNYA</a>
            @else
                <div class="ftr-btn ftr-prev" style="color: #ccc;">&lt; SEBELUMNYA</div>
            @endif

            <button id="btn-show-all" class="ftr-btn ftr-mid">DAFTAR SOAL</button>

            @if($page < $totalSoal)
                <a href="{{ route($route_name, ['ujian' => $ujian->id, 'page' => $page + 1]) }}" class="ftr-btn ftr-next">BERIKUTNYA &gt;</a>
            @else
                <div class="ftr-btn ftr-next" style="color: #ccc;">Selesai</div>
            @endif
        </footer>
    </div>

    <!-- MODAL SHOW ALL -->
    <div id="modal-show-all" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <button id="btn-close-modal" style="background:none; border:none; color:#2563eb; cursor:pointer; font-weight:700;">&lt; KEMBALI</button>
                <div style="font-weight:800; color:#1e293b;">NAVIGASI PREVIEW</div>
            </div>
            <div class="modal-body">
                <div class="modal-section">
                    <div style="font-size: 18px; border-bottom: 2px solid #e5e7eb; padding-bottom: 15px; font-weight:700;">Questions List</div>
                    <div class="grid-nums">
                        @for($i = 1; $i <= $totalSoal; $i++)
                            <a href="{{ route($route_name, ['ujian' => $ujian->id, 'page' => $i]) }}" class="grid-btn {{ $i == $page ? 'current' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor
                    </div>
                </div>
            </div>
            <div class="cbt-footer">
                <button class="ftr-btn ftr-prev" id="btn-close-modal-bottom" style="width:100%">&lt; TUTUP PANEL</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modal-show-all');
            document.getElementById('btn-show-all').addEventListener('click', () => modal.style.display = 'flex');
            document.getElementById('btn-close-modal').addEventListener('click', () => modal.style.display = 'none');
            document.getElementById('btn-close-modal-bottom').addEventListener('click', () => modal.style.display = 'none');
        });
    </script>
</body>
</html>
