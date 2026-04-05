<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\PaketSoal;
use App\Models\Soal;
use Illuminate\Http\Request;

class PaketSoalController extends Controller
{
    public function index()
    {
        $pakets = PaketSoal::withCount('soals')
            ->with('guru') // Load guru for 'Oleh' display
            ->latest()
            ->get();

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

    public function show(PaketSoal $paketSoal)
    {
        $soals = $paketSoal->soals()->with('pilihanJawabans')->latest()->get();
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
