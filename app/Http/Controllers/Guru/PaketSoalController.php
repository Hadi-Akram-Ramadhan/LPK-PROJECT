<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\PaketSoal;
use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaketSoalController extends Controller
{
    public function index(Request $request)
    {
        $query = PaketSoal::query()->withCount('soals')->with('guru');
        
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $pakets = $query->latest()->paginate(15);
        return view('guru.paket_soal.index', compact('pakets'));
    }

    public function create()
    {
        return view('guru.paket_soal.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        PaketSoal::create([
            'guru_id'   => auth()->id(),
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('guru.paket-soal.index')
            ->with('success', "Paket Soal \"{$request->nama}\" berhasil dibuat.");
    }

    public function show(Request $request, PaketSoal $paketSoal)
    {
        $paketSoal->load('guru');
        $query = $paketSoal->soals()->with('pilihanJawabans');
        
        if ($request->filled('search')) {
            $query->where('pertanyaan', 'like', '%' . $request->search . '%');
        }

        $soals = $query->latest()->paginate(20);
        return view('guru.paket_soal.show', compact('paketSoal', 'soals'));
    }

    public function edit(PaketSoal $paketSoal)
    {
        if ($paketSoal->guru_id !== auth()->id()) {
            abort(404);
        }

        return view('guru.paket_soal.edit', compact('paketSoal'));
    }

    public function update(Request $request, PaketSoal $paketSoal)
    {
        if ($paketSoal->guru_id !== auth()->id()) {
            abort(404);
        }

        $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);
        $paketSoal->update($request->only('nama', 'deskripsi'));
        return redirect()->route('guru.paket-soal.show', $paketSoal)
            ->with('success', 'Paket Soal berhasil diperbarui.');
    }

    public function destroy(PaketSoal $paketSoal)
    {
        if ($paketSoal->guru_id !== auth()->id()) {
            abort(404);
        }

        foreach ($paketSoal->soals as $soal) {
            $soal->pilihanJawabans()->delete();
            $soal->delete();
        }
        $paketSoal->delete();
        return redirect()->route('guru.paket-soal.index')
            ->with('success', 'Paket Soal beserta semua soalnya berhasil dihapus.');
    }

    public function duplicate(PaketSoal $paketSoal)
    {
        DB::transaction(function () use ($paketSoal) {
            // 1. Duplikasi paket soal
            $newPaket = PaketSoal::create([
                'guru_id'   => auth()->id(),
                'nama'      => $paketSoal->nama . ' (Salinan)',
                'deskripsi' => $paketSoal->deskripsi,
            ]);

            // 2. Duplikasi semua soal beserta pilihan jawabannya
            foreach ($paketSoal->soals()->with('pilihanJawabans')->get() as $soal) {
                $newSoal = $newPaket->soals()->create([
                    'guru_id'       => auth()->id(),
                    'tipe'          => $soal->tipe,
                    'pertanyaan'    => $soal->pertanyaan,
                    'poin'          => $soal->poin,
                    'audio_path'    => $soal->audio_path,
                    'gambar_path'   => $soal->gambar_path,
                    'jawaban_kunci' => $soal->jawaban_kunci,
                ]);

                // 3. Duplikasi pilihan jawaban untuk setiap soal
                foreach ($soal->pilihanJawabans as $pilihan) {
                    $newSoal->pilihanJawabans()->create([
                        'teks'       => $pilihan->teks,
                        'is_benar'   => $pilihan->is_benar,
                        'media_path' => $pilihan->media_path ?? null,
                        'media_tipe' => $pilihan->media_tipe ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('guru.paket-soal.index')
            ->with('success', "Paket Soal \"{$paketSoal->nama}\" berhasil diduplikasi.");
    }
}
