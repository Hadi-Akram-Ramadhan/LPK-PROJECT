@extends('layouts.admin')

@section('header')
<div style="display:flex;align-items:center;gap:12px;">
    <a href="{{ route('admin.paket-soal.index') }}" style="color:#94a3b8;text-decoration:none;display:flex;align-items:center;">
        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <span>{{ $paketSoal->nama }}</span>
</div>
@endsection
@section('header-sub', 'Admin / Bank Soal / ' . $paketSoal->nama)

@section('content')

@if(session('success'))
<div style="background:#dcfce7;border:1px solid #86efac;color:#166534;padding:14px 20px;border-radius:12px;margin-bottom:20px;font-size:14px;display:flex;align-items:center;gap:10px;">
    <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:14px 20px;border-radius:12px;margin-bottom:20px;font-size:14px;">
    {{ session('error') }}
</div>
@endif

{{-- Info Header --}}
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px 24px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;">
    <div>
        <h2 style="font-size:18px;font-weight:700;color:#1e293b;margin:0 0 4px;">{{ $paketSoal->nama }}</h2>
        @if($paketSoal->deskripsi)
        <p style="font-size:13px;color:#94a3b8;margin:0;">{{ $paketSoal->deskripsi }}</p>
        @endif
        <div style="display:flex;gap:16px;margin-top:10px;">
            <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:4px;">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ $soals->count() }} Soal
            </span>
            <span style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:4px;">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                {{ $paketSoal->guru->name ?? 'Admin' }}
            </span>
        </div>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('admin.soal.create', ['paket' => $paketSoal->id]) }}" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;background:#2563eb;color:#fff;">
            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Soal
        </a>
        <a href="{{ route('admin.soal.import', ['paket' => $paketSoal->id]) }}" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;background:#fff;color:#2563eb;border:1.5px solid #2563eb;">
            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19h6m-3-3v3"/></svg>
            Import Excel
        </a>
    </div>
</div>

{{-- Soal List --}}
@if($soals->isEmpty())
<div style="background:#fff;border:1px dashed #e2e8f0;border-radius:14px;padding:48px;text-align:center;">
    <svg style="width:40px;height:40px;color:#cbd5e1;margin:0 auto 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
    <p style="color:#94a3b8;font-size:14px;margin:0 0 16px;">Belum ada soal dalam paket ini.</p>
    <a href="{{ route('admin.soal.create', ['paket' => $paketSoal->id]) }}" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;background:#2563eb;color:#fff;">
        + Tambah Soal Pertama
    </a>
</div>
@else
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:#f8fafc;">
                <th style="padding:14px 20px;text-align:left;font-size:11px;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:.5px;">#</th>
                <th style="padding:14px 20px;text-align:left;font-size:11px;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:.5px;">Pertanyaan</th>
                <th style="padding:14px 20px;text-align:left;font-size:11px;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:.5px;">Tipe</th>
                <th style="padding:14px 20px;text-align:left;font-size:11px;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:.5px;">Jawaban Benar</th>
                <th style="padding:14px 20px;text-align:left;font-size:11px;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:.5px;">Poin</th>
                <th style="padding:14px 20px;text-align:left;font-size:11px;font-weight:700;color:#2563eb;text-transform:uppercase;letter-spacing:.5px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($soals as $index => $soal)
            <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                <td style="padding:16px 20px;font-size:14px;color:#94a3b8;font-weight:600;">{{ $index + 1 }}</td>
                <td style="padding:16px 20px;max-width:380px;">
                    <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:14px;color:#334155;">
                        {!! strip_tags($soal->pertanyaan) !!}
                    </div>
                    <div style="display:flex;gap:8px;margin-top:6px;flex-wrap:wrap;">
                        @if($soal->audio_path)
                        <span style="font-size:11px;color:#2563eb;display:flex;align-items:center;gap:4px;background:#eff6ff;padding:2px 8px;border-radius:12px;border:1px solid #bfdbfe;">
                            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                            Audio Soal
                        </span>
                        @endif
                        @if($soal->gambar_path)
                        <span style="font-size:11px;color:#ea580c;display:flex;align-items:center;gap:4px;background:#fff7ed;padding:2px 8px;border-radius:12px;border:1px solid #fed7aa;">
                            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Gambar Soal
                        </span>
                        @endif
                        @php
                            $adaOpsiAudio = $soal->pilihanJawabans->where('media_tipe', 'audio')->isNotEmpty();
                            $adaOpsiGambar = $soal->pilihanJawabans->where('media_tipe', 'gambar')->isNotEmpty();
                        @endphp
                        @if($adaOpsiAudio)
                        <span style="font-size:11px;color:#7c3aed;display:flex;align-items:center;gap:4px;background:#f5f3ff;padding:2px 8px;border-radius:12px;border:1px solid #ddd6fe;">
                            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M8 12h.01M12 12h.01M16 12h.01M9 16H5a2 2 0 01-2-2V10a2 2 0 012-2h4l5-5v18l-5-5z"/></svg>
                            Opsi Audio
                        </span>
                        @endif
                        @if($adaOpsiGambar)
                        <span style="font-size:11px;color:#059669;display:flex;align-items:center;gap:4px;background:#ecfdf5;padding:2px 8px;border-radius:12px;border:1px solid #a7f3d0;">
                            <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Opsi Gambar
                        </span>
                        @endif
                    </div>
                </td>
                <td style="padding:16px 20px;">
                    @if($soal->tipe === 'pilihan_ganda')
                        <span style="background:#dbeafe;color:#2563eb;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;">PG (Teks)</span>
                    @elseif($soal->tipe === 'multiple_choice')
                        <span style="background:#ede9fe;color:#7c3aed;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;">MC</span>
                    @elseif($soal->tipe === 'audio')
                        <span style="background:#fef3c7;color:#d97706;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;">PG Audio</span>
                    @elseif($soal->tipe === 'pilihan_ganda_audio')
                        <span style="background:#ffedd5;color:#c2410c;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;">PG (Opsi Audio)</span>
                    @elseif($soal->tipe === 'pilihan_ganda_gambar')
                        <span style="background:#ccfbf1;color:#0f766e;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;">PG (Opsi Gambar)</span>
                    @elseif($soal->tipe === 'short_answer')
                        <span style="background:#e0e7ff;color:#4338ca;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;">Short Answer</span>
                    @else
                        <span style="background:#e2e8f0;color:#475569;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;">Essay</span>
                    @endif
                </td>
                <td style="padding:16px 20px;">
                    @php
                        $benar = $soal->pilihanJawabans->where('is_benar', true)->first();
                        $benarIdx = $soal->pilihanJawabans->where('is_benar', true)->keys()->first();
                        $letter = $benarIdx !== null ? chr(65 + $benarIdx) : '-';
                    @endphp
                    @if($soal->tipe === 'essay')
                        <span style="font-size:12px;color:#94a3b8;">Dinilai Manual</span>
                    @elseif($soal->tipe === 'short_answer')
                        @php $firstKey = explode('|', $soal->jawaban_kunci ?? '')[0] ?? '-'; @endphp
                        <span style="background:#e0e7ff;color:#4f46e5;font-size:12px;font-weight:700;padding:4px 10px;border-radius:6px;max-width:150px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $soal->jawaban_kunci }}">{{ $firstKey }}</span>
                    @elseif($soal->tipe === 'multiple_choice')
                        @php
                            $benarLetters = [];
                            foreach($soal->pilihanJawabans as $pIdx => $pJawab) {
                                if($pJawab->is_benar) $benarLetters[] = chr(65 + $pIdx);
                            }
                        @endphp
                        @if(count($benarLetters) > 0)
                            <span style="background:#ede9fe;color:#7c3aed;font-size:12px;font-weight:700;padding:4px 10px;border-radius:20px;">{{ implode(', ', $benarLetters) }}</span>
                        @else
                            <span style="color:#94a3b8;font-size:13px;">-</span>
                        @endif
                    @elseif($benar)
                        <span style="background:#dcfce7;color:#16a34a;font-size:13px;font-weight:700;padding:4px 12px;border-radius:20px;">{{ $letter }}</span>
                    @else
                        <span style="color:#94a3b8;font-size:13px;">-</span>
                    @endif
                </td>
                <td style="padding:16px 20px;font-size:14px;font-weight:600;color:#334155;">{{ $soal->poin }}</td>
                <td style="padding:16px 20px;">
                    <div style="display:flex;gap:6px;">
                        <a href="{{ route('admin.soal.edit', $soal) }}" style="padding:6px 12px;border-radius:8px;font-size:12px;font-weight:600;border:1px solid #e2e8f0;color:#2563eb;text-decoration:none;">Edit</a>
                        <form action="{{ route('admin.soal.destroy', $soal) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="padding:6px 12px;border-radius:8px;font-size:12px;font-weight:600;border:none;background:#fee2e2;color:#ef4444;cursor:pointer;">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding:14px 20px;font-size:13px;color:#94a3b8;border-top:1px solid #f1f5f9;">
        Total {{ $soals->count() }} soal · Total poin: {{ $soals->sum('poin') }}
    </div>
</div>
@endif

@endsection
