@extends('layouts.admin')

@section('header')
<div class="flex justify-between items-center w-full">
    <span>Audio File Explorer</span>
</div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
    <div class="flex">
        <div class="ml-3">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4">
    <div class="flex">
        <div class="ml-3">
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Upload Section -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                <svg class="h-5 w-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                Unggah Audio Baru
            </h3>
            
            <form action="{{ route('admin.audio.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Pilih File (MP3, WAV, OGG, ZIP)</label>
                        <div class="drag-drop-zone relative border-2 border-dashed border-slate-300 rounded-lg p-6 hover:bg-slate-50 transition-colors bg-white">
                            <input type="file" name="audio_file" accept=".mp3, .wav, .ogg, .zip" required
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"/>
                            <div class="text-center pointer-events-none">
                                <svg class="mx-auto block h-10 w-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <p class="mt-2 text-sm text-slate-600">Drag & drop file di sini, atau klik untuk memilih</p>
                                <p class="file-name-display mt-2 text-xs text-primary-600 font-medium"></p>
                            </div>
                        </div>
                        @error('audio_file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label for="custom_name" class="block text-sm font-medium text-slate-700 mb-1">Nama File Kustom <span class="font-normal text-slate-400">(Opsional)</span></label>
                        <input type="text" name="custom_name" id="custom_name" placeholder="choukai-n4-part1" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-slate-300 rounded-md">
                        <p class="text-xs text-slate-400 mt-1">Kosongkan untuk menggunakan nama file asli.</p>
                        @error('custom_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                <button type="submit" class="mt-6 w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Mulai Unggah
                </button>
            </form>
        </div>
        
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
            <h4 class="text-sm font-bold text-blue-800">Tips Penggunaan (Bulk Upload)</h4>
            <p class="text-xs text-blue-700 mt-1">File yang diunggah di sini dapat dipilih saat membuat atau mengedit soal berjenis _Listening_ (Choukai). <strong>Anda bisa langsung mengunggah file ZIP</strong> yang saling berisi berbagai audio sekaligus (bulk upload), sistem akan secara otomatis mengekstrak isinya.</p>
        </div>
    </div>
    
    <!-- File List Section -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800 w-full flex justify-between items-center">
                    <span>Semua File Audio ({{ $files->count() }})</span>
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">File & Player</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Ukuran</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Diunggah</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($files as $file)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-2">
                                    <div class="flex items-center text-sm font-medium text-slate-900 truncate" title="{{ $file['name'] }}">
                                        <svg class="h-4 w-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M8 12h.01M12 12h.01M16 12h.01M9 16H5a2 2 0 01-2-2V10a2 2 0 012-2h4l5-5v18l-5-5z"></path></svg>
                                        {{ strlen($file['name']) > 30 ? substr($file['name'], 0, 30) . '...' : $file['name'] }}
                                    </div>
                                    <audio controls class="h-8 max-w-[200px]" preload="none">
                                        <source src="{{ $file['url'] }}" type="audio/mpeg">
                                        Browser Anda tidak mendukung audio HTML5.
                                    </audio>
                                    <button class="text-xs text-primary-600 font-medium hover:text-primary-800 mt-1 cursor-pointer flex items-center" onclick="copyToClipboard('{{ $file['url'] }}')">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        Copy URL
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $file['size'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $file['last_modified'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button type="button" onclick="renameFile('{{ $file['name'] }}', '{{ route('admin.audio.rename') }}')" class="mr-2 text-primary-600 hover:text-primary-900 border border-primary-200 hover:bg-primary-50 rounded-md px-3 py-1 transition-colors">Ubah Nama</button>
                                <form action="{{ route('admin.audio.destroy') }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus file audio ini? File yang dihapus tidak dapat dipulihkan dan soal yang menggunakannya mungkin akan error.');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="filename" value="{{ $file['name'] }}">
                                    <button type="submit" class="text-red-600 hover:text-red-900 border border-red-200 hover:bg-red-50 rounded-md px-3 py-1 transition-colors">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                                <span class="mt-2 block text-sm font-medium text-slate-900">Belum ada file audio di server</span>
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
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('URL audio disalin ke clipboard: ' + text);
        }, function(err) {
            console.error('Gagal menyalin: ', err);
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
            confirmButtonColor: '#2563eb',
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
