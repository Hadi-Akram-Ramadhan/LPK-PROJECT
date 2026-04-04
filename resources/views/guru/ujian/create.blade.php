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

@if(session('error'))
<div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 shadow-sm">
    <div class="flex">
        <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800">Gagal Memproses:</h3>
            <p class="mt-1 text-sm text-red-700">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

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

                <div>
                    <label for="jenis_ujian" class="block text-sm font-medium text-slate-700 mb-1">Jenis Ujian <span class="text-red-500">*</span></label>
                    <select name="jenis_ujian" id="jenis_ujian" class="shadow-sm focus:ring-accent-500 focus:border-accent-500 block w-full sm:text-sm border-slate-300 rounded-md" required>
                        <option value="reguler" {{ old('jenis_ujian') == 'reguler' ? 'selected' : '' }}>Ujian Reguler</option>
                        <option value="tryout" {{ old('jenis_ujian') == 'tryout' ? 'selected' : '' }}>Try-Out</option>
                    </select>
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
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-sm font-medium text-slate-700">Pilih Soal dari Bank Soal</label>
                        <span class="text-xs font-semibold bg-slate-100 text-slate-600 px-3 py-1 rounded-full border border-slate-200">
                            <span id="soal_count">0</span> Soal Dipilih
                        </span>
                    </div>

                    {{-- Group Selection by Package --}}
                    <div class="mb-6 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                        <label for="package_selector" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pilih Berdasarkan Paket (Opsi Cepat)</label>
                        <div class="flex space-x-2">
                            <select id="package_selector" class="block w-full text-sm border-slate-300 rounded-lg shadow-sm focus:ring-accent-500 focus:border-accent-500">
                                <option value="">-- Pilih Paket Soal --</option>
                                @foreach($paketSoals as $paket)
                                    <option value="{{ $paket->id }}" data-soals="{{ $paket->soals->pluck('id')->join(',') }}">
                                        {{ $paket->nama }} ({{ $paket->soals->count() }} Soal)
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" id="btn_add_package" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-accent-600 hover:bg-accent-700 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent-500">
                                <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Tambahkan
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-2 italic">* Memilih paket akan mencentang semua soal di dalam paket tersebut secara otomatis.</p>
                    </div>
                    
                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm">
                        <div class="p-3 border-b border-slate-200 bg-slate-50/50 flex justify-between items-center">
                            <label class="flex items-center text-sm font-semibold text-slate-700 cursor-pointer">
                                <input type="checkbox" id="check_all_soal" class="h-4 w-4 text-accent-600 focus:ring-accent-500 border-slate-300 rounded mr-2">
                                Pilih Semua Soal
                            </label>
                            <div class="relative">
                                <input type="text" id="soalSearch" placeholder="Cari pertanyaan..." class="text-xs border-slate-300 rounded-lg shadow-sm h-9 pl-8 focus:ring-accent-500 focus:border-accent-500 w-48 sm:w-64">
                                <svg class="absolute left-2.5 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>
                        
                        <div class="max-h-[500px] overflow-y-auto custom-scrollbar" id="soalListContainer">
                            @forelse($paketSoals as $paket)
                                <div class="bg-slate-50/30 px-4 py-2 border-b border-slate-100 flex justify-between items-center group-header">
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">{{ $paket->nama }}</span>
                                    <button type="button" class="btn-check-group text-[10px] font-bold text-accent-600 hover:text-accent-800 uppercase" data-target=".paket-id-{{ $paket->id }}">Centang Grup</button>
                                </div>
                                <ul class="divide-y divide-slate-100">
                                    @foreach($paket->soals as $soal)
                                        <li class="p-4 hover:bg-blue-50/30 transition-colors soal-item" data-package-id="{{ $paket->id }}">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0 pt-0.5">
                                                    <input id="soal_{{ $soal->id }}" name="soal_id[]" type="checkbox" value="{{ $soal->id }}" 
                                                        class="item_soal paket-id-{{ $paket->id }} h-5 w-5 text-accent-600 focus:ring-accent-500 border-slate-300 rounded cursor-pointer transition-all" 
                                                        {{ (is_array(old('soal_id')) && in_array($soal->id, old('soal_id'))) ? 'checked' : '' }}>
                                                </div>
                                                <label for="soal_{{ $soal->id }}" class="ml-4 flex-1 flex flex-col cursor-pointer">
                                                    <span class="text-sm font-medium text-slate-800 line-clamp-2 question-text leading-relaxed">{{ strip_tags($soal->pertanyaan) }}</span>
                                                    <div class="mt-2 flex items-center space-x-3 text-xs">
                                                        <span class="px-2 py-0.5 rounded-md font-bold {{ $soal->tipe == 'audio' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }}">
                                                            {{ strtoupper(str_replace('_', ' ', $soal->tipe)) }}
                                                        </span>
                                                        <span class="text-slate-400 font-medium tracking-tight">{{ $soal->poin }} Poin</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @empty
                                <div class="p-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                                    <h3 class="mt-2 text-sm font-medium text-slate-900">Belum ada Paket Soal</h3>
                                    <p class="mt-1 text-sm text-slate-500">Mulai dengan membuat Paket Soal terlebih dahulu.</p>
                                </div>
                            @endforelse
                        </div>
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
        const packageSelector = document.getElementById('package_selector');
        const btnAddPackage = document.getElementById('btn_add_package');

        function updateCount() {
            const checkedCount = document.querySelectorAll('.item_soal:checked').length;
            soalCountBadge.textContent = checkedCount;
            checkAll.checked = checkedCount > 0 && checkedCount === checkboxes.length;
            
            if (checkedCount > 0) {
                soalCountBadge.parentElement.classList.add('bg-accent-600', 'text-white', 'border-accent-600');
                soalCountBadge.parentElement.classList.remove('bg-slate-100', 'text-slate-600', 'border-slate-200');
            } else {
                soalCountBadge.parentElement.classList.remove('bg-accent-600', 'text-white', 'border-accent-600');
                soalCountBadge.parentElement.classList.add('bg-slate-100', 'text-slate-600', 'border-slate-200');
            }
        }

        // Add Group logic
        btnAddPackage.addEventListener('click', function() {
            const selectedOption = packageSelector.options[packageSelector.selectedIndex];
            const soalIdsRaw = selectedOption.dataset.soals;
            
            if (soalIdsRaw) {
                const ids = soalIdsRaw.split(',');
                ids.forEach(id => {
                    const el = document.getElementById('soal_' + id);
                    if (el) el.checked = true;
                });
                updateCount();
            } else if (!packageSelector.value) {
                alert('Silakan pilih paket soal terlebih dahulu.');
            }
        });

        // Individual group headers
        document.querySelectorAll('.btn-check-group').forEach(btn => {
            btn.addEventListener('click', function() {
                const target = this.dataset.target;
                const groupChecks = document.querySelectorAll(target);
                
                // Toggle logic: if some are unchecked, check all. else uncheck all.
                const allChecked = Array.from(groupChecks).every(cb => cb.checked);
                groupChecks.forEach(cb => cb.checked = !allChecked);
                updateCount();
            });
        });

        // Toggle all checkboxes
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
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

        // Search logic
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
            
            // Hide/Show headers if all childs hidden
            document.querySelectorAll('#soalListContainer ul').forEach(ul => {
                const hasVisible = Array.from(ul.querySelectorAll('.soal-item')).some(li => li.style.display !== 'none');
                const header = ul.previousElementSibling;
                if (header && header.classList.contains('group-header')) {
                    header.style.display = hasVisible ? 'flex' : 'none';
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
