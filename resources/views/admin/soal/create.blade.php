@extends('layouts.admin')

@section('header')
<div class="flex items-center space-x-4">
    <a href="{{ route('admin.soal.index') }}" class="text-slate-500 hover:text-slate-700 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <span>Tambah Soal Baru</span>
</div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-4xl mx-auto">
    {{-- Paket Info Banner --}}
    @if($paketSoal)
    <div style="background:#eff6ff;border-bottom:1px solid #bfdbfe;padding:14px 28px;display:flex;align-items:center;gap:12px;">
        <svg style="width:18px;height:18px;color:#2563eb;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <div>
            <span style="font-size:12px;color:#64748b;">Menambah soal ke paket:</span>
            <span style="font-size:13px;font-weight:700;color:#1e40af;margin-left:6px;">{{ $paketSoal->nama }}</span>
        </div>
    </div>
    @else
    <div style="background:#fefce8;border-bottom:1px solid #fde68a;padding:14px 28px;">
        <span style="font-size:13px;color:#92400e;">⚠️ Pilih paket terlebih dahulu dari <a href="{{ route('admin.paket-soal.index') }}" style="color:#b45309;font-weight:600;">Bank Soal</a>.</span>
    </div>
    @endif

    <form action="{{ route('admin.soal.store') }}" method="POST" class="p-8">
        @csrf
        @if($paketSoal)
        <input type="hidden" name="paket_soal_id" value="{{ $paketSoal->id }}">
        @endif

        <div class="space-y-8">
            <!-- Tipe Soal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
                <div>
                    <label for="tipe" class="block text-sm font-medium text-slate-700">Tipe Soal</label>
                    <select id="tipe" name="tipe" required class="mt-1 shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md bg-slate-50">
                        <option value="pilihan_ganda" {{ old('tipe') == 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda (Tunggal)</option>
                        <option value="multiple_choice" {{ old('tipe') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice (Lebih dari 1 Jawaban)</option>
                        <option value="essay" {{ old('tipe') == 'essay' ? 'selected' : '' }}>Essay (Teks Bebas)</option>
                        <option value="audio" {{ old('tipe') == 'audio' ? 'selected' : '' }}>Listening / Choukai (Audio & Pilihan Ganda)</option>
                        <option value="pilihan_ganda_audio" {{ old('tipe') == 'pilihan_ganda_audio' ? 'selected' : '' }}>Pilihan Ganda Audio (Jawaban berupa Audio)</option>
                        <option value="pilihan_ganda_gambar" {{ old('tipe') == 'pilihan_ganda_gambar' ? 'selected' : '' }}>Pilihan Ganda Gambar (Jawaban berupa Gambar)</option>
                    </select>
                </div>
                <div>
                    <label for="poin" class="block text-sm font-medium text-slate-700">Poin Soal (Bobot Nilai)</label>
                    <input type="number" name="poin" id="poin" value="{{ old('poin', 10) }}" required min="1" class="mt-1 shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md">
                </div>
            </div>

            <!-- Pertanyaan -->
            <div>
                <label for="pertanyaan" class="block text-sm font-medium text-slate-700 mb-2">Teks Pertanyaan</label>
                <p class="text-xs text-slate-500 mb-2">Anda dapat menggunakan HTML dasar (seperti &lt;br&gt; untuk garis baru, atau &lt;b&gt; untuk teks tebal).</p>
                <textarea id="pertanyaan" name="pertanyaan" rows="4" required class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md">{{ old('pertanyaan') }}</textarea>
                @error('pertanyaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Audio Selection (Hidden by default unless type=audio) -->
                <div id="audio_section" class="bg-blue-50 border border-blue-200 rounded-lg p-5" style="display: none;">
                    <label for="audio_path" class="block text-sm font-medium text-blue-800 flex items-center mb-2">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M8 12h.01M12 12h.01M16 12h.01M9 16H5a2 2 0 01-2-2V10a2 2 0 012-2h4l5-5v18l-5-5z"></path></svg>
                        Pilih File Audio Pendukung
                    </label>
                    <select id="audio_path" name="audio_path" class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-blue-300 rounded-md text-slate-700">
                        <option value="">-- Tanpa Audio / Pilih Audio (Opsional jika tipe Choukai) --</option>
                        @foreach($audioFiles as $audio)
                            <option value="audio/{{ $audio }}" {{ old('audio_path') == 'audio/'.$audio ? 'selected' : '' }}>{{ $audio }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-blue-600 mt-2">File audio ini akan disisipkan di atas pertanyaan saat murid mengerjakan ujian.</p>
                </div>

                <!-- Gambar Pendukung -->
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-5">
                    <label for="gambar_path" class="block text-sm font-medium text-orange-800 flex items-center mb-2">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Pilih Gambar Pendukung (Opsional)
                    </label>
                    <select id="gambar_path" name="gambar_path" class="mt-1 shadow-sm focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm border-orange-300 rounded-md text-slate-700">
                        <option value="">-- Tanpa Gambar --</option>
                        @foreach($imageFiles as $image)
                            <option value="gambar/{{ $image }}" {{ old('gambar_path') == 'gambar/'.$image ? 'selected' : '' }}>{{ $image }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-orange-600 mt-2">Gambar ini akan disisipkan tepat di atas teks pertanyaan.</p>
                </div>
            </div>

            <!-- Pilihan Jawaban Section -->
            <div id="pilihan_section">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-medium text-slate-700">Pilihan Jawaban</label>
                    <button type="button" id="add_pilihan" class="inline-flex items-center px-3 py-1.5 border border-slate-300 shadow-sm text-xs font-medium rounded text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500 transition-colors">
                        <svg class="-ml-1 mr-1.5 h-4 w-4 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Tambah Pilihan
                    </button>
                </div>
                
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                    <p class="text-xs text-slate-500 mb-4" id="pilihan_help">Buat pilihan jawaban dan pilih kotak radio (<span class="font-bold">Jawaban Benar</span>) di sebelah opsi yang benar.</p>
                    <div id="pilihan_container" class="space-y-4">
                        <!-- Default Choices -->
                        @for($i = 0; $i < 4; $i++)
                        <div class="flex items-start space-x-3 pilihan-item">
                            <div class="pt-3">
                                <input type="radio" id="jawaban_benar_{{$i}}" name="jawaban_benar" value="{{$i}}" class="jawaban-selector h-5 w-5 text-accent-600 focus:ring-accent-500 border-slate-300" {{ old('jawaban_benar') == $i ? 'checked' : ($i == 0 ? 'checked' : '') }}>
                            </div>
                            <div class="flex-1 space-y-2">
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-slate-500 sm:text-sm font-bold">{{ chr(65 + $i) }}.</span>
                                    </div>
                                    <input type="text" name="pilihan[{{$i}}]" class="focus:ring-accent-500 focus:border-accent-500 block w-full pl-8 sm:text-sm border-slate-300 rounded-md py-2" placeholder="Opsi jawaban teks (jika perlu)..." value="{{ old('pilihan.'.$i) }}">
                                </div>
                                <div class="media-input-wrapper flex gap-2 w-full" style="display:none;">
                                    <select name="pilihan_media[{{$i}}]" class="audio-select bg-blue-50 border-blue-200 focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm rounded-md py-2" style="display:none;" disabled>
                                        <option value="">-- Pilih Audio Opsi {{ chr(65 + $i) }} --</option>
                                        @foreach($audioFiles as $f) <option value="audio/{{$f}}">{{$f}}</option> @endforeach
                                    </select>
                                    <select name="pilihan_media[{{$i}}]" class="image-select bg-orange-50 border-orange-200 focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm rounded-md py-2" style="display:none;" disabled>
                                        <option value="">-- Pilih Gambar Opsi {{ chr(65 + $i) }} --</option>
                                        @foreach($imageFiles as $f) <option value="gambar/{{$f}}">{{$f}}</option> @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="button" class="remove-pilihan text-slate-400 hover:text-red-500 pt-3 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
            
        </div>

        <div style="padding-top:40px;display:flex;justify-content:flex-end;border-top:1px solid #f1f5f9;margin-top:32px;gap:12px;">
            <a href="{{ $paketSoal ? route('admin.paket-soal.show', $paketSoal) : route('admin.paket-soal.index') }}"
               style="background:#fff;border:1px solid #cbd5e1;border-radius:8px;padding:8px 16px;display:inline-flex;justify-content:center;font-size:14px;font-weight:500;color:#334155;text-decoration:none;">
                Batal
            </a>
            <button type="submit"
                style="display:inline-flex;justify-content:center;padding:8px 24px;border:none;box-shadow:0 1px 2px rgba(0,0,0,.1);font-size:14px;font-weight:600;border-radius:8px;color:#fff;background:#4f46e5;cursor:pointer;">
                Simpan Soal
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipeSelect = document.getElementById('tipe');
        const audioSection = document.getElementById('audio_section');
        const pilihanSection = document.getElementById('pilihan_section');
        const optionsSelectors = document.querySelectorAll('.jawaban-selector');
        
        // Pass server side arrays to JS to compose dynamic HTML easily
        const audioFiles = @json($audioFiles);
        const imageFiles = @json($imageFiles);
        
        function updateUIBasedOnType() {
            const val = tipeSelect.value;
            
            // Toggle Audio Section
            if (val === 'audio' || val === 'pilihan_ganda_audio') {
                audioSection.style.display = 'block';
            } else {
                audioSection.style.display = 'none';
                document.getElementById('audio_path').value = '';
            }
            
            // Toggle Pilihan Section
            if (val === 'essay') {
                pilihanSection.style.display = 'none';
                // disable all inputs
                document.querySelectorAll('#pilihan_container input').forEach(el => el.disabled = true);
                document.querySelectorAll('#pilihan_container select').forEach(el => el.disabled = true);
            } else {
                pilihanSection.style.display = 'block';
                document.querySelectorAll('#pilihan_container input').forEach(el => el.disabled = false);
                
                // Show/hide media selectors for options
                const mdWrappers = document.querySelectorAll('.media-input-wrapper');
                mdWrappers.forEach(wrap => {
                    const audSel = wrap.querySelector('.audio-select');
                    const imgSel = wrap.querySelector('.image-select');
                    
                    if (val === 'pilihan_ganda_audio') {
                        wrap.style.display = 'flex';
                        audSel.style.display = 'block'; audSel.disabled = false;
                        imgSel.style.display = 'none'; imgSel.disabled = true;
                    } else if (val === 'pilihan_ganda_gambar') {
                        wrap.style.display = 'flex';
                        imgSel.style.display = 'block'; imgSel.disabled = false;
                        audSel.style.display = 'none'; audSel.disabled = true;
                    } else {
                        wrap.style.display = 'none';
                        audSel.disabled = true; imgSel.disabled = true;
                    }
                });

                // Change between radio and checkbox based on multiple_choice
                const isMultiple = val === 'multiple_choice';
                document.querySelectorAll('.jawaban-selector').forEach(el => {
                    el.type = isMultiple ? 'checkbox' : 'radio';
                    el.name = isMultiple ? 'jawaban_benar[]' : 'jawaban_benar';
                });
                
                if (isMultiple) {
                    document.getElementById('pilihan_help').innerHTML = 'Buat pilihan jawaban dan centang kotak (<span class="font-bold">Jawaban Benar</span>) di sebelah opsi yang benar (Anda bisa pilih lebih dari 1).';
                } else if (val === 'pilihan_ganda_audio' || val === 'pilihan_ganda_gambar') {
                    document.getElementById('pilihan_help').innerHTML = 'Buat pilihan media jawaban dan pilih kotak radio (<span class="font-bold">Jawaban Benar</span>) di sebelah opsi media yang benar. Teks bersifat opsional.';
                } else {
                    document.getElementById('pilihan_help').innerHTML = 'Buat pilihan jawaban dan pilih kotak radio (<span class="font-bold">Jawaban Benar</span>) di sebelah SATU opsi yang benar.';
                }
            }
        }
        
        tipeSelect.addEventListener('change', updateUIBasedOnType);
        updateUIBasedOnType(); // init
        
        // Add new format logic
        let optionCount = 4;
        document.getElementById('add_pilihan').addEventListener('click', function() {
            const container = document.getElementById('pilihan_container');
            const letter = String.fromCharCode(65 + optionCount); // A, B, C, D, E...
            
            const isMultiple = tipeSelect.value === 'multiple_choice';
            const inputType = isMultiple ? 'checkbox' : 'radio';
            const inputName = isMultiple ? 'jawaban_benar[]' : 'jawaban_benar';
            
            let audioOpts = `<option value="">-- Pilih Audio Opsi ${letter} --</option>`;
            audioFiles.forEach(f => { audioOpts += `<option value="audio/${f}">${f}</option>`; });
            
            let imageOpts = `<option value="">-- Pilih Gambar Opsi ${letter} --</option>`;
            imageFiles.forEach(f => { imageOpts += `<option value="gambar/${f}">${f}</option>`; });

            const html = `
                <div class="flex items-start space-x-3 pilihan-item mt-4">
                    <div class="pt-3">
                        <input type="${inputType}" name="${inputName}" value="${optionCount}" class="jawaban-selector h-5 w-5 text-accent-600 focus:ring-accent-500 border-slate-300">
                    </div>
                    <div class="flex-1 space-y-2">
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-slate-500 sm:text-sm font-bold">${letter}.</span>
                            </div>
                            <input type="text" name="pilihan[${optionCount}]" class="focus:ring-accent-500 focus:border-accent-500 block w-full pl-8 sm:text-sm border-slate-300 rounded-md py-2" placeholder="Opsi jawaban teks (jika perlu)...">
                        </div>
                        <div class="media-input-wrapper flex gap-2 w-full" style="display:none;">
                            <select name="pilihan_media[${optionCount}]" class="audio-select bg-blue-50 border-blue-200 focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm rounded-md py-2" style="display:none;" disabled>
                                ${audioOpts}
                            </select>
                            <select name="pilihan_media[${optionCount}]" class="image-select bg-orange-50 border-orange-200 focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm rounded-md py-2" style="display:none;" disabled>
                                ${imageOpts}
                            </select>
                        </div>
                    </div>
                    <button type="button" class="remove-pilihan text-slate-400 hover:text-red-500 pt-3 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            optionCount++;
            
            // Re-trigger GUI update to show/hide new inputs properly based on current type
            updateUIBasedOnType();
            
            // Attach event listener to new remove button
            attachRemoveListeners();
        });
        
        function attachRemoveListeners() {
            document.querySelectorAll('.remove-pilihan').forEach(btn => {
                // remove old listeners securely by replacing node if necessary
                btn.onclick = function() {
                    if (document.querySelectorAll('.pilihan-item').length > 2) {
                        this.closest('.pilihan-item').remove();
                    } else {
                        alert('Minimal harus ada 2 pilihan jawaban.');
                    }
                };
            });
        }
        
        attachRemoveListeners();
    });
</script>
@endsection
