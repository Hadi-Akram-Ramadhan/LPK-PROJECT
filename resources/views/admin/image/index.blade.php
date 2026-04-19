@extends('layouts.admin')

@section('header')
<div class="flex justify-between items-center w-full">
    <span>Image Explorer</span>
</div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 shadow-sm rounded-r-lg">
    <div class="flex"><div class="ml-3"><p class="text-sm text-green-700 font-medium">{{ session('success') }}</p></div></div>
</div>
@endif
@if(session('error'))
<div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 shadow-sm rounded-r-lg">
    <div class="flex"><div class="ml-3"><p class="text-sm text-red-700 font-medium">{{ session('error') }}</p></div></div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Upload Panel ── --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-base font-semibold text-slate-800 mb-4 flex items-center">
                <svg class="h-5 w-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Unggah File Gambar
            </h3>

            <form action="{{ route('admin.image.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pilih File (JPG, PNG, WEBP, ZIP)</label>
                    <div class="drag-drop-zone relative border-2 border-dashed border-slate-300 rounded-lg p-6 hover:bg-slate-50 transition-colors bg-white">
                        <input type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,.zip" required
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="text-center pointer-events-none">
                            <svg class="mx-auto block h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="mt-2 text-sm text-slate-600">Drag & drop file di sini, atau klik untuk memilih</p>
                            <p class="file-name-display mt-2 text-xs text-orange-600 font-medium"></p>
                        </div>
                    </div>
                    @error('image_file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="custom_name" class="block text-sm font-medium text-slate-700 mb-1">
                        Nama Kustom <span class="font-normal text-slate-400">(Opsional)</span>
                    </label>
                    <input type="text" name="custom_name" id="custom_name"
                        placeholder="gambar-pendukung-1"
                        class="shadow-sm focus:ring-orange-500 focus:border-orange-500 block w-full sm:text-sm border-slate-300 rounded-md">
                    <p class="text-[11px] text-slate-400 mt-1">Kosongkan untuk memakai nama file asli.</p>
                    @error('custom_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm
                           text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 transition-colors
                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Unggah Sekarang
                </button>
            </form>
        </div>

        <div class="bg-orange-50 border-l-4 border-orange-400 p-4 rounded-r-lg">
            <h4 class="text-sm font-bold text-orange-800">Tips Penggunaan (Bulk Upload)</h4>
            <p class="text-xs text-orange-700 mt-1">
                File yang diunggah di sini dapat dipilih langsung saat membuat / mengedit soal. Gambar ini dapat digunakan sebagai lampiran pertanyaan maupun jawaban pilihan ganda tipe gambar. <strong>Anda bisa langsung mengunggah file ZIP</strong> yang saling berisi berbagai gambar sekaligus (bulk upload), sistem akan secara otomatis mengekstrak isinya.
            </p>
        </div>
    </div>

    {{-- ── File List Panel ── --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-800">
                    Semua File Gambar
                    <span class="ml-2 bg-slate-100 text-slate-600 text-xs font-semibold px-2 py-0.5 rounded-full">
                        {{ $files->count() }} file
                    </span>
                </h3>
            </div>

            <div class="overflow-x-auto overflow-y-auto max-h-[600px] relative">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Preview &amp; File</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Ukuran</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Diunggah</th>
                            <th class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($files as $file)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0 h-12 w-12 border border-slate-200 rounded-md overflow-hidden bg-slate-100 flex items-center justify-center cursor-pointer hover:opacity-80 transition-opacity" onclick="viewImage('{{ $file['url'] }}')">
                                        <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" class="object-cover w-full h-full">
                                    </div>
                                    <div class="flex flex-col">
                                        <div class="text-sm font-medium text-slate-900" title="{{ $file['name'] }}">
                                            <span class="truncate block max-w-[150px]">{{ $file['name'] }}</span>
                                        </div>
                                        <button type="button"
                                            onclick="navigator.clipboard.writeText('{{ $file['name'] }}').then(()=>alert('Nama file disalin: {{ $file['name'] }}'))"
                                            class="text-xs text-orange-600 font-medium hover:text-orange-800 flex items-center w-fit mt-1">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                            </svg>
                                            Salin Nama
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $file['size'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $file['last_modified'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button type="button" onclick="renameFile('{{ $file['name'] }}', '{{ route('admin.image.rename') }}')" class="mr-2 text-orange-600 hover:text-orange-900 border border-orange-200 hover:bg-orange-50 rounded-md px-3 py-1 transition-colors text-xs">Ubah Nama</button>
                                <form action="{{ route('admin.image.destroy') }}" method="POST" class="inline"
                                    onsubmit="return confirm('Yakin hapus gambar ini? Soal yang menggunakannya mungkin akan rusak (gagal load gambar).');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="filename" value="{{ $file['name'] }}">
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-900 border border-red-200 hover:bg-red-50 rounded-md px-3 py-1 transition-colors text-xs">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                <svg class="mx-auto block h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="mt-2 block text-sm font-medium text-slate-900">Belum ada file gambar di server.</span>
                                <span class="text-xs text-slate-500">Unggah file di panel kiri untuk memulai.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    function viewImage(url) {
        Swal.fire({
            imageUrl: url,
            imageAlt: 'Preview Image',
            showConfirmButton: false,
            showCloseButton: true,
            width: 'auto',
            padding: '1rem',
            background: 'transparent',
            backdrop: 'rgba(0,0,0,0.85)',
            customClass: {
                image: 'max-h-[85vh] max-w-[90vw] object-contain rounded-lg shadow-2xl m-0'
            }
        });
    }

    function renameFile(oldName, routeUrl) {
        Swal.fire({
            title: 'Ubah Nama File',
            text: 'Catatan: Perubahan nama ini akan otomatis tersinkronisasi tanpa memecahkan media di Soal Ujian.',
            input: 'text',
            inputValue: oldName,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#f97316',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Simpan Nama',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                if (!value) return 'Nama file tidak boleh kosong!';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let newName = result.value;
                if (newName !== oldName) {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = routeUrl;
                    
                    let csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    
                    let inputOld = document.createElement('input');
                    inputOld.type = 'hidden';
                    inputOld.name = 'old_name';
                    inputOld.value = oldName;
                    
                    let inputNew = document.createElement('input');
                    inputNew.type = 'hidden';
                    inputNew.name = 'new_name';
                    inputNew.value = newName;
                    
                    form.appendChild(csrf);
                    form.appendChild(inputOld);
                    form.appendChild(inputNew);
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const dropzones = document.querySelectorAll('.drag-drop-zone');
        dropzones.forEach(zone => {
            const input = zone.querySelector('input[type="file"]');
            const display = zone.querySelector('.file-name-display');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                zone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                }, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                zone.addEventListener(eventName, () => {
                    zone.classList.add('border-blue-500', 'bg-blue-50');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                zone.addEventListener(eventName, () => {
                    zone.classList.remove('border-blue-500', 'bg-blue-50');
                }, false);
            });

            zone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                if(dt.files.length > 0) {
                    input.files = dt.files;
                    if(display) display.innerHTML = `File terpilih: <span class="font-bold">${dt.files[0].name}</span>`;
                }
            });

            input.addEventListener('change', function(e) {
                if(this.files && this.files.length > 0) {
                    if(display) display.innerHTML = `File terpilih: <span class="font-bold">${this.files[0].name}</span>`;
                }
            });
        });
    });
</script>
@endsection
