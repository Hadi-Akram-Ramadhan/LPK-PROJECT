<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\SoalButaWarna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\ImageCompressor;

class SoalButaWarnaController extends Controller
{
    use ImageCompressor;
    public function index()
    {
        // Menampilkan seluruh soal buta warna (shared antar admin & guru)
        $soals = SoalButaWarna::latest()->get();
        return view('guru.soal_buta_warna.index', compact('soals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'jawaban_kunci' => 'required|string|max:50',
        ]);

        $file = $request->file('gambar');
        $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        
        // Compress and save directly
        $targetDir = storage_path('app/public/buta_warna');
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        $finalPath = $targetDir . '/' . $filename;
        $this->compressAndSaveImage($file->getRealPath(), $finalPath);

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
