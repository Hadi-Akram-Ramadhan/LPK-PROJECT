@extends('layouts.admin')

@section('header', 'Manajemen Pengguna')
@section('header-sub', 'Kelola data guru, admin & siswa')

@section('extra-css')
<style>
    /* ===== ANIMATIONS ===== */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideDown {
        from { opacity: 0; max-height: 0; }
        to { opacity: 1; max-height: 600px; }
    }
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    @keyframes pulse-ring {
        0% { transform: scale(0.8); opacity: 1; }
        100% { transform: scale(1.4); opacity: 0; }
    }

    .fade-up { animation: fadeInUp 0.5s ease both; }
    .fade-up-delay { animation: fadeInUp 0.5s ease 0.15s both; }

    /* ===== STAT MINI CARDS ===== */
    .stat-row { display: flex; gap: 14px; margin-bottom: 16px; }
    .stat-mini {
        flex: 1; display: flex; align-items: center; gap: 14px;
        background: #fff; border-radius: 14px; padding: 16px 20px;
        border: 1px solid #e2e8f0; transition: all 0.25s ease;
        position: relative; overflow: hidden;
    }
    .stat-mini::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        border-radius: 14px 14px 0 0;
    }
    .stat-mini:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
    .stat-mini.guru::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .stat-mini.admin::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
    .stat-mini.siswa::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px; display: flex;
        align-items: center; justify-content: center; flex-shrink: 0;
    }
    .stat-icon svg { width: 22px; height: 22px; }
    .stat-icon.blue { background: #dbeafe; color: #2563eb; }
    .stat-icon.purple { background: #ede9fe; color: #7c3aed; }
    .stat-icon.green { background: #d1fae5; color: #059669; }
    .stat-label { font-size: 12px; color: #94a3b8; font-weight: 500; }
    .stat-value { font-size: 24px; font-weight: 800; color: #1e293b; margin-top: 2px; }

    /* ===== SECTION CARD ===== */
    .section-card {
        background: #fff; border-radius: 16px; border: 1px solid #e2e8f0;
        overflow: hidden; transition: box-shadow 0.3s ease;
    }
    .section-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    .section-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 18px 24px; border-bottom: 1px solid #f1f5f9;
    }
    .section-title-wrap { display: flex; align-items: center; gap: 12px; }
    .section-dot {
        width: 10px; height: 10px; border-radius: 50%; position: relative;
    }
    .section-dot::after {
        content: ''; position: absolute; inset: -3px; border-radius: 50%;
        animation: pulse-ring 2s ease-out infinite;
    }
    .section-dot.blue { background: #3b82f6; }
    .section-dot.blue::after { border: 2px solid #3b82f680; }
    .section-dot.green { background: #10b981; }
    .section-dot.green::after { border: 2px solid #10b98180; }
    .section-title { font-size: 15px; font-weight: 700; color: #1e293b; }
    .section-subtitle { font-size: 12px; color: #94a3b8; font-weight: 400; }
    .section-actions { display: flex; align-items: center; gap: 10px; }

    /* ===== TOOLBAR BUTTONS ===== */
    .btn-add {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 18px; border-radius: 10px; font-size: 13px; font-weight: 600;
        border: none; cursor: pointer; text-decoration: none; transition: all 0.2s;
        font-family: 'Inter', sans-serif;
    }
    .btn-add svg { width: 16px; height: 16px; }
    .btn-add.primary { background: #2563eb; color: #fff; }
    .btn-add.primary:hover { background: #1d4ed8; box-shadow: 0 4px 12px rgba(37,99,235,0.3); }
    .btn-add.success { background: #059669; color: #fff; }
    .btn-add.success:hover { background: #047857; box-shadow: 0 4px 12px rgba(5,150,105,0.3); }

    /* ===== SEARCH INPUT ===== */
    .mini-search {
        position: relative; display: inline-flex; align-items: center;
    }
    .mini-search input {
        padding: 8px 12px 8px 34px; border: 1px solid #e2e8f0; border-radius: 10px;
        font-size: 13px; outline: none; background: #f8fafc; font-family: 'Inter', sans-serif;
        width: 200px; transition: all 0.2s;
    }
    .mini-search input:focus { border-color: #3b82f6; background: #fff; width: 240px; }
    .mini-search svg {
        position: absolute; left: 10px; width: 15px; height: 15px; color: #94a3b8;
        pointer-events: none;
    }

    /* ===== TABLE ===== */
    .u-table { width: 100%; border-collapse: collapse; }
    .u-table th {
        text-align: left; padding: 12px 20px; font-size: 11px; font-weight: 700;
        color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;
        background: #f8fafc; border-bottom: 1px solid #e2e8f0;
    }
    .u-table td {
        padding: 14px 20px; font-size: 13.5px; color: #334155;
        border-bottom: 1px solid #f1f5f9; vertical-align: middle;
    }
    .u-table tbody tr { transition: background 0.15s ease; }
    .u-table tbody tr:hover td { background: #f8fafc; }
    .u-table tbody tr:last-child td { border-bottom: none; }

    /* ===== AVATAR ===== */
    .u-avatar {
        width: 34px; height: 34px; border-radius: 10px; display: inline-flex;
        align-items: center; justify-content: center; font-weight: 700; font-size: 13px;
        color: #fff; flex-shrink: 0; margin-right: 12px; vertical-align: middle;
    }
    .u-name { font-weight: 600; color: #1e293b; }
    .u-email { font-size: 12px; color: #94a3b8; font-family: 'JetBrains Mono', monospace; margin-top: 2px; }

    /* ===== ROLE BADGE ===== */
    .role-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700;
        letter-spacing: 0.3px; text-transform: uppercase;
    }
    .role-badge::before {
        content: ''; width: 6px; height: 6px; border-radius: 50%;
    }
    .role-guru { background: #dbeafe; color: #1d4ed8; }
    .role-guru::before { background: #3b82f6; }
    .role-admin { background: #ede9fe; color: #6d28d9; }
    .role-admin::before { background: #8b5cf6; }
    .role-murid { background: #d1fae5; color: #047857; }
    .role-murid::before { background: #10b981; }

    /* ===== ACTION BUTTONS ===== */
    .act-btn {
        width: 32px; height: 32px; border-radius: 8px; display: inline-flex;
        align-items: center; justify-content: center; border: 1px solid transparent;
        cursor: pointer; transition: all 0.2s; background: transparent;
    }
    .act-btn svg { width: 16px; height: 16px; }
    .act-btn.edit { color: #2563eb; }
    .act-btn.edit:hover { background: #eff6ff; border-color: #bfdbfe; }
    .act-btn.del { color: #dc2626; }
    .act-btn.del:hover { background: #fef2f2; border-color: #fecaca; }

    /* ===== TABLE FOOTER ===== */
    .table-foot {
        display: flex; justify-content: space-between; align-items: center;
        padding: 12px 20px; border-top: 1px solid #f1f5f9;
        font-size: 12px; color: #94a3b8;
    }

    /* ===== KELAS BADGE ===== */
    .kelas-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 6px; font-size: 12px; font-weight: 500;
        background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;
    }
    .kelas-badge svg { width: 12px; height: 12px; color: #94a3b8; }

    /* ===== ALERT ===== */
    .alert-bar {
        display: flex; align-items: center; gap: 10px; padding: 14px 18px;
        border-radius: 12px; margin-bottom: 20px; font-size: 13.5px; font-weight: 500;
        animation: fadeInUp 0.4s ease both;
    }
    .alert-bar svg { width: 18px; height: 18px; flex-shrink: 0; }
    .alert-bar.success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .alert-bar.success svg { color: #22c55e; }
    .alert-bar.error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .alert-bar.error svg { color: #ef4444; }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        text-align: center; padding: 48px 20px; color: #94a3b8;
    }
    .empty-state svg { width: 48px; height: 48px; margin: 0 auto 12px; color: #cbd5e1; }
    .empty-state p { font-size: 14px; }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .stat-row { flex-direction: column; }
        .section-header { flex-direction: column; gap: 12px; align-items: flex-start; }
        .section-actions { width: 100%; justify-content: space-between; }
        .mini-search input { width: 140px; }
        .mini-search input:focus { width: 180px; }
        .u-table th, .u-table td { padding: 10px 12px; font-size: 12px; }
        .hide-mobile { display: none; }
    }

    /* ===== DELETE MODAL ===== */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(15,23,42,0.5); z-index: 9999;
        display: flex; align-items: center; justify-content: center;
        opacity: 0; visibility: hidden; transition: all 0.25s ease;
        backdrop-filter: blur(4px);
    }
    .modal-overlay.show { opacity: 1; visibility: visible; }
    .modal-box {
        background: #fff; border-radius: 16px; padding: 32px; width: 420px; max-width: 92vw;
        text-align: center; transform: scale(0.9); transition: transform 0.25s ease;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .modal-overlay.show .modal-box { transform: scale(1); }
    .modal-icon {
        width: 56px; height: 56px; border-radius: 50%; background: #fef2f2;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px;
    }
    .modal-icon svg { width: 28px; height: 28px; color: #ef4444; }
    .modal-title { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
    .modal-desc { font-size: 14px; color: #64748b; margin-bottom: 24px; line-height: 1.5; }
    .modal-desc strong { color: #1e293b; }
    .modal-btns { display: flex; gap: 12px; justify-content: center; }
    .modal-btn {
        padding: 10px 24px; border-radius: 10px; font-size: 13px; font-weight: 600;
        border: none; cursor: pointer; transition: 0.2s; font-family: 'Inter', sans-serif;
    }
    .modal-btn.cancel { background: #f1f5f9; color: #475569; }
    .modal-btn.cancel:hover { background: #e2e8f0; }
    .modal-btn.danger { background: #ef4444; color: #fff; }
    .modal-btn.danger:hover { background: #dc2626; }
</style>
@endsection

@section('content')

{{-- Alerts --}}
@if(session('success'))
<div class="alert-bar success">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert-bar error">
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- Stats Row --}}
<div class="stat-row fade-up">
    <div class="stat-mini guru">
        <div class="stat-icon blue">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <div>
            <div class="stat-label">Total Guru</div>
            <div class="stat-value">{{ $totalGuru }}</div>
        </div>
    </div>
    <div class="stat-mini admin">
        <div class="stat-icon purple">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div>
            <div class="stat-label">Total Admin</div>
            <div class="stat-value">{{ $totalAdmin }}</div>
        </div>
    </div>
    <div class="stat-mini siswa">
        <div class="stat-icon green">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <div>
            <div class="stat-label">Total Siswa</div>
            <div class="stat-value">{{ $totalSiswa }}</div>
        </div>
    </div>
</div>

{{-- ======================== SECTION 1: GURU & ADMIN (Compact Card) ======================== --}}
<div class="section-card fade-up" style="margin-bottom: 24px;">
    <div class="section-header">
        <div class="section-title-wrap">
            <span class="section-dot blue"></span>
            <div>
                <div class="section-title">Guru & Administrator</div>
                <span class="section-subtitle">Kelola akun pengajar dan admin sistem</span>
            </div>
        </div>
        <div class="section-actions" style="flex-wrap: wrap; gap: 8px;">
            <form action="{{ route('admin.users.index') }}" method="GET" class="mini-search" style="margin-bottom: 0;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search_guru" value="{{ request('search_guru') }}" placeholder="Cari guru/admin...">
            </form>
            <a href="{{ route('admin.users.create') }}?role=guru" class="btn-add primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Tambah Guru
            </a>
        </div>
    </div>
    <table class="u-table" id="guruTable">
        <thead>
            <tr>
                <th style="width: 50px;">#</th>
                <th>Nama</th>
                <th class="hide-mobile">Email</th>
                <th>Role</th>
                <th style="width: 100px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($guruUsers as $index => $user)
            <tr data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}">
                <td>{{ $index + 1 }}</td>
                <td>
                    <div style="display: flex; align-items: center;">
                        <span class="u-avatar" style="background: {{ $user->role === 'admin' ? 'linear-gradient(135deg, #8b5cf6, #6d28d9)' : 'linear-gradient(135deg, #3b82f6, #1d4ed8)' }}">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        <div>
                            <div class="u-name">{{ $user->name }}</div>
                            <div class="u-email" style="display: none;">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="hide-mobile" style="font-family: 'JetBrains Mono', monospace; font-size: 12.5px; color: #94a3b8;">{{ $user->email }}</td>
                <td>
                    @if($user->role === 'admin')
                        <span class="role-badge role-admin">Admin</span>
                    @else
                        <span class="role-badge role-guru">Guru</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 4px;">
                        <a href="{{ route('admin.users.edit', $user) }}" class="act-btn edit" title="Edit">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        @if($user->id !== auth()->id())
                        <button class="act-btn del" title="Hapus" onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <p>Belum ada guru atau admin terdaftar</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($guruUsers->count() > 0)
    <div class="table-foot">
        <span>Menampilkan {{ $guruUsers->count() }} pengguna</span>
        <span>{{ $totalGuru }} guru · {{ $totalAdmin }} admin</span>
    </div>
    @endif
</div>

{{-- ======================== SECTION 2: DATA SISWA (Larger Card) ======================== --}}
<div class="section-card fade-up-delay">
    <div class="section-header" style="padding: 22px 24px;">
        <div class="section-title-wrap">
            <span class="section-dot green"></span>
            <div>
                <div class="section-title" style="font-size: 17px;">Data Siswa</div>
                <span class="section-subtitle">Kelola semua akun peserta didik</span>
            </div>
        </div>
        <div class="section-actions" style="flex-wrap: wrap; gap: 8px;">
            <form action="{{ route('admin.users.index') }}" method="GET" class="mini-search" style="margin-bottom: 0;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search_siswa" value="{{ request('search_siswa') }}" placeholder="Cari nama siswa...">
            </form>
            <div style="display: flex; gap: 8px; flex-wrap: nowrap;">
              <a href="{{ route('admin.users.import') }}" class="btn-add" style="background: transparent; color: #10b981; border: 1.5px solid #10b981; white-space: nowrap;">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                  Import
              </a>
              <a href="{{ route('admin.users.create') }}?role=murid" class="btn-add success" style="white-space: nowrap;">
                  <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                  Tambah
              </a>
            </div>
        </div>
    </div>
    <table class="u-table" id="siswaTable">
        <thead>
            <tr>
                <th style="width: 50px;">#</th>
                <th>Nama Siswa</th>
                <th class="hide-mobile">Email</th>
                <th>Kelas</th>
                <th class="hide-mobile">Status</th>
                <th style="width: 100px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $avatarColors = ['#3b82f6','#8b5cf6','#ec4899','#f97316','#14b8a6','#06b6d4','#84cc16','#f43f5e']; @endphp
            @forelse($siswaUsers as $index => $user)
            <tr data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}">
                <td style="color: #94a3b8; font-weight: 500;">{{ $siswaUsers->firstItem() + $index }}</td>
                <td>
                    <div style="display: flex; align-items: center;">
                        <span class="u-avatar" style="background: linear-gradient(135deg, {{ $avatarColors[$index % count($avatarColors)] }}, {{ $avatarColors[($index + 3) % count($avatarColors)] }})">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        <div>
                            <div class="u-name">{{ $user->name }}</div>
                            <div class="u-email">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="hide-mobile" style="font-family: 'JetBrains Mono', monospace; font-size: 12.5px; color: #94a3b8;">{{ $user->email }}</td>
                <td>
                    @if($user->kelas)
                        <span class="kelas-badge">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            {{ $user->kelas->nama }}
                        </span>
                    @else
                        <span style="color: #cbd5e1; font-size: 13px;">-</span>
                    @endif
                </td>
                <td class="hide-mobile">
                    <span class="role-badge role-murid">Aktif</span>
                </td>
                <td>
                    <div style="display: flex; gap: 4px;">
                        <a href="{{ route('admin.users.edit', $user) }}" class="act-btn edit" title="Edit">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <button class="act-btn del" title="Hapus" onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <p>Belum ada data siswa</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($siswaUsers->count() > 0)
    <div class="table-foot">
        <span>Menampilkan {{ $siswaUsers->firstItem() }}-{{ $siswaUsers->lastItem() }} dari {{ $siswaUsers->total() }} siswa</span>
        @if($siswaUsers->hasPages())
        <div>
            {{ $siswaUsers->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
    @endif
</div>

{{-- ======================== DELETE CONFIRMATION MODAL ======================== --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </div>
        <div class="modal-title">Hapus Pengguna?</div>
        <div class="modal-desc">
            Anda yakin ingin menghapus <strong id="deleteUserName"></strong>?<br>
            Tindakan ini tidak dapat dibatalkan.
        </div>
        <div class="modal-btns">
            <button class="modal-btn cancel" onclick="closeDeleteModal()">Batal</button>
            <form id="deleteForm" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="modal-btn danger">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('extra-js')
<script>
    // ===== CLIENT-SIDE SEARCH =====
    function setupSearch(inputId, tableId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        input.addEventListener('input', function() {
            const val = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#' + tableId + ' tbody tr[data-name]');
            rows.forEach(function(row) {
                const name = row.getAttribute('data-name') || '';
                const email = row.getAttribute('data-email') || '';
                if (name.includes(val) || email.includes(val)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    // setupSearch('searchGuru', 'guruTable');
    // setupSearch('searchSiswa', 'siswaTable');

    // ===== DELETE MODAL =====
    function confirmDelete(userId, userName) {
        document.getElementById('deleteUserName').textContent = userName;
        document.getElementById('deleteForm').action = '/admin/users/' + userId;
        document.getElementById('deleteModal').classList.add('show');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('show');
    }
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteModal();
    });

    // Auto-dismiss alerts
    document.querySelectorAll('.alert-bar').forEach(function(el) {
        setTimeout(function() {
            el.style.transition = 'opacity 0.5s, transform 0.5s';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-10px)';
            setTimeout(function() { el.remove(); }, 500);
        }, 5000);
    });
</script>
@endsection
