@extends('layouts.guru')

@section('header', 'Daftar Kelas')
@section('header-sub', 'Lihat daftar kelas dan jumlah murid terdaftar')

@section('content')
<div class="flex-between mb-6">
    <form action="{{ route('guru.kelas.index') }}" method="GET" class="search-box" style="width: 300px;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kelas..." onchange="this.form.submit()">
    </form>
</div>

<div class="card overflow-hidden">
    <table class="tbl">
        <thead>
            <tr>
                <th style="width: 60px;">#</th>
                <th>Nama Kelas</th>
                <th>Jumlah Murid</th>
                <th>Tanggal Dibuat</th>
                <th style="text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kelas as $index => $k)
            <tr>
                <td>{{ $kelas->firstItem() + $index }}</td>
                <td><strong>{{ $k->nama }}</strong></td>
                <td><span class="badge badge-blue">{{ $k->users_count }} Murid</span></td>
                <td style="color: #94a3b8;">{{ $k->created_at->format('d M Y') }}</td>
                <td>
                    <div style="display: flex; justify-content: center;">
                        <a href="{{ route('guru.kelas.show', $k) }}" class="btn btn-primary" style="padding: 6px 14px; font-size: 12px; border-radius: 8px;">
                            Lihat Murid
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 60px 0;">
                    <div style="color: #94a3b8; font-size: 14px;">Belum ada data kelas.</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($kelas->hasPages())
    <div style="padding: 16px; border-top: 1px solid #f1f5f9;">
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
