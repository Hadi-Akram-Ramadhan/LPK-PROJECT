<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\UjianPeserta;
use App\Models\SoalButaWarna;
use App\Models\Setting;
use Illuminate\Http\Request;

class ColorBlindTestController extends Controller
{
    public function show(UjianPeserta $ujian_peserta)
    {
        // Pastikan hanya pemilik yang bisa akses
        if ($ujian_peserta->user_id !== auth()->id()) {
            abort(403);
        }

        // Kalau ujian tidak meminta buta warna atau sudah ada hasilnya
        if (!$ujian_peserta->ujian->tes_buta_warna || !is_null($ujian_peserta->hasil_buta_warna)) {
            if ($ujian_peserta->status === 'selesai') {
                return redirect()->route('murid.exam.result', $ujian_peserta);
            }
            return redirect()->route('murid.exam.show', $ujian_peserta);
        }

        // Ambil jumlah soal dari pengaturan (default 5 jika belum diatur)
        $maxSoal = (int) Setting::get('max_soal_buta_warna', 5);
        $soals = SoalButaWarna::inRandomOrder()->limit($maxSoal)->get();

        if ($soals->isEmpty()) {
            $ujian_peserta->update(['hasil_buta_warna' => 'Dilewati (Tidak ada bank soal)']);
            if ($ujian_peserta->status === 'selesai') {
                return redirect()->route('murid.exam.result', $ujian_peserta);
            }
            return redirect()->route('murid.exam.show', $ujian_peserta);
        }

        return view('murid.exam.buta-warna', compact('ujian_peserta', 'soals'));
    }

    public function submit(Request $request, UjianPeserta $ujian_peserta)
    {
        if ($ujian_peserta->user_id !== auth()->id()) {
            abort(403);
        }

        // Pastikan belum pernah disubmit
        if (!is_null($ujian_peserta->hasil_buta_warna)) {
            if ($ujian_peserta->status === 'selesai') {
                return redirect()->route('murid.exam.result', $ujian_peserta);
            }
            return redirect()->route('murid.exam.show', $ujian_peserta);
        }

        // Proses pengecekan
        $answers = $request->input('answers', []);
        
        $correctCount = 0;
        $totalQuestions = count($answers);

        // Answers is array of soal_id => text
        foreach ($answers as $soalId => $answer) {
            $soal = SoalButaWarna::find($soalId);
            if ($soal) {
                // Hapus spasi dan jadikan lowercase untuk toleransi
                $userAns = strtolower(trim($answer));
                $keyAns = strtolower(trim($soal->jawaban_kunci));

                if ($userAns === $keyAns) {
                    $correctCount++;
                }
            }
        }

        // Hitung persentase
        $percentage = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;
        
        $kategori = '';
        if ($percentage == 100) {
            $kategori = 'Normal';
        } elseif ($percentage >= 50) {
            $kategori = 'Parsial';
        } else {
            $kategori = 'Indikasi Buta Warna';
        }

        $hasilText = "Skor: {$correctCount}/{$totalQuestions} ($kategori)";

        $ujian_peserta->update([
            'hasil_buta_warna' => $hasilText
        ]);

        if ($ujian_peserta->status === 'selesai') {
            return redirect()->route('murid.exam.result', $ujian_peserta)->with('success', 'Tes buta warna berhasil. Ujian telah selesai sepenuhnya.');
        }

        return redirect()->route('murid.exam.show', $ujian_peserta)->with('success', 'Tes buta warna berhasil diselesaikan.');
    }
}
