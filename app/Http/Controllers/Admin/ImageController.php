<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
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
                        copy("zip://".$file->getRealPath()."#".$filename, $targetPath.'/'.$safeFilename);
                        $extractedCount++;
                    }
                }
                $zip->close();
                
                if ($extractedCount > 0) {
                    return back()->with('success', "$extractedCount file gambar berhasil diekstrak dari ZIP.");
                } else {
                    return back()->with('error', 'ZIP tidak mengandung file gambar yang valid (JPG, PNG, WEBP).');
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

        $file->storeAs('gambar', $filename, 'public');

        return back()->with('success', "File gambar \"$filename\" berhasil diunggah.");
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
}
