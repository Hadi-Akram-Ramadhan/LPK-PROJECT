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
        $query = PaketSoal::withCount('soals')->with('guru');
        
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
        $query = $paketSoal->soals()->with('pilihanJawabans');
        
        if ($request->filled('search')) {
            $query->where('pertanyaan', 'like', '%' . $request->search . '%');
        }

        $soals = $query->latest()->paginate(20);
        return view('guru.paket_soal.show', compact('paketSoal', 'soals'));
    }

    public function edit(PaketSoal $paketSoal)
    {
        return view('guru.paket_soal.edit', compact('paketSoal'));
    }

    public function update(Request $request, PaketSoal $paketSoal)
    {
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
        foreach ($paketSoal->soals as $soal) {
            $soal->pilihanJawabans()->delete();
            $soal->delete();
        }
        $paketSoal->delete();
        return redirect()->route('guru.paket-soal.index')
            ->with('success', 'Paket Soal beserta semua soalnya berhasil dihapus.');
    }
}
