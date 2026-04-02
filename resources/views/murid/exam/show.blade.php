<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ujian CBT - {{ $ujian->judul }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Set variables for JS logic
        window.EXAM_ID = {{ $ujian_peserta->id }};
        window.TIMER_SECONDS = {{ $sisaDetik }};
        window.AUTO_SAVE_URL = "{{ route('murid.exam.autoSave', $ujian_peserta) }}";
        window.REPORT_CHEAT_URL = "{{ route('murid.exam.reportCheat', $ujian_peserta) }}";
        window.FINISH_URL = "{{ route('murid.exam.finish', $ujian_peserta) }}";
    </script>
</head>
<body class="h-full font-sans antialiased flex flex-col pt-16">

    <!-- Topbar (Timer & Identitas) -->
    <header class="fixed top-0 inset-x-0 bg-white border-b border-slate-200 z-50 h-16 flex items-center justify-between px-4 lg:px-8 shadow-sm">
        <div class="flex items-center space-x-4 max-w-sm truncate">
            <h1 class="text-sm font-bold text-slate-800">{{ $ujian->judul }}</h1>
            <span class="hidden md:block text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-full">Soal {{ $page }} dari {{ $totalSoal }}</span>
        </div>
        
        <div class="flex items-center space-x-6">
            <div id="save-indicator" class="hidden items-center space-x-1 text-xs font-medium text-slate-400">
                <svg class="animate-spin h-3.5 w-3.5 text-accent-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>Menyimpan...</span>
            </div>
            <div id="save-success" class="hidden items-center space-x-1 text-xs font-medium text-green-500">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Tersimpan</span>
            </div>

            <div class="flex items-center space-x-2 bg-slate-800 text-white px-4 py-1.5 rounded-lg border border-slate-700 shadow-inner">
                <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span id="countdown_timer" class="font-mono text-sm tracking-widest font-bold">00:00:00</span>
            </div>
        </div>
    </header>

    <div class="flex-1 flex flex-col lg:flex-row max-w-7xl mx-auto w-full">
        
        <!-- Left Sidebar: Navigasi Nomor Soal -->
        <aside class="w-full lg:w-64 flex-shrink-0 border-b lg:border-b-0 lg:border-r border-slate-200 bg-white p-4 lg:min-h-[calc(100vh-4rem)]">
            <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-4">Navigasi Soal</h3>
            
            <div class="grid grid-cols-5 gap-2 lg:gap-3">
                @for ($i = 1; $i <= $totalSoal; $i++)
                    @php
                        $isCurrent = $i == $page;
                        $isAnswered = in_array($soals[$i-1]->id, $answeredSoalIds);
                        
                        $btnClass = 'flex justify-center items-center h-10 w-full rounded border text-sm font-medium transition-colors hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-1';
                        
                        if ($isCurrent) {
                            $btnClass .= ' border-accent-600 bg-accent-50 text-accent-700 ring-1 ring-accent-600';
                        } elseif ($isAnswered) {
                            $btnClass .= ' border-green-500 bg-green-500 text-white';
                        } else {
                            $btnClass .= ' border-slate-300 bg-white text-slate-600 hover:bg-slate-50';
                        }
                    @endphp
                    
                    <a href="{{ route('murid.exam.show', ['ujian_peserta' => $ujian_peserta, 'page' => $i]) }}" class="{{ $btnClass }}">
                        {{ $i }}
                    </a>
                @endfor
            </div>

            <div class="mt-8 border-t border-slate-200 pt-6">
                <form id="finish-form" action="{{ route('murid.exam.finish', $ujian_peserta) }}" method="POST" onsubmit="return confirm('Anda yakin ingin mengakhiri ujian ini? Anda tidak bisa mengulangi atau memperbaiki jawaban setelah Anda selesai.');">
                    @csrf
                    <button type="submit" class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-800 hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-colors">
                        Akhiri Ujian
                    </button>
                    <p class="text-[10px] text-slate-500 mt-2 text-center text-balance">Pastikan semua kotak nomor berwarna hijau sebelum mengakhiri.</p>
                </form>
            </div>
        </aside>

        <!-- Main Content: Area Soal -->
        <main class="flex-1 bg-slate-50 p-4 lg:p-8">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                
                <!-- Helper Tipe Soal & Poin -->
                <div class="bg-slate-50 border-b border-slate-100 px-6 py-3 flex justify-between items-center text-xs text-slate-500">
                    <span class="uppercase tracking-wider font-semibold">{{ str_replace('_', ' ', $currentSoal->tipe) }}</span>
                    <span>Bobot Maksimal: <strong>{{ $currentSoal->poin }} Poin</strong></span>
                </div>
                
                <div class="p-6 lg:p-8">
                    <!-- Pertanyaan Utama -->
                    <div class="prose prose-slate max-w-none text-slate-800 text-base md:text-lg mb-8">
                        <span class="font-bold mr-2 text-slate-400">{{ $page }}.</span> 
                        {!! nl2br($currentSoal->pertanyaan) !!}
                    </div>

                    <!-- Audio Player jika tipe soal Choukai -->
                    @if($currentSoal->tipe === 'audio' && $currentSoal->audio_path)
                    <div class="mb-8 p-4 bg-accent-50 border border-accent-100 rounded-lg max-w-md">
                        <div class="flex items-center mb-2">
                            <svg class="h-5 w-5 text-accent-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M8 12h.01M12 12h.01M16 12h.01M9 16H5a2 2 0 01-2-2V10a2 2 0 012-2h4l5-5v18l-5-5z"></path></svg>
                            <span class="text-sm font-medium text-accent-900">Audio Pendukung Ujian</span>
                        </div>
                        <audio controls controlsList="nodownload" class="w-full h-10">
                            <!-- In real production we use Storage::url, for current setting we use asset('storage/...') -->
                            <source src="{{ asset('storage/' . $currentSoal->audio_path) }}" type="audio/mpeg">
                            Browser Anda tidak mendukung elemen audio.
                        </audio>
                    </div>
                    @endif

                    <!-- Area Input Jawaban -->
                    <form id="answer-form" class="space-y-3">
                        <input type="hidden" name="soal_id" value="{{ $currentSoal->id }}">
                        
                        @if($currentSoal->tipe === 'pilihan_ganda' || $currentSoal->tipe === 'audio')
                            <div class="space-y-4 pt-2">
                                @foreach($currentSoal->pilihanJawabans as $index => $opsi)
                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:bg-slate-50 transition-colors {{ ($jawabanSaatIni && $jawabanSaatIni->pilihan_jawaban_id == $opsi->id) ? 'border-accent-600 ring-1 ring-accent-600 bg-accent-50' : 'border-slate-300' }}">
                                        <input type="radio" name="jawaban" value="{{ $opsi->id }}" class="sr-only auto-save-trigger" {{ ($jawabanSaatIni && $jawabanSaatIni->pilihan_jawaban_id == $opsi->id) ? 'checked' : '' }}>
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium text-slate-900">
                                                    <span class="inline-block w-6 font-bold text-slate-400">{{ chr(65 + $index) }}.</span> 
                                                    {{ $opsi->teks }}
                                                </span>
                                            </span>
                                        </span>
                                        <!-- Circle visual indicator -->
                                        <svg class="h-5 w-5 {{ ($jawabanSaatIni && $jawabanSaatIni->pilihan_jawaban_id == $opsi->id) ? 'text-accent-600' : 'text-transparent' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    </label>
                                @endforeach
                            </div>

                        @elseif($currentSoal->tipe === 'multiple_choice')
                            @php
                                $checkedMultiple = $jawabanSaatIni && $jawabanSaatIni->jawaban_multiple ? json_decode($jawabanSaatIni->jawaban_multiple, true) : [];
                            @endphp
                            <div class="space-y-4 pt-2">
                                <p class="text-xs text-slate-500 mb-2 italic">* Anda dapat memilih lebih dari 1 jawaban.</p>
                                @foreach($currentSoal->pilihanJawabans as $index => $opsi)
                                    @php $isChecked = in_array($opsi->id, $checkedMultiple); @endphp
                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:bg-slate-50 transition-colors {{ $isChecked ? 'border-accent-600 ring-1 ring-accent-600 bg-accent-50' : 'border-slate-300' }}">
                                        <input type="checkbox" name="jawaban[]" value="{{ $opsi->id }}" class="sr-only auto-save-trigger-mc" {{ $isChecked ? 'checked' : '' }}>
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium text-slate-900">
                                                    <span class="inline-block w-6 font-bold text-slate-400">{{ chr(65 + $index) }}.</span> 
                                                    {{ $opsi->teks }}
                                                </span>
                                            </span>
                                        </span>
                                        <!-- Checkbox visual indicator -->
                                        <div class="h-5 w-5 border border-slate-300 rounded overflow-hidden flex items-center justify-center {{ $isChecked ? 'bg-accent-600 border-accent-600' : 'bg-white' }}">
                                            <svg class="h-3 w-3 text-white {{ $isChecked ? 'block' : 'hidden' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            
                        @elseif($currentSoal->tipe === 'essay')
                            <div class="pt-2">
                                <textarea name="jawaban" rows="6" placeholder="Ketik jawaban Anda di sini..." class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md auto-save-trigger-typing">{{ $jawabanSaatIni ? $jawabanSaatIni->jawaban_text : '' }}</textarea>
                                <p class="mt-2 text-xs text-slate-500">Jawaban akan otomatis tersimpan beberapa saat setelah Anda berhenti mengetik.</p>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Prevs & Next Buttons -->
            <div class="flex justify-between items-center">
                @if($page > 1)
                    <a href="{{ route('murid.exam.show', ['ujian_peserta' => $ujian_peserta, 'page' => $page - 1]) }}" class="inline-flex items-center px-4 py-2 border border-slate-300 shadow-sm text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                        &larr; Soal Sebelumnya
                    </a>
                @else
                    <div></div> <!-- placeholder for spacing -->
                @endif
                
                @if($page < $totalSoal)
                    <a href="{{ route('murid.exam.show', ['ujian_peserta' => $ujian_peserta, 'page' => $page + 1]) }}" class="inline-flex items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-colors">
                        Soal Selanjutnya &rarr;
                    </a>
                @else
                    <button type="button" onclick="document.getElementById('finish-form').submit()" class="inline-flex items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-slate-800 hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-colors">
                        Akhiri Ujian
                    </button>
                @endif
            </div>
            
        </main>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- TIMER LOGIC ---
        let timeRemaining = window.TIMER_SECONDS;
        const timerDisplay = document.getElementById('countdown_timer');
        
        function updateTimer() {
            if (timeRemaining <= 0) {
                // Time up! Submit auto
                timerDisplay.innerText = "00:00:00";
                document.getElementById('finish-form').submit();
                return;
            }
            
            const h = Math.floor(timeRemaining / 3600).toString().padStart(2, '0');
            const m = Math.floor((timeRemaining % 3600) / 60).toString().padStart(2, '0');
            const s = (timeRemaining % 60).toString().padStart(2, '0');
            
            timerDisplay.innerText = `${h}:${m}:${s}`;
            timeRemaining--;
        }
        
        // Initial call + interval
        updateTimer();
        setInterval(updateTimer, 1000);
        
        // --- ANTI-CHEAT: TAB VISIBILITY ---
        let isNavigating = false;

        // Tandai jika murid sedang melakukan navigasi sengaja
        document.querySelectorAll('a, button, form').forEach(el => {
            el.addEventListener('click', () => {
                isNavigating = true;
            });
        });

        document.getElementById('finish-form').addEventListener('submit', () => {
            isNavigating = true;
        });

        document.addEventListener("visibilitychange", function() {
            if (document.hidden && !isNavigating) {
                // Murid pindah tab atau minimize
                triggerAntiCheatLog();
            }
        });

        // Anti Alt-Tab (Blur event on window)
        window.addEventListener('blur', function() {
            if (!isNavigating) {
                triggerAntiCheatLog();
            }
        });

        let cheatReported = false;
        function triggerAntiCheatLog() {
            if (cheatReported) return;
            cheatReported = true;
            
            fetch(window.REPORT_CHEAT_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(response => response.json())
              .then(data => {
                  if(data.redirect) window.location.href = data.redirect;
              }).catch(err => {
                  window.location.reload(); // fallback if error to prompt server route logic
              });
        }

        // --- AUTO-SAVE LOGIC ---
        const saveIndicator = document.getElementById('save-indicator');
        const saveSuccess = document.getElementById('save-success');
        let saveTimeout = null;

        function submitAnswer(dataObj) {
            saveIndicator.classList.remove('hidden');
            saveIndicator.classList.add('flex');
            saveSuccess.classList.add('hidden');
            saveSuccess.classList.remove('flex');
            
            fetch(window.AUTO_SAVE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(dataObj)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    saveIndicator.classList.add('hidden');
                    saveIndicator.classList.remove('flex');
                    saveSuccess.classList.remove('hidden');
                    saveSuccess.classList.add('flex');
                    
                    // Hilangkan success setelah 2 detik
                    setTimeout(() => {
                        saveSuccess.classList.add('hidden');
                        saveSuccess.classList.remove('flex');
                    }, 2000);
                }
            });
        }

        const form = document.getElementById('answer-form');
        const soalId = form.querySelector('[name="soal_id"]').value;

        // Visual update for radio/checkbox styling
        function updateLabels() {
            form.querySelectorAll('label').forEach(lbl => {
                const input = lbl.querySelector('input');
                const isRadio = input.type === 'radio';
                const svgRadio = lbl.querySelector('svg');
                const boxCheck = lbl.querySelector('.border.rounded.flex > svg');
                
                if (input.checked) {
                    lbl.classList.add('border-accent-600', 'ring-1', 'ring-accent-600', 'bg-accent-50');
                    lbl.classList.remove('border-slate-300');
                    if(isRadio && svgRadio) svgRadio.classList.replace('text-transparent', 'text-accent-600');
                    if(!isRadio && boxCheck) {
                        boxCheck.parentElement.classList.replace('bg-white', 'bg-accent-600');
                        boxCheck.parentElement.classList.replace('border-slate-300', 'border-accent-600');
                        boxCheck.classList.remove('hidden');
                    }
                } else {
                    lbl.classList.remove('border-accent-600', 'ring-1', 'ring-accent-600', 'bg-accent-50');
                    lbl.classList.add('border-slate-300');
                    if(isRadio && svgRadio) svgRadio.classList.replace('text-accent-600', 'text-transparent');
                    if(!isRadio && boxCheck) {
                        boxCheck.parentElement.classList.replace('bg-accent-600', 'bg-white');
                        boxCheck.parentElement.classList.replace('border-accent-600', 'border-slate-300');
                        boxCheck.classList.add('hidden');
                    }
                }
            });
        }

        // For Radio (PG & Audio)
        document.querySelectorAll('.auto-save-trigger').forEach(el => {
            el.addEventListener('change', function() {
                updateLabels();
                submitAnswer({ soal_id: soalId, jawaban: this.value });
            });
        });

        // For Checkbox (Multiple Choice)
        document.querySelectorAll('.auto-save-trigger-mc').forEach(el => {
            el.addEventListener('change', function() {
                updateLabels();
                // Collect all checked
                let checked = [];
                document.querySelectorAll('.auto-save-trigger-mc:checked').forEach(c => checked.push(c.value));
                submitAnswer({ soal_id: soalId, jawaban: JSON.stringify(checked) });
            });
        });

        // For Textarea (Essay)
        document.querySelectorAll('.auto-save-trigger-typing').forEach(el => {
            el.addEventListener('input', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    submitAnswer({ soal_id: soalId, jawaban: this.value });
                }, 1000); // 1 detik setelah berhenti mengetik
            });
        });
        
    });
</script>

</body>
</html>
