@extends('layouts.admin')

@section('header')
<div class="flex justify-between items-center w-full">
    <span>Daftar Kelas LPK</span>
    <a href="{{ route('admin.kelas.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
        Tambah Kelas Baru
    </a>
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

<!-- Table List Kelas -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Kelas</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah Murid terdaftar</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tgl Dibuat</th>
                    <th scope="col" class="relative px-6 py-3">
                        <span class="sr-only">Aksi</span>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($kelas as $k)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-slate-800">{{ $k->nama }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $k->users_count }} Murid
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                        {{ $k->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('admin.kelas.edit', $k) }}" class="text-primary-600 hover:text-primary-900 mr-3">Edit</a>
                        <form action="{{ route('admin.kelas.destroy', $k) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kelas ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" {{ $k->users_count > 0 ? 'disabled title="Masih ada murid"' : '' }} class="disabled:opacity-50">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span class="mt-2 block text-sm font-medium text-slate-900">Belum ada kelas yang terdaftar</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($kelas->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $kelas->links() }}
    </div>
    @endif
</div>
@endsection
