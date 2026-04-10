<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaketSoal;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaketSoalController extends Controller
{
    public function index(Request $request)
    {
        $query = PaketSoal::withCount('soals')->with('guru');
        
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $pakets = $query->latest()->paginate(15);
        return view('admin.paket_soal.index', compact('pakets'));
    }

    public function create()
    {
        $gurus = User::whereIn('role', ['admin', 'guru'])->get();
        return view('admin.paket_soal.create', compact('gurus'));
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

        return redirect()->route('admin.paket-soal.index')
            ->with('success', "Paket Soal \"{$request->nama}\" berhasil dibuat.");
    }

    public function show(Request $request, PaketSoal $paketSoal)
    {
        $query = $paketSoal->soals()->with('pilihanJawabans');
        
        if ($request->filled('search')) {
            $query->where('pertanyaan', 'like', '%' . $request->search . '%');
        }

        $soals = $query->latest()->paginate(20);
        return view('admin.paket_soal.show', compact('paketSoal', 'soals'));
    }

    public function edit(PaketSoal $paketSoal)
    {
        return view('admin.paket_soal.edit', compact('paketSoal'));
    }

    public function update(Request $request, PaketSoal $paketSoal)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $paketSoal->update([
            'nama'      => $request->nama,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.paket-soal.show', $paketSoal)
            ->with('success', 'Paket Soal berhasil diperbarui.');
    }

    public function destroy(PaketSoal $paketSoal)
    {
        // Hapus semua soal dalam paket beserta pilihan jawabannya
        foreach ($paketSoal->soals as $soal) {
            $soal->pilihanJawabans()->delete();
            $soal->delete();
        }
        $paketSoal->delete();

        return redirect()->route('admin.paket-soal.index')
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

        return redirect()->route('admin.paket-soal.index')
            ->with('success', "Paket Soal \"{$paketSoal->nama}\" berhasil diduplikasi.");
    }
}
