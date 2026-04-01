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
            'audio_file'  => 'required|mimes:mp3,wav,ogg|max:10240',
            'custom_name' => 'nullable|string|max:100',
        ], [
            'audio_file.required' => 'Silakan pilih file audio terlebih dahulu.',
            'audio_file.mimes'    => 'Format file harus berupa MP3, WAV, atau OGG.',
            'audio_file.max'      => 'Ukuran file maksimal 10MB.',
        ]);

        $file      = $request->file('audio_file');
        $extension = $file->getClientOriginalExtension();

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
}
