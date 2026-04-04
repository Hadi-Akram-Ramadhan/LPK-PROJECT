@extends('layouts.admin')

@section('header')
<div class="flex items-center space-x-4">
    <a href="{{ route('admin.soal.index') }}" class="text-slate-500 hover:text-slate-700 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <span>Edit Soal: {{ strip_tags(substr($soal->pertanyaan, 0, 30)) }}...</span>
</div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-4xl mx-auto">
    <form action="{{ route('admin.soal.update', $soal) }}" method="POST" class="p-8">
        @csrf
        @method('PUT')
        
        <div class="space-y-8">
            <!-- Tipe Soal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-slate-100">
                <div>
                    <label for="tipe" class="block text-sm font-medium text-slate-700">Tipe Soal</label>
                    <select id="tipe" name="tipe" required class="mt-1 shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md bg-slate-50">
                        <option value="pilihan_ganda" {{ old('tipe', $soal->tipe) == 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda (Tunggal)</option>
                        <option value="multiple_choice" {{ old('tipe', $soal->tipe) == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice (Lebih dari 1 Jawaban)</option>
                        <option value="essay" {{ old('tipe', $soal->tipe) == 'essay' ? 'selected' : '' }}>Essay (Teks Bebas)</option>
                        <option value="audio" {{ old('tipe', $soal->tipe) == 'audio' ? 'selected' : '' }}>Listening / Choukai (Audio & Pilihan Ganda)</option>
                        <option value="pilihan_ganda_audio" {{ old('tipe', $soal->tipe) == 'pilihan_ganda_audio' ? 'selected' : '' }}>Pilihan Ganda Audio (Jawaban berupa Audio)</option>
                        <option value="pilihan_ganda_gambar" {{ old('tipe', $soal->tipe) == 'pilihan_ganda_gambar' ? 'selected' : '' }}>Pilihan Ganda Gambar (Jawaban berupa Gambar)</option>
                    </select>
                </div>
                <div>
                    <label for="poin" class="block text-sm font-medium text-slate-700">Poin Soal (Bobot Nilai)</label>
                    <input type="number" name="poin" id="poin" value="{{ old('poin', $soal->poin) }}" required min="1" class="mt-1 shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md">
                </div>
            </div>

            <!-- Pertanyaan -->
            <div>
                <label for="pertanyaan" class="block text-sm font-medium text-slate-700 mb-2">Teks Pertanyaan</label>
                <textarea id="pertanyaan" name="pertanyaan" rows="4" required class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md">{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
                @error('pertanyaan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Audio Selection -->
                <div id="audio_section" class="bg-blue-50 border border-blue-200 rounded-lg p-5" style="display: none;">
                    <label for="audio_path" class="block text-sm font-medium text-blue-800 flex items-center mb-2">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M8 12h.01M12 12h.01M16 12h.01M9 16H5a2 2 0 01-2-2V10a2 2 0 012-2h4l5-5v18l-5-5z"></path></svg>
                        Pilih File Audio Pendukung
                    </label>
                    <select id="audio_path" name="audio_path" class="mt-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-blue-300 rounded-md text-slate-700">
                        <option value="">-- Tanpa Audio --</option>
                        @foreach($audioFiles as $audio)
                            <option value="audio/{{ $audio }}" {{ old('audio_path', $soal->audio_path) == 'audio/'.$audio ? 'selected' : '' }}>{{ $audio }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Gambar Pendukung -->
                <div id="gambar_section" class="bg-orange-50 border border-orange-200 rounded-lg p-5">
                    <label for="gambar_path" class="block text-sm font-medium text-orange-800 flex items-center mb-2">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Pilih Gambar Pendukung
                    </label>
                    <select id="gambar_path" name="gambar_path" class="mt-1 shadow-sm focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm border-orange-300 rounded-md text-slate-700">
                        <option value="">-- Tanpa Gambar --</option>
                        @foreach($imageFiles as $image)
                            <option value="gambar/{{ $image }}" {{ old('gambar_path', $soal->gambar_path) == 'gambar/'.$image ? 'selected' : '' }}>{{ $image }}</option>
                        @endforeach
                    </select>
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
                        @php $i = 0; @endphp
                        @forelse($soal->pilihanJawabans as $index => $pilihan)
                        <div class="flex items-start space-x-3 pilihan-item">
                            <div class="pt-3">
                                @if($soal->tipe == 'multiple_choice')
                                    <input type="checkbox" id="jawaban_benar_{{$i}}" name="jawaban_benar[]" value="{{$i}}" class="jawaban-selector h-5 w-5 text-accent-600 focus:ring-accent-500 border-slate-300 rounded" {{ $pilihan->is_benar ? 'checked' : '' }}>
                                @else
                                    <input type="radio" id="jawaban_benar_{{$i}}" name="jawaban_benar" value="{{$i}}" class="jawaban-selector h-5 w-5 text-accent-600 focus:ring-accent-500 border-slate-300" {{ $pilihan->is_benar ? 'checked' : '' }}>
                                @endif
                            </div>
                            <div class="flex-1 space-y-2">
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-slate-500 sm:text-sm font-bold">{{ chr(65 + $i) }}.</span>
                                    </div>
                                    <input type="text" name="pilihan[{{$i}}]" class="focus:ring-accent-500 focus:border-accent-500 block w-full pl-8 sm:text-sm border-slate-300 rounded-md py-2" placeholder="Opsi jawaban teks (jika perlu)..." value="{{ $pilihan->teks }}">
                                </div>
                                <div class="media-input-wrapper flex gap-2 w-full" style="display:none;">
                                    <select name="pilihan_media[{{$i}}]" class="audio-select bg-blue-50 border-blue-200 focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm rounded-md py-2" style="display:none;" disabled>
                                        <option value="">-- Pilih Audio Opsi {{ chr(65 + $i) }} --</option>
                                        @foreach($audioFiles as $f) <option value="audio/{{$f}}" {{ $pilihan->media_path == 'audio/'.$f ? 'selected' : '' }}>{{$f}}</option> @endforeach
                                    </select>
                                    <select name="pilihan_media[{{$i}}]" class="image-select bg-orange-50 border-orange-200 focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm rounded-md py-2" style="display:none;" disabled>
                                        <option value="">-- Pilih Gambar Opsi {{ chr(65 + $i) }} --</option>
                                        @foreach($imageFiles as $f) <option value="gambar/{{$f}}" {{ $pilihan->media_path == 'gambar/'.$f ? 'selected' : '' }}>{{$f}}</option> @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="button" class="remove-pilihan text-slate-400 hover:text-red-500 pt-3 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        @php $i++; @endphp
                        @empty
                        {{-- Fallback empty state if changing from essay --}}
                        @for($j = 0; $j < 4; $j++)
                        <div class="flex items-start space-x-3 pilihan-item">
                            <div class="pt-3">
                                <input type="radio" id="jawaban_benar_{{$j}}" name="jawaban_benar" value="{{$j}}" class="jawaban-selector h-5 w-5 text-accent-600 focus:ring-accent-500 border-slate-300" {{ $j == 0 ? 'checked' : '' }}>
                            </div>
                            <div class="flex-1 space-y-2">
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-slate-500 sm:text-sm font-bold">{{ chr(65 + $j) }}.</span>
                                    </div>
                                    <input type="text" name="pilihan[{{$j}}]" class="focus:ring-accent-500 focus:border-accent-500 block w-full pl-8 sm:text-sm border-slate-300 rounded-md py-2" placeholder="Opsi jawaban teks (jika perlu)..." value="">
                                </div>
                                <div class="media-input-wrapper flex gap-2 w-full" style="display:none;">
                                    <select name="pilihan_media[{{$j}}]" class="audio-select bg-blue-50 border-blue-200 focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm rounded-md py-2" style="display:none;" disabled>
                                        <option value="">-- Pilih Audio Opsi {{ chr(65 + $j) }} --</option>
                                        @foreach($audioFiles as $f) <option value="audio/{{$f}}">{{$f}}</option> @endforeach
                                    </select>
                                    <select name="pilihan_media[{{$j}}]" class="image-select bg-orange-50 border-orange-200 focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm rounded-md py-2" style="display:none;" disabled>
                                        <option value="">-- Pilih Gambar Opsi {{ chr(65 + $j) }} --</option>
                                        @foreach($imageFiles as $f) <option value="gambar/{{$f}}">{{$f}}</option> @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="button" class="remove-pilihan text-slate-400 hover:text-red-500 pt-3 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        @php $i++; @endphp
                        @endfor
                        @endforelse
                    </div>
                </div>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const tipeSelect = document.getElementById('tipe');
                    const audioSection = document.getElementById('audio_section');
                    const pilihanSection = document.getElementById('pilihan_section');
                    const optionsSelectors = document.querySelectorAll('.jawaban-selector');
                    
                    const audioFiles = @json($audioFiles);
                    const imageFiles = @json($imageFiles);
                    
                    function updateUIBasedOnType() {
                        const val = tipeSelect.value;
                        
                        if (val === 'audio' || val === 'pilihan_ganda_audio') {
                            audioSection.style.display = 'block';
                        } else {
                            audioSection.style.display = 'none';
                        }
                        
                        if (val === 'essay') {
                            pilihanSection.style.display = 'none';
                            document.querySelectorAll('#pilihan_container input').forEach(el => el.disabled = true);
                            document.querySelectorAll('#pilihan_container select').forEach(el => el.disabled = true);
                        } else {
                            pilihanSection.style.display = 'block';
                            document.querySelectorAll('#pilihan_container input').forEach(el => el.disabled = false);
                            
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

                            const isMultiple = val === 'multiple_choice';
                            document.querySelectorAll('.jawaban-selector').forEach(el => {
                                el.type = isMultiple ? 'checkbox' : 'radio';
                                el.name = isMultiple ? 'jawaban_benar[]' : 'jawaban_benar';
                            });
                            
                            if (isMultiple) {
                                document.getElementById('pilihan_help').innerHTML = 'Buat pilihan jawaban dan centang kotak (<span class="font-bold">Jawaban Benar</span>) di sebelah opsi yang benar.';
                            } else if (val === 'pilihan_ganda_audio' || val === 'pilihan_ganda_gambar') {
                                document.getElementById('pilihan_help').innerHTML = 'Buat pilihan media jawaban dan pilih kotak radio (<span class="font-bold">Jawaban Benar</span>) di sebelah opsi media yang benar.';
                            } else {
                                document.getElementById('pilihan_help').innerHTML = 'Buat pilihan jawaban dan pilih kotak radio (<span class="font-bold">Jawaban Benar</span>) di sebelah SATU opsi yang benar.';
                            }
                        }
                    }
                    
                    tipeSelect.addEventListener('change', updateUIBasedOnType);
                    updateUIBasedOnType(); // init
                    
                    let optionCount = {{ max(4, $soal->pilihanJawabans->count()) }};
                    document.getElementById('add_pilihan').addEventListener('click', function() {
                        const container = document.getElementById('pilihan_container');
                        const letter = String.fromCharCode(65 + optionCount); // A, B, C...
                        
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
                        updateUIBasedOnType();
                        attachRemoveListeners();
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
                    attachRemoveListeners();
                });
            </script>
        </div>

        <div class="pt-10 flex justify-end border-t border-slate-100 mt-8">
            <a href="{{ route('admin.paket-soal.show', $soal->paket_soal_id) }}" class="bg-white border border-slate-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500">
                Batal
            </a>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500">
                Perbarui Soal
            </button>
        </div>
    </form>
</div>
@endsection
