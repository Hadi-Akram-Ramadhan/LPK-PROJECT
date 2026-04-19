<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\PaketSoal;
use App\Models\Soal;
use Illuminate\Http\Request;

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
            'nama'      => 'required|string|max:20',
            'deskripsi' => 'nullable|string|max:50',
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

        $soals = $query->paginate(20);
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
            'nama'      => 'required|string|max:20',
            'deskripsi' => 'nullable|string|max:50',
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
        // Teachers can duplicate any packet they can see
        $newPaket = $paketSoal->replicate();
        $newPaket->nama = $paketSoal->nama . ' (Copy)';
        $newPaket->guru_id = auth()->id();
        $newPaket->save();

        foreach ($paketSoal->soals as $soal) {
            $newSoal = $soal->replicate();
            $newSoal->paket_soal_id = $newPaket->id;
            $newSoal->save();

            foreach ($soal->pilihanJawabans as $pilihan) {
                $newPilihan = $pilihan->replicate();
                $newPilihan->soal_id = $newSoal->id;
                $newPilihan->save();
            }
        }

        return redirect()->route('guru.paket-soal.index')->with('success', 'Paket Soal berhasil diduplikat.');
    }
}
