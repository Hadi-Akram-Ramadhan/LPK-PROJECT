<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EPS-TOPIK CBT Simulator - {{ $ujian->judul }}</title>
    
    <script>
        window.EXAM_ID = {{ $ujian_peserta->id }};
        window.TIMER_SECONDS = {{ $sisaDetik }};
        window.IS_TRYOUT = {{ $ujian->jenis_ujian === 'tryout' ? 'true' : 'false' }};
        window.AUTO_SAVE_URL = "{{ route('murid.exam.autoSave', $ujian_peserta, false) }}";
        window.REPORT_CHEAT_URL = "{{ route('murid.exam.reportCheat', $ujian_peserta, false) }}";
        window.FINISH_URL = "{{ route('murid.exam.finish', $ujian_peserta, false) }}";
    </script>

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
            background-color: #3b82f6;
            color: #fff;
            border-color: #3b82f6;
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

        /* RESPONSIVE LANDSCAPE MOBILE (MODIFIKASI DISINI) */
        @media screen and (max-height: 500px) {
            .cbt-container {
                height: 100vh !important;
                width: 100vw !important;
                max-width: none !important;
                border: none !important;
                min-height: 0 !important;
                display: flex !important;
                flex-direction: column !important;
            }
            .cbt-header, .cbt-footer {
                height: 35px !important;
                flex-shrink: 0 !important;
            }
            .hdr-timer, .hdr-val, .hdr-btn-finish { font-size: 11px !important; }
            .hdr-label { display: none !important; }
            
            .cbt-main {
                flex-direction: row !important;
                height: calc(100vh - 70px) !important;
                overflow: hidden !important;
            }
            .cbt-left {
                flex: 1 !important;
                width: 50% !important;
                border-right: 1px solid #000 !important;
                padding: 10px !important;
                overflow-y: auto !important;
                font-size: 13px !important;
            }
            .cbt-right {
                flex: 1 !important;
                width: 50% !important;
                padding: 10px !important;
                overflow-y: auto !important;
            }
            .opt-body {
                padding: 4px 10px !important;
            }
            .opt-circle {
                width: 20px !important;
                height: 20px !important;
                margin-right: 8px !important;
                font-size: 10px !important;
            }
            .opt-text { font-size: 12px !important; }
            .question-flex { font-size: 13px !important; margin-bottom: 5px !important; }
            
            /* Essay adjustment */
            .cbt-right textarea {
                height: calc(100% - 25px) !important;
                min-height: 100px !important;
                font-size: 13px !important;
                padding: 8px !important;
            }
            .cbt-right > div {
                padding: 0 !important;
                height: 100% !important;
            }
            .ftr-btn { font-size: 11px !important; }
        }
    </style>
</head>
<body>

    <div id="landscape-overlay">
        <svg class="rotate-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        <h2 style="font-size: 26px; font-weight: 800; margin-bottom: 15px; letter-spacing: -0.5px;">Gunakan Mode Landscape</h2>
        <p style="font-size: 15px; color: #94a3b8; max-width: 300px; line-height: 1.6;">Ujian simulator CBT tidak dapat dikerjakan dalam mode layar potrait. Mohon putar ponsel Anda ke posisi landscape untuk melanjutkan.</p>
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
                <button class="hdr-btn-finish" onclick="document.getElementById('finish-form').submit()">SUBMIT</button>
            </div>
        </header>

        <form id="finish-form" action="{{ route('murid.exam.finish', $ujian_peserta) }}" method="POST" onsubmit="return confirm('Are you sure you want to finish the exam?');" style="display: none;">
            @csrf
        </form>

        <form id="answer-form" style="display: none;">
            <input type="hidden" name="soal_id" value="{{ $currentSoal->id }}">
        </form>

        <!-- MAIN -->
        <main class="cbt-main">
            <!-- QUESTION (LEFT) -->
            <div class="cbt-left">
                <div class="question-flex">
                    <div class="question-number">{{ $page }}.</div>
                    <div class="question-text">
                        {!! $currentSoal->pertanyaan !!}
                    </div>
                </div>

                @if($currentSoal->audio_path)
                    <div class="audio-wrapper">
                        <audio controls controlsList="nodownload" class="audio-player">
                            <source src="{{ asset('storage/' . $currentSoal->audio_path) }}" type="audio/mpeg">
                        </audio>
                    </div>
                @endif
                
                @if($currentSoal->gambar_path)
                    <div style="text-align: center; margin-top: 20px;">
                        <img src="{{ asset('storage/' . $currentSoal->gambar_path) }}" style="max-width: 100%; border: 1px solid #d1d5db; padding: 4px; border-radius: 8px;">
                    </div>
                @endif
            </div>

            <!-- OPTIONS (RIGHT) -->
            <div class="cbt-right">
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
                                        <audio controls controlsList="nodownload" style="height: 40px; max-width: 220px; outline:none;">
                                            <source src="{{ asset('storage/' . $opsi->media_path) }}" type="audio/mpeg">
                                        </audio>
                                    </div>
                                @elseif($opsi->media_tipe === 'gambar' && $opsi->media_path)
                                    <img src="{{ asset('storage/' . $opsi->media_path) }}" style="max-height: 120px; border-radius: 6px; border: 1px solid #e5e7eb; padding: 2px; max-width: 100%; object-fit: contain;">
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
                                        <audio controls controlsList="nodownload" style="height: 40px; max-width: 220px; outline:none;">
                                            <source src="{{ asset('storage/' . $opsi->media_path) }}" type="audio/mpeg">
                                        </audio>
                                    </div>
                                @elseif($opsi->media_tipe === 'gambar' && $opsi->media_path)
                                    <img src="{{ asset('storage/' . $opsi->media_path) }}" style="max-height: 120px; border-radius: 6px; border: 1px solid #e5e7eb; padding: 2px; max-width: 100%; object-fit: contain;">
                                @endif
                            </div>
                        </div>
                    </label>
                    @endforeach
                @elseif($currentSoal->tipe === 'essay')
                    <div style="padding: 35px; display: flex; flex-direction: column; height: 100%;">
                        <p style="font-size: 18px; margin-top: 0; color: #111;">Type Your Answer:</p>
                        <textarea name="jawaban" form="answer-form" class="auto-save-trigger-typing" style="flex: 1; width: 100%; border: 1px solid #9ca3af; padding: 15px; font-size: 18px; resize: none; border-radius: 8px;"></textarea>
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
    <div class="bottom-text">EPS-TOPIK CBT Simulator Interface</div>


    <!-- MODAL SHOW ALL QUESTIONS -->
    <div id="modal-show-all" class="modal-overlay">
        <div class="modal-box">
            
            <div class="modal-header">
                <button id="btn-close-modal" style="background:none; border:none; color:#2563eb; cursor:pointer;">&lt; BACK</button>
                <div style="display: flex; gap: 20px; font-size: 13px; color: #4b5563;">
                    <div style="display:flex; align-items:center;">
                        <span style="display:inline-block; width:12px; height:12px; background:#3b82f6; border-radius:50%; margin-right:6px;"></span>
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
                    $halfPoint = floor($totalSoal / 2);
                    if($halfPoint == 0) $halfPoint = $totalSoal;
                @endphp

                <!-- Sesi 1 -->
                <div class="modal-section">
                    <div style="font-size: 18px; border-bottom: 2px solid #e5e7eb; padding-bottom: 15px;">Session 1: Reading</div>
                    <div class="grid-nums">
                        @for ($i = 1; $i <= $halfPoint; $i++)
                            @php
                                $isAnsw = in_array($soals[$i-1]->id, $answeredSoalIds);
                                $isCurr = $i == $page;
                                $cls = "grid-btn " . ($isAnsw ? 'answered ' : '') . ($isCurr ? 'current ' : '');
                            @endphp
                            <a href="{{ route('murid.exam.show', ['ujian_peserta' => $ujian_peserta, 'page' => $i]) }}" class="{{ $cls }}">
                                {{ $i }}
                            </a>
                        @endfor
                    </div>
                </div>

                <!-- Sesi 2 -->
                @if($totalSoal > $halfPoint)
                <div class="modal-section">
                    <div style="font-size: 18px; border-bottom: 2px solid #e5e7eb; padding-bottom: 15px;">Session 2: Listening</div>
                    <div class="grid-nums">
                        @for ($i = $halfPoint + 1; $i <= $totalSoal; $i++)
                            @php
                                $isAnsw = in_array($soals[$i-1]->id, $answeredSoalIds);
                                $isCurr = $i == $page;
                                $cls = "grid-btn " . ($isAnsw ? 'answered ' : '') . ($isCurr ? 'current ' : '');
                            @endphp
                            <a href="{{ route('murid.exam.show', ['ujian_peserta' => $ujian_peserta, 'page' => $i]) }}" class="{{ $cls }}">
                                {{ $i }}
                            </a>
                        @endfor
                    </div>
                </div>
                @endif
            </div>

            <!-- Modal Footer mirrors app footer -->
            <div class="cbt-footer">
                <button class="ftr-btn ftr-prev" id="btn-close-modal-bottom">&lt; BACK</button>
                <div class="ftr-btn ftr-mid" style="color: #6b7280;">QUESTIONS LIST</div>
                <button class="ftr-btn ftr-next" onclick="document.getElementById('finish-form').submit()">Finish</button>
            </div>
        </div>
    </div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // MODAL
        const modal = document.getElementById('modal-show-all');
        document.getElementById('btn-show-all').addEventListener('click', () => modal.style.display = 'flex');
        document.getElementById('btn-close-modal').addEventListener('click', () => modal.style.display = 'none');
        document.getElementById('btn-close-modal-bottom').addEventListener('click', () => modal.style.display = 'none');

        // TIMER
        let timeRemaining = window.TIMER_SECONDS;
        const timerDisplay = document.getElementById('countdown_timer');
        
        function updateTimer() {
            if (timeRemaining <= 0) {
                timerDisplay.innerText = "00:00";
                document.getElementById('finish-form').submit();
                return;
            }
            
            // Format 59:58 (MM:SS)
            const m = Math.floor(timeRemaining / 60).toString().padStart(2, '0');
            const s = Math.floor(timeRemaining % 60).toString().padStart(2, '0');
            
            timerDisplay.innerText = `${m}:${s}`;
            timeRemaining--;
        }
        
        updateTimer();
        setInterval(updateTimer, 1000);
        
        // ANTI CHEAT & TAB DETECTION
        let isNavigating = false;
        
        // Hanya elemen yang memindahkan halaman yang mematikan cheat detector
        document.querySelectorAll('a[href], button[onclick*="submit"]').forEach(el => {
            el.addEventListener('click', () => { isNavigating = true; });
        });
        document.getElementById('finish-form').addEventListener('submit', () => { isNavigating = true; });

        if (!window.IS_TRYOUT) {
            document.addEventListener("visibilitychange", function() {
                if (document.hidden && !isNavigating) triggerAntiCheatLog();
            });
            window.addEventListener('blur', function() {
                // Abaikan blur jika user sedang klik dalam window/iframe
                if (document.activeElement !== document.body && document.activeElement.tagName === 'IFRAME') return;
                if (!isNavigating) triggerAntiCheatLog();
            });
        }

        let cheatReported = false;
        function triggerAntiCheatLog() {
            if (cheatReported) return;
            cheatReported = true;

            // UI Peringatan Kecurangan Langsung Muncul
            const cheatUI = document.createElement('div');
            cheatUI.style.cssText = "position:fixed;inset:0;background:rgba(220,38,38,1);z-index:999999;display:flex;flex-direction:column;align-items:center;justify-content:center;color:white;font-family:sans-serif;text-align:center;";
            cheatUI.innerHTML = `
                <svg style="width:120px;height:120px;margin-bottom:20px;animation: pulse 2s infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h1 style="font-size:3rem;font-weight:900;letter-spacing:1px;margin:0;">PELANGGARAN TERDETEKSI</h1>
                <p style="font-size:1.25rem;margin-top:10px;">Anda terdeteksi berpindah tab atau keluar dari browserujian!</p>
                <div style="margin-top:30px;padding:15px 30px;background:rgba(0,0,0,0.2);border-radius:10px;">
                    <p style="font-size:1rem;margin:0;">Laporan terkirim ke sistem Guru & Admin secara realtime...</p>
                </div>
            `;
            document.body.appendChild(cheatUI);

            fetch(window.REPORT_CHEAT_URL, {
                method: 'POST',
                credentials: 'same-origin',
                keepalive: true, // PENTING: Mencegah browser mobile membatalkan request saat tab diminimize
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(async res => {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    throw new Error(data.message || ("HTTP error " + res.status));
                }
                return data;
            })
            .then(data => { 
                if(data.redirect) {
                    window.location.replace(data.redirect); 
                } else {
                    alert("Akses diblokir (Response tidak lengkap).");
                }
            })
            .catch((err) => {
                console.error("Anti cheat error:", err);
                alert("Sistem gagal memverifikasi sesi (" + err.message + "). Silakan hubungi pengawas.");
                // Tetap di halaman ini tanpa reload
            });
        }

        // AUTO SAVE 
        let saveTimeout = null;
        function submitAnswer(dataObj) {
            fetch(window.AUTO_SAVE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(dataObj)
            });
        }

        const form = document.getElementById('answer-form');
        const soalId = form.querySelector('[name="soal_id"]').value;

        // Auto Save on Select
        document.querySelectorAll('.auto-save-trigger').forEach(el => {
            el.addEventListener('change', function() {
                submitAnswer({ soal_id: soalId, jawaban: this.value });
            });
        });

        document.querySelectorAll('.auto-save-trigger-mc').forEach(el => {
            el.addEventListener('change', function() {
                let checked = [];
                document.querySelectorAll('.auto-save-trigger-mc:checked').forEach(c => checked.push(c.value));
                submitAnswer({ soal_id: soalId, jawaban: JSON.stringify(checked) });
            });
        });

        document.querySelectorAll('.auto-save-trigger-typing').forEach(el => {
            el.addEventListener('input', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => submitAnswer({ soal_id: soalId, jawaban: this.value }), 1000);
            });
        });

    });
</script>

</body>
</html>
