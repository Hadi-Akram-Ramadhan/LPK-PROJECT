<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $ujian->judul }} - UBT Learning LPK URISOWON</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <script>
        window.EXAM_ID = {{ $ujian_peserta->id }};
        window.TIMER_SECONDS = {{ $sisaDetik }};
        window.IS_TRYOUT = {{ $ujian->jenis_ujian === 'tryout' ? 'true' : 'false' }};
        window.AUTO_SAVE_URL = "{{ route('murid.exam.autoSave', $ujian_peserta, false) }}";
        window.REPORT_CHEAT_URL = "{{ route('murid.exam.reportCheat', $ujian_peserta, false) }}";
        window.FINISH_URL = "{{ route('murid.exam.finish', $ujian_peserta, false) }}";
        window.ACAK_JAWABAN = {{ $acakJawaban ? 'true' : 'false' }};
        window.SHUFFLE_SEED = {{ ($ujian_peserta->user_id * 31) + ($currentSoal->id * 7) }};
        // Timestamp mulai ujian — dipakai sebagai bagian storageKey audio agar
        // percobaan tryout baru (setelah reset) tidak terpengaruh lock percobaan sebelumnya
        window.EXAM_ATTEMPT_TS = "{{ $ujian_peserta->mulai_at ? \Carbon\Carbon::parse($ujian_peserta->mulai_at)->timestamp : 0 }}";
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sortablejs/1.15.0/Sortable.min.js"></script>

    <style>
        /* FORCE CLICKABLE FOOTER */
        .cbt-footer { position: relative; z-index: 1000 !important; }
        .ftr-btn { pointer-events: all !important; position: relative; z-index: 1001 !important; }

        /* Ensure No Screen Blocking */
        #landscape-overlay { pointer-events: none; }
        #landscape-overlay * { pointer-events: none; }
        @media screen and (max-width: 1024px) and (orientation: portrait) {
            #landscape-overlay { pointer-events: all !important; display: flex !important; }
        }
    </style>

    <style>
        /* RESET & BASE */
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

        /* MAIN CONTAINER */
        .cbt-container {
            width: 95vw;
            max-width: 1280px;
            height: 85vh;
            min-height: 550px;
            background-color: #fff;
            border: 1px solid #000;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            position: relative;
            z-index: 10;
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
        .hdr-timer {
            font-size: 20px;
            letter-spacing: 1px;
            color: #000;
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
        .hdr-btn-finish {
            color: #2563eb;
            font-size: 14px;
            cursor: pointer;
            background: transparent;
            border: none;
            width: 100%;
            height: 100%;
            text-transform: uppercase;
        }
        .hdr-btn-finish:hover {
            background-color: #f8fafc;
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
            cursor: pointer;
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
            transition: background 0.15s;
        }
        .opt-label:hover .opt-body {
            background-color: #f9fafb;
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

        /* Radio Selection States */
        input[type="radio"], input[type="checkbox"] {
            display: none;
        }
        input[type="radio"]:checked + .opt-body,
        input[type="checkbox"]:checked + .opt-body {
            background-color: #f0fdf4;
        }
        input[type="radio"]:checked + .opt-body .opt-circle,
        input[type="checkbox"]:checked + .opt-body .opt-circle {
            background-color: #2563eb;
            color: #fff;
            border-color: #2563eb;
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
        }
        .ftr-btn:hover { background-color: #f8fafc; }
        .ftr-prev {
            width: 18%;
            border-right: 1px solid #000;
            color: #9ca3af;
        }
        .ftr-prev.active {
            color: #111;
        }
        .ftr-mid {
            flex: 1;
            color: #1d4ed8;
        }
        .ftr-next {
            width: 18%;
            border-left: 1px solid #000;
            color: #111;
        }

        /* === MATCHING: JARING-JARING (CONNECT-LINES) UI === */
        .match-container {
            position: relative;
            display: flex;
            align-items: stretch;
            width: 100%;
            height: 100%;
            background: #f8fafc;
            /* NO overflow:hidden – SVG lines must not be clipped */
            box-sizing: border-box;
            gap: 0;
        }
        .match-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px;
            overflow-y: auto;
            z-index: 2;
            position: relative;
        }
        /* Add inner padding where lines will show */
        #match-col-left  { padding-right: 48px; }
        #match-col-right { padding-left:  48px; }
        /* .match-svg styles are now inline on the element; this class is kept for mobile override only */
        .match-svg { pointer-events: none; overflow: visible; }
        .match-col-header {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 2px;
            flex-shrink: 0;
        }
        /* Match items */
        .match-item {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            background: #fff;
            cursor: pointer;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            color: #334155;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s, transform 0.15s;
            min-height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            user-select: none;
            -webkit-user-select: none;
            word-break: break-word;
            position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .match-item:hover {
            border-color: #94a3b8;
            background: #f8fafc;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .match-item.selected {
            border-color: #3b82f6 !important;
            background: #eff6ff !important;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.25) !important;
            transform: translateY(-1px);
        }
        .match-item.connected {
            box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        }
        /* Connection dot on right edge of left items - positioned in the padding area */
        .match-item-left::after {
            content: '';
            position: absolute;
            right: -32px;
            top: 50%;
            transform: translateY(-50%);
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid #fff;
            background: #cbd5e1;
            box-shadow: 0 1px 2px rgba(0,0,0,0.15);
            transition: background 0.2s;
            z-index: 3;
        }
        .match-item-left.connected::after { background: var(--pair-color, #10b981); }
        .match-item-left.selected::after { background: #3b82f6; }
        /* Connection dot on left edge of right items - positioned in the padding area */
        .match-item-right::after {
            content: '';
            position: absolute;
            left: -32px;
            top: 50%;
            transform: translateY(-50%);
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid #fff;
            background: #cbd5e1;
            box-shadow: 0 1px 2px rgba(0,0,0,0.15);
            transition: background 0.2s;
            z-index: 3;
        }
        .match-item-right.connected::after { background: var(--pair-color, #10b981); }
        .match-item img { max-height: 60px; max-width: 100%; object-fit: contain; pointer-events: none; }
        .match-item span { pointer-events: none; }
        /* Hint bar */
        .match-hint-bar {
            font-size: 11px;
            color: #64748b;
            background: #f1f5f9;
            border-radius: 6px;
            padding: 5px 12px;
            text-align: center;
            margin: 8px 16px 0;
            flex-shrink: 0;
        }

        /* Mobile: side-by-side but smaller so it fits portrait layout */
        @media (max-width: 900px) {
            .match-container {
                flex-direction: row;
                height: 100%;
                overflow-x: hidden;
            }
            .match-col {
                padding: 10px 4px;
                gap: 6px;
                flex: 1;
            }
            #match-col-left  { padding-right: 24px; }
            #match-col-right { padding-left:  24px; }

            .match-svg { display: block; }
            .match-item { font-size: 11px; padding: 8px 6px; min-height: 40px; }

            /* Responsive dot scale and position */
            .match-item-left::after { right: -16px; width: 8px; height: 8px; }
            .match-item-right::after { left: -16px; width: 8px; height: 8px; }

            /* Hide hint on very small screens to save space if needed, or scale it */
            .match-hint-bar { font-size: 10px; margin: 4px 8px 0; padding: 4px 8px; }
        }

        /* ── MATCHING: Ensure visibility on landscape mobile ── */
        @media screen and (max-height: 500px) {
            /* The cbt-right must allow its children to fill properly */
            .cbt-right-matching {
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }
            .match-hint-bar {
                font-size: 9px !important;
                margin: 2px 8px 0 !important;
                padding: 3px 8px !important;
                flex-shrink: 0;
            }
            .match-container {
                flex: 1 !important;
                min-height: 0 !important;
                height: auto !important;
                overflow: hidden !important;
                position: relative !important;
            }
            .match-col {
                padding: 6px 4px !important;
                gap: 4px !important;
                overflow-y: auto !important;
            }
            #match-col-left  { padding-right: 20px !important; }
            #match-col-right { padding-left:  20px !important; }
            .match-item {
                font-size: 10px !important;
                padding: 6px 4px !important;
                min-height: 32px !important;
            }
            .match-item-left::after  { right: -12px !important; width: 7px !important; height: 7px !important; }
            .match-item-right::after { left:  -12px !important; width: 7px !important; height: 7px !important; }
            .match-col-header {
                font-size: 9px !important;
                margin-bottom: 1px !important;
            }
        }

        /* BOTTOM TEXT */
        .bottom-text {
            margin-top: 12px;
            font-size: 11px;
            color: #6b7280;
            letter-spacing: 0.5px;
        }

        /* MODAL SHOW ALL QUESTIONS */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
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
        .grid-btn.answered {
            background-color: rgba(59, 130, 246, 0.2);
            color: #1d4ed8;
            border-color: #93c5fd;
        }
        .grid-btn.current {
            border: 3px solid #111;
        }
        /* LANDSCAPE WARNING */
        #landscape-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background-color: #111827;
            z-index: 99999;
            color: #fff;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px;
        }
        @media screen and (max-width: 1024px) and (orientation: portrait) {
            #landscape-overlay {
                display: flex;
            }
        }
        .rotate-icon {
            width: 80px;
            height: 80px;
            margin-bottom: 24px;
            animation: rotate-anim 2s ease-in-out infinite;
        }
        @keyframes rotate-anim {
            0% { transform: rotate(0deg); }
            50% { transform: rotate(90deg); }
            100% { transform: rotate(0deg); }
        }

        /* WATERMARK & PROTECTION */
        .cbt-container {
            user-select: none;
            -webkit-user-select: none;
        }
        textarea {
            user-select: text;
            -webkit-user-select: text;
        }
        .watermark-container {
            position: relative;
            display: inline-block;
            user-select: none;
            -webkit-user-select: none;
            -webkit-user-drag: none;
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
            color: rgba(255,255,255,0.3); /* dibuat lebih samar (transparent) */
            text-shadow: 1px 1px 2px rgba(0,0,0,0.4); /* bayangan diperhalus agar tidak terlalu tebal */
            font-size: 24px;
            font-weight: 600; /* dikurangi ketebalannya */
            letter-spacing: 2px;
            text-transform: uppercase;
            transform: rotate(-20deg);
            white-space: nowrap;
            overflow: hidden;
            z-index: 10;
        }
        .watermark-overlay.small {
            font-size: 14px;
        }

        /* RESPONSIVE LANDSCAPE MOBILE (MODIFIKASI DISINI) */
        @media screen and (max-height: 500px) {
            .cbt-container {
                height: 90vh !important;
                width: 96vw !important;
                max-width: none !important;
                border: 1px solid #000 !important;
                min-height: 0 !important;
            }
            .cbt-header, .cbt-footer {
                height: 45px !important;
            }
            .hdr-timer, .hdr-val, .hdr-btn-finish { font-size: 14px !important; }
            .hdr-label { display: inline-block !important; font-size: 10px !important; margin-right: 5px !important; }

            .cbt-left {
                padding: 15px 25px !important;
            }
            .opt-body {
                padding: 10px 25px !important;
            }
            .opt-circle {
                width: 32px !important;
                height: 32px !important;
                margin-right: 15px !important;
                font-size: 14px !important;
            }
            .opt-text { font-size: 16px !important; }
            .question-flex { font-size: 16px !important; margin-bottom: 20px !important; }

            /* Essay / Short Answer adjustment — scoped so it doesn't break matching */
            .cbt-right textarea {
                height: calc(100% - 25px) !important;
                min-height: 80px !important;
                font-size: 14px !important;
                padding: 10px !important;
            }
            .cbt-right > div:not(.match-container):not(.match-hint-bar) {
                padding: 15px !important;
                height: 100% !important;
            }
            .ftr-btn { font-size: 12px !important; }

            /* Modal Show All Adjustments */
            .modal-overlay {
                padding: 0 !important;
            }
            .modal-box {
                height: 90vh !important;
                width: 96vw !important;
                max-width: none !important;
                border: 1px solid #000 !important;
            }
            .grid-nums {
                gap: 8px !important;
            }
            .grid-btn {
                width: 40px !important;
                height: 40px !important;
                font-size: 14px !important;
                margin: 0 auto !important;
            }
            .bottom-text {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100">


    <div id="landscape-overlay">
        <svg class="rotate-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        <h2 style="font-size: 26px; font-weight: 800; margin-bottom: 15px; letter-spacing: -0.5px;">Gunakan Mode Landscape</h2>
        <p style="font-size: 15px; color: #94a3b8; max-width: 300px; line-height: 1.6;">Ujian Ubiquitous Base Test tidak dapat dikerjakan dalam mode layar potrait. Mohon putar ponsel Anda ke posisi landscape untuk melanjutkan.</p>
    </div>

    <div class="cbt-container">

        <!-- HEADER -->
        <header class="cbt-header">
            <div class="hdr-col">
                <span class="hdr-timer" id="countdown_timer">00:00</span>
            </div>
            <div class="hdr-col">
                <span class="hdr-label">QUESTION</span>
                <span class="hdr-val">{{ $page }}</span>
            </div>
            <div class="hdr-col">
                <span class="hdr-label">TOTAL QUESTIONS</span>
                <span class="hdr-val">{{ $totalSoal }}</span>
            </div>
            <div class="hdr-col">
                <button type="button" class="hdr-btn-finish" onclick="submitFinish()">SUBMIT</button>
            </div>
        </header>

        <form id="finish-form" action="{{ route('murid.exam.finish', $ujian_peserta) }}" method="POST" style="display: none;">
            @csrf
        </form>

        <form id="answer-form" style="display: none;" onsubmit="event.preventDefault();">
            <input type="hidden" name="soal_id" value="{{ $currentSoal->id }}">
        </form>

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
                    <div class="audio-wrapper flex-col items-center">
                        <audio controls controlsList="nodownload" class="audio-player soal-audio" preload="auto" data-id="soal_{{ $currentSoal->id }}" data-max="{{ $currentSoal->audio_max_play }}">
                            <source src="{{ route('murid.exam.media', ['ujian_peserta' => $ujian_peserta, 'id' => $currentSoal->id, 'type' => 'soal']) }}?v={{ $currentSoal->id }}" type="audio/mpeg">
                        </audio>
                        @if($currentSoal->audio_max_play)
                            @php
                                $played = $audioLogs['soal']->play_count ?? 0;
                                $sisa = max(0, $currentSoal->audio_max_play - $played);
                            @endphp
                            {{-- Counter disembunyikan: div tetap ada untuk referensi JS --}}
                            <div style="display:none" id="counter_soal_{{ $currentSoal->id }}" data-remaining="{{ $sisa }}"></div>
                        @else
                            {{-- Bebas putar: tidak perlu ditampilkan --}}
                        @endif
                    </div>
                @endif

                @if($currentSoal->gambar_path)
                    <div style="text-align: center; margin-top: 20px;">
                        <div class="watermark-container">
                            <img src="{{ asset('storage/' . $currentSoal->gambar_path) }}" style="max-width: 100%; border: 1px solid #d1d5db; padding: 4px; border-radius: 8px;" oncontextmenu="return false;" draggable="false">
                            <div class="watermark-overlay">Property of Urisowon</div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- OPTIONS (RIGHT) -->
            <div class="cbt-right {{ $currentSoal->tipe === 'matching' ? 'cbt-right-matching' : '' }}">
                @if(in_array($currentSoal->tipe, ['pilihan_ganda', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar']))
                    @foreach($currentSoal->pilihanJawabans as $index => $opsi)
                    <label class="opt-label">
                        <input type="radio" name="jawaban" value="{{ $opsi->id }}" form="answer-form" class="auto-save-trigger" {{ ($jawabanSaatIni && $jawabanSaatIni->pilihan_jawaban_id == $opsi->id) ? 'checked' : '' }}>
                        <div class="opt-body" style="padding-top: 15px; padding-bottom: 15px;">
                            <div class="opt-circle">{{ chr(65 + $index) }}</div>
                            <div class="opt-text" style="display:flex; flex-direction:column; gap:8px;">
                                @if($opsi->teks)
                                    <span>{{ $opsi->teks }}</span>
                                @endif

                                @if($opsi->media_tipe === 'audio' && $opsi->media_path)
                                    <!-- Stop propagation on audio play so the radio button isn't toggled incorrectly on some devices -->
                                    <div onclick="event.stopPropagation()">
                                        <audio controls controlsList="nodownload" preload="auto" class="opsi-audio" data-id="opt_{{ $opsi->id }}" data-max="{{ $opsi->audio_max_play }}" style="height: 40px; max-width: 220px; outline:none;">
                                            <source src="{{ route('murid.exam.media', ['ujian_peserta' => $ujian_peserta, 'id' => $opsi->id, 'type' => 'pilihan']) }}?v={{ $opsi->id }}" type="audio/mpeg">
                                        </audio>
                                        @if($opsi->audio_max_play)
                                            @php
                                                $playedOpt = $audioLogs['opsi_'.$opsi->id]->play_count ?? 0;
                                                $sisaOpt = max(0, $opsi->audio_max_play - $playedOpt);
                                            @endphp
                                            {{-- Counter disembunyikan --}}
                                            <div style="display:none" id="counter_opt_{{ $opsi->id }}" data-remaining="{{ $sisaOpt }}"></div>
                                        @endif
                                    </div>
                                @elseif($opsi->media_tipe === 'gambar' && $opsi->media_path)
                                    <div class="watermark-container" style="margin-top: 8px;">
                                        <img src="{{ asset('storage/' . $opsi->media_path) }}" style="max-height: 160px; border-radius: 12px; border: 2px solid #f1f5f9; padding: 4px; max-width: 100%; object-fit: contain; background: #fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); transition: all 0.2s;" oncontextmenu="return false;" draggable="false">
                                        <div class="watermark-overlay small">Property of Urisowon</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </label>
                    @endforeach
                @elseif($currentSoal->tipe === 'multiple_choice')
                    @php $checkedMultiple = $jawabanSaatIni && $jawabanSaatIni->jawaban_multiple ? json_decode($jawabanSaatIni->jawaban_multiple, true) : []; @endphp
                    @foreach($currentSoal->pilihanJawabans as $index => $opsi)
                    @php $isChecked = in_array($opsi->id, $checkedMultiple); @endphp
                    <label class="opt-label">
                        <input type="checkbox" name="jawaban[]" value="{{ $opsi->id }}" form="answer-form" class="auto-save-trigger-mc" {{ $isChecked ? 'checked' : '' }}>
                        <div class="opt-body" style="padding-top: 15px; padding-bottom: 15px;">
                            <div class="opt-circle" style="border-radius: 4px;">{{ chr(65 + $index) }}</div>
                            <div class="opt-text" style="display:flex; flex-direction:column; gap:8px;">
                                @if($opsi->teks)
                                    <span>{{ $opsi->teks }}</span>
                                @endif

                                @if($opsi->media_tipe === 'audio' && $opsi->media_path)
                                    <div onclick="event.stopPropagation()">
                                        <audio controls controlsList="nodownload" preload="none" style="height: 40px; max-width: 220px; outline:none;">
                                            <source src="{{ asset('storage/' . $opsi->media_path) }}?v={{ $opsi->id }}" type="audio/mpeg">
                                        </audio>
                                    </div>
                                @elseif($opsi->media_tipe === 'gambar' && $opsi->media_path)
                                    <div class="watermark-container">
                                        <img src="{{ asset('storage/' . $opsi->media_path) }}" style="max-height: 120px; border-radius: 6px; border: 1px solid #e5e7eb; padding: 2px; max-width: 100%; object-fit: contain;" oncontextmenu="return false;" draggable="false">
                                        <div class="watermark-overlay small">Property of Urisowon</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </label>
                    @endforeach
                @elseif($currentSoal->tipe === 'matching')
                    @php
                        $pairs = $currentSoal->pilihanJawabans;
                        $leftItems = [];
                        $rightItems = [];

                        // Array of right indices to shuffle
                        $shuffledIndices = [];
                        for ($i = 0; $i < $pairs->count(); $i++) { $shuffledIndices[] = $i; }

                        // Seed random so shuffle is consistent on reload for this user+exam+page mix
                        mt_srand($ujian_peserta->id + $currentSoal->id);
                        shuffle($shuffledIndices);
                        mt_srand(); // reset seed

                        // Generate Map: rightShuffledIndex -> originalPairIndex
                        $shuffleMap = [];
                        foreach ($shuffledIndices as $rightShuffledIndex => $originalPairIndex) {
                            $shuffleMap[$rightShuffledIndex] = $originalPairIndex;
                        }

                        $existingJawaban = $jawabanSaatIni ? json_decode($jawabanSaatIni->jawaban_text, true) : [];

                        foreach ($pairs as $idx => $pair) {
                            $leftItems[] = ['id' => $idx, 'val' => $pair->teks, 'tipe' => in_array($pair->media_tipe, ['matching_gambar_kiri', 'matching_gambar_keduanya']) ? 'gambar' : 'teks'];
                            $rightItems[] = [
                                'id' => $idx, // Array display index
                                'original' => $shuffledIndices[$idx], // the pair index it originally belongs to
                                'val' => $pairs[$shuffledIndices[$idx]]->media_path,
                                'tipe' => in_array($pairs[$shuffledIndices[$idx]]->media_tipe, ['matching_gambar_kanan', 'matching_gambar_keduanya']) ? 'gambar' : 'teks'
                            ];
                        }
                    @endphp

                    <input type="hidden" id="matching_shuffle_map" form="answer-form" name="shuffle_map" value="{{ json_encode($shuffleMap) }}">
                    <input type="hidden" id="matching_answer" form="answer-form" name="jawaban" value="{{ json_encode($existingJawaban ?? new stdClass) }}">

                    {{-- Hint bar --}}
                    <div class="match-hint-bar">👆 Tap item di kiri → lalu tap pasangannya di kanan. Tap item yang sudah terhubung untuk melepaskan.</div>

                    <div class="match-container" id="match-wrapper">
                        {{-- SVG overlay: FIRST child, inline style guarantees position:absolute. Z-index 1 keeps it beneath text --}}
                        <svg id="match-svg" xmlns="http://www.w3.org/2000/svg"
                             style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;overflow:visible;z-index:1;"></svg>

                        {{-- Left column: Prompts --}}
                        <div class="match-col" id="match-col-left">
                            <div class="match-col-header">Prompts</div>
                            @foreach($leftItems as $item)
                                <div class="match-item match-item-left"
                                     data-left-id="{{ $item['id'] }}"
                                     id="mleft_{{ $item['id'] }}">
                                    @if($item['tipe'] === 'gambar')
                                        <img src="{{ asset('storage/' . $item['val']) }}" oncontextmenu="return false;" draggable="false">
                                    @else
                                        <span>{{ $item['val'] }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Right column: Answers (shuffled) --}}
                        <div class="match-col" id="match-col-right">
                            <div class="match-col-header">Answers</div>
                            @foreach($rightItems as $rIdx => $item)
                                <div class="match-item match-item-right"
                                     data-piece-id="{{ $item['original'] }}"
                                     data-shuffled-index="{{ $rIdx }}"
                                     id="mright_{{ $item['original'] }}">
                                    @if($item['tipe'] === 'gambar')
                                        <img src="{{ asset('storage/' . $item['val']) }}" oncontextmenu="return false;" draggable="false">
                                    @else
                                        <span>{{ $item['val'] }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                @elseif($currentSoal->tipe === 'essay')
                    <div style="padding: 35px; display: flex; flex-direction: column; height: 100%;">
                        <p style="font-size: 18px; margin-top: 0; color: #111;">Type Your Answer:</p>
                        <textarea name="jawaban" form="answer-form" class="auto-save-trigger-typing" style="flex: 1; width: 100%; border: 1px solid #9ca3af; padding: 15px; font-size: 18px; resize: none; border-radius: 8px;">{{ $jawabanSaatIni?->jawaban_text ?? '' }}</textarea>
                    </div>
                @elseif($currentSoal->tipe === 'short_answer')
                    <div style="padding: 35px; display: flex; flex-direction: column; height: 100%;">
                        <p style="font-size: 16px; margin-top: 0; color: #374151; font-weight: 600;">✏️ Jawaban Singkat:</p>
                        <p style="font-size: 13px; color: #6b7280; margin-top: 0; margin-bottom: 16px;">Tulis jawaban Anda di bawah ini. Sistem akan menilai otomatis.<br>Tidak perlu khawatir soal huruf besar/kecil atau typo kecil.</p>
                        <input type="text" name="jawaban" form="answer-form"
                            class="auto-save-trigger-typing"
                            value="{{ $jawabanSaatIni?->jawaban_text ?? '' }}"
                            autocomplete="off" autocorrect="off" spellcheck="false"
                            placeholder="Ketik jawaban Anda di sini..."
                            style="width: 100%; border: 2px solid #d1fae5; padding: 16px 18px; font-size: 18px; border-radius: 10px; outline: none; background: #f0fdf4; color: #065f46; font-weight: 500; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#10b981'" onblur="this.style.borderColor='#d1fae5'">
                    </div>
                @endif
            </div>
        </main>

        <!-- FOOTER -->
        <footer class="cbt-footer">
            @if($page > 1)
                <a href="{{ route('murid.exam.show', ['ujian_peserta' => $ujian_peserta, 'page' => $page - 1]) }}" class="ftr-btn ftr-prev active">&lt; Prev</a>
            @else
                <div class="ftr-btn ftr-prev">&lt; Prev</div>
            @endif

            <button id="btn-show-all" class="ftr-btn ftr-mid">QUESTIONS LIST</button>

            @if($page < $totalSoal)
                <a href="{{ route('murid.exam.show', ['ujian_peserta' => $ujian_peserta, 'page' => $page + 1]) }}" class="ftr-btn ftr-next">Next &gt;</a>
            @else
                <button onclick="document.getElementById('finish-form').submit()" class="ftr-btn ftr-next">Finish</button>
            @endif
        </footer>
    </div>

    <!-- BOTTOM TEXT -->
    <div class="bottom-text">UBT Learning LPK URISOWON</div>


    <!-- MODAL SHOW ALL QUESTIONS -->
    <div id="modal-show-all" class="modal-overlay">
        <div class="modal-box">

            <div class="modal-header">
                <button id="btn-close-modal" style="background:none; border:none; color:#2563eb; cursor:pointer;">&lt; BACK</button>
                <div style="display: flex; gap: 20px; font-size: 13px; color: #4b5563;">
                    <div style="display:flex; align-items:center;">
                        <span style="display:inline-block; width:12px; height:12px; background:rgba(59, 130, 246, 0.2); border:1px solid #93c5fd; border-radius:50%; margin-right:6px;"></span>
                        Answered ({{ count($answeredSoalIds) }})
                    </div>
                    <div style="display:flex; align-items:center;">
                        <span style="display:inline-block; width:12px; height:12px; border:1px solid #9ca3af; border-radius:50%; margin-right:6px;"></span>
                        Unanswered ({{ $totalSoal - count($answeredSoalIds) }})
                    </div>
                </div>
            </div>

            <div class="modal-body">
                @php
                    $qNum = 1;
                @endphp

                <!-- Sesi Reading / General -->
                @if(count($readingSoals) > 0)
                <div class="modal-section">
                    <div style="font-size: 18px; border-bottom: 2px solid #e5e7eb; padding-bottom: 15px;">
                        {{ count($listeningSoals) > 0 ? 'Session 1: Reading' : 'Questions List' }}
                    </div>
                    <div class="grid-nums">
                        @foreach ($readingSoals as $s)
                            @php
                                $isAnsw = in_array($s->id, $answeredSoalIds);
                                $isCurr = $qNum == $page;
                                $cls = "grid-btn " . ($isAnsw ? 'answered ' : '') . ($isCurr ? 'current ' : '');
                            @endphp
                            <a href="{{ route('murid.exam.show', ['ujian_peserta' => $ujian_peserta, 'page' => $qNum]) }}" class="{{ $cls }}">
                                {{ $qNum }}
                            </a>
                            @php $qNum++; @endphp
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Sesi Listening -->
                @if(count($listeningSoals) > 0)
                <div class="modal-section">
                    <div style="font-size: 18px; border-bottom: 2px solid #e5e7eb; padding-bottom: 15px;">Session 2: Listening</div>
                    <div class="grid-nums">
                        @foreach ($listeningSoals as $s)
                            @php
                                $isAnsw = in_array($s->id, $answeredSoalIds);
                                $isCurr = $qNum == $page;
                                $cls = "grid-btn " . ($isAnsw ? 'answered ' : '') . ($isCurr ? 'current ' : '');
                            @endphp
                            <a href="{{ route('murid.exam.show', ['ujian_peserta' => $ujian_peserta, 'page' => $qNum]) }}" class="{{ $cls }}">
                                {{ $qNum }}
                            </a>
                            @php $qNum++; @endphp
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Modal Footer mirrors app footer -->
            <div class="cbt-footer">
                <button class="ftr-btn ftr-prev" id="btn-close-modal-bottom">&lt; BACK</button>
                <div class="ftr-btn ftr-mid" style="color: #6b7280;">QUESTIONS LIST</div>
                <button type="button" class="ftr-btn ftr-next" onclick="submitFinish()">Finish</button>
            </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
            // ── SHUFFLE JAWABAN (Frontend Only) ──
            if (window.ACAK_JAWABAN) {
                // Simple seeded PRNG (Mulberry32)
                function seededRand(seed) {
                    return function() {
                        seed |= 0; seed = seed + 0x6D2B79F5 | 0;
                        var t = Math.imul(seed ^ seed >>> 15, 1 | seed);
                        t = t + Math.imul(t ^ t >>> 7, 61 | t) ^ t;
                        return ((t ^ t >>> 14) >>> 0) / 4294967296;
                    };
                }
                const rand = seededRand(window.SHUFFLE_SEED);

                // Fisher-Yates shuffle for each opt container
                document.querySelectorAll('.cbt-right label.opt-label').forEach(() => {});
                const containers = [
                    document.querySelector('.cbt-right'),
                ];
                containers.forEach(container => {
                    if (!container) return;
                    const items = Array.from(container.querySelectorAll('label.opt-label'));
                    if (items.length < 2) return;
                    // Fisher-Yates
                    for (let i = items.length - 1; i > 0; i--) {
                        const j = Math.floor(rand() * (i + 1));
                        // Swap in DOM
                        const parent = items[i].parentNode;
                        const nextI = items[i].nextSibling;
                        const nextJ = items[j].nextSibling;
                        parent.insertBefore(items[j], nextI);
                        if (nextJ) parent.insertBefore(items[i], nextJ);
                        else parent.appendChild(items[i]);
                        // Swap in array
                        [items[i], items[j]] = [items[j], items[i]];
                    }
                    // Re-letter the circles A, B, C... after shuffle (cosmetic only)
                    items.forEach((lbl, idx) => {
                        const circle = lbl.querySelector('.opt-circle');
                        if (circle && !circle.querySelector('img') && !circle.querySelector('audio')) {
                            // Only re-letter text-based circles
                            if (circle.textContent.trim().match(/^[A-Z]$/)) {
                                circle.textContent = String.fromCharCode(65 + idx);
                            }
                        }
                    });
                });
            }
            const modal = document.getElementById('modal-show-all');
            const btnShowAll = document.getElementById('btn-show-all');
            if (btnShowAll && modal) {
                btnShowAll.addEventListener('click', () => {
                    modal.style.display = 'flex';
                });
                document.getElementById('btn-close-modal').addEventListener('click', () => modal.style.display = 'none');
                document.getElementById('btn-close-modal-bottom').addEventListener('click', () => modal.style.display = 'none');
            }



            // TIMER
            let timeRemaining = window.TIMER_SECONDS;
            const timerDisplay = document.getElementById('countdown_timer');
            if (timerDisplay) {
                const timerInterval = setInterval(() => {
                    if (timeRemaining <= 0) {
                        clearInterval(timerInterval);
                        document.getElementById('finish-form').submit();
                        return;
                    }
                    const m = Math.floor(timeRemaining / 60).toString().padStart(2, '0');
                    const s = Math.floor(timeRemaining % 60).toString().padStart(2, '0');
                    timerDisplay.innerText = `${m}:${s}`;
                    timeRemaining--;
                }, 1000);
            }

            // ANTI CHEAT
            let isNavigating = false;
            document.querySelectorAll('a[href], button[onclick*="submit"], button.hdr-btn-finish, a.ftr-btn, button.ftr-btn').forEach(el => {
                el.addEventListener('click', () => { isNavigating = true; });
            });

            // Report Cheat Function
            const reportCheat = () => {
                if (isNavigating || window.IS_TRYOUT) return;

                fetch(window.REPORT_CHEAT_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                })
                .catch(err => console.error("Cheat reporting failed:", err));
            };

            // Detect Tab Switch / Minimize
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'hidden') {
                    reportCheat();
                }
            });

            // Detect Blur (Clicking outside browser)
            window.addEventListener('blur', () => {
                reportCheat();
            });

            // ── GENERAL SUBMIT & SOAL ID (defined at outer scope so auto-save works for all types) ──
            const _form = document.getElementById('answer-form');
            const soalId = _form ? _form.querySelector('[name="soal_id"]').value : null;

            const submitAnswer = (data) => {
                fetch(window.AUTO_SAVE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                }).catch(e => console.error("Auto-save failed", e));
            };

            window.submitFinish = function() {
                if (confirm('Are you sure you want to finish the exam?')) {
                    // Disable buttons to prevent double click
                    document.querySelectorAll('.hdr-btn-finish, .ftr-next').forEach(b => {
                        if(b.innerText.toLowerCase() === 'submit' || b.innerText.toLowerCase() === 'finish') {
                            b.disabled = true;
                            b.innerText = 'Saving...';
                        }
                    });

                    // Force form submission after 600ms to allow pending fetch requests to finish
                    setTimeout(() => {
                        const form = document.getElementById('finish-form');
                        if (form) form.submit();
                    }, 600);
                }
            };

            // ── MATCHING: JARING-JARING (CLICK-TO-CONNECT) ──
            const matchWrapper = document.getElementById('match-wrapper');
            if (matchWrapper) {
                const ansInput = document.getElementById('matching_answer');
                const shuffleMapInput = document.getElementById('matching_shuffle_map');
                const svg = document.getElementById('match-svg');
                const leftCol = document.getElementById('match-col-left');
                const rightCol = document.getElementById('match-col-right');

                // Pair colors
                const COLORS = ['#3b82f6','#ef4444','#10b981','#f59e0b','#8b5cf6','#ec4899','#06b6d4','#84cc16','#f97316','#6366f1'];
                let selectedLeft = null;
                let connections = {}; // { leftId: rightOriginalId }
                let colorMap = {};   // { leftId: colorIndex }
                let nextColor = 0;

                // Load existing answers
                try {
                    const ex = JSON.parse(ansInput.value || '{}');
                    if (ex && typeof ex === 'object') {
                        Object.entries(ex).forEach(([lid, rid]) => {
                            connections[String(lid)] = parseInt(rid);
                            colorMap[String(lid)] = nextColor % COLORS.length;
                            nextColor++;
                        });
                    }
                } catch(e) {}

                const resizeSvg = () => {
                    // Use svg's own rendered size for the viewBox coordinate system
                    const sr = svg.getBoundingClientRect();
                    if (sr.width > 0) svg.setAttribute('viewBox', `0 0 ${sr.width} ${sr.height}`);
                };

                // Coordinates relative to the SVG element targeting the connection dots
                const getLeftPoint = (el) => {
                    const sr = svg.getBoundingClientRect();
                    const er = el.getBoundingClientRect();
                    // Mobile: width<=900 uses 16px dots (offset 12), height<=500 uses 12px dots (offset 8), desktop uses 32px dots (offset 27)
                    const offset = window.innerHeight <= 500 ? 8 : (window.innerWidth <= 900 ? 12 : 27);
                    return {
                        x: (er.right - sr.left) + offset,
                        y: er.top  - sr.top  + er.height / 2
                    };
                };

                const getRightPoint = (el) => {
                    const sr = svg.getBoundingClientRect();
                    const er = el.getBoundingClientRect();
                    const offset = window.innerHeight <= 500 ? 8 : (window.innerWidth <= 900 ? 12 : 27);
                    return {
                        x: (er.left - sr.left) - offset,
                        y: er.top  - sr.top  + er.height / 2
                    };
                };

                const drawLines = () => {
                    resizeSvg();
                    svg.innerHTML = '';
                    Object.entries(connections).forEach(([lid, rid]) => {
                        const lEl = document.getElementById('mleft_'  + lid);
                        const rEl = document.getElementById('mright_' + rid);
                        if (!lEl || !rEl) return;
                        const l = getLeftPoint(lEl);
                        const r = getRightPoint(rEl);
                        const color = COLORS[colorMap[lid] ?? 0];
                        const cx1 = l.x + (r.x - l.x) * 0.35;
                        const cx2 = l.x + (r.x - l.x) * 0.65;
                        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                        path.setAttribute('d', `M${l.x},${l.y} C${cx1},${l.y} ${cx2},${r.y} ${r.x},${r.y}`);
                        path.setAttribute('stroke', color);
                        path.setAttribute('stroke-width', '2.5');
                        path.setAttribute('fill', 'none');
                        path.setAttribute('stroke-linecap', 'round');
                        path.setAttribute('opacity', '0.85');
                        // Animated draw-in effect (use large dasharray to cover any path length)
                        const pathLen = 1000;
                        path.style.strokeDasharray = pathLen;
                        path.style.strokeDashoffset = pathLen;
                        path.style.animation = 'matchLineIn 0.4s ease forwards';
                        svg.appendChild(path);
                    });
                };

                // Keyframe animation for lines appearing
                if (!document.getElementById('match-keyframes')) {
                    const style = document.createElement('style');
                    style.id = 'match-keyframes';
                    style.textContent = `@keyframes matchLineIn { to { stroke-dashoffset: 0; } }`;
                    document.head.appendChild(style);
                }

                const applyStyles = () => {
                    document.querySelectorAll('.match-item-left').forEach(el => {
                        const lid = String(el.dataset.leftId);
                        if (lid in connections) {
                            const color = COLORS[colorMap[lid]];
                            el.classList.add('connected');
                            el.style.setProperty('--pair-color', color);
                            el.style.borderColor = color;
                            el.style.background   = color + '15';
                        } else {
                            el.classList.remove('connected');
                            el.style.borderColor = '';
                            el.style.background  = '';
                            el.style.removeProperty('--pair-color');
                        }
                    });
                    document.querySelectorAll('.match-item-right').forEach(el => {
                        const rid = parseInt(el.dataset.pieceId);
                        const entry = Object.entries(connections).find(([,r]) => r === rid);
                        if (entry) {
                            const color = COLORS[colorMap[entry[0]]];
                            el.classList.add('connected');
                            el.style.setProperty('--pair-color', color);
                            el.style.borderColor = color;
                            el.style.background  = color + '15';
                        } else {
                            el.classList.remove('connected');
                            el.style.borderColor = '';
                            el.style.background  = '';
                            el.style.removeProperty('--pair-color');
                        }
                    });
                };

                const save = () => {
                    ansInput.value = JSON.stringify(connections);
                    submitAnswer({ soal_id: soalId, jawaban: ansInput.value, shuffle_map: shuffleMapInput.value });
                };

                const refresh = () => { applyStyles(); drawLines(); };

                // Click: LEFT items
                document.querySelectorAll('.match-item-left').forEach(el => {
                    el.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const lid = String(el.dataset.leftId);

                        if (selectedLeft === el) {
                            // Clicking again on the same left item → deselect it
                            el.classList.remove('selected');
                            selectedLeft = null;
                        } else if (lid in connections && !selectedLeft) {
                            // Clicking a connected left item when nothing is selected → disconnect it
                            delete connections[lid];
                            delete colorMap[lid];
                            save(); refresh();
                        } else {
                            // Select this left item (pending to connect to a right item)
                            if (selectedLeft) selectedLeft.classList.remove('selected');
                            selectedLeft = el;
                            el.classList.add('selected');
                        }
                    });
                });

                // Click: RIGHT items
                document.querySelectorAll('.match-item-right').forEach(el => {
                    el.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const rid = parseInt(el.dataset.pieceId);

                        if (selectedLeft) {
                            const lid = String(selectedLeft.dataset.leftId);
                            // Remove any existing connection pointing TO this right item
                            Object.keys(connections).forEach(k => {
                                if (connections[k] === rid) { delete connections[k]; delete colorMap[k]; }
                            });
                            // Remove previous connection from this left item
                            delete connections[lid];
                            // Assign color if new
                            if (!(lid in colorMap)) { colorMap[lid] = nextColor % COLORS.length; nextColor++; }
                            // Connect
                            connections[lid] = rid;
                            selectedLeft.classList.remove('selected');
                            selectedLeft = null;
                            save(); refresh();
                        } else {
                            // No left selected: clicking right disconnects its pair
                            const entry = Object.entries(connections).find(([, r]) => r === rid);
                            if (entry) {
                                delete connections[entry[0]];
                                delete colorMap[entry[0]];
                                save(); refresh();
                            }
                        }
                    });
                });

                // Deselect on outside click
                document.addEventListener('click', () => {
                    if (selectedLeft) { selectedLeft.classList.remove('selected'); selectedLeft = null; }
                });

                // Redraw on scroll/resize
                leftCol?.addEventListener('scroll',  drawLines);
                rightCol?.addEventListener('scroll', drawLines);
                window.addEventListener('resize', () => { resizeSvg(); drawLines(); });

                // Initial render — use double rAF so layout is settled before drawing
                applyStyles();
                requestAnimationFrame(() => requestAnimationFrame(() => drawLines()));
            }

            // AUTO SAVE (radio: pilihan_ganda, audio, pilihan_ganda_audio, pilihan_ganda_gambar)
            document.querySelectorAll('.auto-save-trigger').forEach(el => {
                el.addEventListener('change', function() { submitAnswer({ soal_id: soalId, jawaban: this.value }); });
            });

            // AUTO SAVE (checkbox: multiple_choice)
            document.querySelectorAll('.auto-save-trigger-mc').forEach(el => {
                el.addEventListener('change', function() {
                    const checked = Array.from(document.querySelectorAll('.auto-save-trigger-mc:checked')).map(cb => parseInt(cb.value));
                    submitAnswer({ soal_id: soalId, jawaban: JSON.stringify(checked) });
                });
            });

            // AUTO SAVE (typing: essay, short_answer) — debounced 800ms
            let typingTimer = null;
            document.querySelectorAll('.auto-save-trigger-typing').forEach(el => {
                el.addEventListener('input', function() {
                    clearTimeout(typingTimer);
                    const val = this.value;
                    typingTimer = setTimeout(() => {
                        submitAnswer({ soal_id: soalId, jawaban: val });
                    }, 800);
                });
            });

            // AUDIO LIMITER (Hardened + sessionStorage persistent lock)
            function setupAudioLimiter(selector) {
                document.querySelectorAll(selector).forEach(audioEl => {
                    const aid      = audioEl.getAttribute('data-id');
                    const maxAttr  = audioEl.getAttribute('data-max');
                    const counterEl = document.getElementById('counter_' + aid);

                    // Jika tidak ada counter (bebas putar) atau tidak ada max, skip
                    if (!counterEl || !maxAttr || parseInt(maxAttr) <= 0) return;

                    let sisa        = parseInt(counterEl.getAttribute('data-remaining') || '0');
                    let isLocked    = false;
                    let sessionPlaying = false; // Guard agar event 'play' ganda tidak kurangi sisa 2x

                    // --- sessionStorage key unik per ujian + attempt (mulai_at timestamp) + soal/opsi ---
                    const storageKey = 'audio_locked_' + window.EXAM_ID + '_' + window.EXAM_ATTEMPT_TS + '_' + aid;

                    const lockAudio = () => {
                        if (isLocked) return;
                        isLocked = true;
                        audioEl.pause();
                        // Sembunyikan audio tanpa teks apapun
                        audioEl.style.display = 'none';
                        // Simpan ke sessionStorage agar tetap terkunci saat kembali ke soal ini
                        try { sessionStorage.setItem(storageKey, '1'); } catch(e) {}
                    };

                    const updateCounter = () => {
                        // Tidak tampilkan apapun ke siswa
                    };

                    // Cek sessionStorage dulu — jika pernah terkunci di tab ini, langsung kunci
                    try {
                        if (sessionStorage.getItem(storageKey) === '1') {
                            sisa = 0;
                            lockAudio();
                            return;
                        }
                    } catch(e) {}

                    // Lock immediately jika sisa sudah 0 saat halaman dimuat (dari server)
                    if (sisa <= 0) {
                        lockAudio();
                        return;
                    }

                    updateCounter();

                    let inLastPlay = false; // true selama dalam putaran terakhir yg diizinkan (termasuk saat browser buffer/resume)

                    // Listener permanen: blokir play jika sudah terkunci atau limit habis
                    audioEl.addEventListener('play', function() {
                        if (isLocked) {
                            audioEl.pause();
                            audioEl.currentTime = 0;
                            return;
                        }

                        // Jika sedang dalam putaran terakhir yang sudah dimulai
                        // (browser fire play lagi setelah buffer/pause) → izinkan tanpa kurangi sisa
                        if (inLastPlay) return;

                        if (sisa <= 0) {
                            // Tidak ada jatah lagi dan bukan dalam putaran aktif → kunci
                            audioEl.pause();
                            lockAudio();
                            return;
                        }

                        // Guard: cegah event 'play' ganda dari browser (double-fire)
                        if (sessionPlaying) return;
                        sessionPlaying = true;

                        // Catat ke server bahwa audio diputar
                        const typeUrl = aid.startsWith('soal') ? 'soal' : 'pilihan';
                        const actualId = aid.split('_')[1];
                        fetch(`{{ url('exam/media') }}/${window.EXAM_ID}/${actualId}/${typeUrl}/played`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        }).catch(e => console.error("Media tracker err:", e));

                        sisa--;
                        updateCounter();
                        if (sisa <= 0) {
                            inLastPlay = true; // Tandai: sedang dalam putaran terakhir
                            try { sessionStorage.setItem(storageKey, '1'); } catch(e) {}
                        }
                    });

                    audioEl.addEventListener('pause', function() {
                        sessionPlaying = false;
                        // JANGAN lock saat pause — bisa terjadi karena buffering browser
                        // atau user sengaja pause dalam putaran yang masih diizinkan
                    });

                    audioEl.addEventListener('ended', function() {
                        sessionPlaying = false;
                        inLastPlay = false; // Putaran selesai
                        if (sisa <= 0) {
                            lockAudio(); // Kunci setelah audio benar-benar selesai didengar
                        }
                    });
                });
            }
            setupAudioLimiter('.soal-audio');
            setupAudioLimiter('.opsi-audio');

            // ── MEDIA PRELOADING ──
            window.MEDIA_PRELOAD_REGISTRY = @json($mediaRegistry);

            const preloadedUrls = new Set();
            function preloadMedia(page) {
                const urls = window.MEDIA_PRELOAD_REGISTRY[page];
                if (!urls) return;
                urls.forEach(url => {
                    if (preloadedUrls.has(url)) return;
                    preloadedUrls.add(url);
                    
                    if (url.includes('.mp3') || url.includes('/media/')) {
                        const a = new Audio();
                        a.preload = 'auto';
                        a.src = url;
                    } else {
                        const img = new Image();
                        img.src = url;
                    }
                });
            }

            // Preload next 5 pages
            const currentPage = {{ $page }};
            for (let i = 1; i <= 5; i++) {
                preloadMedia(currentPage + i);
            }
        });
</script>

</body>
</html>
