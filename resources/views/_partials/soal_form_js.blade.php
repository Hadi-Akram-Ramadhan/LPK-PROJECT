{{-- Shared JS for soal form (matching, upload, audio limit, pilihan jawaban) --}}
@php
    $uploadUrl = isset($uploadRoute) ? route($uploadRoute) : '';
    $csrfToken = csrf_token();
    $audioFilesJson = $audioFiles->values()->toJson();
    $imageFilesJson = $imageFiles->values()->toJson();
    $currentTipe = old('tipe', isset($soal) ? $soal->tipe : 'pilihan_ganda');
    $matchingPairCount = (isset($soal) && $soal->tipe === 'matching')
        ? max(4, $soal->pilihanJawabans->count())
        : 4;
    $optionCount = (isset($soal) && $soal->tipe !== 'matching')
        ? max(4, $soal->pilihanJawabans->count())
        : 4;
@endphp
<script>
'use strict';
const _UPLOAD_URL    = @json($uploadUrl);
const _CSRF          = @json($csrfToken);
const audioFiles     = @json(json_decode($audioFilesJson));
const imageFiles     = @json(json_decode($imageFilesJson));
let optionCount      = {{ $optionCount }};
let matchingPairCount= {{ $matchingPairCount }};

// ── UI Visibility ────────────────────────────────────────────────
function updateUIBasedOnType() {
    const val = document.getElementById('tipe').value;
    const ids = {
        audio:    document.getElementById('audio_section'),
        kunci:    document.getElementById('kunci_jawaban_section'),
        matching: document.getElementById('matching_section'),
        pilihan:  document.getElementById('pilihan_section'),
    };

    // Audio section — show only for listening types
    ids.audio.style.display  = ['audio','pilihan_ganda_audio'].includes(val) ? 'block' : 'none';
    if (!['audio','pilihan_ganda_audio'].includes(val)) {
        const ap = document.getElementById('audio_path');
        if (ap) ap.value = '';
    }

    // Kunci jawaban (short_answer only)
    ids.kunci.style.display    = val === 'short_answer' ? 'block' : 'none';

    // Matching vs regular options
    ids.matching.style.display = val === 'matching' ? 'block' : 'none';
    ids.pilihan.style.display  = (val === 'matching' || val === 'essay' || val === 'short_answer') ? 'none' : 'block';

    if (!['matching','essay','short_answer'].includes(val)) {
        // Enable/disable form elements
        document.querySelectorAll('#pilihan_container input, #pilihan_container select').forEach(el => el.disabled = false);

        // Media wrappers visibility per type
        document.querySelectorAll('.media-input-wrapper').forEach(wrap => {
            const audioField = wrap.querySelector('.audio-field');
            const imageField = wrap.querySelector('.image-field');
            const audioLimitField = wrap.querySelector('.audio-limit-field');

            if (val === 'pilihan_ganda_audio') {
                wrap.style.display = 'flex';
                showField(audioField, true); showField(imageField, false); showField(audioLimitField, true);
                wrap.querySelectorAll('.audio-select').forEach(s => { s.disabled = false; s.name = s.name; });
                wrap.querySelectorAll('.image-select').forEach(s => { s.disabled = true; });
            } else if (val === 'pilihan_ganda_gambar') {
                wrap.style.display = 'flex';
                showField(audioField, false); showField(imageField, true); showField(audioLimitField, false);
                wrap.querySelectorAll('.image-select').forEach(s => { s.disabled = false; });
                wrap.querySelectorAll('.audio-select').forEach(s => { s.disabled = true; });
            } else {
                wrap.style.display = 'none';
                wrap.querySelectorAll('select').forEach(s => s.disabled = true);
            }
        });

        const isMultiple = val === 'multiple_choice';
        document.querySelectorAll('.jawaban-selector').forEach(el => {
            el.type = isMultiple ? 'checkbox' : 'radio';
            el.name = isMultiple ? 'jawaban_benar[]' : 'jawaban_benar';
        });

        const help = document.getElementById('pilihan_help');
        if (help) {
            if (isMultiple)           help.innerHTML = 'Centang (<b>Jawaban Benar</b>) di sebelah semua opsi yang benar.';
            else if (val === 'pilihan_ganda_audio' || val === 'pilihan_ganda_gambar')
                                    help.innerHTML = 'Pilih radio (<b>Jawaban Benar</b>) di sebelah media yang benar.';
            else                    help.innerHTML = 'Pilih radio (<b>Jawaban Benar</b>) di sebelah SATU opsi yang benar.';
        }
    } else {
        document.querySelectorAll('#pilihan_container input, #pilihan_container select').forEach(el => el.disabled = true);
    }
}

function showField(el, show) {
    if (el) el.style.display = show ? 'flex' : 'none';
}

// ── Tambah Pilihan Jawaban ────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('tipe').addEventListener('change', updateUIBasedOnType);
    updateUIBasedOnType();
    attachRemoveListeners();
    attachRemovePasanganListeners();

    // ── Add Pilihan ──
    const addBtn = document.getElementById('add_pilihan');
    if (addBtn) {
        addBtn.addEventListener('click', () => {
            const container = document.getElementById('pilihan_container');
            const idx  = optionCount;
            const letter = String.fromCharCode(65 + idx);
            const val = document.getElementById('tipe').value;
            const isMultiple = val === 'multiple_choice';

            let audioOpts = `<option value="">-- Audio ${letter} --</option>`;
            audioFiles.forEach(f => audioOpts += `<option value="audio/${f}">${f}</option>`);
            let imageOpts = `<option value="">-- Gambar ${letter} --</option>`;
            imageFiles.forEach(f => imageOpts += `<option value="gambar/${f}">${f}</option>`);

            container.insertAdjacentHTML('beforeend', `
            <div class="flex items-start space-x-3 pilihan-item">
                <div class="pt-3">
                    <input type="${isMultiple ? 'checkbox' : 'radio'}" name="${isMultiple ? 'jawaban_benar[]' : 'jawaban_benar'}" value="${idx}" class="jawaban-selector h-5 w-5 text-accent-600 border-slate-300">
                </div>
                <div class="flex-1 space-y-2">
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-500 sm:text-sm font-bold">${letter}.</span>
                        </div>
                        <input type="text" name="pilihan[${idx}]" class="focus:ring-accent-500 focus:border-accent-500 block w-full pl-8 sm:text-sm border-slate-300 rounded-md py-2" placeholder="Teks opsi...">
                    </div>
                    <div class="media-input-wrapper flex gap-2 w-full items-start" style="display:none;">
                        <div class="audio-field flex-1 flex gap-2" style="display:none;">
                            <select name="pilihan_media[${idx}]" class="audio-select bg-blue-50 border-blue-200 block w-full sm:text-sm rounded-md py-2 flex-1" disabled>${audioOpts}</select>
                            <button type="button" class="flex-shrink-0 px-2 py-1.5 border border-blue-200 bg-white text-blue-600 text-xs rounded-md" onclick="triggerOptionUpload(${idx},'audio')">⬆</button>
                        </div>
                        <div class="audio-limit-field" style="display:none;">
                            <input type="number" name="pilihan_audio_max_play[${idx}]" min="1" max="99" placeholder="∞" class="w-16 text-center sm:text-sm border-blue-200 rounded-md py-2 bg-blue-50" title="Batas putar">
                        </div>
                        <div class="image-field flex-1 flex gap-2" style="display:none;">
                            <select name="pilihan_media[${idx}]" class="image-select bg-orange-50 border-orange-200 block w-full sm:text-sm rounded-md py-2 flex-1" disabled onchange="previewOptionImage(this,'${idx}')">${imageOpts}</select>
                            <button type="button" class="flex-shrink-0 px-2 py-1.5 border border-orange-200 bg-white text-orange-600 text-xs rounded-md" onclick="triggerOptionUpload(${idx},'gambar')">⬆</button>
                        </div>
                        <input type="file" id="opt_upload_${idx}" class="hidden">
                    </div>
                    <div id="option_image_preview_container_${idx}" class="mt-2 text-center" style="display:none;">
                        <img id="option_image_preview_${idx}" src="" class="max-h-32 mx-auto rounded-md border border-orange-200 object-contain">
                    </div>
                </div>
                <button type="button" class="remove-pilihan text-slate-400 hover:text-red-500 pt-3">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>`);
            optionCount++;
            updateUIBasedOnType();
            attachRemoveListeners();
        });
    }

    // ── Add Pasangan Matching ──
    const addPasangan = document.getElementById('add_pasangan');
    if (addPasangan) {
        addPasangan.addEventListener('click', () => {
            const container = document.getElementById('matching_container');
            const idx = matchingPairCount;
            let imgOptsKiri  = `<option value="">-- Pilih Gambar --</option>`;
            let imgOptsKanan = `<option value="">-- Pilih Gambar --</option>`;
            imageFiles.forEach(f => {
                imgOptsKiri  += `<option value="gambar/${f}">${f}</option>`;
                imgOptsKanan += `<option value="gambar/${f}">${f}</option>`;
            });
            container.insertAdjacentHTML('beforeend', `
            <div class="matching-pair-row flex gap-0 group" data-idx="${idx}">
                <div class="flex-1 p-3 border-r border-slate-200">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-bold text-slate-600 bg-slate-200 rounded px-2 py-0.5">${idx + 1}</span>
                        <label class="flex items-center gap-1.5 cursor-pointer text-xs text-slate-500">
                            <input type="checkbox" class="kiri-gambar-toggle rounded border-slate-300 text-indigo-600" onchange="toggleMatchingSide(this,'kiri',${idx})">
                            Gunakan Gambar
                        </label>
                    </div>
                    <div class="kiri-teks-field">
                        <input type="text" name="pasang_kiri[${idx}]" maxlength="500" placeholder="Teks sisi kiri..." class="w-full sm:text-sm border-slate-300 rounded-md py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="kiri-gambar-field" style="display:none;">
                        <div class="flex gap-2 items-center">
                            <select name="pasang_kiri_gambar[${idx}]" class="flex-1 sm:text-sm border-orange-300 bg-orange-50 rounded-md py-2 px-2" onchange="previewMatchImg(this,'kiri_prev_${idx}')">${imgOptsKiri}</select>
                            <button type="button" class="flex-shrink-0 px-2 py-1.5 border border-orange-200 bg-white text-orange-600 text-xs rounded-md" onclick="triggerMatchUpload(${idx},'kiri')">⬆</button>
                        </div>
                        <input type="file" id="match_upload_kiri_${idx}" class="hidden" accept=".jpg,.jpeg,.png,.webp">
                        <img id="kiri_prev_${idx}" src="" class="mt-2 max-h-20 mx-auto rounded border border-orange-200 object-contain" style="display:none;">
                    </div>
                </div>
                <div class="flex-1 p-3 relative">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <label class="flex items-center gap-1.5 cursor-pointer text-xs text-slate-500">
                            <input type="checkbox" class="kanan-gambar-toggle rounded border-slate-300 text-indigo-600" onchange="toggleMatchingSide(this,'kanan',${idx})">
                            Gunakan Gambar
                        </label>
                        <button type="button" class="remove-pasangan text-slate-300 hover:text-red-400" title="Hapus">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="kanan-teks-field">
                        <input type="text" name="pasang_kanan[${idx}]" maxlength="500" placeholder="Teks sisi kanan..." class="w-full sm:text-sm border-slate-300 rounded-md py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="kanan-gambar-field" style="display:none;">
                        <div class="flex gap-2 items-center">
                            <select name="pasang_kanan_gambar[${idx}]" class="flex-1 sm:text-sm border-orange-300 bg-orange-50 rounded-md py-2 px-2" onchange="previewMatchImg(this,'kanan_prev_${idx}')">${imgOptsKanan}</select>
                            <button type="button" class="flex-shrink-0 px-2 py-1.5 border border-orange-200 bg-white text-orange-600 text-xs rounded-md" onclick="triggerMatchUpload(${idx},'kanan')">⬆</button>
                        </div>
                        <input type="file" id="match_upload_kanan_${idx}" class="hidden" accept=".jpg,.jpeg,.png,.webp">
                        <img id="kanan_prev_${idx}" src="" class="mt-2 max-h-20 mx-auto rounded border border-orange-200 object-contain" style="display:none;">
                    </div>
                </div>
            </div>`);
            matchingPairCount++;
            attachRemovePasanganListeners();
        });
    }
});

function attachRemoveListeners() {
    document.querySelectorAll('.remove-pilihan').forEach(btn => {
        btn.onclick = function() {
            if (document.querySelectorAll('.pilihan-item').length > 2) {
                this.closest('.pilihan-item').remove();
            } else {
                alert('Minimal harus ada 2 pilihan jawaban.');
            }
        };
    });
}

function attachRemovePasanganListeners() {
    document.querySelectorAll('.remove-pasangan').forEach(btn => {
        btn.onclick = function() {
            if (document.querySelectorAll('.matching-pair-row').length > 2) {
                this.closest('.matching-pair-row').remove();
            } else {
                alert('Minimal harus ada 2 pasangan matching.');
            }
        };
    });
}

// ── Toggle Matching Side ─────────────────────────────────────────
function toggleMatchingSide(checkbox, side, idx) {
    const row  = document.querySelector(`.matching-pair-row[data-idx="${idx}"]`);
    if (!row) return;
    const teksEl   = row.querySelector(`.${side}-teks-field`);
    const gambarEl = row.querySelector(`.${side}-gambar-field`);
    if (checkbox.checked) {
        teksEl.style.display   = 'none';
        gambarEl.style.display = 'block';
    } else {
        teksEl.style.display   = 'block';
        gambarEl.style.display = 'none';
    }
}

function previewMatchImg(sel, imgId) {
    const img = document.getElementById(imgId);
    if (!img) return;
    if (sel.value && sel.value.startsWith('gambar/')) {
        img.src = '/storage/' + sel.value;
        img.style.display = 'block';
    } else {
        img.src = '';
        img.style.display = 'none';
    }
}

// ── Trigger Upload ────────────────────────────────────────────────
function triggerUpload(fileInputId, jenis, selectId) {
    const fileInput = document.getElementById(fileInputId);
    if (!fileInput) return;
    fileInput.setAttribute('data-jenis', jenis);
    fileInput.setAttribute('data-select', selectId);
    fileInput.onchange = function() {
        if (!this.files || !this.files[0]) return;
        uploadMediaFile(this.files[0], jenis, (result) => {
            // Add to select and select it
            const sel = document.getElementById(selectId);
            if (sel) {
                const opt = new Option(result.filename, result.path, true, true);
                sel.add(opt);
                sel.value = result.path;
                sel.dispatchEvent(new Event('change'));
            }
        }, fileInputId.replace('_upload', '_progress'), fileInputId.replace('_upload', '_bar'), fileInputId.replace('_upload', '_msg'));
    };
    fileInput.click();
}

function triggerMatchUpload(idx, side) {
    const fileInput = document.getElementById(`match_upload_${side}_${idx}`);
    if (!fileInput) return;
    fileInput.onchange = function() {
        if (!this.files || !this.files[0]) return;
        uploadMediaFile(this.files[0], 'gambar', (result) => {
            // Add to the select for this row+side
            const row = document.querySelector(`.matching-pair-row[data-idx="${idx}"]`);
            if (!row) return;
            const sel = row.querySelector(`.${side}-gambar-field select`);
            const img = document.getElementById(`${side}_prev_${idx}`);
            if (sel) {
                const opt = new Option(result.filename, result.path, true, true);
                sel.add(opt);
                sel.value = result.path;
            }
            if (img) {
                img.src = '/storage/' + result.path;
                img.style.display = 'block';
            }
        });
    };
    fileInput.click();
}

function triggerOptionUpload(idx, jenis) {
    const fileInput = document.getElementById(`opt_upload_${idx}`);
    if (!fileInput) return;
    fileInput.setAttribute('accept', jenis === 'gambar' ? '.jpg,.jpeg,.png,.webp' : '.mp3,.wav,.ogg');
    fileInput.onchange = function() {
        if (!this.files || !this.files[0]) return;
        uploadMediaFile(this.files[0], jenis, (result) => {
            const row = this.closest('.pilihan-item');
            if (!row) return;
            if (jenis === 'gambar') {
                const sel = row.querySelector('.image-select');
                if (sel) {
                    const opt = new Option(result.filename, result.path, true, true);
                    sel.add(opt);
                    sel.value = result.path;
                    sel.dispatchEvent(new Event('change'));
                }
            } else {
                const sel = row.querySelector('.audio-select');
                if (sel) {
                    const opt = new Option(result.filename, result.path, true, true);
                    sel.add(opt);
                    sel.value = result.path;
                }
            }
        });
    };
    fileInput.click();
}

function uploadMediaFile(file, jenis, onSuccess, progressWrapperId, barId, msgId) {
    const formData = new FormData();
    formData.append('file', file);
    formData.append('jenis', jenis);
    formData.append('_token', _CSRF);

    const progressWrap = progressWrapperId ? document.getElementById(progressWrapperId) : null;
    const bar          = barId  ? document.getElementById(barId)  : null;
    const msg          = msgId  ? document.getElementById(msgId)  : null;

    if (progressWrap) progressWrap.classList.remove('hidden');
    if (bar) bar.style.width = '10%';
    if (msg) msg.textContent = 'Mengunggah...';

    const xhr = new XMLHttpRequest();
    xhr.open('POST', _UPLOAD_URL);
    xhr.upload.addEventListener('progress', e => {
        if (e.lengthComputable && bar) {
            bar.style.width = Math.round((e.loaded / e.total) * 90) + '%';
        }
    });
    xhr.onload = function() {
        if (bar) bar.style.width = '100%';
        try {
            const resp = JSON.parse(this.responseText);
            if (resp.success) {
                if (msg) msg.textContent = '✅ Berhasil diunggah: ' + resp.filename;
                onSuccess(resp);
                setTimeout(() => { if (progressWrap) progressWrap.classList.add('hidden'); }, 3000);
            } else {
                if (msg) msg.textContent = '❌ ' + (resp.message || 'Gagal upload');
            }
        } catch(e) {
            if (msg) msg.textContent = '❌ Respons tidak valid dari server.';
        }
    };
    xhr.onerror = function() {
        if (msg) msg.textContent = '❌ Koneksi gagal. Pastikan file tidak melebihi batas ukuran.';
    };
    xhr.send(formData);
}

// ── Preview Helpers ──────────────────────────────────────────────
function previewMainImage(selectEl) {
    const container = document.getElementById('main_image_preview_container');
    const img = document.getElementById('main_image_preview');
    if (!container || !img) return;
    if (selectEl.value && selectEl.value.startsWith('gambar/')) {
        img.src = '/storage/' + selectEl.value;
        container.style.display = 'block';
    } else {
        img.src = ''; container.style.display = 'none';
    }
}

function previewOptionImage(selectEl, idx) {
    const container = document.getElementById('option_image_preview_container_' + idx);
    const img = document.getElementById('option_image_preview_' + idx);
    if (!container || !img) return;
    if (selectEl.value && selectEl.value.startsWith('gambar/')) {
        img.src = '/storage/' + selectEl.value;
        container.style.display = 'block';
    } else {
        img.src = ''; container.style.display = 'none';
    }
}
</script>
