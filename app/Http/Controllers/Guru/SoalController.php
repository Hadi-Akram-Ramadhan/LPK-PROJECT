<?php

namespace App\Http\Controllers\Guru;

use App\Helpers\HtmlSanitizer;
use App\Http\Controllers\Controller;
use App\Models\Soal;
use App\Models\PaketSoal;
use App\Models\PilihanJawaban;
use App\Traits\ImageCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SoalController extends Controller
{
    use ImageCompressor;
    const TIPE_VALID = [
        'pilihan_ganda', 'multiple_choice', 'essay', 'audio',
        'pilihan_ganda_audio', 'pilihan_ganda_gambar', 'short_answer', 'matching'
    ];
    const TIPE_LISTENING = ['audio', 'pilihan_ganda_audio'];

    public function index(Request $request)
    {
        return redirect()->route('guru.paket-soal.index');
    }

    public function create(Request $request)
    {
        $paketSoal = null;
        if ($request->filled('paket')) {
            $paketSoal = PaketSoal::where('id', $request->paket)
                ->where('guru_id', auth()->id())
                ->firstOrFail();
        }
        $audioFiles = collect(Storage::disk('public')->files('audio'))->map(fn($f) => basename($f));
        $imageFiles = collect(Storage::disk('public')->files('gambar'))->map(fn($f) => basename($f));
        return view('guru.soal.create', compact('audioFiles', 'imageFiles', 'paketSoal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paket_soal_id'  => 'required|exists:paket_soals,id',
            'tipe'           => 'required|in:' . implode(',', self::TIPE_VALID),
            'pertanyaan'     => 'required|string|max:2000',
            'poin'           => 'required|integer|min:1|max:1000',
            'audio_path'     => 'nullable|string|max:255',
            'gambar_path'    => 'nullable|string|max:255',
            'audio_max_play' => 'nullable|integer|min:1|max:99',
            'jawaban_kunci'  => 'nullable|string|max:300',
            'pilihan.*'      => 'nullable|string|max:300',
            'pasang_kiri.*'  => 'nullable|string|max:200',
            'pasang_kanan.*' => 'nullable|string|max:200',
        ]);

        $paketSoal = PaketSoal::where('id', $request->paket_soal_id)
            ->where('guru_id', auth()->id())
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $soal = Soal::create([
                'guru_id'        => auth()->id(),
                'paket_soal_id'  => $request->paket_soal_id,
                'tipe'           => $request->tipe,
                'pertanyaan'     => HtmlSanitizer::clean($request->pertanyaan),
                'poin'           => $request->poin,
                'audio_path'     => $request->audio_path,
                'gambar_path'    => $request->gambar_path,
                'jawaban_kunci'  => $request->tipe === 'short_answer' ? $request->jawaban_kunci : null,
                'audio_max_play' => in_array($request->tipe, self::TIPE_LISTENING) ? $request->audio_max_play : null,
            ]);

            if ($request->tipe === 'matching') {
                $this->saveMatchingPairs($soal->id, $request);
            } elseif (in_array($request->tipe, ['pilihan_ganda', 'multiple_choice', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
                $this->savePilihanJawaban($soal, $request);
            }

            DB::commit();
            return redirect()->route('guru.paket-soal.show', $request->paket_soal_id)
                ->with('success', 'Soal berhasil disimpan ke paket.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Soal $soal)
    {
        if ($soal->guru_id !== auth()->id()) abort(404);
        $soal->load('pilihanJawabans');
        $audioFiles = collect(Storage::disk('public')->files('audio'))->map(fn($f) => basename($f));
        $imageFiles = collect(Storage::disk('public')->files('gambar'))->map(fn($f) => basename($f));
        return view('guru.soal.edit', compact('soal', 'audioFiles', 'imageFiles'));
    }

    public function update(Request $request, Soal $soal)
    {
        if ($soal->guru_id !== auth()->id()) abort(404);

        $request->validate([
            'pertanyaan'     => 'required|string|max:2000',
            'poin'           => 'required|integer|min:1|max:1000',
            'audio_path'     => 'nullable|string|max:255',
            'gambar_path'    => 'nullable|string|max:255',
            'audio_max_play' => 'nullable|integer|min:1|max:99',
            'jawaban_kunci'  => 'nullable|string|max:300',
            'pilihan.*'      => 'nullable|string|max:300',
            'pasang_kiri.*'  => 'nullable|string|max:200',
            'pasang_kanan.*' => 'nullable|string|max:200',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'pertanyaan'  => HtmlSanitizer::clean($request->pertanyaan),
                'poin'        => $request->poin,
                'audio_path'  => $request->audio_path,
                'gambar_path' => $request->gambar_path,
            ];
            if ($request->filled('tipe')) {
                $updateData['tipe'] = $request->tipe;
            }
            $effectiveTipe = $updateData['tipe'] ?? $soal->tipe;
            $updateData['jawaban_kunci']  = $effectiveTipe === 'short_answer' ? $request->jawaban_kunci : null;
            $updateData['audio_max_play'] = in_array($effectiveTipe, self::TIPE_LISTENING) ? $request->audio_max_play : null;

            $soal->update($updateData);

            if ($effectiveTipe === 'matching') {
                $soal->pilihanJawabans()->delete();
                $this->saveMatchingPairs($soal->id, $request);
            } elseif (in_array($effectiveTipe, ['pilihan_ganda', 'multiple_choice', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
                $soal->pilihanJawabans()->delete();
                $this->savePilihanJawaban($soal, $request);
            } else {
                $soal->pilihanJawabans()->delete();
            }

            DB::commit();
            return redirect()->route('guru.paket-soal.show', $soal->paket_soal_id)
                ->with('success', 'Soal berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Soal $soal)
    {
        if ($soal->guru_id !== auth()->id()) abort(404);
        $paketId = $soal->paket_soal_id;
        $soal->pilihanJawabans()->delete();
        $soal->delete();
        return redirect()->route('guru.paket-soal.show', $paketId)
            ->with('success', 'Soal berhasil dihapus.');
    }

    // ── Upload Media Langsung (AJAX) ─────────────────────────────
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file'  => 'required|file|max:51200',
            'jenis' => 'required|in:gambar,audio',
        ]);

        $file = $request->file('file');
        $jenis = $request->jenis;

        if ($jenis === 'gambar') {
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $allowedExt)) {
                return response()->json(['success' => false, 'message' => 'Format gambar tidak valid.'], 422);
            }
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $ext;
            $targetDir = storage_path('app/public/gambar');
            if (!file_exists($targetDir)) mkdir($targetDir, 0755, true);

            $targetPath = $targetDir . '/' . $filename;
            $this->compressAndSaveImage($file->getRealPath(), $targetPath);
            return response()->json(['success' => true, 'path' => 'gambar/' . $filename, 'filename' => $filename]);
        } else {
            $allowedExt = ['mp3', 'mpeg', 'mpga', 'wav', 'ogg'];
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $allowedExt)) {
                return response()->json(['success' => false, 'message' => 'Format audio tidak valid.'], 422);
            }
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $ext;
            $file->storeAs('audio', $filename, 'public');
            return response()->json(['success' => true, 'path' => 'audio/' . $filename, 'filename' => $filename]);
        }
    }

    // ── Helpers ──────────────────────────────────────────────────
    private function saveMatchingPairs(int $soalId, Request $request): void
    {
        $kiriList    = $request->input('pasang_kiri', []);
        $kananList   = $request->input('pasang_kanan', []);
        $kiriGambar  = $request->input('pasang_kiri_gambar', []);
        $kananGambar = $request->input('pasang_kanan_gambar', []);

        foreach ($kiriList as $idx => $kiriTeks) {
            $kananTeks     = $kananList[$idx] ?? '';
            $kiriVal       = !empty($kiriGambar[$idx]) ? $kiriGambar[$idx] : trim($kiriTeks);
            $kananVal      = !empty($kananGambar[$idx]) ? $kananGambar[$idx] : trim($kananTeks);
            $kiriIsGambar  = !empty($kiriGambar[$idx]);
            $kananIsGambar = !empty($kananGambar[$idx]);

            if (empty($kiriVal) && empty($kananVal)) continue;

            if ($kiriIsGambar && $kananIsGambar) {
                $mediaTipe = 'matching_gambar_keduanya';
            } elseif ($kiriIsGambar) {
                $mediaTipe = 'matching_gambar_kiri';
            } elseif ($kananIsGambar) {
                $mediaTipe = 'matching_gambar_kanan';
            } else {
                $mediaTipe = 'matching_teks';
            }

            PilihanJawaban::create([
                'soal_id'    => $soalId,
                'teks'       => $kiriVal,
                'media_path' => $kananVal,
                'media_tipe' => $mediaTipe,
                'is_benar'   => true,
            ]);
        }
    }

    private function savePilihanJawaban(Soal $soal, Request $request): void
    {
        if ($request->has('pilihan') && is_array($request->pilihan)) {
            foreach ($request->pilihan as $index => $teks) {
                $mediaPath    = $request->pilihan_media[$index] ?? null;
                $audioMaxPlay = null;

                if (!empty($teks) || !empty($mediaPath)) {
                    $isBenar = false;
                    if (in_array($soal->tipe, ['pilihan_ganda', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
                        $isBenar = ($request->jawaban_benar == $index);
                    } elseif ($soal->tipe == 'multiple_choice') {
                        $isBenar = (is_array($request->jawaban_benar) && in_array($index, $request->jawaban_benar));
                    }

                    $mediaTipe = null;
                    if (!empty($mediaPath)) {
                        $mediaTipe = ($soal->tipe === 'pilihan_ganda_audio') ? 'audio' : 'gambar';
                        if ($mediaTipe === 'audio') {
                            $audioMaxPlay = $request->input('pilihan_audio_max_play.' . $index) ?: null;
                        }
                    }

                    PilihanJawaban::create([
                        'soal_id'        => $soal->id,
                        'teks'           => $teks ?? '',
                        'media_path'     => $mediaPath,
                        'media_tipe'     => $mediaTipe,
                        'is_benar'       => $isBenar,
                        'audio_max_play' => $audioMaxPlay,
                    ]);
                }
            }
        }
    }
}
