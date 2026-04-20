<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\SoalButaWarna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SoalButaWarnaController extends Controller
{
    public function index()
    {
        // Menampilkan seluruh soal buta warna (shared antar admin & guru)
        $soals = SoalButaWarna::latest()->get();
        return view('guru.soal_buta_warna.index', compact('soals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'jawaban_kunci' => 'required|string|max:50',
        ]);

        $file = $request->file('gambar');
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        
        // Simpan ke storage local/public
        $file->storeAs('buta_warna', $filename, 'public');

        SoalButaWarna::create([
            'gambar_path' => 'buta_warna/' . $filename,
            'jawaban_kunci' => strtoupper($request->jawaban_kunci),
        ]);

        return redirect()->back()->with('success', 'Soal Buta Warna berhasil ditambahkan.');
    }

    public function destroy(SoalButaWarna $soal_buta_warna)
    {
        if (Storage::disk('public')->exists($soal_buta_warna->gambar_path)) {
            Storage::disk('public')->delete($soal_buta_warna->gambar_path);
        }
        $soal_buta_warna->delete();

        return redirect()->back()->with('success', 'Soal Buta Warna berhasil dihapus.');
    }
}
