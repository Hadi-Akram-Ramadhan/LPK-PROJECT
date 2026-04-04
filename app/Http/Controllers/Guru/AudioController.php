<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AudioController extends Controller
{
    /**
     * Guru dapat melihat & mengelola file audio yang sama dengan Admin.
     * Storage disk 'public' → storage/app/public/audio/
     */
    public function index()
    {
        if (!Storage::disk('public')->exists('audio')) {
            Storage::disk('public')->makeDirectory('audio');
        }

        $files = collect(Storage::disk('public')->files('audio'))->map(function ($file) {
            return [
                'name'          => basename($file),
                'url'           => Storage::url($file),
                'size'          => round(Storage::disk('public')->size($file) / 1024, 2) . ' KB',
                'last_modified' => \Carbon\Carbon::createFromTimestamp(
                    Storage::disk('public')->lastModified($file)
                )->diffForHumans(),
            ];
        })->sortByDesc('last_modified')->values();

        return view('guru.audio.index', compact('files'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'audio_file'  => 'required|mimes:mp3,wav,ogg,zip|max:51200', // max 50MB for zip potentially
            'custom_name' => 'nullable|string|max:100',
        ], [
            'audio_file.required' => 'Silakan pilih file audio atau ZIP terlebih dahulu.',
            'audio_file.mimes'    => 'Format file harus berupa MP3, WAV, OGG, atau ZIP.',
            'audio_file.max'      => 'Ukuran file maksimal 50MB.',
        ]);

        $file      = $request->file('audio_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'zip') {
            $zip = new \ZipArchive();
            $res = $zip->open($file->getRealPath());
            
            if ($res === TRUE) {
                $extractedCount = 0;
                $targetPath = storage_path('app/public/audio');
                
                if (!file_exists($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }

                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    $fileInfo = pathinfo($filename);
                    
                    // Skip MacOS _MACOSX or hidden files and check valid extension
                    if (strpos($filename, '__MACOSX') !== false || substr($fileInfo['basename'], 0, 1) === '.') {
                        continue;
                    }

                    $ext = strtolower($fileInfo['extension'] ?? '');
                    if (in_array($ext, ['mp3', 'wav', 'ogg'])) {
                        // Secure filename
                        $safeFilename = Str::slug($fileInfo['filename']) . '.' . $ext;
                        
                        // Extract specific file to target directory
                        copy("zip://".$file->getRealPath()."#".$filename, $targetPath.'/'.$safeFilename);
                        $extractedCount++;
                    }
                }
                $zip->close();
                
                if ($extractedCount > 0) {
                    return back()->with('success', "$extractedCount file audio berhasil diekstrak dari ZIP.");
                } else {
                    return back()->with('error', 'ZIP tidak mengandung file audio yang valid (MP3, WAV, OGG).');
                }
            } else {
                return back()->with('error', 'Gagal membuka file ZIP.');
            }
        }

        // Single file upload logic
        if ($request->filled('custom_name')) {
            $filename = Str::slug($request->custom_name) . '.' . $extension;
        } else {
            $filename = time() . '_' . Str::slug(
                    pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                ) . '.' . $extension;
        }

        $file->storeAs('audio', $filename, 'public');

        return back()->with('success', "File audio \"$filename\" berhasil diunggah.");
    }

    public function destroy(Request $request)
    {
        $request->validate(['filename' => 'required|string']);

        $path = 'audio/' . $request->filename;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
            return back()->with('success', 'File audio berhasil dihapus.');
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

        $oldPath = 'audio/' . $oldName;
        $newPath = 'audio/' . $newName;

        if (Storage::disk('public')->exists($newPath)) {
            return back()->with('error', 'File dengan nama tujuan sudah ada. Silakan gunakan nama lain.');
        }

        if (Storage::disk('public')->exists($oldPath)) {
            // Ubah file fisik
            Storage::disk('public')->move($oldPath, $newPath);

            // Relational Sync ke Database Soals
            \App\Models\Soal::where('audio_path', $oldName)->update(['audio_path' => $newName]);
            
            // Relational Sync ke Pilihan Jawabans
            \App\Models\PilihanJawaban::where('media_path', $oldName)
                ->where('media_tipe', 'audio')
                ->update(['media_path' => $newName]);

            return back()->with('success', "Nama file Audio berhasil diubah dari {$oldName} ke {$newName}. Data relasional pada soal juga berhasil disinkronisasi.");
        }

        return back()->with('error', 'File sumber tidak ditemukan.');
    }
}
