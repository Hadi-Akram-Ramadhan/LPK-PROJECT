{{--
    Shared partial: _soal_form_fields.blade.php
    Usage: @include('admin.soal._form_fields', ['soal' => $soal ?? null, 'audioFiles' => $audioFiles, 'imageFiles' => $imageFiles, 'baseRoute' => 'admin'])
    Includes: tipe dropdown, pertanyaan, gambar/audio picker + upload widget, audio limit, matching UI, pilihan jawaban.
--}}

@php
    $soal = $soal ?? null;
    $baseRoute = $baseRoute ?? 'admin';
    $uploadRoute = $baseRoute . '.soal.uploadMedia';
    $isEdit = !is_null($soal);
@endphp

<div class="space-y-8">
    {{-- ── Tipe & Poin ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
        <div>
            <label for="tipe" class="block text-sm font-medium text-slate-700">Tipe Soal</label>
            <select id="tipe" name="tipe" required
                class="mt-1 shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md bg-slate-50">
                @php
                    $tipes = [
                        'pilihan_ganda'       => 'Pilihan Ganda (Tunggal)',
                        'multiple_choice'     => 'Multiple Choice (Lebih dari 1 Jawaban)',
                        'essay'               => 'Essay (Teks Bebas, Dinilai Manual)',
                        'short_answer'        => 'Short Answer (Jawaban Singkat, Otomatis)',
                        'audio'               => 'Listening / Choukai (Audio & Pilihan Ganda)',
                        'pilihan_ganda_audio' => 'Pilihan Ganda Audio (Jawaban berupa Audio)',
                        'pilihan_ganda_gambar'=> 'Pilihan Ganda Gambar (Jawaban berupa Gambar)',
                        'matching'            => '🔗 Matching (Pasangkan Kata / Gambar)',
                    ];
                    $selectedTipe = old('tipe', $soal?->tipe ?? 'pilihan_ganda');
                @endphp
                @foreach($tipes as $val => $label)
                    <option value="{{ $val }}" {{ $selectedTipe === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="poin" class="block text-sm font-medium text-slate-700">Poin Soal (Bobot Nilai)</label>
            <input type="number" name="poin" id="poin"
                value="{{ old('poin', $soal?->poin ?? 10) }}"
                required min="1" max="1000"
                class="mt-1 shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md">
        </div>
    </div>

    {{-- ── Pertanyaan ── --}}
    <div>
        <label for="pertanyaan" class="block text-sm font-medium text-slate-700 mb-1">Teks Pertanyaan</label>
        <p class="text-xs text-slate-400 mb-2">HTML dasar diperbolehkan (&lt;b&gt;, &lt;i&gt;, &lt;br&gt;). Untuk Matching, tulis instruksi seperti "Pasangkan kata Korea berikut dengan artinya!"</p>
        <textarea id="pertanyaan" name="pertanyaan" rows="4" required maxlength="2000"
            class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md">{{ old('pertanyaan', $soal?->pertanyaan) }}</textarea>
        @error('pertanyaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- ── Audio & Gambar Soal ── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Audio --}}
        <div id="audio_section" class="bg-blue-50 border border-blue-200 rounded-lg p-5" style="display:none;">
            <label class="block text-sm font-medium text-blue-800 mb-2">🎵 File Audio Soal</label>
            <div class="flex gap-2 items-start">
                <select id="audio_path" name="audio_path"
                    class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-blue-300 rounded-md text-slate-700 flex-1">
                    <option value="">-- Tanpa Audio --</option>
                    @foreach($audioFiles as $audio)
                        <option value="audio/{{ $audio }}" {{ old('audio_path', $soal?->audio_path) == 'audio/'.$audio ? 'selected' : '' }}>{{ $audio }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="triggerUpload('soal_audio_upload', 'audio', 'audio_path')"
                    class="mt-1 flex-shrink-0 inline-flex items-center px-3 py-2 border border-blue-300 bg-white text-blue-700 text-xs font-medium rounded-md hover:bg-blue-50 transition-colors whitespace-nowrap">
                    ⬆ Upload Baru
                </button>
            </div>
            <input type="file" id="soal_audio_upload" accept=".mp3,.wav,.ogg" class="hidden">
            <div id="soal_audio_progress" class="mt-2 hidden">
                <div class="w-full bg-blue-100 rounded-full h-1.5">
                    <div id="soal_audio_bar" class="bg-blue-500 h-1.5 rounded-full transition-all" style="width:0%"></div>
                </div>
                <p id="soal_audio_msg" class="text-xs text-blue-600 mt-1"></p>
            </div>

            {{-- Batas Putar Audio --}}
            <div class="mt-4 border-t border-blue-100 pt-4">
                <label for="audio_max_play" class="block text-sm font-medium text-blue-800 mb-1">🔁 Batas Putar Audio</label>
                <div class="flex items-center gap-3">
                    <input type="number" name="audio_max_play" id="audio_max_play"
                        value="{{ old('audio_max_play', $soal?->audio_max_play) }}"
                        min="1" max="99" placeholder="∞"
                        class="w-24 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-blue-300 rounded-md text-center">
                    <p class="text-xs text-blue-600">Kosongkan = tidak terbatas. Isi angka (misal: 2) = murid hanya bisa putar 2 kali.</p>
                </div>
            </div>
        </div>

        {{-- Gambar Soal --}}
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-5">
            <label class="block text-sm font-medium text-orange-800 mb-2">🖼 Gambar Pendukung (Opsional)</label>
            <div class="flex gap-2 items-start">
                <select id="gambar_path" name="gambar_path"
                    class="mt-1 shadow-sm focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm border-orange-300 rounded-md text-slate-700 flex-1"
                    onchange="previewMainImage(this)">
                    <option value="">-- Tanpa Gambar --</option>
                    @foreach($imageFiles as $image)
                        <option value="gambar/{{ $image }}" {{ old('gambar_path', $soal?->gambar_path) == 'gambar/'.$image ? 'selected' : '' }}>{{ $image }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="triggerUpload('soal_img_upload', 'gambar', 'gambar_path')"
                    class="mt-1 flex-shrink-0 inline-flex items-center px-3 py-2 border border-orange-300 bg-white text-orange-700 text-xs font-medium rounded-md hover:bg-orange-50 transition-colors whitespace-nowrap">
                    ⬆ Upload Baru
                </button>
            </div>
            <input type="file" id="soal_img_upload" accept=".jpg,.jpeg,.png,.webp" class="hidden">
            <div id="soal_img_progress" class="mt-2 hidden">
                <div class="w-full bg-orange-100 rounded-full h-1.5">
                    <div id="soal_img_bar" class="bg-orange-500 h-1.5 rounded-full transition-all" style="width:0%"></div>
                </div>
                <p id="soal_img_msg" class="text-xs text-orange-600 mt-1"></p>
            </div>
            <div id="main_image_preview_container" class="mt-3 text-center" style="{{ old('gambar_path', $soal?->gambar_path) ? 'display:block;' : 'display:none;' }}">
                <img id="main_image_preview" src="{{ old('gambar_path', $soal?->gambar_path) ? asset('storage/' . old('gambar_path', $soal?->gambar_path)) : '' }}" class="max-h-48 mx-auto rounded-md border border-orange-200 object-contain">
            </div>
        </div>
    </div>

    {{-- ── Short Answer: Kunci Jawaban ── --}}
    <div id="kunci_jawaban_section" class="bg-green-50 border border-green-200 rounded-lg p-5" style="display:none;">
        <label for="jawaban_kunci" class="block text-sm font-medium text-green-800 flex items-center mb-2">
            🔑 Kunci Jawaban
        </label>
        <textarea id="jawaban_kunci" name="jawaban_kunci" rows="2" maxlength="300"
            class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-green-300 rounded-md"
            placeholder="Contoh: Tokyo  atau beberapa: Tokyo|Tokio|東京">{{ old('jawaban_kunci', $soal?->jawaban_kunci) }}</textarea>
        <p class="text-xs text-green-600 mt-2">
            💡 Pisahkan beberapa jawaban dengan <code class="bg-green-100 px-1 rounded">|</code>.<br>
            Sistem otomatis abaikan huruf besar/kecil dan toleransi typo ≥85% kemiripan.
        </p>
    </div>

    {{-- ── Matching Section ── --}}
    <div id="matching_section" style="display:none;">
        <div class="flex items-center justify-between mb-3">
            <label class="block text-sm font-medium text-slate-700">🔗 Daftar Pasangan (Matching)</label>
            <button type="button" id="add_pasangan"
                class="inline-flex items-center px-3 py-1.5 border border-indigo-300 shadow-sm text-xs font-medium rounded text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                + Tambah Pasangan
            </button>
        </div>
        <div class="bg-slate-50 rounded-lg border border-slate-200 overflow-hidden">
            <div class="grid grid-cols-2 gap-0 border-b border-slate-200 bg-slate-100 text-xs font-semibold text-slate-600 uppercase tracking-wider">
                <div class="px-4 py-2.5 border-r border-slate-200">SISI KIRI (Prompt)</div>
                <div class="px-4 py-2.5">SISI KANAN (Jawaban)</div>
            </div>
            <div id="matching_container" class="divide-y divide-slate-200">
                @php
                    $existingPairs = $soal && $soal->tipe === 'matching' ? $soal->pilihanJawabans : collect();
                    $pairCount = max(4, $existingPairs->count());
                @endphp
                @for($i = 0; $i < $pairCount; $i++)
                    @php
                        $pair = $existingPairs->get($i);
                        $kiriIsGambar  = $pair && in_array($pair->media_tipe, ['matching_gambar_kiri', 'matching_gambar_keduanya']);
                        $kananIsGambar = $pair && in_array($pair->media_tipe, ['matching_gambar_kanan', 'matching_gambar_keduanya']);
                        $kiriVal  = $pair?->teks ?? '';
                        $kananVal = $pair?->media_path ?? '';
                    @endphp
                    @include('_partials.matching_pair_row', [
                        'i' => $i,
                        'kiriIsGambar' => $kiriIsGambar,
                        'kananIsGambar' => $kananIsGambar,
                        'kiriVal' => $kiriVal,
                        'kananVal' => $kananVal,
                        'audioFiles' => $audioFiles,
                        'imageFiles' => $imageFiles,
                        'uploadRoute' => $uploadRoute,
                    ])
                @endfor
            </div>
        </div>
        <p class="text-xs text-slate-500 mt-2">💡 Untuk konten gambar: centang toggle "Gunakan Gambar" dan pilih file atau upload baru.</p>
    </div>

    {{-- ── Pilihan Jawaban Section (non-matching) ── --}}
    <div id="pilihan_section">
        <div class="flex items-center justify-between mb-4">
            <label class="block text-sm font-medium text-slate-700">Pilihan Jawaban</label>
            <button type="button" id="add_pilihan"
                class="inline-flex items-center px-3 py-1.5 border border-slate-300 shadow-sm text-xs font-medium rounded text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                <svg class="-ml-1 mr-1.5 h-4 w-4 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Pilihan
            </button>
        </div>
        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
            <p class="text-xs text-slate-500 mb-4" id="pilihan_help">Buat pilihan jawaban dan pilih kotak radio (<span class="font-bold">Jawaban Benar</span>) di sebelah opsi yang benar.</p>
            <div id="pilihan_container" class="space-y-4">
                @php
                    $existingPilihan = $soal && $soal->tipe !== 'matching' ? $soal->pilihanJawabans : collect();
                    $opsiCount = max(4, $existingPilihan->count());
                @endphp
                @for($i = 0; $i < $opsiCount; $i++)
                @php
                    $pilihan = $existingPilihan->get($i);
                    $teks = $pilihan?->teks ?? '';
                    $media_path = $pilihan?->media_path ?? '';
                    $is_benar = $pilihan ? $pilihan->is_benar : ($i == 0);
                    $optAudioMaxPlay = $pilihan?->audio_max_play ?? '';
                @endphp
                <div class="flex items-start space-x-3 pilihan-item">
                    <div class="pt-3">
                        @if($soal?->tipe == 'multiple_choice')
                            <input type="checkbox" id="jawaban_benar_{{$i}}" name="jawaban_benar[]" value="{{$i}}" class="jawaban-selector h-5 w-5 text-accent-600 border-slate-300 rounded" {{ $is_benar ? 'checked' : '' }}>
                        @else
                            <input type="radio" id="jawaban_benar_{{$i}}" name="jawaban_benar" value="{{$i}}" class="jawaban-selector h-5 w-5 text-accent-600 border-slate-300" {{ $is_benar ? 'checked' : '' }}>
                        @endif
                    </div>
                    <div class="flex-1 space-y-2">
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-slate-500 sm:text-sm font-bold">{{ chr(65 + $i) }}.</span>
                            </div>
                            <input type="text" name="pilihan[{{$i}}]" maxlength="300"
                                class="focus:ring-accent-500 focus:border-accent-500 block w-full pl-8 sm:text-sm border-slate-300 rounded-md py-2"
                                placeholder="Teks opsi jawaban..." value="{{ $teks }}">
                        </div>
                        <div class="media-input-wrapper flex gap-2 w-full items-start" style="display:none;">
                            {{-- Audio opsi --}}
                            <div class="audio-field flex-1 flex gap-2" style="display:none;">
                                <select name="pilihan_media[{{$i}}]" class="audio-select bg-blue-50 border-blue-200 focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm rounded-md py-2 flex-1" disabled>
                                    <option value="">-- Pilih Audio {{ chr(65 + $i) }} --</option>
                                    @foreach($audioFiles as $f) <option value="audio/{{$f}}" {{ $media_path == 'audio/'.$f ? 'selected' : '' }}>{{$f}}</option> @endforeach
                                </select>
                                <button type="button" class="upload-opt-btn flex-shrink-0 inline-flex items-center px-2 py-1.5 border border-blue-200 bg-white text-blue-600 text-xs rounded-md hover:bg-blue-50"
                                    data-idx="{{$i}}" data-jenis="audio" onclick="triggerOptionUpload({{$i}}, 'audio')">⬆</button>
                            </div>
                            {{-- Batas putar opsi audio --}}
                            <div class="audio-limit-field" style="display:none;">
                                <input type="number" name="pilihan_audio_max_play[{{$i}}]" value="{{ $optAudioMaxPlay }}"
                                    min="1" max="99" placeholder="∞"
                                    class="w-16 text-center sm:text-sm border-blue-200 rounded-md py-2 bg-blue-50"
                                    title="Batas putar audio opsi ini">
                            </div>
                            {{-- Gambar opsi --}}
                            <div class="image-field flex-1 flex gap-2" style="display:none;">
                                <select name="pilihan_media[{{$i}}]" class="image-select bg-orange-50 border-orange-200 focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm rounded-md py-2 flex-1" disabled onchange="previewOptionImage(this, '{{$i}}')">
                                    <option value="">-- Pilih Gambar {{ chr(65 + $i) }} --</option>
                                    @foreach($imageFiles as $f) <option value="gambar/{{$f}}" {{ $media_path == 'gambar/'.$f ? 'selected' : '' }}>{{$f}}</option> @endforeach
                                </select>
                                <button type="button" class="flex-shrink-0 inline-flex items-center px-2 py-1.5 border border-orange-200 bg-white text-orange-600 text-xs rounded-md hover:bg-orange-50"
                                    onclick="triggerOptionUpload({{$i}}, 'gambar')">⬆</button>
                            </div>
                            <input type="file" id="opt_upload_{{$i}}" class="hidden">
                        </div>
                        <div id="option_image_preview_container_{{$i}}" class="mt-2 text-center" style="{{ ($media_path && str_starts_with($media_path, 'gambar/')) ? 'display:block;' : 'display:none;' }}">
                            <img id="option_image_preview_{{$i}}" src="{{ ($media_path && str_starts_with($media_path, 'gambar/')) ? asset('storage/' . $media_path) : '' }}" class="max-h-32 mx-auto rounded-md border border-orange-200 object-contain">
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
