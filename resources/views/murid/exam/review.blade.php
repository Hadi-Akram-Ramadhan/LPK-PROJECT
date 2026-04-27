@extends('layouts.murid')

@section('header')
<div style="display: flex; align-items: center; color: #64748b;">
    <svg style="width: 24px; height: 24px; margin-right: 8px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <span style="font-weight: 600;">Review Jawaban — {{ $ujian->judul }}</span>
</div>
@endsection

@section('content')
<style>
    .review-wrap {
        max-width: 860px;
        margin: 0 auto;
        padding: 32px 16px 80px;
    }
    .review-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #2563eb;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        margin-bottom: 24px;
    }
    .review-back:hover { text-decoration: underline; }
    .review-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        overflow: hidden;
        box-shadow: 0 1px 6px rgba(0,0,0,0.06);
    }
    .review-card-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 18px 24px;
        border-bottom: 1px solid #f1f5f9;
    }
    .q-num {
        width: 38px; height: 38px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 15px;
        flex-shrink: 0;
    }
    .q-num.benar { background: #dcfce7; color: #16a34a; }
    .q-num.salah { background: #fee2e2; color: #dc2626; }
    .q-num.manual { background: #fef9c3; color: #92400e; }
    .q-num.kosong { background: #f1f5f9; color: #94a3b8; }
    .review-question-text {
        font-size: 15px;
        color: #1e293b;
        line-height: 1.6;
        flex: 1;
    }
    .review-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.5px;
        flex-shrink: 0;
    }
    .badge-benar { background: #dcfce7; color: #15803d; }
    .badge-salah { background: #fee2e2; color: #b91c1c; }
    .badge-manual { background: #fef9c3; color: #92400e; }
    .badge-kosong { background: #f1f5f9; color: #94a3b8; }
    .review-options {
        padding: 16px 24px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .review-option {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        font-size: 14px;
        color: #334155;
    }
    .review-option.dipilih-benar {
        background: #f0fdf4;
        border-color: #22c55e;
        color: #166534;
        font-weight: 700;
    }
    .review-option.dipilih-salah {
        background: #fff1f2;
        border-color: #f43f5e;
        color: #9f1239;
        font-weight: 700;
    }
    .review-option.jawaban-benar-saja {
        background: #f0fdf4;
        border-color: #86efac;
        color: #166534;
    }
    .opt-letter {
        width: 28px; height: 28px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 800;
        flex-shrink: 0;
        background: #f1f5f9;
        color: #475569;
    }
    .dipilih-benar .opt-letter { background: #22c55e; color: #fff; }
    .dipilih-salah .opt-letter { background: #f43f5e; color: #fff; }
    .jawaban-benar-saja .opt-letter { background: #86efac; color: #166534; }
    .review-essay-box {
        padding: 16px 24px;
    }
    .review-essay-answer {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 18px;
        font-size: 14px;
        color: #334155;
        line-height: 1.6;
    }
    .review-score-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #0f172a;
        color: #fff;
        padding: 20px 28px;
        border-radius: 14px;
        margin-bottom: 28px;
    }
    .review-score-bar h2 { font-size: 18px; font-weight: 800; margin: 0; }
    .review-score-bar p { font-size: 12px; color: #94a3b8; margin: 4px 0 0; }
    .review-score-val { font-size: 40px; font-weight: 900; }

    @media (max-width: 640px) {
        .review-wrap { padding: 16px 8px 60px; }
        .review-card-header { padding: 14px 16px; }
        .review-options { padding: 12px 16px; }
    }

    /* Audio di review */
    .review-audio-wrap {
        padding: 10px 24px 6px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
    }
    .review-audio-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        flex-shrink: 0;
    }
    .review-audio-wrap audio {
        height: 36px;
        max-width: 280px;
        border-radius: 20px;
        outline: none;
    }
    /* Gambar di review */
    .review-gambar-wrap {
        padding: 10px 24px 12px;
        border-bottom: 1px solid #f1f5f9;
        text-align: center;
    }
    .review-gambar-wrap img {
        max-width: 100%;
        max-height: 200px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 4px;
        object-fit: contain;
    }
</style>

<div class="review-wrap">
    <a href="{{ route('murid.exam.result', $ujian_peserta) }}" class="review-back">
        <svg style="width:16px;height:16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Hasil
    </a>

    {{-- Score Summary Bar --}}
    <div class="review-score-bar">
        <div>
            <h2>Review Jawaban Anda</h2>
            <p>{{ $ujian->judul }} &middot; {{ $soals->count() }} soal</p>
        </div>
        <div style="text-align: right;">
            <div class="review-score-val">{{ $ujian_peserta->skor }}</div>
            <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Skor Akhir</div>
        </div>
    </div>

    {{-- Soal List --}}
    @foreach($soals as $i => $soal)
        @php
            $jawaban  = $jawabanMurid[$soal->id] ?? null;
            $pilihans = $semuaPilihan[$soal->id] ?? collect();

            // Tentukan status soal
            if (in_array($soal->tipe, ['essay', 'matching'])) {
                $status = 'manual';
            } elseif (!$jawaban) {
                $status = 'kosong';
            } else {
                $poin = $jawaban->poin_didapat ?? 0;
                $maxPoin = $soal->poin;
                $status = ($poin > 0 && $poin >= $maxPoin) ? 'benar' : 'salah';
            }

            $statusLabel = [
                'benar'  => 'Benar ✓',
                'salah'  => 'Salah ✗',
                'manual' => 'Perlu Review Guru',
                'kosong' => 'Tidak Dijawab',
            ][$status];
        @endphp

        <div class="review-card">
            <div class="review-card-header">
                <div class="q-num {{ $status }}">{{ $i + 1 }}</div>
                <div class="review-question-text">
                    {!! \App\Helpers\HtmlSanitizer::clean($soal->pertanyaan) !!}
                </div>
                <span class="review-badge badge-{{ $status }}">{{ $statusLabel }}</span>
            </div>

            {{-- Audio Soal --}}
            @if($soal->audio_path)
            <div class="review-audio-wrap">
                <span class="review-audio-label">🔊 Audio Soal</span>
                <audio controls controlsList="nodownload" preload="none" class="review-audio-wrap audio">
                    <source src="{{ route('murid.exam.media.review', ['ujian_peserta' => $ujian_peserta, 'id' => $soal->id, 'type' => 'soal']) }}" type="audio/mpeg">
                    Browser tidak mendukung audio.
                </audio>
            </div>
            @endif

            {{-- Gambar Soal --}}
            @if($soal->gambar_path)
            <div class="review-gambar-wrap">
                <img src="{{ asset('storage/' . $soal->gambar_path) }}" alt="Gambar Soal" loading="lazy">
            </div>
            @endif

            {{-- Pilihan Ganda / Audio / Multiple Choice --}}
            @if(in_array($soal->tipe, ['pilihan_ganda', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar', 'multiple_choice']))
                @php
                    $jawabanBenar = $pilihans->where('is_benar', true)->pluck('id')->toArray();
                    $dipilihIds = [];
                    if ($jawaban) {
                        if ($jawaban->pilihan_jawaban_id) {
                            $dipilihIds = [$jawaban->pilihan_jawaban_id];
                        } elseif ($jawaban->jawaban_multiple) {
                            $dipilihIds = json_decode($jawaban->jawaban_multiple, true) ?? [];
                        }
                    }
                @endphp
                <div class="review-options">
                    @foreach($pilihans as $idx => $pilihan)
                        @php
                            $dipilih = in_array($pilihan->id, $dipilihIds);
                            $benar   = in_array($pilihan->id, $jawabanBenar);
                            $cls = '';
                            if ($dipilih && $benar)  $cls = 'dipilih-benar';
                            elseif ($dipilih && !$benar) $cls = 'dipilih-salah';
                            elseif (!$dipilih && $benar) $cls = 'jawaban-benar-saja';
                        @endphp
                        <div class="review-option {{ $cls }}">
                            <span class="opt-letter">{{ chr(65 + $idx) }}</span>
                            <div style="flex: 1; line-height: 1.5;">
                                @if($pilihan->teks)
                                    <span>{{ $pilihan->teks }}</span>
                                @endif
                                @if($pilihan->media_tipe === 'gambar' && $pilihan->media_path)
                                    <img src="{{ asset('storage/' . $pilihan->media_path) }}" style="max-height: 100px; border-radius: 8px; margin-top: 6px; display: block;">
                                @endif
                                @if($pilihan->media_tipe === 'audio' && $pilihan->media_path)
                                    <div style="margin-top: 6px;">
                                        <audio controls controlsList="nodownload" preload="none" style="height: 32px; max-width: 220px; outline:none; border-radius: 20px;">
                                            <source src="{{ route('murid.exam.media.review', ['ujian_peserta' => $ujian_peserta, 'id' => $pilihan->id, 'type' => 'pilihan']) }}" type="audio/mpeg">
                                        </audio>
                                    </div>
                                @endif
                                @if($dipilih && $benar)
                                    <span style="font-size: 11px; margin-top: 4px; display: block; opacity: 0.8;">✓ Pilihan Anda — Benar</span>
                                @elseif($dipilih && !$benar)
                                    <span style="font-size: 11px; margin-top: 4px; display: block; opacity: 0.8;">✗ Pilihan Anda — Salah</span>
                                @elseif(!$dipilih && $benar)
                                    <span style="font-size: 11px; margin-top: 4px; display: block; opacity: 0.8;">← Jawaban Benar</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            {{-- Short Answer --}}
            @elseif($soal->tipe === 'short_answer')
                <div class="review-essay-box">
                    <div style="font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;">Jawaban Anda</div>
                    <div class="review-essay-answer">{{ $jawaban?->jawaban_text ?? '(Tidak dijawab)' }}</div>
                    @if($soal->jawaban_kunci)
                    <div style="font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin: 12px 0 8px;">Kunci Jawaban</div>
                    <div class="review-essay-answer" style="background: #f0fdf4; border-color: #86efac; color: #166534;">{{ $soal->jawaban_kunci }}</div>
                    @endif
                </div>

            {{-- Essay & Matching --}}
            @elseif(in_array($soal->tipe, ['essay', 'matching']))
                <div class="review-essay-box">
                    @if($soal->tipe === 'essay')
                        <div style="font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 8px;">Jawaban Anda</div>
                        <div class="review-essay-answer">{{ $jawaban?->jawaban_text ?? '(Tidak dijawab)' }}</div>
                    @else
                        <div class="review-essay-answer">Soal tipe <b>Matching (Jodohkan)</b>. Jawaban akan ditinjau kembali oleh guru.</div>
                    @endif
                    <div style="margin-top: 12px; padding: 10px 14px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 10px; font-size: 12px; color: #92400e;">
                        <b>ℹ️ Catatan:</b> Jawaban untuk soal ini akan ditinjau kembali oleh pihak guru/admin.
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>
@endsection
