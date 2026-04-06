@extends('layouts.admin')

@section('header')
<div style="display:flex;align-items:center;gap:12px;">
    <a href="{{ route('admin.kelas.index') }}" style="color:#94a3b8;text-decoration:none;display:flex;align-items:center;">
        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <span>Daftar Murid: {{ $kelas->nama }}</span>
</div>
@endsection
@section('header-sub', 'Manajemen Kelas / ' . $kelas->nama)

@section('extra-css')
<style>
    .student-card {
        background: #fff; border-radius: 16px; border: 1px solid #e2e8f0;
        overflow: hidden; transition: box-shadow 0.3s ease;
    }
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
    .u-avatar {
        width: 36px; height: 36px; border-radius: 10px; display: inline-flex;
        align-items: center; justify-content: center; font-weight: 700; font-size: 14px;
        color: #fff; flex-shrink: 0; margin-right: 12px;
    }
    .u-name { font-weight: 600; color: #1e293b; }
    .u-email { font-size: 12px; color: #94a3b8; font-family: 'JetBrains Mono', monospace; }
</style>
@endsection

@section('content')
<div class="student-card">
    <div style="padding: 18px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b;">Peserta Didik Terdaftar</h3>
            <p style="font-size: 12px; color: #94a3b8;">Total {{ $students->count() }} murid di kelas ini</p>
        </div>
        <a href="{{ route('admin.users.create') }}?kelas_id={{ $kelas->id }}&role=murid" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
            + Tambah Murid
        </a>
    </div>

    <table class="u-table">
        <thead>
            <tr>
                <th style="width: 60px;">#</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Status</th>
                <th style="width: 100px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $colors = ['#3b82f6','#8b5cf6','#ec4899','#f97316','#14b8a6','#06b6d4','#84cc16','#f43f5e']; @endphp
            @forelse($students as $index => $student)
            <tr>
                <td style="color: #94a3b8; font-weight: 500;">{{ $index + 1 }}</td>
                <td>
                    <div style="display: flex; align-items: center;">
                        <div class="u-avatar" style="background: linear-gradient(135deg, {{ $colors[$index % count($colors)] }}, {{ $colors[($index + 2) % count($colors)] }})">
                            {{ strtoupper(substr($student->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="u-name">{{ $student->name }}</div>
                        </div>
                    </div>
                </td>
                <td class="u-email">{{ $student->email }}</td>
                <td>
                    <span style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #d1fae5; color: #047857;">
                        Aktif
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.users.edit', $student) }}" style="color: #2563eb; text-decoration: none; font-size: 13px; font-weight: 600;">Edit Akun</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 60px 20px;">
                    <div style="color: #cbd5e1; margin-bottom: 12px;">
                        <svg style="width: 48px; height: 48px; margin: 0 auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p style="color: #94a3b8; font-size: 14px;">Belum ada murid yang terdaftar di kelas ini.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
