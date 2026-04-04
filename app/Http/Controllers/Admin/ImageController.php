<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\ImageCompressor;

class ImageController extends Controller
{
    use ImageCompressor;
    /**
     * Admin dapat melihat & mengelola file gambar.
     * Storage disk 'public' → storage/app/public/gambar/
     */
    public function index()
    {
        if (!Storage::disk('public')->exists('gambar')) {
            Storage::disk('public')->makeDirectory('gambar');
        }

        $files = collect(Storage::disk('public')->files('gambar'))->map(function ($file) {
            return [
                'name'          => basename($file),
                'url'           => Storage::url($file),
                'size'          => round(Storage::disk('public')->size($file) / 1024, 2) . ' KB',
                'last_modified' => \Carbon\Carbon::createFromTimestamp(
                    Storage::disk('public')->lastModified($file)
                )->diffForHumans(),
            ];
        })->sortByDesc('last_modified')->values();

        return view('admin.image.index', compact('files'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image_file'  => 'required|mimes:jpg,jpeg,png,webp,zip|max:51200',
            'custom_name' => 'nullable|string|max:100',
        ], [
            'image_file.required' => 'Silakan pilih file gambar atau ZIP terlebih dahulu.',
            'image_file.mimes'    => 'Format file harus berupa JPG, PNG, WEBP, atau ZIP.',
            'image_file.max'      => 'Ukuran file maksimal 50MB.',
        ]);

        $file      = $request->file('image_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'zip') {
            $zip = new \ZipArchive();
            $res = $zip->open($file->getRealPath());
            
            if ($res === TRUE) {
                $extractedCount = 0;
                $skippedCount = 0;
                $targetPath = storage_path('app/public/gambar');
                
                if (!file_exists($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }

                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    $fileInfo = pathinfo($filename);
                    
                    if (strpos($filename, '__MACOSX') !== false || substr($fileInfo['basename'], 0, 1) === '.') {
                        continue;
                    }

                    $ext = strtolower($fileInfo['extension'] ?? '');
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $safeFilename = Str::slug($fileInfo['filename']) . '.' . $ext;
                        $finalPath = $targetPath . '/' . $safeFilename;
                        
                        // Handle duplicates for bulk
                        if (file_exists($finalPath)) {
                            $skippedCount++;
                            continue;
                        }

                        $tempExtractFile = $targetPath . '/temp_' . time() . '_' . $safeFilename;
                        
                        copy("zip://".$file->getRealPath()."#".$filename, $tempExtractFile);
                        $this->compressAndSaveImage($tempExtractFile, $finalPath);
                        @unlink($tempExtractFile);

                        $extractedCount++;
                    }
                }
                $zip->close();
                
                $msg = "$extractedCount file gambar berhasil diekstrak.";
                if ($skippedCount > 0) $msg .= " ($skippedCount file dilewati karena sudah ada).";

                if ($extractedCount > 0) {
                    return back()->with('success', $msg);
                } else {
                    return back()->with('error', $skippedCount > 0 ? "Gagal: Semua file ($skippedCount) sudah ada di server." : 'ZIP tidak mengandung file gambar yang valid.');
                }
            } else {
                return back()->with('error', 'Gagal membuka file ZIP.');
            }
        }

        if ($request->filled('custom_name')) {
            $filename = Str::slug($request->custom_name) . '.' . $extension;
        } else {
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $extension;
        }

        if (Storage::disk('public')->exists('gambar/' . $filename)) {
            return back()->with('error', "Gagal: Gambar dengan nama \"$filename\" sudah ada. Silakan gunakan nama lain.");
        }

        $targetPath = storage_path('app/public/gambar');
        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0755, true);
        }

        $this->compressAndSaveImage($file->getRealPath(), $targetPath . '/' . $filename);

        return back()->with('success', "File gambar \"$filename\" berhasil diunggah dan dikompresi otomatis.");
    }

    public function destroy(Request $request)
    {
        $request->validate(['filename' => 'required|string']);

        $path = 'gambar/' . $request->filename;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return back()->with('success', 'File gambar berhasil dihapus.');
        }

        return back()->with('error', 'File tidak ditemukan.');
    }

    public function rename(Request $request)
    {
        $request->validate([
            'old_name' => 'required|string',
            'new_name' => 'required|string'
        ]);

        $oldName = $request->old_name;
        $newName = \Illuminate\Support\Str::slug(pathinfo($request->new_name, PATHINFO_FILENAME)) . '.' . pathinfo($oldName, PATHINFO_EXTENSION);

        if ($oldName === $newName) {
            return back()->with('success', 'Nama file tidak berubah.');
        }

        $oldPath = 'gambar/' . $oldName;
        $newPath = 'gambar/' . $newName;

        if (Storage::disk('public')->exists($newPath)) {
            return back()->with('error', 'File dengan nama tujuan sudah ada. Silakan gunakan nama lain.');
        }

        if (Storage::disk('public')->exists($oldPath)) {
            // Ubah file fisik
            Storage::disk('public')->move($oldPath, $newPath);

            // Relational Sync ke Database Soals
            \App\Models\Soal::where('gambar_path', $oldName)->update(['gambar_path' => $newName]);
            
            // Relational Sync ke Pilihan Jawabans
            \App\Models\PilihanJawaban::where('media_path', $oldName)
                ->where('media_tipe', 'gambar')
                ->update(['media_path' => $newName]);

            return back()->with('success', "Nama file berhasil diubah dari {$oldName} ke {$newName}. Data relasional pada soal juga berhasil disinkronisasi.");
        }

        return back()->with('error', 'File sumber tidak ditemukan.');
    }
}
