<?php

namespace App\Http\Controllers\Guru;

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
        return view('guru.soal.create', compact('audioFiles', 'paketSoal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'paket_soal_id' => 'required|exists:paket_soals,id',
            'tipe'          => 'required|in:pilihan_ganda,multiple_choice,essay,audio',
            'pertanyaan'    => 'required|string',
            'poin'          => 'required|integer|min:1',
            'audio_path'    => 'nullable|string',
        ]);

        // Security check: Ensure the guru owns the destination package
        $paketSoal = PaketSoal::findOrFail($request->paket_soal_id);
        if ($paketSoal->guru_id !== auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki akses ke paket soal ini.')->withInput();
        }

        DB::beginTransaction();
        try {
            $soal = Soal::create([
                'guru_id'       => auth()->id(),
                'paket_soal_id' => $request->paket_soal_id,
                'tipe'          => $request->tipe,
                'pertanyaan'    => $request->pertanyaan,
                'poin'          => $request->poin,
                'audio_path'    => $request->audio_path,
            ]);

            if (in_array($request->tipe, ['pilihan_ganda', 'multiple_choice', 'audio'])) {
                if ($request->has('pilihan') && is_array($request->pilihan)) {
                    foreach ($request->pilihan as $index => $teks) {
                        if (!empty($teks)) {
                            $isBenar = false;
                            if ($request->tipe == 'pilihan_ganda' || $request->tipe == 'audio') {
                                $isBenar = ($request->jawaban_benar == $index);
                            } else if ($request->tipe == 'multiple_choice') {
                                $isBenar = (is_array($request->jawaban_benar) && in_array($index, $request->jawaban_benar));
                            }
                            PilihanJawaban::create([
                                'soal_id'  => $soal->id,
                                'teks'     => $teks,
                                'is_benar' => $isBenar,
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
            abort(403);
        }

        $soal->load('pilihanJawabans');
        $audioFiles = collect(Storage::disk('public')->files('audio'))->map(function($file) {
            return basename($file);
        });

        return view('guru.soal.edit', compact('soal', 'audioFiles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Soal $soal)
    {
        if ($soal->guru_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'pertanyaan' => 'required|string',
            'poin' => 'required|integer|min:1',
            'audio_path' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $soal->update([
                'pertanyaan' => $request->pertanyaan,
                'poin' => $request->poin,
                'audio_path' => $request->audio_path,
            ]);

            // Update Pilihan Jawaban
            if (in_array($soal->tipe, ['pilihan_ganda', 'multiple_choice', 'audio'])) {
                // Hapus yang lama
                $soal->pilihanJawabans()->delete();

                // Buat baru
                if ($request->has('pilihan') && is_array($request->pilihan)) {
                    foreach ($request->pilihan as $index => $teks) {
                        if (!empty($teks)) {
                            $isBenar = false;
                            
                            if ($soal->tipe == 'pilihan_ganda' || $soal->tipe == 'audio') {
                                $isBenar = ($request->jawaban_benar == $index);
                            } else if ($soal->tipe == 'multiple_choice') {
                                $isBenar = (is_array($request->jawaban_benar) && in_array($index, $request->jawaban_benar));
                            }

                            PilihanJawaban::create([
                                'soal_id' => $soal->id,
                                'teks' => $teks,
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
        if ($soal->guru_id !== auth()->id()) abort(403);
        $paketId = $soal->paket_soal_id;
        $soal->pilihanJawabans()->delete();
        $soal->delete();
        return redirect()->route('guru.paket-soal.show', $paketId)
            ->with('success', 'Soal berhasil dihapus.');
    }
}
