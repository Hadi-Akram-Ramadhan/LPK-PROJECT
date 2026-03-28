@extends('layouts.admin')

@section('header')
<div class="flex items-center space-x-4">
    <a href="{{ route('admin.users.index') }}" class="text-slate-500 hover:text-slate-700 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <span>Tambah Pengguna Baru</span>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <form action="{{ route('admin.users.store') }}" method="POST" class="p-8">
        @csrf
        
        <h3 class="text-lg font-medium leading-6 text-slate-900 mb-6 border-b border-slate-100 pb-4">Informasi Profil</h3>
        
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-3">
                <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                <div class="mt-1">
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-slate-300 rounded-md">
                </div>
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-3">
                <label for="email" class="block text-sm font-medium text-slate-700">Alamat Email</label>
                <div class="mt-1">
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-slate-300 rounded-md">
                </div>
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            
            <div class="sm:col-span-3">
                <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                <div class="mt-1">
                    <input type="password" name="password" id="password" required class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-slate-300 rounded-md">
                </div>
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            
            <div class="sm:col-span-3">
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password</label>
                <div class="mt-1">
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-slate-300 rounded-md">
                </div>
            </div>
            
            <div class="sm:col-span-3">
                <label for="role" class="block text-sm font-medium text-slate-700">Role Pengguna</label>
                <div class="mt-1">
                    <select id="role" name="role" required class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-slate-300 rounded-md">
                        <option value="murid" {{ old('role') == 'murid' ? 'selected' : '' }}>Murid</option>
                        <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru / Instrukur</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>
                @error('role') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            
            <div class="sm:col-span-3" id="kelas_container">
                <label for="kelas_id" class="block text-sm font-medium text-slate-700">Penempatan Kelas <span class="text-slate-400 text-xs">(Khusus Murid)</span></label>
                <div class="mt-1">
                    <select id="kelas_id" name="kelas_id" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-slate-300 rounded-md">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                @error('kelas_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-8 flex justify-end">
            <a href="{{ route('admin.users.index') }}" class="bg-white border border-slate-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Batal
            </a>
            <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Simpan Pengguna
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const kelasContainer = document.getElementById('kelas_container');
        
        function toggleKelas() {
            if (roleSelect.value === 'murid') {
                kelasContainer.style.display = 'block';
            } else {
                kelasContainer.style.display = 'none';
                document.getElementById('kelas_id').value = '';
            }
        }
        
        roleSelect.addEventListener('change', toggleKelas);
        toggleKelas(); // Run on load
    });
</script>
@endsection
