@extends('layouts.admin')

@section('header')
<div class="flex justify-between items-center w-full">
    <span>Manajemen Pengguna</span>
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Tambah Pengguna
    </a>
</div>
@endsection

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col items-center justify-center text-center">
        <p class="text-sm text-slate-500 font-medium">Total Seluruh Sistem</p>
        <p class="text-3xl font-bold text-slate-800 mt-2">{{ $totalUsers }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col items-center justify-center text-center opacity-90">
        <p class="text-sm text-slate-500 font-medium">Murid Terdaftar</p>
        <p class="text-3xl font-bold text-primary-600 mt-2">{{ $totalMurid }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col items-center justify-center text-center opacity-90">
        <p class="text-sm text-slate-500 font-medium">Guru / Instrukur</p>
        <p class="text-3xl font-bold text-accent-600 mt-2">{{ $totalGuru }}</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-6 p-4">
    <div class="flex items-center space-x-4">
        <span class="text-sm font-medium text-slate-500">Filter Role:</span>
        <a href="{{ route('admin.users.index') }}" class="px-3 py-1 rounded-full text-sm {{ !request('role') ? 'bg-primary-100 text-primary-700 font-medium' : 'text-slate-600 hover:bg-slate-100' }}">Semua</a>
        <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="px-3 py-1 rounded-full text-sm {{ request('role') == 'admin' ? 'bg-indigo-100 text-indigo-700 font-medium' : 'text-slate-600 hover:bg-slate-100' }}">Admin</a>
        <a href="{{ route('admin.users.index', ['role' => 'guru']) }}" class="px-3 py-1 rounded-full text-sm {{ request('role') == 'guru' ? 'bg-accent-100 text-accent-700 font-medium' : 'text-slate-600 hover:bg-slate-100' }}">Guru</a>
        <a href="{{ route('admin.users.index', ['role' => 'murid']) }}" class="px-3 py-1 rounded-full text-sm {{ request('role') == 'murid' ? 'bg-primary-100 text-primary-700 font-medium' : 'text-slate-600 hover:bg-slate-100' }}">Murid</a>
    </div>
</div>

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

<!-- Table List Users -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama & Email</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kelas (Jika Murid)</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tgl Daftar</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Aksi</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-primary-100 to-accent-100 flex items-center justify-center font-bold text-slate-600">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-slate-900">{{ $user->name }}</div>
                                <div class="text-sm text-slate-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->role === 'admin')
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800">Admin</span>
                        @elseif($user->role === 'guru')
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-accent-100 text-accent-800">Guru</span>
                        @else
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-primary-100 text-primary-800">Murid</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        @if($user->role === 'murid' && $user->kelas)
                            {{ $user->kelas->nama }}
                        @else
                            <span class="text-slate-300">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-primary-600 hover:text-primary-900 mr-3">Edit</a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="mt-2 block text-sm font-medium text-slate-900">Belum ada pengguna ditemukan</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
