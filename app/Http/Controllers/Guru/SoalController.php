<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Soal;
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
        $query = Soal::where('guru_id', auth()->id())->latest();
        
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        
        $soals = $query->paginate(15);
        $totalSoal = Soal::where('guru_id', auth()->id())->count();
        
        return view('guru.soal.index', compact('soals', 'totalSoal'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Mendapatkan list file audio untuk dropdown (jika soal audio dipilih)
        $audioFiles = collect(Storage::disk('public')->files('audio'))->map(function($file) {
            return basename($file);
        });

        return view('guru.soal.create', compact('audioFiles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:pilihan_ganda,multiple_choice,essay,audio',
            'pertanyaan' => 'required|string',
            'poin' => 'required|integer|min:1',
            'audio_path' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $soal = Soal::create([
                'guru_id' => auth()->id(),
                'tipe' => $request->tipe,
                'pertanyaan' => $request->pertanyaan,
                'poin' => $request->poin,
                'audio_path' => $request->audio_path,
            ]);

            // Save Pilihan Jawaban for choices type
            if (in_array($request->tipe, ['pilihan_ganda', 'multiple_choice', 'audio'])) {
                if ($request->has('pilihan') && is_array($request->pilihan)) {
                    foreach ($request->pilihan as $index => $teks) {
                        if (!empty($teks)) {
                            // Cek apakah opsi ini ditandai sebagai jawaban benar
                            $isBenar = false;
                            
                            if ($request->tipe == 'pilihan_ganda' || $request->tipe == 'audio') {
                                // radio button returns single value
                                $isBenar = ($request->jawaban_benar == $index);
                            } else if ($request->tipe == 'multiple_choice') {
                                // checkbox returns array
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
            return redirect()->route('guru.soal.index')->with('success', 'Soal berhasil disimpan.');
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
            return redirect()->route('guru.soal.index')->with('success', 'Soal berhasil diperbarui.');
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
            abort(403);
        }
        
        // Hapus soal (pastikan migration punya cascade delete untuk pilihan jawaban)
        // Kalau belum, kita hapus manual pilihan jawabannya
        $soal->pilihanJawabans()->delete();
        $soal->delete();

        return back()->with('success', 'Soal berhasil dihapus.');
    }
}
