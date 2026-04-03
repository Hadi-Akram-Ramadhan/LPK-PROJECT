@extends('layouts.admin')

@section('header', 'Manajemen Guru & Admin')
@section('header-sub', 'Kelola data pengajar dan administrator')

@section('extra-css')
<style>
    .page-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .toolbar-btns { display: flex; gap: 10px; }
    .tbtn { display: inline-flex; align-items: center; padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; transition: 0.15s; font-family: 'Inter', sans-serif; }
    .tbtn svg { width: 16px; height: 16px; margin-right: 6px; }
    .tbtn-green { background: #16a34a; color: #fff; }
    .tbtn-green:hover { background: #15803d; }

    .staff-table { width: 100%; border-collapse: collapse; }
    .staff-table th { text-align: left; padding: 14px 20px; font-size: 11px; font-weight: 700; color: #2563eb; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
    .staff-table td { padding: 18px 20px; font-size: 14px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .staff-table tr:hover td { background: #f8fafc; }
    .staff-avatar { width: 34px; height: 34px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; color: #fff; margin-right: 12px; flex-shrink: 0; vertical-align: middle; }
    .role-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .role-guru { background: #dbeafe; color: #2563eb; }
    .role-admin { background: #ede9fe; color: #7c3aed; }
    .action-icon { width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: 0.15s; background: transparent; margin-left: 4px; }
    .action-icon:hover { background: #f1f5f9; }
    .action-icon svg { width: 18px; height: 18px; }
    .action-edit svg { color: #2563eb; }
    .action-delete svg { color: #dc2626; }
    .page-footer-text { padding: 16px 20px; font-size: 13px; color: #94a3b8; }
</style>
@endsection

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <p class="text-sm text-slate-500 font-medium mb-1">Total Guru</p>
        <p class="text-3xl font-bold text-blue-600">{{ $totalGuru }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <p class="text-sm text-slate-500 font-medium mb-1">Total Admin</p>
        <p class="text-3xl font-bold text-purple-600">{{ $totalAdmin }}</p>
    </div>
</div>

<!-- Toolbar -->
<div class="page-toolbar">
    <div>
        <h3 class="text-lg font-semibold text-slate-800">Daftar Guru & Admin</h3>
    </div>
    <div class="toolbar-btns">
        <a href="{{ route('admin.users.create') }}" class="tbtn tbtn-green">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            + Tambah Guru/Admin
        </a>
    </div>
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
    <table class="staff-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $user)
            <tr>
                <td>{{ $users->firstItem() + $index }}</td>
                <td>
                    <div style="display: flex; align-items: center;">
                        <span class="staff-avatar" style="background: {{ $user->role === 'admin' ? '#7c3aed' : '#2563eb' }}">{{ substr($user->name, 0, 1) }}</span>
                        <strong>{{ $user->name }}</strong>
                    </div>
                </td>
                <td style="color: #94a3b8; font-family: monospace;">{{ $user->email }}</td>
                <td>
                    @if($user->role === 'admin')
                        <span class="role-badge role-admin">Admin</span>
                    @else
                        <span class="role-badge role-guru">Guru</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.users.edit', $user) }}" class="action-icon action-edit">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-icon action-delete">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 48px; color: #94a3b8;">Belum ada data guru atau admin.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="page-footer-text">Total {{ $totalGuru + $totalAdmin }} pengguna ditampilkan</div>
    @if($users->hasPages())
    <div style="padding: 12px 20px; border-top: 1px solid #e2e8f0;">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
