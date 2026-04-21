<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UjianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ujians = Ujian::with(['guru'])
            ->withCount('soals')
            ->latest()
            ->paginate(10);
            
        return view('guru.ujian.index', compact('ujians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil Semua Paket Soal (Termasuk Milik Orang Lain/Admin)
        $paketSoals = \App\Models\PaketSoal::with(['guru', 'soals' => function($q) {
                $q->orderBy('id', 'asc');
            }])
            ->get();

        // Kelas untuk assign peserta
        $kelas = Kelas::all();
        
        return view('guru.ujian.create', compact('paketSoals', 'kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:20',
            'deskripsi' => 'nullable|string|max:50',
            'durasi' => 'required|integer|min:1',
            'mulai' => 'nullable|date',
            'selesai' => 'nullable|date|after_or_equal:mulai',
            'soal_id' => 'required|array|min:1',
            'soal_id.*' => 'exists:soals,id',
            'acak_soal' => 'nullable|boolean',
            'jenis_ujian' => 'nullable|in:reguler,tryout',
        ]);

        DB::beginTransaction();
        try {
            $ujian = Ujian::create([
                'guru_id' => auth()->id(),
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'durasi' => $request->durasi,
                'mulai' => $request->mulai,
                'selesai' => $request->selesai,
                'acak_soal' => $request->has('acak_soal'),
                'acak_jawaban' => $request->has('acak_jawaban'),
                'tes_buta_warna' => $request->has('tes_buta_warna'),
                'jenis_ujian' => $request->jenis_ujian ?? 'reguler',
            ]);

            // Sync Soal dengan Pivot
            if ($request->has('soal_id')) {
                $urutan = 1;
                $syncData = [];
                foreach ($request->soal_id as $s_id) {
                    $syncData[$s_id] = ['urutan' => $urutan];
                    $urutan++;
                }
                $ujian->soals()->sync($syncData);
            }

            // Jika ada assign ke kelas
            if ($request->has('kelas_id') && is_array($request->kelas_id)) {
                // Di sini kita assign murid dalam kelas tersebut ke ujian_peserta
                $murids = \App\Models\User::whereIn('kelas_id', $request->kelas_id)
                                        ->where('role', 'murid')
                                        ->get();
                
                $pesertaData = [];
                foreach ($murids as $murid) {
                    $pesertaData[] = [
                        'ujian_id' => $ujian->id,
                        'user_id' => $murid->id,
                        'status' => 'belum_mulai',
                        'skor' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                \App\Models\UjianPeserta::insert($pesertaData);
            }

            DB::commit();
            return redirect()->route('guru.ujian.index')->with('success', 'Ujian berhasil dibuat dan soal telah diassign.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ujian $ujian)
    {
        if ($ujian->guru_id !== auth()->id()) {
            abort(404);
        }

        $ujian->load('soals');
        $selectedSoal = $ujian->soals->pluck('id')->toArray();
        
        $paketSoals = \App\Models\PaketSoal::with(['guru', 'soals' => function($q) {
                $q->orderBy('id', 'asc');
            }])
            ->get();
        
        return view('guru.ujian.edit', compact('ujian', 'paketSoals', 'selectedSoal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ujian $ujian)
    {
        if ($ujian->guru_id !== auth()->id()) {
            abort(404);
        }

        $request->validate([
            'judul' => 'required|string|max:20',
            'deskripsi' => 'nullable|string|max:50',
            'durasi' => 'required|integer|min:1',
            'mulai' => 'nullable|date',
            'selesai' => 'nullable|date|after_or_equal:mulai',
            'soal_id' => 'required|array|min:1',
            'soal_id.*' => 'exists:soals,id',
            'acak_soal' => 'nullable|boolean',
            'jenis_ujian' => 'nullable|in:reguler,tryout',
        ]);

        DB::beginTransaction();
        try {
            $ujian->update([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'durasi' => $request->durasi,
                'mulai' => $request->mulai,
                'selesai' => $request->selesai,
                'acak_soal' => $request->has('acak_soal'),
                'acak_jawaban' => $request->has('acak_jawaban'),
                'tes_buta_warna' => $request->has('tes_buta_warna'),
                'jenis_ujian' => $request->jenis_ujian ?? 'reguler',
            ]);

            // Sync Soal dengan Pivot
            if ($request->has('soal_id')) {
                $urutan = 1;
                $syncData = [];
                foreach ($request->soal_id as $s_id) {
                    $syncData[$s_id] = ['urutan' => $urutan];
                    $urutan++;
                }
                $ujian->soals()->sync($syncData);
            }

            DB::commit();
            return redirect()->route('guru.ujian.index')->with('success', 'Detail ujian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ujian $ujian)
    {
        if ($ujian->guru_id !== auth()->id()) {
            abort(404);
        }

        $ujian->delete();
        return redirect()->route('guru.ujian.index')->with('success', 'Ujian berhasil dihapus.');
    }

    public function preview(Request $request, Ujian $ujian)
    {
        // Untuk guru, kita izinkan preview semua ujian sebagai referensi
        $ujian->load(['soals.pilihanJawabans']);
        
        // Kelompokkan soal: Reading vs Listening (Konsisten dengan SoalController::TIPE_LISTENING)
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

        $soals = $readingSoals->concat($listeningSoals)->values();

        $totalSoal = $soals->count();
        if ($totalSoal == 0) {
            return back()->with('error', 'Ujian ini tidak memiliki soal untuk dipreview.');
        }

        $page = (int) $request->query('page', 1);
        $currentSoal = $soals->get($page - 1);

        if (!$currentSoal) {
            return redirect()->route('guru.ujian.preview', ['ujian' => $ujian->id, 'page' => 1]);
        }

        // Generate Media Registry for Preloading (Preview Mode)
        $mediaRegistry = $soals->mapWithKeys(function($s, $idx) {
            $urls = [];
            if($s->audio_path) $urls[] = route('shared.media-preview', ['id' => $s->id, 'type' => 'soal']);
            if($s->gambar_path) $urls[] = asset('storage/' . $s->gambar_path);
            foreach($s->pilihanJawabans as $o) {
                if($o->media_tipe === 'audio' && $o->media_path) $urls[] = route('shared.media-preview', ['id' => $o->id, 'type' => 'pilihan']);
                if($o->media_path && in_array($o->media_tipe, ['gambar', 'matching_gambar_kanan', 'matching_gambar_keduanya'])) {
                    $urls[] = asset('storage/' . $o->media_path);
                }
                if($o->teks && in_array($o->media_tipe, ['matching_gambar_kiri', 'matching_gambar_keduanya'])) {
                    $urls[] = asset('storage/' . $o->teks);
                }
            }
            return [$idx + 1 => array_values(array_unique($urls))];
        });

        return view('shared.preview_ujian', compact('ujian', 'currentSoal', 'totalSoal', 'page', 'soals', 'readingSoals', 'listeningSoals', 'mediaRegistry'));
    }
}

