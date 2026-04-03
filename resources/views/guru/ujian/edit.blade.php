@extends('layouts.guru')

@section('header')
<div class="flex items-center space-x-4">
    <a href="{{ route('guru.ujian.index') }}" class="text-slate-500 hover:text-slate-700 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <span>Edit Ujian: {{ $ujian->judul }}</span>
</div>
@endsection

@section('content')

@if($errors->any())
<div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 shadow-sm">
    <div class="flex">
        <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada input Anda:</h3>
            <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-5xl mx-auto">
    <form action="{{ route('guru.ujian.update', $ujian) }}" method="POST" class="p-8">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Konfigurasi Ujian -->
            <div class="lg:col-span-1 space-y-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-2">Konfigurasi Ujian</h3>
                
                <div>
                    <label for="judul" class="block text-sm font-medium text-slate-700 mb-1">Judul Ujian <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul', $ujian->judul) }}" required class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md">
                </div>

                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-slate-700 mb-1">Deksripsi (Opsional)</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3" class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md">{{ old('deskripsi', $ujian->deskripsi) }}</textarea>
                </div>

                <div>
                    <label for="durasi" class="block text-sm font-medium text-slate-700 mb-1">Durasi Pengerjaan (Menit) <span class="text-red-500">*</span></label>
                    <input type="number" name="durasi" id="durasi" value="{{ old('durasi', $ujian->durasi) }}" required min="1" class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md">
                </div>

                <div class="bg-slate-50 p-4 border border-slate-200 rounded-lg space-y-4">
                    <div>
                        <label for="mulai" class="block text-sm font-medium text-slate-700 mb-1">Jadwal Mulai Ujian (Opsional)</label>
                        <input type="datetime-local" name="mulai" id="mulai" value="{{ old('mulai', $ujian->mulai ? \Carbon\Carbon::parse($ujian->mulai)->format('Y-m-d\TH:i') : '') }}" class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md">
                    </div>

                    <div>
                        <label for="selesai" class="block text-sm font-medium text-slate-700 mb-1">Batas Waktu Masuk Terakhir</label>
                        <input type="datetime-local" name="selesai" id="selesai" value="{{ old('selesai', $ujian->selesai ? \Carbon\Carbon::parse($ujian->selesai)->format('Y-m-d\TH:i') : '') }}" class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md">
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="acak_soal" name="acak_soal" type="checkbox" value="1" {{ old('acak_soal', $ujian->acak_soal) ? 'checked' : '' }} class="h-4 w-4 text-accent-600 focus:ring-accent-500 border-slate-300 rounded">
                    <label for="acak_soal" class="ml-2 block text-sm text-slate-700">
                        Acak urutan soal untuk setiap peserta
                    </label>
                </div>
            </div>

            <!-- Peserta Dan Soal -->
            <div class="lg:col-span-2 space-y-6 lg:border-l lg:border-slate-200 lg:pl-8">
                <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-2">Pemilihan Soal</h3>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">Perhatian: Anda tidak dapat menambah/mengurangi target kelas yang sudah diassign ke ujian yang sedang berjalan. Anda harus menghapus ujian dan membuatnya ulang. Pengeditan di sini difokuskan pada soal dan konfigurasi detail ujian.</p>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-slate-700">Pilih Soal dari Bank Soal</label>
                        <span class="text-xs bg-accent-600 text-white px-2 py-1 rounded-full"><span id="soal_count">{{ count($selectedSoal) }}</span> Soal Dipilih</span>
                    </div>
                    
                    <div class="border border-slate-200 rounded-md overflow-hidden bg-white">
                        <div class="p-2 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                            <label class="flex items-center text-sm font-medium text-slate-700 cursor-pointer">
                                <input type="checkbox" id="check_all_soal" class="h-4 w-4 text-accent-600 focus:ring-accent-500 border-slate-300 rounded mr-2" {{ (count($selectedSoal) === count($bankSoal) && count($bankSoal) > 0) ? 'checked' : '' }}>
                                Pilih Semua
                            </label>
                            <input type="text" id="soalSearch" placeholder="Cari pertanyaan..." class="text-xs border-slate-300 rounded-md shadow-sm h-8">
                        </div>
                        <ul class="divide-y divide-slate-100 max-h-96 overflow-y-auto" id="soalList">
                            @forelse($bankSoal as $soal)
                                <li class="p-3 hover:bg-slate-50 transition-colors soal-item">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 pt-0.5">
                                            <input id="soal_{{ $soal->id }}" name="soal_id[]" type="checkbox" value="{{ $soal->id }}" class="item_soal h-4 w-4 text-accent-600 focus:ring-accent-500 border-slate-300 rounded cursor-pointer" {{ (in_array($soal->id, is_array(old('soal_id')) ? old('soal_id') : $selectedSoal)) ? 'checked' : '' }}>
                                        </div>
                                        <label for="soal_{{ $soal->id }}" class="ml-3 flex-1 flex flex-col cursor-pointer">
                                            <span class="text-sm font-medium text-slate-900 line-clamp-2 question-text">{{ strip_tags($soal->pertanyaan) }}</span>
                                            <div class="mt-1 flex space-x-2 text-xs text-slate-500">
                                                <span class="bg-slate-100 px-1.5 py-0.5 rounded">{{ strtoupper(str_replace('_', ' ', $soal->tipe)) }}</span>
                                                <span>{{ $soal->poin }} poin</span>
                                            </div>
                                        </label>
                                    </div>
                                </li>
                            @empty
                                <li class="p-4 text-sm text-slate-500 text-center">Bank Soal Anda, Kosong. Silakan buat soal terlebih dahulu di menu Bank Soal.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <div class="pt-8 flex justify-end mt-8 border-t border-slate-200">
            <a href="{{ route('guru.ujian.index') }}" class="bg-white border border-slate-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500">
                Batal
            </a>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-accent-600 hover:bg-accent-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mirrored script from create
        const checkAll = document.getElementById('check_all_soal');
        const checkboxes = document.querySelectorAll('.item_soal');
        const soalCountBadge = document.getElementById('soal_count');
        const searchInput = document.getElementById('soalSearch');

        function updateCount() {
            const checkedCount = document.querySelectorAll('.item_soal:checked').length;
            soalCountBadge.textContent = checkedCount;
            // update check all visual
            checkAll.checked = checkedCount === checkboxes.length && checkboxes.length > 0;
            
            if (checkedCount > 0) {
                soalCountBadge.parentElement.classList.add('bg-accent-600', 'text-white');
                soalCountBadge.parentElement.classList.remove('bg-slate-100', 'text-slate-600');
            } else {
                soalCountBadge.parentElement.classList.remove('bg-accent-600', 'text-white');
                soalCountBadge.parentElement.classList.add('bg-slate-100', 'text-slate-600');
            }
        }

        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                const li = cb.closest('li');
                if (li.style.display !== 'none') {
                    cb.checked = checkAll.checked;
                }
            });
            updateCount();
        });

        checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
        updateCount();

        searchInput.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.soal-item').forEach(item => {
                const text = item.querySelector('.question-text').textContent.toLowerCase();
                if (text.includes(term)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
</script>
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
</style>
@endsection
