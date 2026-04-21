@extends('layouts.admin')

@section('header', 'Tambah Ujian Baru')
@section('header-sub', 'Buat paket ujian dan tentukan durasi pengerjaan')

@section('content')
<div class="card p-8" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ route('admin.ujian.store') }}" method="POST">
        @csrf
        <div class="grid-2 mb-6">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-700">Judul Ujian</label>
                <input type="text" name="judul" class="w-full px-4 py-3 border border-slate-200 rounded-lg outline-none focus:border-blue-500" value="{{ old('judul') }}" placeholder="Contoh: UTS Bahasa Korea Dasar" maxlength="20" required>
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-700">Jenis Ujian</label>
                <select name="jenis_ujian" id="jenis_ujian_selector" class="w-full px-4 py-3 border border-slate-200 rounded-lg outline-none focus:border-blue-500" required>
                    <option value="reguler">Ujian Reguler</option>
                    <option value="tryout">Try-Out (Akses Bebas)</option>
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold mb-2 text-slate-700">Deskripsi Singkat</label>
            <textarea name="deskripsi" class="w-full px-4 py-3 border border-slate-200 rounded-lg outline-none focus:border-blue-500" placeholder="Tuliskan keterangan singkat mengenai ujian ini..." rows="3" maxlength="50">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="grid-2 mb-6">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-700">Durasi (Menit)</label>
                <input type="number" name="durasi" class="w-full px-4 py-3 border border-slate-200 rounded-lg outline-none focus:border-blue-500" value="60" required>
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-700">Target Kelas (Bisa pilih multi)</label>
                <div style="max-height: 120px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; background: #fff;">
                    @forelse($kelases as $kls)
                        <label style="display: flex; align-items: center; font-size: 13px; margin-bottom: 8px; cursor: pointer; color: #334155;">
                            <input name="kelas_id[]" value="{{ $kls->id }}" type="checkbox" style="width: 16px; height: 16px; margin-right: 8px;" {{ (is_array(old('kelas_id')) && in_array($kls->id, old('kelas_id'))) ? 'checked' : '' }}>
                            {{ $kls->nama }}
                        </label>
                    @empty
                        <div class="text-sm text-red-500">Belum ada data kelas dari Admin.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid-2 mb-8" id="time_restricted_section">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-700">Waktu Mulai</label>
                <input type="datetime-local" name="mulai" class="w-full px-4 py-3 border border-slate-200 rounded-lg outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-700">Waktu Berakhir</label>
                <input type="datetime-local" name="selesai" class="w-full px-4 py-3 border border-slate-200 rounded-lg outline-none focus:border-blue-500">
            </div>
        </div>

        <div class="grid-2 mb-6">
            <div class="flex items-center">
                <input id="acak_soal" name="acak_soal" type="checkbox" value="1" {{ old('acak_soal') ? 'checked' : '' }} class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-slate-300 rounded cursor-pointer">
                <label for="acak_soal" class="ml-2 block text-sm font-bold text-slate-700 cursor-pointer">
                    Acak urutan soal untuk setiap peserta
                </label>
            </div>
            <div class="flex items-center">
                <input id="acak_jawaban" name="acak_jawaban" type="checkbox" value="1" {{ old('acak_jawaban') ? 'checked' : '' }} class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-slate-300 rounded cursor-pointer">
                <label for="acak_jawaban" class="ml-2 block text-sm font-bold text-slate-700 cursor-pointer">
                    Acak urutan jawaban untuk setiap peserta
                </label>
            </div>
            <div class="flex items-center bg-blue-50 p-3 rounded-lg border border-blue-100">
                <input id="tes_buta_warna" name="tes_buta_warna" type="checkbox" value="1" {{ old('tes_buta_warna') ? 'checked' : '' }} class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-slate-300 rounded cursor-pointer">
                <label for="tes_buta_warna" class="ml-2 block text-sm font-bold text-blue-800 cursor-pointer">
                    Aktifkan Sistem Cerdas Tes Buta Warna
                </label>
            </div>
        </div>

        <div style="border-top: 2px solid #f1f5f9; padding-top: 32px; margin-top: 32px;">
            <div class="flex-between mb-4">
                <h3 class="text-xl font-bold text-slate-800">Pilih Soal (Pilih minimal satu)</h3>
                <span id="soal_count_badge" class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold border border-slate-200">
                    <span id="soal_count">0</span> Soal Dipilih
                </span>
            </div>

            {{-- Package Selection --}}
            <div style="margin-bottom: 24px; padding: 20px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px;">
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Opsi Cepat: Pilih Berdasarkan Paket</label>
                <div style="display: flex; gap: 10px;">
                    <select id="package_selector" style="flex: 1; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; font-size: 14px;">
                        <option value="">-- Pilih Paket Soal --</option>
                        @foreach($paketSoals as $paket)
                            <option value="{{ $paket->id }}" data-soals="{{ $paket->soals->pluck('id')->join(',') }}">
                                {{ $paket->nama }} ({{ $paket->soals->count() }} Soal)
                            </option>
                        @endforeach
                    </select>
                    <button type="button" id="btn_add_package" class="btn btn-primary" style="padding: 10px 20px;">Tambahkan</button>
                </div>
            </div>

            <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <label class="flex items-center text-sm font-bold text-slate-600 cursor-pointer">
                    <input type="checkbox" id="check_all_soal" style="width: 16px; height: 16px; margin-right: 8px;">
                    Pilih Semua Soal
                </label>
                <input type="text" id="soalSearch" placeholder="Cari pertanyaan..." style="padding: 8px 15px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; width: 250px;">
            </div>

            <div id="soalListContainer" style="max-height: 500px; overflow-y: auto; border: 1.5px solid #e2e8f0; border-radius: 12px;">
                @forelse($paketSoals as $paket)
                    <div class="group-header" style="background: #f1f5f9; padding: 8px 15px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">{{ $paket->nama }}</span>
                        <button type="button" class="btn-check-group" data-target=".paket-id-{{ $paket->id }}" style="background: none; border: none; color: #2563eb; font-size: 10px; font-weight: 800; cursor: pointer; text-transform: uppercase;">Centang Grup</button>
                    </div>
                    <table class="w-full text-left" style="border-collapse: collapse;">
                        <tbody>
                            @foreach($paket->soals as $s)
                            <tr class="soal-item" data-package-id="{{ $paket->id }}" style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                                <td class="py-4 px-4 text-center" style="width: 50px;">
                                    <input type="checkbox" name="soal_ids[]" id="soal_{{ $s->id }}" value="{{ $s->id }}" class="item_soal paket-id-{{ $paket->id }}" style="width: 18px; height: 18px; cursor: pointer;">
                                </td>
                                <td class="py-4 px-2 cursor-pointer" onclick="document.getElementById('soal_{{ $s->id }}').click()">
                                    <div class="question-text" style="font-size: 14px; color: #1e293b; font-weight: 500; margin-bottom: 4px;">
                                        {!! strip_tags($s->pertanyaan) !!}
                                    </div>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <span style="font-size: 10px; color: #64748b; font-weight: 700; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">{{ $s->tipe }}</span>
                                        <span style="font-size: 10px; color: #94a3b8; font-weight: 600;">{{ $s->poin }} POIN</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @empty
                    <div class="p-10 text-center text-slate-400">Belum ada paket soal tersedia.</div>
                @endforelse
            </div>
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
                if (checkAll) checkAll.checked = checkedCount > 0 && checkedCount === checkboxes.length;
                
                const badgeWrap = document.getElementById('soal_count_badge');
                if (checkedCount > 0) {
                    badgeWrap.style.background = '#2563eb';
                    badgeWrap.style.color = '#fff';
                    badgeWrap.style.borderColor = '#2563eb';
                } else {
                    badgeWrap.style.background = '#f1f5f9';
                    badgeWrap.style.color = '#64748b';
                    badgeWrap.style.borderColor = '#e2e8f0';
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
                    const allChecked = Array.from(groupChecks).every(cb => cb.checked);
                    groupChecks.forEach(cb => cb.checked = !allChecked);
                    updateCount();
                });
            });

            // Toggle all checkboxes
            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => {
                        const tr = cb.closest('tr');
                        if (tr.style.display !== 'none') {
                            cb.checked = checkAll.checked;
                        }
                    });
                    updateCount();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateCount);
            });
            
            updateCount();

            // Search logic
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.soal-item').forEach(item => {
                    const text = item.querySelector('.question-text').textContent.toLowerCase();
                    item.style.display = text.includes(term) ? '' : 'none';
                });
                
                // Hide/Show headers
                document.querySelectorAll('#soalListContainer table').forEach(table => {
                    const hasVisible = Array.from(table.querySelectorAll('.soal-item')).some(tr => tr.style.display !== 'none');
                    const header = table.previousElementSibling;
                    if (header && header.classList.contains('group-header')) {
                        header.style.display = hasVisible ? 'flex' : 'none';
                    }
                });
            });

            // Logic to hide time for Tryout
            const typeSelector = document.getElementById('jenis_ujian_selector');
            const timeSection = document.getElementById('time_restricted_section');
            
            function toggleTimeSection() {
                if (typeSelector.value === 'tryout') {
                    timeSection.style.display = 'none';
                } else {
                    timeSection.style.display = 'grid';
                }
            }

            typeSelector.addEventListener('change', toggleTimeSection);
            toggleTimeSection(); // Initial call
        });
        </script>

        <div class="flex-between mt-10" style="border-top: 1px solid #f1f5f9; padding-top: 24px;">
            <a href="{{ route('admin.ujian.index') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan & Daftarkan Ujian</button>
        </div>
    </form>
</div>
@endsection
