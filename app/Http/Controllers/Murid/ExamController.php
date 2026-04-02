<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\UjianPeserta;
use App\Models\JawabanMurid;
use App\Models\CheatLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    /**
     * Dashboard Murid - Daftar Ujian
     */
    public function index()
    {
        $userId = auth()->id();
        $ujianPesertas = UjianPeserta::with(['ujian' => function ($query) {
                // Jangan load semua relasi ujian di awal, cuma modelnya saja
            }])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return view('murid.dashboard', compact('ujianPesertas'));
    }

    /**
     * Memulai Ujian
     */
    public function start(UjianPeserta $ujian_peserta)
    {
        if ($ujian_peserta->user_id !== auth()->id()) abort(403);
        
        $ujian = $ujian_peserta->ujian;

        // Validasi waktu
        $now = Carbon::now();
        if ($ujian->mulai && $now->lt(Carbon::parse($ujian->mulai))) {
            return back()->with('error', 'Ujian belum dimulai.');
        }
        if ($ujian->selesai && $now->gt(Carbon::parse($ujian->selesai)) && $ujian_peserta->status == 'belum_mulai') {
            return back()->with('error', 'Batas waktu masuk ujian telah berakhir.');
        }

        if ($ujian_peserta->status == 'belum_mulai') {
            $ujian_peserta->update([
                'status' => 'mengerjakan',
                'mulai_at' => $now,
            ]);
        }

        return redirect()->route('murid.exam.show', $ujian_peserta);
    }

    /**
     * Layar Utama Ujian (Mode Fokus)
     */
    public function show(UjianPeserta $ujian_peserta, Request $request)
    {
        if ($ujian_peserta->user_id !== auth()->id()) abort(403);

        // Jika diblokir
        if ($ujian_peserta->status === 'diblokir') {
            return redirect()->route('murid.exam.blocked', $ujian_peserta);
        }

        // Jika selesai
        if ($ujian_peserta->status === 'selesai') {
            return redirect()->route('murid.exam.result', $ujian_peserta);
        }

        $ujian = $ujian_peserta->ujian;
        $soals = $ujian->soals;

        if ($ujian->acak_soal) {
            // Pengacakan yang konsisten dengan seed user_id + ujian_id
            $seed = $ujian_peserta->user_id . $ujian->id;
            mt_srand($seed);
            $items = $soals->all();
            for ($i = count($items) - 1; $i > 0; $i--) {
                $j = mt_rand(0, $i);
                $tmp = $items[$i];
                $items[$i] = $items[$j];
                $items[$j] = $tmp;
            }
            $soals = collect($items);
            mt_srand();
        }

        $totalSoal = $soals->count();
        $page = (int) $request->get('page', 1);
        if ($page < 1) $page = 1;
        if ($page > $totalSoal) $page = $totalSoal;

        $currentSoal = $soals[$page - 1];

        // Ambil jawaban saat ini
        $jawabanSaatIni = JawabanMurid::where('ujian_peserta_id', $ujian_peserta->id)
            ->where('soal_id', $currentSoal->id)
            ->first();

        // Data semua jawaban untuk navigasi (indikator hijau/merah)
        $semuaJawaban = JawabanMurid::where('ujian_peserta_id', $ujian_peserta->id)->get()->keyBy('soal_id');
        $answeredSoalIds = [];
        foreach ($semuaJawaban as $j) {
            if ($j->pilihan_jawaban_id || $j->jawaban_text || $j->jawaban_multiple) {
                $answeredSoalIds[] = $j->soal_id;
            }
        }

        // Durasi & Deadline
        $mulaiAt = Carbon::parse($ujian_peserta->mulai_at);
        $deadline = $mulaiAt->copy()->addMinutes($ujian->durasi);
        $sisaDetik = $deadline->diffInSeconds(now(), false) * -1; // bernilai positif jika belum lewat
        
        if ($sisaDetik <= 0) {
            // Waktu habis, otomatis finish
            return $this->forceFinish($ujian_peserta);
        }

        return view('murid.exam.show', compact(
            'ujian_peserta', 'ujian', 'soals', 'currentSoal', 
            'page', 'totalSoal', 'jawabanSaatIni', 'answeredSoalIds', 'sisaDetik', 'deadline'
        ));
    }

    /**
     * AJAX Endpoint: Simpan Jawaban
     */
    public function storeAnswer(Request $request, UjianPeserta $ujian_peserta)
    {
        if ($ujian_peserta->user_id !== auth()->id() || $ujian_peserta->status !== 'mengerjakan') {
            return response()->json(['success' => false, 'message' => 'Not authorized or exam finished/blocked'], 403);
        }

        $soalId = $request->soal_id;
        $soal = $ujian_peserta->ujian->soals()->find($soalId);
        if (!$soal) {
            return response()->json(['success' => false], 404);
        }

        $dataUpdate = [];
        $poinDidapat = 0;

        if ($soal->tipe === 'pilihan_ganda' || $soal->tipe === 'audio') {
            $dataUpdate['pilihan_jawaban_id'] = $request->jawaban;
            
            // Auto scoring PG:
            $pilihan = \App\Models\PilihanJawaban::find($request->jawaban);
            if ($pilihan && $pilihan->is_benar) {
                $poinDidapat = $soal->poin;
            }

        } else if ($soal->tipe === 'multiple_choice') {
            $jawabanArray = json_decode($request->jawaban, true) ?? [];
            sort($jawabanArray);
            $dataUpdate['jawaban_multiple'] = json_encode($jawabanArray);
            
            // Auto scoring Multiple Choice (Harus benar semua baru dapat poin)
            $correctOptions = \App\Models\PilihanJawaban::where('soal_id', $soalId)->where('is_benar', true)->pluck('id')->toArray();
            sort($correctOptions);
            
            if ($jawabanArray === $correctOptions) {
                $poinDidapat = $soal->poin;
            }

        } else if ($soal->tipe === 'essay') {
            $dataUpdate['jawaban_text'] = $request->jawaban;
            // Essay dinilai manual oleh guru nanti.
        }

        $dataUpdate['poin_didapat'] = $poinDidapat;

        JawabanMurid::updateOrCreate(
            [
                'ujian_peserta_id' => $ujian_peserta->id,
                'soal_id' => $soalId
            ],
            $dataUpdate
        );

        return response()->json(['success' => true]);
    }

    /**
     * Aksi Selesai Ujian (Manual dari Murid)
     */
    public function finish(Request $request, UjianPeserta $ujian_peserta)
    {
        // Jika request via GET (refresh halaman), arahkan secara cerdas
        if ($request->isMethod('get')) {
            if ($ujian_peserta->status === 'selesai') {
                return redirect()->route('murid.exam.result', $ujian_peserta);
            }
            if ($ujian_peserta->status === 'diblokir') {
                return redirect()->route('murid.exam.blocked', $ujian_peserta);
            }
            return redirect()->route('murid.dashboard');
        }

        return $this->forceFinish($ujian_peserta);
    }

    private function forceFinish(UjianPeserta $ujian_peserta)
    {
        if ($ujian_peserta->user_id !== auth()->id() && !auth()->user()->isAdmin() && !auth()->user()->isGuru()) abort(403);
        if ($ujian_peserta->status === 'selesai') {
            return redirect()->route('murid.exam.result', $ujian_peserta);
        }

        // Kalkulasi skor akhir (hanya PG dan Multiple Choice yg sdh ternilai)
        $totalSkor = JawabanMurid::where('ujian_peserta_id', $ujian_peserta->id)->sum('poin_didapat');

        $ujian_peserta->update([
            'status' => 'selesai',
            'selesai_at' => Carbon::now(),
            'skor' => $totalSkor
        ]);

        return redirect()->route('murid.exam.result', $ujian_peserta)->with('success', 'Ujian telah selesai.');
    }

    /**
     * Laporan Tab Switch (Cheat) Via AJAX Action
     */
    public function reportCheat(UjianPeserta $ujian_peserta)
    {
        if ($ujian_peserta->user_id !== auth()->id() || $ujian_peserta->status !== 'mengerjakan') {
            return response()->json(['success' => false], 403);
        }

        // Blokir ujian
        $ujian_peserta->update(['status' => 'diblokir']);

        // Catat ke log
        $log = CheatLog::create([
            'ujian_peserta_id' => $ujian_peserta->id,
            'keterangan' => 'Terdeteksi memindahkan/menyembunyikan tab ujian.',
            'status' => 'pending',
            'timestamp' => Carbon::now(),
        ]);

        // Broadcast ke Guru/Admin
        broadcast(new \App\Events\CheatLogReported($log));

        return response()->json(['success' => true, 'redirect' => route('murid.exam.blocked', $ujian_peserta)]);
    }

    /**
     * Halaman Ujian Terblokir
     */
    public function blocked(UjianPeserta $ujian_peserta)
    {
        if ($ujian_peserta->user_id !== auth()->id()) abort(403);

        if ($ujian_peserta->status !== 'diblokir') {
            return redirect()->route('murid.exam.show', $ujian_peserta);
        }

        $cheatLog = CheatLog::where('ujian_peserta_id', $ujian_peserta->id)->latest()->first();

        // Jika sudah di-approve oleh admin
        if ($cheatLog && $cheatLog->status === 'approved') {
            // Auto resume
            $ujian_peserta->update(['status' => 'mengerjakan']);
            return redirect()->route('murid.exam.show', $ujian_peserta)->with('success', 'Akses ujian Anda telah dipulihkan oleh penjaga/admin.');
        }

        return view('murid.exam.blocked', compact('ujian_peserta'));
    }

    /**
     * Halaman Hasil
     */
    public function result(UjianPeserta $ujian_peserta)
    {
        if ($ujian_peserta->user_id !== auth()->id() && !auth()->user()->isAdmin() && !auth()->user()->isGuru()) abort(403);
        if ($ujian_peserta->status !== 'selesai') {
            return redirect()->route('murid.dashboard');
        }

        $ujian = $ujian_peserta->ujian;
        $adaEssay = $ujian->soals()->where('tipe', 'essay')->exists();

        return view('murid.exam.result', compact('ujian_peserta', 'ujian', 'adaEssay'));
    }
}
