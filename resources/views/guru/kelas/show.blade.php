@extends('layouts.guru')

@section('header')
<div style="display:flex;align-items:center;gap:12px;">
    <a href="{{ route('guru.kelas.index') }}" style="color:#94a3b8;text-decoration:none;display:flex;align-items:center;">
        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <span>Daftar Murid: {{ $kelas->nama }}</span>
</div>
@endsection
@section('header-sub', 'Daftar Kelas / ' . $kelas->nama)

@section('content')
<div class="card overflow-hidden">
    <div style="padding: 18px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b;">Peserta Didik Terdaftar</h3>
            <p style="font-size: 12px; color: #94a3b8;">Total {{ $students->count() }} murid di kelas ini</p>
        </div>
    </div>

    <table class="tbl">
        <thead>
            <tr>
                <th style="width: 60px;">#</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $colors = ['#3b82f6','#8b5cf6','#ec4899','#f97316','#14b8a6','#06b6d4','#84cc16','#f43f5e']; @endphp
            @forelse($students as $index => $student)
            <tr>
                <td style="color: #94a3b8; font-weight: 500;">{{ $index + 1 }}</td>
                <td>
                    <div style="display: flex; align-items: center;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; color: #fff; flex-shrink: 0; margin-right: 12px; background: linear-gradient(135deg, {{ $colors[$index % count($colors)] }}, {{ $colors[($index + 2) % count($colors)] }})">
                            {{ strtoupper(substr($student->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #1e293b;">{{ $student->name }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-size: 12px; color: #94a3b8; font-family: 'JetBrains Mono', monospace;">{{ $student->email }}</td>
                <td>
                    <span class="badge badge-green">Aktif</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 60px 20px;">
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
