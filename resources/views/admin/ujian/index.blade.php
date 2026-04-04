@extends('layouts.admin')

@section('header', 'Manajemen Ujian')
@section('header-sub', 'Kelola paket ujian dan jadwal pelaksanaan')

@section('content')
<div class="flex-between mb-6">
    <div class="search-box" style="width: 300px;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" placeholder="Cari nama ujian...">
    </div>
    <a href="{{ route('admin.ujian.create') }}" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah Ujian Baru
    </a>
</div>

@if(session('success'))
<div class="badge badge-green mb-6" style="width: 100%; padding: 12px; border-radius: 10px;">
    {{ session('success') }}
</div>
@endif

<div class="card overflow-hidden">
    <table class="tbl">
        <thead>
            <tr>
                <th>Judul Ujian</th>
                <th>Jenis</th>
                <th>Kelas</th>
                <th>Durasi</th>
                <th>Jumlah Soal</th>
                <th>Waktu Mulai</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ujians as $u)
            <tr>
                <td>
                    <div style="font-weight: 600; color: #1e293b;">{{ $u->judul }}</div>
                    <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">{{ $u->kategori ?? 'Umum' }}</div>
                </td>
                <td>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border uppercase {{ $u->jenis_ujian === 'tryout' ? 'bg-blue-100 text-blue-800 border-blue-200' : 'bg-purple-100 text-purple-800 border-purple-200' }}">
                        {{ $u->jenis_ujian }}
                    </span>
                </td>
                <td><span class="badge badge-blue">Semua Kelas</span></td>
                <td><span class="badge badge-gray">{{ $u->durasi }} Menit</span></td>
                <td>{{ $u->soals_count ?? $u->soals->count() }}</td>
                <td>
                    @if($u->mulai)
                        {{ $u->mulai->format('d M Y, H:i') }}
                    @else
                        <span class="badge badge-gray">Tidak Diatur</span>
                    @endif
                </td>
                <td>
                    <div style="display: flex; gap: 8px; justify-content: center;">
                        <a href="{{ route('admin.ujian.edit', $u) }}" class="btn-outline" style="padding: 6px 10px; border-radius: 6px; font-size: 12px; text-decoration: none;">Edit</a>
                        <a href="{{ route('admin.ujian.soal', $u) }}" class="btn-primary" style="padding: 6px 10px; border-radius: 6px; font-size: 12px; text-decoration: none;">Soal</a>
                        <form action="{{ route('admin.ujian.destroy', $u) }}" method="POST" onsubmit="return confirm('Hapus ujian ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger" style="padding: 6px 10px; border-radius: 6px; font-size: 12px; background: transparent;">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 60px 0;">
                    <div style="color: #94a3b8; font-size: 14px;">Belum ada data ujian.</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
