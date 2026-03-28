@extends('layouts.guru')

@section('header')
<div class="flex items-center space-x-4">
    <a href="{{ route('guru.ujian.index') }}" class="text-slate-500 hover:text-slate-700 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <span>Buat Ujian / Jadwal Baru</span>
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
    <form action="{{ route('guru.ujian.store') }}" method="POST" class="p-8">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Konfigurasi Ujian -->
            <div class="lg:col-span-1 space-y-6">
                <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-2">Konfigurasi Ujian</h3>
                
                <div>
                    <label for="judul" class="block text-sm font-medium text-slate-700 mb-1">Judul Ujian <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required placeholder="Contoh: Tryout N4 Sesi 1" class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md">
                </div>

                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-slate-700 mb-1">Deksripsi (Opsional)</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3" class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md">{{ old('deskripsi') }}</textarea>
                </div>

                <div>
                    <label for="durasi" class="block text-sm font-medium text-slate-700 mb-1">Durasi Pengerjaan (Menit) <span class="text-red-500">*</span></label>
                    <input type="number" name="durasi" id="durasi" value="{{ old('durasi', 60) }}" required min="1" class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md">
                </div>

                <div class="bg-slate-50 p-4 border border-slate-200 rounded-lg space-y-4 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-2 -mr-2 bg-slate-200 rounded-full h-12 w-12 flex items-center justify-center opacity-50">
                        <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    
                    <div>
                        <label for="mulai" class="block text-sm font-medium text-slate-700 mb-1 relative z-10">Jadwal Mulai Ujian (Opsional)</label>
                        <input type="datetime-local" name="mulai" id="mulai" value="{{ old('mulai') }}" class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md relative z-10">
                        <p class="text-[10px] text-slate-500 mt-1">Jika dikosongkan, ujian bisa diakses kapan saja setelah dibuat.</p>
                    </div>

                    <div>
                        <label for="selesai" class="block text-sm font-medium text-slate-700 mb-1 relative z-10">Batas Waktu Masuk Terakhir</label>
                        <input type="datetime-local" name="selesai" id="selesai" value="{{ old('selesai') }}" class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md relative z-10">
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="acak_soal" name="acak_soal" type="checkbox" value="1" {{ old('acak_soal') ? 'checked' : '' }} class="h-4 w-4 text-accent-600 focus:ring-accent-500 border-slate-300 rounded">
                    <label for="acak_soal" class="ml-2 block text-sm text-slate-700">
                        Acak urutan soal untuk setiap peserta
                    </label>
                </div>
            </div>

            <!-- Peserta Dan Soal -->
            <div class="lg:col-span-2 space-y-6 lg:border-l lg:border-slate-200 lg:pl-8">
                <h3 class="text-lg font-semibold text-slate-800 border-b border-slate-200 pb-2">Alokasi & Pemilihan Soal</h3>

                <div class="bg-blue-50 border border-blue-200 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-blue-800 mb-2">Tugaskan Ujian ke Kelas (Pilih lebih dari satu)</label>
                    <div class="grid grid-cols-2 gap-2 mt-2 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($kelas as $kls)
                            <div class="flex items-center">
                                <input id="kelas_{{ $kls->id }}" name="kelas_id[]" value="{{ $kls->id }}" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded" {{ (is_array(old('kelas_id')) && in_array($kls->id, old('kelas_id'))) ? 'checked' : '' }}>
                                <label for="kelas_{{ $kls->id }}" class="ml-2 block text-sm text-slate-700">
                                    {{ $kls->nama }}
                                </label>
                            </div>
                        @empty
                            <div class="col-span-2 text-sm text-red-500">Belum ada data kelas dari Admin. Silakan buat ujian tanpa kelas dan tugaskan nanti.</div>
                        @endforelse
                    </div>
                    <p class="text-xs text-blue-600 mt-2">* Murid yang berada di kelas yang dicentang akan otomatis terdaftar sebagai peserta ujian ini.</p>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-slate-700">Pilih Soal dari Bank Soal</label>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-full"><span id="soal_count">0</span> Soal Dipilih</span>
                    </div>
                    
                    <div class="border border-slate-200 rounded-md overflow-hidden bg-white">
                        <div class="p-2 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                            <label class="flex items-center text-sm font-medium text-slate-700 cursor-pointer">
                                <input type="checkbox" id="check_all_soal" class="h-4 w-4 text-accent-600 focus:ring-accent-500 border-slate-300 rounded mr-2">
                                Pilih Semua
                            </label>
                            <input type="text" id="soalSearch" placeholder="Cari pertanyaan..." class="text-xs border-slate-300 rounded-md shadow-sm h-8">
                        </div>
                        <ul class="divide-y divide-slate-100 max-h-96 overflow-y-auto" id="soalList">
                            @forelse($bankSoal as $soal)
                                <li class="p-3 hover:bg-slate-50 transition-colors soal-item">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 pt-0.5">
                                            <input id="soal_{{ $soal->id }}" name="soal_id[]" type="checkbox" value="{{ $soal->id }}" class="item_soal h-4 w-4 text-accent-600 focus:ring-accent-500 border-slate-300 rounded cursor-pointer" {{ (is_array(old('soal_id')) && in_array($soal->id, old('soal_id'))) ? 'checked' : '' }}>
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
                Simpan & Distribusikan Ujian
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('check_all_soal');
        const checkboxes = document.querySelectorAll('.item_soal');
        const soalCountBadge = document.getElementById('soal_count');
        const searchInput = document.getElementById('soalSearch');

        function updateCount() {
            const checkedCount = document.querySelectorAll('.item_soal:checked').length;
            soalCountBadge.textContent = checkedCount;
            checkAll.checked = checkedCount === checkboxes.length && checkboxes.length > 0;
            
            if (checkedCount > 0) {
                soalCountBadge.classList.add('bg-accent-600', 'text-white');
                soalCountBadge.classList.remove('bg-slate-100', 'text-slate-600');
            } else {
                soalCountBadge.classList.remove('bg-accent-600', 'text-white');
                soalCountBadge.classList.add('bg-slate-100', 'text-slate-600');
            }
        }

        // Toggle all checkboxes
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                // Only toggle visible checks if searching
                const li = cb.closest('li');
                if (li.style.display !== 'none') {
                    cb.checked = checkAll.checked;
                }
            });
            updateCount();
        });

        // Individual toggles updates count
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateCount);
        });
        
        // Initial count
        updateCount();

        // Primitive Search
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
