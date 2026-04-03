@extends('layouts.guru')

@section('header')
<div class="flex items-center space-x-4">
    <a href="{{ route('guru.soal.index') }}" class="text-slate-500 hover:text-slate-700 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <span>Tambah Soal Baru</span>
</div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-4xl mx-auto">
    <form action="{{ route('guru.soal.store') }}" method="POST" class="p-8">
        @csrf
        
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
                <p class="text-xs text-blue-600 mt-2">File audio ini akan disisipkan di atas pertanyaan saat murid mengerjakan ujian. File bisa diupload melalui menu Admin > Audio Explorer.</p>
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
                    <div id="pilihan_container" class="space-y-3">
                        <!-- Default 4 Choices -->
                        @for($i = 0; $i < 4; $i++)
                        <div class="flex items-center space-x-3 pilihan-item">
                            <div class="pt-2">
                                <input type="radio" id="jawaban_benar_{{$i}}" name="jawaban_benar" value="{{$i}}" class="jawaban-selector h-5 w-5 text-accent-600 focus:ring-accent-500 border-slate-300" {{ old('jawaban_benar') == $i ? 'checked' : ($i == 0 ? 'checked' : '') }}>
                            </div>
                            <div class="flex-1">
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-slate-500 sm:text-sm">{{ chr(65 + $i) }}.</span>
                                    </div>
                                    <input type="text" name="pilihan[{{$i}}]" class="focus:ring-accent-500 focus:border-accent-500 block w-full pl-8 sm:text-sm border-slate-300 rounded-md py-2" placeholder="Opsi jawaban..." value="{{ old('pilihan.'.$i) }}">
                                </div>
                            </div>
                            <button type="button" class="remove-pilihan text-slate-400 hover:text-red-500 pt-2 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
            
        </div>

        <div class="pt-10 flex justify-end border-t border-slate-100 mt-8">
            <a href="{{ route('guru.soal.index') }}" class="bg-white border border-slate-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500">
                Batal
            </a>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500">
                Simpan Bank Soal
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
        
        function updateUIBasedOnType() {
            const val = tipeSelect.value;
            
            // Toggle Audio Section
            if (val === 'audio') {
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
            } else {
                pilihanSection.style.display = 'block';
                document.querySelectorAll('#pilihan_container input').forEach(el => el.disabled = false);
                
                // Change between radio and checkbox based on multiple_choice
                const isMultiple = val === 'multiple_choice';
                document.querySelectorAll('.jawaban-selector').forEach(el => {
                    el.type = isMultiple ? 'checkbox' : 'radio';
                    el.name = isMultiple ? 'jawaban_benar[]' : 'jawaban_benar';
                });
                
                if (isMultiple) {
                    document.getElementById('pilihan_help').innerHTML = 'Buat pilihan jawaban dan centang kotak (<span class="font-bold">Jawaban Benar</span>) di sebelah opsi yang benar (Anda bisa pilih lebih dari 1).';
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
            
            const html = `
                <div class="flex items-center space-x-3 pilihan-item mt-3">
                    <div class="pt-2">
                        <input type="${inputType}" name="${inputName}" value="${optionCount}" class="jawaban-selector h-5 w-5 text-accent-600 focus:ring-accent-500 border-slate-300">
                    </div>
                    <div class="flex-1">
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-slate-500 sm:text-sm">${letter}.</span>
                            </div>
                            <input type="text" name="pilihan[${optionCount}]" class="focus:ring-accent-500 focus:border-accent-500 block w-full pl-8 sm:text-sm border-slate-300 rounded-md py-2" placeholder="Opsi jawaban...">
                        </div>
                    </div>
                    <button type="button" class="remove-pilihan text-slate-400 hover:text-red-500 pt-2 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            optionCount++;
            
            // Attach event listener to new remove button
            attachRemoveListeners();
        });
        
        function attachRemoveListeners() {
            document.querySelectorAll('.remove-pilihan').forEach(btn => {
                // remove old listeners securely by replacing node if necessary
                btn.onclick = function() {
                    if (document.querySelectorAll('.pilihan-item').length > 2) {
                        this.closest('.pilihan-item').remove();
                        // Re-index tags (A, B, C...) is ideal, but for now we just remove element
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
