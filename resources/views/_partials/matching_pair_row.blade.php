{{-- Matching pair row partial --}}
@php $pair_upload_route = $uploadRoute ?? 'admin.soal.uploadMedia'; @endphp
<div class="matching-pair-row grid grid-cols-2 gap-0 group" data-idx="{{ $i }}">
    {{-- Sisi Kiri --}}
    <div class="min-w-0 p-3 border-r border-slate-200">
        <div class="flex items-center gap-2 mb-2">
            <span class="text-xs font-bold text-slate-600 bg-slate-200 rounded px-2 py-0.5">{{ $i + 1 }}</span>
            <label class="flex items-center gap-1.5 cursor-pointer text-xs text-slate-500">
                <input type="checkbox" class="kiri-gambar-toggle rounded border-slate-300 text-indigo-600"
                    {{ isset($kiriIsGambar) && $kiriIsGambar ? 'checked' : '' }}
                    onchange="toggleMatchingSide(this, 'kiri', {{ $i }})">
                Gunakan Gambar
            </label>
        </div>
        {{-- Teks kiri --}}
        <div class="kiri-teks-field" style="{{ isset($kiriIsGambar) && $kiriIsGambar ? 'display:none;' : '' }}">
            <input type="text" name="pasang_kiri[{{ $i }}]" maxlength="200"
                value="{{ isset($kiriIsGambar) && $kiriIsGambar ? '' : ($kiriVal ?? '') }}"
                placeholder="Teks sisi kiri..."
                class="w-full sm:text-sm border-slate-300 rounded-md py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        {{-- Gambar kiri --}}
        <div class="kiri-gambar-field" style="{{ isset($kiriIsGambar) && $kiriIsGambar ? '' : 'display:none;' }}">
            <div class="flex gap-2 items-center">
                <select name="pasang_kiri_gambar[{{ $i }}]"
                    class="flex-1 min-w-0 sm:text-sm border-orange-300 bg-orange-50 rounded-md py-2 px-2 focus:ring-orange-500 focus:border-orange-500"
                    onchange="previewMatchImg(this, 'kiri_prev_{{ $i }}')">
                    <option value="">-- Pilih Gambar --</option>
                    @foreach($imageFiles as $f)
                        <option value="gambar/{{ $f }}" {{ (isset($kiriIsGambar) && $kiriIsGambar && ($kiriVal ?? '') == 'gambar/'.$f) ? 'selected' : '' }}>{{ $f }}</option>
                    @endforeach
                </select>
                <button type="button" class="flex-shrink-0 inline-flex items-center px-2 py-1.5 border border-orange-200 bg-white text-orange-600 text-xs rounded-md hover:bg-orange-50"
                    onclick="triggerMatchUpload({{ $i }}, 'kiri')">⬆</button>
            </div>
            <input type="file" id="match_upload_kiri_{{ $i }}" class="hidden" accept=".jpg,.jpeg,.png,.webp">
            <img id="kiri_prev_{{ $i }}" src="{{ (isset($kiriIsGambar) && $kiriIsGambar && !empty($kiriVal)) ? asset('storage/' . $kiriVal) : '' }}"
                class="mt-2 max-h-20 mx-auto rounded border border-orange-200 object-contain" style="{{ (isset($kiriIsGambar) && $kiriIsGambar && !empty($kiriVal)) ? '' : 'display:none;' }}">
        </div>
    </div>

    {{-- Sisi Kanan --}}
    <div class="min-w-0 p-3 relative">
        <div class="flex items-center justify-between gap-2 mb-2">
            <label class="flex items-center gap-1.5 cursor-pointer text-xs text-slate-500">
                <input type="checkbox" class="kanan-gambar-toggle rounded border-slate-300 text-indigo-600"
                    {{ isset($kananIsGambar) && $kananIsGambar ? 'checked' : '' }}
                    onchange="toggleMatchingSide(this, 'kanan', {{ $i }})">
                Gunakan Gambar
            </label>
            <button type="button" class="remove-pasangan text-slate-300 hover:text-red-400 transition-colors" title="Hapus pasangan ini">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        {{-- Teks kanan --}}
        <div class="kanan-teks-field" style="{{ isset($kananIsGambar) && $kananIsGambar ? 'display:none;' : '' }}">
            <input type="text" name="pasang_kanan[{{ $i }}]" maxlength="200"
                value="{{ isset($kananIsGambar) && $kananIsGambar ? '' : ($kananVal ?? '') }}"
                placeholder="Teks sisi kanan..."
                class="w-full sm:text-sm border-slate-300 rounded-md py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        {{-- Gambar kanan --}}
        <div class="kanan-gambar-field" style="{{ isset($kananIsGambar) && $kananIsGambar ? '' : 'display:none;' }}">
            <div class="flex gap-2 items-center">
                <select name="pasang_kanan_gambar[{{ $i }}]"
                    class="flex-1 sm:text-sm border-orange-300 bg-orange-50 rounded-md py-2 px-2 focus:ring-orange-500 focus:border-orange-500"
                    onchange="previewMatchImg(this, 'kanan_prev_{{ $i }}')">
                    <option value="">-- Pilih Gambar --</option>
                    @foreach($imageFiles as $f)
                        <option value="gambar/{{ $f }}" {{ (isset($kananIsGambar) && $kananIsGambar && ($kananVal ?? '') == 'gambar/'.$f) ? 'selected' : '' }}>{{ $f }}</option>
                    @endforeach
                </select>
                <button type="button" class="flex-shrink-0 inline-flex items-center px-2 py-1.5 border border-orange-200 bg-white text-orange-600 text-xs rounded-md hover:bg-orange-50"
                    onclick="triggerMatchUpload({{ $i }}, 'kanan')">⬆</button>
            </div>
            <input type="file" id="match_upload_kanan_{{ $i }}" class="hidden" accept=".jpg,.jpeg,.png,.webp">
            <img id="kanan_prev_{{ $i }}" src="{{ (isset($kananIsGambar) && $kananIsGambar && !empty($kananVal)) ? asset('storage/' . $kananVal) : '' }}"
                class="mt-2 max-h-20 mx-auto rounded border border-orange-200 object-contain" style="{{ (isset($kananIsGambar) && $kananIsGambar && !empty($kananVal)) ? '' : 'display:none;' }}">
        </div>
    </div>
</div>
