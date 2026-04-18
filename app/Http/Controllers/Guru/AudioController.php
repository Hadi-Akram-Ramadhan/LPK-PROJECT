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
        if (!Storage::disk('local')->exists('audio')) {
            Storage::disk('local')->makeDirectory('audio');
        }

        $files = collect(Storage::disk('local')->files('audio'))->map(function ($file) {
            return [
                'name'          => basename($file),
                'url'           => '#',
                'size'          => round(Storage::disk('local')->size($file) / 1024, 2) . ' KB',
                'last_modified' => \Carbon\Carbon::createFromTimestamp(
                    Storage::disk('local')->lastModified($file)
                )->diffForHumans(),
            ];
        })->sortByDesc('last_modified')->values();

        return view('guru.audio.index', compact('files'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'audio_file'  => [
                'required',
                'extensions:mp3,mpeg,mpga,wav,ogg,zip',
                'max:40000'
            ],
            'custom_name' => 'nullable|string|max:100',
        ], [
            'audio_file.required' => 'Silakan pilih file audio atau ZIP terlebih dahulu.',
            'audio_file.extensions' => 'Ekstensi file tidak valid. Gunakan MP3, WAV, OGG, atau ZIP.',
            'audio_file.max'      => 'Ukuran file terlalu besar. Server Anda membatasi unggahan maksimal hingga 40MB.',
        ]);

        $file      = $request->file('audio_file');
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'zip') {
            $zip = new \ZipArchive();
            $res = $zip->open($file->getRealPath());
            
            if ($res === TRUE) {
                $extractedCount = 0;
                $skippedCount = 0;
                $targetPath = storage_path('app/audio');
                
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
                    if (in_array($ext, ['mp3', 'mpeg', 'mpga', 'wav', 'ogg'])) {
                        $safeFilename = Str::slug($fileInfo['filename']) . '.' . $ext;
                        
                        // Check if exists
                        if (file_exists($targetPath . '/' . $safeFilename)) {
                            $skippedCount++;
                            continue;
                        }

                        copy("zip://".$file->getRealPath()."#".$filename, $targetPath.'/'.$safeFilename);
                        $extractedCount++;
                    }
                }
                $zip->close();
                
                $msg = "$extractedCount file audio berhasil diekstrak.";
                if ($skippedCount > 0) $msg .= " ($skippedCount file dilewati karena sudah ada).";

                if ($extractedCount > 0) {
                    return back()->with('success', $msg);
                } else {
                    return back()->with('error', $skippedCount > 0 ? "Gagal: Semua file ($skippedCount) sudah ada di server." : 'ZIP tidak mengandung file audio yang valid.');
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

        if (Storage::disk('local')->exists('audio/' . $filename)) {
            return back()->with('error', "Gagal: File dengan nama \"$filename\" sudah ada. Silakan gunakan nama lain atau ubah nama file Anda.");
        }

        $file->storeAs('audio', $filename, 'local');

        return back()->with('success', "File audio \"$filename\" berhasil diunggah.");
    }

    public function destroy(Request $request)
    {
        $request->validate(['filename' => 'required|string']);

        $path = 'audio/' . $request->filename;

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
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

        if (Storage::disk('local')->exists($newPath)) {
            return back()->with('error', 'File dengan nama tujuan sudah ada. Silakan gunakan nama lain.');
        }

        if (Storage::disk('local')->exists($oldPath)) {
            // Ubah file fisik
            Storage::disk('local')->move($oldPath, $newPath);

            // Relational Sync ke Database Soals
            \App\Models\Soal::where('audio_path', 'audio/' . $oldName)->update(['audio_path' => 'audio/' . $newName]);
            
            // Relational Sync ke Pilihan Jawabans
            \App\Models\PilihanJawaban::where('media_path', 'audio/' . $oldName)
                ->where('media_tipe', 'audio')
                ->update(['media_path' => 'audio/' . $newName]);

            return back()->with('success', "Nama file Audio berhasil diubah dari {$oldName} ke {$newName}. Data relasional pada soal juga berhasil disinkronisasi.");
        }

        return back()->with('error', 'File sumber tidak ditemukan.');
    }
}
