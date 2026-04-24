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

        $ujianPesertas = UjianPeserta::with(['ujian'])
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
        if ($ujian->selesai && $now->gt(Carbon::parse($ujian->selesai)) && in_array(strtolower($ujian_peserta->status), ['belum_mulai', 'belum'])) {
            return back()->with('error', 'Batas waktu masuk ujian telah berakhir.');
        }

        if (in_array(strtolower($ujian_peserta->status), ['belum_mulai', 'belum'])) {
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

        // Sistem Cerdas Tes Buta Warna kini dipindah SETELAH ujian selesai.
        $ujian = $ujian_peserta->ujian;
        // Kelompokkan soal: Reading vs Listening (Konsisten dengan SoalController::TIPE_LISTENING)
        $listeningTypes = ['audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'];
        
        // Cari ID paket yang paling mungkin merupakan paket Reading
        $allSoals = $ujian->soals;
        // Identifikasi semua paket yang merupakan paket Reading (Mayoritas soal non-audio)
        $packetStats = $allSoals->groupBy('paket_soal_id')->map(function($pSoals) use ($listeningTypes) {
            $readingCount = $pSoals->filter(fn($s) => !in_array($s->tipe, $listeningTypes))->count();
            $listeningCount = $pSoals->count() - $readingCount;
            return $readingCount >= $listeningCount ? 'reading' : 'listening';
        });

        $readingPacketIds = $packetStats->filter(fn($type) => $type === 'reading')->keys()->toArray();

        // Pisahkan soal berdasarkan klasifikasi paketnya
        $readingSoals  = $allSoals->filter(function($s) use ($readingPacketIds) {
            return in_array($s->paket_soal_id, $readingPacketIds);
        })->sortBy('id');

        $listeningSoals = $allSoals->reject(function($s) use ($readingPacketIds) {
            return in_array($s->paket_soal_id, $readingPacketIds);
        })->sortBy('id');

        if ($ujian->acak_soal) {
            $baseSeed = $ujian_peserta->id;
            
            // Shuffle Reading
            mt_srand($baseSeed + 100);
            $readingItems = $readingSoals->all();
            for ($i = count($readingItems) - 1; $i > 0; $i--) {
                $j = mt_rand(0, $i);
                $tmp = $readingItems[$i]; $readingItems[$i] = $readingItems[$j]; $readingItems[$j] = $tmp;
            }
            $readingSoals = collect($readingItems);

            // Shuffle Listening
            mt_srand($baseSeed + 200);
            $listeningItems = $listeningSoals->all();
            for ($i = count($listeningItems) - 1; $i > 0; $i--) {
                $j = mt_rand(0, $i);
                $tmp = $listeningItems[$i]; $listeningItems[$i] = $listeningItems[$j]; $listeningItems[$j] = $tmp;
            }
            $listeningSoals = collect($listeningItems);
            
            mt_srand();
        }

        // Gabungkan untuk urutan halaman: Reading selalu duluan (Page 1..N), Listening menyusul (Page N+1..M)
        $soals = $readingSoals->concat($listeningSoals)->values();

        $totalSoal = $soals->count();

        if ($totalSoal == 0) {
            return redirect()->route('murid.dashboard')->with('error', 'Ujian ini belum memiliki soal. Silakan hubungi admin.');
        }

        $page = (int) $request->input('page', 1);
        $soals = $soals->values(); // Penting: Reset index agar berurutan dari 0
        
        if ($page < 1) $page = 1;
        if ($page > $totalSoal) $page = $totalSoal;

        $currentSoal = $soals->get($page - 1);

        if (!$currentSoal) {
             return redirect()->route('murid.dashboard')->with('error', 'Soal tidak dapat ditemukan. Silakan hubungi admin.');
        }

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

        // Ambil logs playback audio untuk soal ini
        $audioLogs = \App\Models\AudioPlaybackLog::where('ujian_peserta_id', $ujian_peserta->id)
            ->where('soal_id', $currentSoal->id)
            ->get()
            ->keyBy(function($item) {
                return $item->pilihan_jawaban_id ? 'opsi_'.$item->pilihan_jawaban_id : 'soal';
            });

        $acakJawaban = (bool) ($ujian->acak_jawaban ?? false);

        // Generate Media Registry for Preloading
        $mediaRegistry = $soals->mapWithKeys(function($s, $idx) use ($ujian_peserta) {
            $urls = [];
            if($s->audio_path) $urls[] = route('murid.exam.media', ['ujian_peserta' => $ujian_peserta, 'id' => $s->id, 'type' => 'soal']) . '?v=' . $s->id;
            if($s->gambar_path) $urls[] = asset('storage/' . $s->gambar_path);
            foreach($s->pilihanJawabans as $o) {
                if($o->media_tipe === 'audio' && $o->media_path) $urls[] = route('murid.exam.media', ['ujian_peserta' => $ujian_peserta, 'id' => $o->id, 'type' => 'pilihan']) . '?v=' . $o->id;
                if($o->media_path && in_array($o->media_tipe, ['gambar', 'matching_gambar_kanan', 'matching_gambar_keduanya'])) {
                    $urls[] = asset('storage/' . $o->media_path);
                }
                if($o->teks && in_array($o->media_tipe, ['matching_gambar_kiri', 'matching_gambar_keduanya'])) {
                    $urls[] = asset('storage/' . $o->teks);
                }
            }
            return [$idx + 1 => array_values(array_unique($urls))];
        });

        return view('murid.exam.show', compact(
            'ujian_peserta', 'ujian', 'soals', 'currentSoal', 
            'page', 'totalSoal', 'jawabanSaatIni', 'answeredSoalIds', 'sisaDetik', 'deadline',
            'audioLogs', 'acakJawaban', 'readingSoals', 'listeningSoals', 'mediaRegistry'
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

        if (in_array($soal->tipe, ['pilihan_ganda', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
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

        } else if ($soal->tipe === 'short_answer') {
            $jawabanMurid = trim(strtolower((string) $request->jawaban));
            $dataUpdate['jawaban_text'] = $request->jawaban;
            
            // Multiple accepted keys separated by '|'
            $kunciRaw  = $soal->jawaban_kunci ?? '';
            $kunciList = array_filter(array_map('trim', explode('|', $kunciRaw)));

            foreach ($kunciList as $kunci) {
                $kunciNorm = strtolower($kunci);
                // Exact match (case-insensitive)
                if ($jawabanMurid === $kunciNorm) {
                    $poinDidapat = $soal->poin;
                    break;
                }
                // Fuzzy match — threshold 85%
                similar_text($jawabanMurid, $kunciNorm, $percent);
                if ($percent >= 85.0) {
                    $poinDidapat = $soal->poin;
                    break;
                }
            }
        } else if ($soal->tipe === 'matching') {
            // Jawaban: JSON string {"0": 2, "1": 0, "2": 1} 
            // key = index of left prompt, value = index of right option (setelah diacak di UI)
            $dataUpdate['jawaban_text'] = $request->jawaban;
            $jawabanArr = json_decode($request->jawaban, true) ?? [];

            $pairs = $soal->pilihanJawabans()->get();
            $totalPairs = $pairs->count();

            if ($totalPairs > 0) {
                $benar = 0;
                foreach ($jawabanArr as $promptIdx => $rightOriginalIdx) {
                    // Pasangan benar: promptIdx harus sama dengan rightOriginalId (pieceId yang dikirim frontend)
                    if ((int)$promptIdx === (int)$rightOriginalIdx) {
                        $benar++;
                    }
                }
                // Partial scoring: proporsional
                $poinDidapat = round($soal->poin * ($benar / $totalPairs), 2);
            }
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

        if ($ujian_peserta->ujian->tes_buta_warna && is_null($ujian_peserta->hasil_buta_warna)) {
            return redirect()->route('murid.exam.buta_warna.show', $ujian_peserta);
        }

        return redirect()->route('murid.exam.result', $ujian_peserta)->with('success', 'Ujian telah selesai.');
    }

    /**
     * Laporan Tab Switch (Cheat) Via AJAX Action
     */
    public function reportCheat(UjianPeserta $ujian_peserta)
    {
        try {
            if (!auth()->check()) {
                \Illuminate\Support\Facades\Log::error("Anti-Cheat 403: User not authenticated.");
                return response()->json(['success' => false, 'message' => 'Sesi login tidak terdeteksi.'], 403);
            }
            if ($ujian_peserta->user_id !== auth()->id()) {
                \Illuminate\Support\Facades\Log::error("Anti-Cheat 403: User ID mismatch. Ujian user: {$ujian_peserta->user_id}, Auth ID: " . auth()->id());
                return response()->json(['success' => false, 'message' => 'Ujian ini bukan milik Anda.'], 403);
            }
            $status = strtolower($ujian_peserta->status);
            if ($status !== 'mengerjakan' && $status !== 'diblokir') {
                \Illuminate\Support\Facades\Log::error("Anti-Cheat 403: Status mismatch. Status: {$ujian_peserta->status}");
                return response()->json(['success' => false, 'message' => "Status ujian tidak valid: {$ujian_peserta->status}"], 403);
            }

            // FITUR TRY-OUT: Jangan blokir jika jenis ujian adalah tryout
            if ($ujian_peserta->ujian->jenis_ujian === 'tryout') {
                return response()->json(['success' => true, 'message' => 'Try-out mode: tab switch allowed.']);
            }

            // Blokir ujian (Hanya untuk reguler)
            $ujian_peserta->update(['status' => 'diblokir']);

            // Cek apakah log sudah ada agar tidak tumpang tindih berulang kali
            $existingLog = CheatLog::where('ujian_peserta_id', $ujian_peserta->id)
                                ->where('status', 'pending')
                                ->first();

            if (!$existingLog) {
                // Catat ke log
                $log = CheatLog::create([
                    'ujian_peserta_id' => $ujian_peserta->id,
                    'keterangan' => 'Terdeteksi memindahkan/menyembunyikan tab ujian.',
                    'status' => 'pending',
                    'timestamp' => Carbon::now(),
                ]);

                // Broadcast ke Guru/Admin. Dibungkus try-catch agar ujian tetap diblokir meski websocket mati.
                try {
                    broadcast(new \App\Events\CheatLogReported($log));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Websocket failed: " . $e->getMessage());
                }
            }

            return response()->json(['success' => true, 'redirect' => route('murid.exam.blocked', $ujian_peserta)]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Cheat Log Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
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

        if ($ujian_peserta->ujian->tes_buta_warna && is_null($ujian_peserta->hasil_buta_warna)) {
            return redirect()->route('murid.exam.buta_warna.show', $ujian_peserta);
        }

        $ujian = $ujian_peserta->ujian;
        $adaEssay = $ujian->soals()->where('tipe', 'essay')->exists();

        return view('murid.exam.result', compact('ujian_peserta', 'ujian', 'adaEssay'));
    }

    /**
     * Halaman Review Jawaban Murid
     */
    public function review(UjianPeserta $ujian_peserta)
    {
        if ($ujian_peserta->user_id !== auth()->id()) abort(403);
        if ($ujian_peserta->status !== 'selesai') {
            return redirect()->route('murid.dashboard');
        }

        $ujian = $ujian_peserta->ujian;

        // Load soal dalam urutan yang sama seperti saat ujian (Reading then Listening)
        $listeningTypes = ['audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'];
        
        $allSoals = $ujian->soals;
        $packetStats = $allSoals->groupBy('paket_soal_id')->map(function($pSoals) use ($listeningTypes) {
            $readingCount = $pSoals->filter(fn($s) => !in_array($s->tipe, $listeningTypes))->count();
            $listeningCount = $pSoals->count() - $readingCount;
            return $readingCount >= $listeningCount ? 'reading' : 'listening';
        });
        $readingPacketIds = $packetStats->filter(fn($type) => $type === 'reading')->keys()->toArray();
        
        $readingSoals  = $allSoals->filter(function($s) use ($readingPacketIds) {
            return in_array($s->paket_soal_id, $readingPacketIds);
        })->sortBy('id');

        $listeningSoals = $allSoals->reject(function($s) use ($readingPacketIds) {
            return in_array($s->paket_soal_id, $readingPacketIds);
        })->sortBy('id');

        if ($ujian->acak_soal) {
            $baseSeed = $ujian_peserta->id;
            
            // Shuffle Reading
            mt_srand($baseSeed + 100);
            $readingItems = $readingSoals->all();
            for ($i = count($readingItems) - 1; $i > 0; $i--) {
                $j = mt_rand(0, $i);
                $tmp = $readingItems[$i]; $readingItems[$i] = $readingItems[$j]; $readingItems[$j] = $tmp;
            }
            $readingSoals = collect($readingItems);

            // Shuffle Listening
            mt_srand($baseSeed + 200);
            $listeningItems = $listeningSoals->all();
            for ($i = count($listeningItems) - 1; $i > 0; $i--) {
                $j = mt_rand(0, $i);
                $tmp = $listeningItems[$i]; $listeningItems[$i] = $listeningItems[$j]; $listeningItems[$j] = $tmp;
            }
            $listeningSoals = collect($listeningItems);
            
            mt_srand();
        }

        $soals = $readingSoals->concat($listeningSoals)->values();

        // Load semua jawaban murid untuk ujian ini, di-key by soal_id
        $jawabanMurid = JawabanMurid::where('ujian_peserta_id', $ujian_peserta->id)
            ->get()->keyBy('soal_id');

        // Load semua pilihan jawaban beserta is_benar
        $soalIds = $soals->pluck('id');
        $semuaPilihan = \App\Models\PilihanJawaban::whereIn('soal_id', $soalIds)->get()->groupBy('soal_id');

        // Generate Media Registry for Preloading (Review Mode)
        $mediaRegistry = $soals->mapWithKeys(function($s, $idx) use ($ujian_peserta) {
            $urls = [];
            if($s->audio_path) $urls[] = route('murid.exam.media', ['ujian_peserta' => $ujian_peserta, 'id' => $s->id, 'type' => 'soal']) . '?v=' . $s->id;
            if($s->gambar_path) $urls[] = asset('storage/' . $s->gambar_path);
            foreach($s->pilihanJawabans as $o) {
                if($o->media_tipe === 'audio' && $o->media_path) $urls[] = route('murid.exam.media', ['ujian_peserta' => $ujian_peserta, 'id' => $o->id, 'type' => 'pilihan']) . '?v=' . $o->id;
                if($o->media_path && in_array($o->media_tipe, ['gambar', 'matching_gambar_kanan', 'matching_gambar_keduanya'])) {
                    $urls[] = asset('storage/' . $o->media_path);
                }
                if($o->teks && in_array($o->media_tipe, ['matching_gambar_kiri', 'matching_gambar_keduanya'])) {
                    $urls[] = asset('storage/' . $o->teks);
                }
            }
            return [$idx + 1 => array_values(array_unique($urls))];
        });

        return view('murid.exam.review', compact(
            'ujian_peserta', 'ujian', 'soals', 'jawabanMurid', 'semuaPilihan', 'readingSoals', 'listeningSoals', 'mediaRegistry'
        ));
    }

    /**
     * Reset Try-Out: Hapus data jawaban lalu redirect ke dashboard.
     * Dipanggil saat murid klik "Kembali ke Dashboard" dari halaman result tryout.
     */
    public function resetTryout(UjianPeserta $ujian_peserta)
    {
        if ($ujian_peserta->user_id !== auth()->id()) abort(403);

        $ujian = $ujian_peserta->ujian;

        // Hanya proses jika memang tryout dan sudah selesai
        if ($ujian->jenis_ujian === 'tryout' && $ujian_peserta->status === 'selesai') {
            JawabanMurid::where('ujian_peserta_id', $ujian_peserta->id)->delete();
            \App\Models\AudioPlaybackLog::where('ujian_peserta_id', $ujian_peserta->id)->delete();
            $ujian_peserta->update([
                'status'     => 'belum',
                'mulai_at'   => null,
                'selesai_at' => null,
                'skor'       => 0,
            ]);
        }

        return redirect()->route('murid.dashboard');
    }
}
