@extends('layouts.admin')

@section('header', 'Daftar Kelas LPK')
@section('header-sub', 'Admin / Kelas')

@section('extra-css')
<style>
    .page-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .toolbar-search { position: relative; }
    .toolbar-search input { padding: 10px 14px 10px 40px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 13px; outline: none; background: #fff; font-family: 'Inter', sans-serif; width: 280px; }
    .toolbar-search input:focus { border-color: #3b82f6; }
    .toolbar-search svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #94a3b8; }
    .tbtn { display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; transition: 0.15s; font-family: 'Inter', sans-serif; }
    .tbtn svg { width: 16px; height: 16px; margin-right: 6px; }
    .tbtn-green { background: #16a34a; color: #fff; }
    .tbtn-green:hover { background: #15803d; }

    .kelas-table { width: 100%; border-collapse: collapse; }
    .kelas-table th { text-align: left; padding: 14px 20px; font-size: 11px; font-weight: 700; color: #2563eb; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
    .kelas-table td { padding: 18px 20px; font-size: 14px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .kelas-table tr:hover td { background: #f8fafc; }
    .kbadge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; background: #dbeafe; color: #2563eb; }
    .action-icon { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: 0.15s; background: transparent; margin-left: 4px; }
    .action-icon:hover { background: #f1f5f9; }
    .action-icon svg { width: 18px; height: 18px; }
    .action-edit svg { color: #2563eb; }
    .action-delete svg { color: #dc2626; }
</style>
@endsection

@section('content')

<!-- Toolbar -->
<div class="page-toolbar">
    <form action="{{ route('admin.kelas.index') }}" method="GET" class="toolbar-search">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kelas..." onchange="this.form.submit()">
    </form>
    <a href="{{ route('admin.kelas.create') }}" class="tbtn tbtn-green">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Tambah Kelas Baru
    </a>
</div>

@if(session('success'))
<div style="margin-bottom: 20px; background: #f0fdf4; border-left: 4px solid #4ade80; padding: 14px 16px; border-radius: 8px; font-size: 14px; color: #166534;">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="margin-bottom: 20px; background: #fef2f2; border-left: 4px solid #f87171; padding: 14px 16px; border-radius: 8px; font-size: 14px; color: #991b1b;">
    {{ session('error') }}
</div>
@endif

<!-- Table -->
<div style="background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; overflow: hidden;">
    <table class="kelas-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Kelas</th>
                <th>Jumlah Murid</th>
                <th>Tgl Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kelas as $index => $k)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $k->nama }}</strong></td>
                <td><span class="kbadge">{{ $k->users_count }} Murid</span></td>
                <td style="color: #94a3b8;">{{ $k->created_at->format('d M Y') }}</td>
                <td>
                    <div style="display: flex; gap: 4px;">
                        <a href="{{ route('admin.kelas.show', $k) }}" class="action-icon" style="color: #64748b;" title="Lihat Murid">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('admin.kelas.edit', $k) }}" class="action-icon action-edit" title="Edit">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                    <form action="{{ route('admin.kelas.destroy', $k) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus kelas ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-icon action-delete" {{ $k->users_count > 0 ? 'disabled title="Masih ada murid" style="opacity:0.3;cursor:not-allowed;"' : '' }}>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 48px; color: #94a3b8;">Belum ada kelas yang terdaftar</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($kelas->hasPages())
    <div style="padding: 12px 20px; border-top: 1px solid #e2e8f0;">
        {{ $kelas->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection

@section('extra-js')
<script>
    // Server-side search implemented via form submit
</script>
@endsection
