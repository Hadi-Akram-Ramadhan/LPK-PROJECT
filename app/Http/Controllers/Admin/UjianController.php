<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;

class UjianController extends Controller
{
    public function index()
    {
        $ujians = Ujian::with(['guru'])->latest()->get();
        return view('admin.ujian.index', compact('ujians'));
    }

    public function create()
    {
        $kelases = Kelas::all();
        $paketSoals = \App\Models\PaketSoal::with(['soals' => function($q) {
            $q->orderBy('id', 'asc');
        }])->get();
        return view('admin.ujian.create', compact('kelases', 'paketSoals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:20',
            'jenis_ujian' => 'nullable|in:reguler,tryout',
            'deskripsi' => 'nullable|string|max:50',
            'durasi' => 'required|numeric|min:1',
            'kelas_id' => 'required|array',
            'acak_soal' => 'nullable|boolean',
        ]);

        $ujian = Ujian::create([
            'judul' => $request->judul,
            'jenis_ujian' => $request->jenis_ujian ?? 'reguler',
            'deskripsi' => $request->deskripsi,
            'durasi' => $request->durasi,
            'mulai' => $request->mulai,
            'selesai' => $request->selesai,
            'acak_soal' => $request->has('acak_soal'),
            'acak_jawaban' => $request->has('acak_jawaban'),
            'tes_buta_warna' => $request->has('tes_buta_warna'),
            'guru_id' => auth()->id(),
        ]);

        // Otomatis daftarkan semua siswa di kelas ini ke ujian
        $siswas = \App\Models\User::where('role', 'murid')
            ->whereIn('kelas_id', $request->kelas_id)
            ->get();

        foreach($siswas as $s) {
            \App\Models\UjianPeserta::create([
                'ujian_id' => $ujian->id,
                'user_id' => $s->id,
                'status' => 'belum',
            ]);
        }

        // Simpan relasi soal ke dalam ujian
        $soalIds = $request->input('soal_ids', []);
        $syncData = [];
        foreach($soalIds as $index => $id) {
            $syncData[$id] = ['urutan' => $index + 1];
        }
        $ujian->soals()->sync($syncData);

        return redirect()->route('admin.ujian.index')->with('success', 'Ujian berhasil dibuat, didaftarkan ke ' . $siswas->count() . ' siswa, dan ' . count($soalIds) . ' soal ditambahkan.');
    }

    public function edit(Ujian $ujian)
    {
        $kelases = Kelas::all();
        $paketSoals = \App\Models\PaketSoal::with(['soals' => function($q) {
            $q->orderBy('id', 'asc');
        }])->get();
        $selectedSoal = $ujian->soals->pluck('id')->toArray();
        return view('admin.ujian.edit', compact('ujian', 'kelases', 'paketSoals', 'selectedSoal'));
    }

    public function update(Request $request, Ujian $ujian)
    {
        $request->validate([
            'judul' => 'required|string|max:20',
            'jenis_ujian' => 'nullable|in:reguler,tryout',
            'deskripsi' => 'nullable|string|max:50',
            'durasi' => 'required|numeric|min:1',
        ]);

        $ujian->update([
            'judul' => $request->judul,
            'jenis_ujian' => $request->jenis_ujian ?? 'reguler',
            'deskripsi' => $request->deskripsi,
            'durasi' => $request->durasi,
            'mulai' => $request->mulai,
            'selesai' => $request->selesai,
            'acak_soal' => $request->has('acak_soal'),
            'acak_jawaban' => $request->has('acak_jawaban'),
            'tes_buta_warna' => $request->has('tes_buta_warna'),
        ]);
        
        // Sync Soal
        $soalIds = $request->input('soal_ids', []);
        $syncData = [];
        foreach($soalIds as $index => $id) {
            $syncData[$id] = ['urutan' => $index + 1];
        }
        $ujian->soals()->sync($syncData);

        return redirect()->route('admin.ujian.index')->with('success', 'Ujian berhasil diperbarui.');
    }

    public function destroy(Ujian $ujian)
    {
        $ujian->delete();
        return redirect()->route('admin.ujian.index')->with('success', 'Ujian berhasil dihapus.');
    }

    public function manajemenSoal(Ujian $ujian)
    {
        $paketSoals = \App\Models\PaketSoal::with(['soals' => function($q) {
            $q->orderBy('id', 'asc');
        }])->get();
        $ujianSoalIds = $ujian->soals()->pluck('soals.id')->toArray();
        return view('admin.ujian.soal', compact('ujian', 'paketSoals', 'ujianSoalIds'));
    }

    public function updateSoal(Request $request, Ujian $ujian)
    {
        $soalIds = $request->input('soal_ids', []);
        
        $syncData = [];
        foreach($soalIds as $index => $id) {
            $syncData[$id] = ['urutan' => $index + 1];
        }

        $ujian->soals()->sync($syncData);

        return redirect()->route('admin.ujian.index')->with('success', 'Soal berhasil diperbarui untuk ujian ini.');
    }

    public function preview(Request $request, Ujian $ujian)
    {
        $ujian->load(['soals.pilihanJawabans']);
        
        // Kelompokkan soal: Reading vs Listening (Konsisten dengan SoalController::TIPE_LISTENING)
        $listeningTypes = ['audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'];
        
        $allSoals = $ujian->soals;
        $readingPacketId = null;
        $packetIds = $allSoals->pluck('paket_soal_id')->unique();
        foreach($packetIds as $pid) {
            $packetSoals = $allSoals->where('paket_soal_id', $pid);
            if (!$packetSoals->contains(fn($s) => in_array($s->tipe, $listeningTypes))) {
                $readingPacketId = $pid;
                break;
            }
        }
        if (!$readingPacketId) $readingPacketId = $allSoals->first()?->paket_soal_id;

        $readingSoals  = $allSoals->filter(function($s) use ($listeningTypes, $readingPacketId) {
            return !in_array($s->tipe, $listeningTypes) && ($s->paket_soal_id == $readingPacketId);
        })->sortBy('id');

        $listeningSoals = $allSoals->filter(function($s) use ($listeningTypes, $readingPacketId) {
            return in_array($s->tipe, $listeningTypes) || ($s->paket_soal_id != $readingPacketId);
        })->sortBy('id');

        $soals = $readingSoals->concat($listeningSoals)->values();

        $totalSoal = $soals->count();
        if ($totalSoal == 0) {
            return back()->with('error', 'Ujian ini tidak memiliki soal untuk dipreview.');
        }

        $page = (int) $request->query('page', 1);
        $currentSoal = $soals->get($page - 1);

        if (!$currentSoal) {
            return redirect()->route('admin.ujian.preview', ['ujian' => $ujian->id, 'page' => 1]);
        }

        return view('shared.preview_ujian', compact('ujian', 'currentSoal', 'totalSoal', 'page', 'soals', 'readingSoals', 'listeningSoals'));
    }
}

