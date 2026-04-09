<?php

namespace App\Http\Controllers\Guru;

use App\Helpers\HtmlSanitizer;
use App\Http\Controllers\Controller;
use App\Models\Soal;
use App\Models\PaketSoal;
use App\Models\PilihanJawaban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SoalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return redirect()->route('guru.paket-soal.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $paketSoal = null;
        if ($request->filled('paket')) {
            $paketSoal = PaketSoal::where('id', $request->paket)
                ->where('guru_id', auth()->id())
                ->firstOrFail();
        }
        $audioFiles = collect(Storage::disk('public')->files('audio'))->map(function($file) {
            return basename($file);
        });
        $imageFiles = collect(Storage::disk('public')->files('gambar'))->map(function($file) {
            return basename($file);
        });
        return view('guru.soal.create', compact('audioFiles', 'imageFiles', 'paketSoal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'paket_soal_id' => 'required|exists:paket_soals,id',
            'tipe'          => 'required|in:pilihan_ganda,multiple_choice,essay,audio,pilihan_ganda_audio,pilihan_ganda_gambar,short_answer',
            'pertanyaan'    => 'required|string',
            'poin'          => 'required|integer|min:1',
            'audio_path'    => 'nullable|string',
            'gambar_path'   => 'nullable|string',
        ]);

        // Security check: Verify paket soal exists and belongs to the guru
        $paketSoal = PaketSoal::where('id', $request->paket_soal_id)
            ->where('guru_id', auth()->id())
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $soal = Soal::create([
                'guru_id'       => auth()->id(),
                'paket_soal_id' => $request->paket_soal_id,
                'tipe'          => $request->tipe,
                'pertanyaan'    => HtmlSanitizer::clean($request->pertanyaan),
                'poin'          => $request->poin,
                'audio_path'    => $request->audio_path,
                'gambar_path'   => $request->gambar_path,
                'jawaban_kunci' => $request->tipe === 'short_answer' ? $request->jawaban_kunci : null,
            ]);

            if (in_array($request->tipe, ['pilihan_ganda', 'multiple_choice', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
                if ($request->has('pilihan') && is_array($request->pilihan)) {
                    foreach ($request->pilihan as $index => $teks) {
                        // Check if a media path was provided for this option index
                        $mediaPath = $request->pilihan_media[$index] ?? null;
                        
                        if (!empty($teks) || !empty($mediaPath)) {
                            $isBenar = false;
                            if (in_array($request->tipe, ['pilihan_ganda', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
                                $isBenar = ($request->jawaban_benar == $index);
                            } else if ($request->tipe == 'multiple_choice') {
                                $isBenar = (is_array($request->jawaban_benar) && in_array($index, $request->jawaban_benar));
                            }
                            
                            $mediaTipe = null;
                            if (!empty($mediaPath)) {
                                $mediaTipe = ($request->tipe === 'pilihan_ganda_audio') ? 'audio' : 'gambar';
                            }
                            
                            PilihanJawaban::create([
                                'soal_id'    => $soal->id,
                                'teks'       => $teks ?? '',
                                'media_path' => $mediaPath,
                                'media_tipe' => $mediaTipe,
                                'is_benar'   => $isBenar,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('guru.paket-soal.show', $request->paket_soal_id)
                ->with('success', 'Soal berhasil disimpan ke paket.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan soal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Soal $soal)
    {
        if ($soal->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized action. Anda hanya dapat mengubah soal milik Anda sendiri.');
        }

        $soal->load('pilihanJawabans');
        $audioFiles = collect(Storage::disk('public')->files('audio'))->map(function($file) {
            return basename($file);
        });
        $imageFiles = collect(Storage::disk('public')->files('gambar'))->map(function($file) {
            return basename($file);
        });

        return view('guru.soal.edit', compact('soal', 'audioFiles', 'imageFiles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Soal $soal)
    {
        if ($soal->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized action. Anda hanya dapat mengubah soal milik Anda sendiri.');
        }

        $request->validate([
            'pertanyaan' => 'required|string',
            'poin' => 'required|integer|min:1',
            'audio_path' => 'nullable|string',
            'gambar_path' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Because 'tipe' is locked in after initial creation for most logic, we just carry it over, 
            // but let's assume 'tipe' might be slightly modified? Usually we don't allow changing 'tipe' on edit,
            // but the system doesn't restrict it heavily in the model update, though it's not in the validator.
            // Let's stick to updating non-structural fields unless tipe is provided.
            
            $updateData = [
                'pertanyaan' => HtmlSanitizer::clean($request->pertanyaan),
                'poin' => $request->poin,
                'audio_path' => $request->audio_path,
                'gambar_path' => $request->gambar_path,
            ];
            
            if ($request->filled('tipe')) {
                $updateData['tipe'] = $request->tipe;
            }

            // Update jawaban_kunci only for short_answer
            $effectiveTipe = $updateData['tipe'] ?? $soal->tipe;
            $updateData['jawaban_kunci'] = $effectiveTipe === 'short_answer' ? $request->jawaban_kunci : null;

            $soal->update($updateData);

            // Update Pilihan Jawaban
            if (in_array($soal->tipe, ['pilihan_ganda', 'multiple_choice', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
                // Hapus yang lama
                $soal->pilihanJawabans()->delete();

                // Buat baru
                if ($request->has('pilihan') && is_array($request->pilihan)) {
                    foreach ($request->pilihan as $index => $teks) {
                        $mediaPath = $request->pilihan_media[$index] ?? null;
                        
                        if (!empty($teks) || !empty($mediaPath)) {
                            $isBenar = false;
                            
                            if (in_array($soal->tipe, ['pilihan_ganda', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
                                $isBenar = ($request->jawaban_benar == $index);
                            } else if ($soal->tipe == 'multiple_choice') {
                                $isBenar = (is_array($request->jawaban_benar) && in_array($index, $request->jawaban_benar));
                            }
                            
                            $mediaTipe = null;
                            if (!empty($mediaPath)) {
                                $mediaTipe = ($soal->tipe === 'pilihan_ganda_audio') ? 'audio' : 'gambar';
                            }

                            PilihanJawaban::create([
                                'soal_id' => $soal->id,
                                'teks' => $teks ?? '',
                                'media_path' => $mediaPath,
                                'media_tipe' => $mediaTipe,
                                'is_benar' => $isBenar,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('guru.paket-soal.show', $soal->paket_soal_id)
                ->with('success', 'Soal berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat mengupdate soal: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Soal $soal)
    {
        if ($soal->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized action. Anda hanya dapat menghapus soal milik Anda sendiri.');
        }

        $paketId = $soal->paket_soal_id;
        $soal->pilihanJawabans()->delete();
        $soal->delete();
        return redirect()->route('guru.paket-soal.show', $paketId)
            ->with('success', 'Soal berhasil dihapus.');
    }
}
