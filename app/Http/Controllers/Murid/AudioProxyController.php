<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\Soal;
use App\Models\PilihanJawaban;
use App\Models\UjianPeserta;
use App\Models\AudioPlaybackLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AudioProxyController extends Controller
{
    /**
     * Stream protected audio file and enforce playback limits in the backend.
     */
    public function stream(Request $request, UjianPeserta $ujian_peserta, $id, $type)
    {
        // 1. Basic Auth & Status Check
        if ($ujian_peserta->user_id !== auth()->id()) abort(403, 'Unauthorized access.');
        if ($ujian_peserta->status !== 'mengerjakan') abort(403, 'Exam is not in progress.');

        // 2. Validate Time (Sync with ExamController logic)
        $mulaiAt = Carbon::parse($ujian_peserta->mulai_at);
        $deadline = $mulaiAt->copy()->addMinutes($ujian_peserta->ujian->durasi);
        if (now()->gt($deadline)) abort(403, 'Time limit exceeded.');

        $soal_id = null;
        $opsi_id = null;
        $audioPath = null;
        $maxPlay = 0;

        // 3. Identify Media & Max Play Count
        if ($type === 'soal') {
            $soal = Soal::findOrFail($id);
            $soal_id = $soal->id;
            $audioPath = $soal->audio_path;
            $maxPlay = $soal->audio_max_play;
        } else {
            $opsi = PilihanJawaban::findOrFail($id);
            $soal_id = $opsi->soal_id;
            $opsi_id = $opsi->id;
            $audioPath = $opsi->media_path;
            $maxPlay = $opsi->audio_max_play;
        }

        if (!$audioPath) abort(404, 'Audio file not found for this item.');

        // 4. Check Backend Limits
        if ($maxPlay > 0) {
            $log = AudioPlaybackLog::firstOrCreate([
                'ujian_peserta_id' => $ujian_peserta->id,
                'soal_id' => $soal_id,
                'pilihan_jawaban_id' => $opsi_id
            ]);

            if ($log->play_count >= $maxPlay) {
                abort(403, 'Playback limit reached.');
            }

            // Debounce mechanism: only increment play_count once every 10 seconds for the same audio
            $cacheKey = "audio_lock_{$ujian_peserta->id}_{$soal_id}_{$opsi_id}";
            if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                $range = $request->header('Range');
                // Only increment on the initial byte requests, ignore deep seek requests
                if (!$range || str_starts_with($range, 'bytes=0')) {
                    $log->increment('play_count');
                    \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addSeconds(10));
                }
            }
        }

        // 5. Serve the File from PRIVATE storage
        // The file is expected to be in storage/app/audio/filename.mp3
        // $audioPath in DB is typically "audio/filename.mp3"
        if (!Storage::disk('local')->exists($audioPath)) {
            // Check if it's still in public (for backward compatibility if move failed)
            if (Storage::disk('public')->exists($audioPath)) {
                return response()->file(Storage::disk('public')->path($audioPath));
            }
            abort(404, 'Physical audio file not found on server.');
        }

        return $this->serveAudioFile($audioPath);
    }

    /**
     * Stream audio for Preview mode (Admin/Guru) with NO playback limits.
     */
    public function streamPreview(Request $request, $id, $type)
    {
        // 1. Auth Check (Admin or Guru only)
        if (!auth()->user()->isAdmin() && !auth()->user()->isGuru()) abort(403);

        $audioPath = null;

        // 2. Identify Media
        if ($type === 'soal') {
            $soal = Soal::findOrFail($id);
            $audioPath = $soal->audio_path;
        } else {
            $opsi = PilihanJawaban::findOrFail($id);
            $audioPath = $opsi->media_path;
        }

        if (!$audioPath) abort(404, 'Audio file not found.');

        return $this->serveAudioFile($audioPath);
    }

    /**
     * Private helper to serve the file from multiple disks if needed.
     */
    private function serveAudioFile($audioPath)
    {
        // Check local first (Private /storage/app/audio/)
        if (Storage::disk('local')->exists($audioPath)) {
            return response()->file(Storage::disk('local')->path($audioPath), [
                'Content-Type' => 'audio/mpeg',
                'Cache-Control' => 'private, max-age=7200',
            ]);
        }

        // Check public fallback (Public /storage/app/public/gambar/ or audio/)
        if (Storage::disk('public')->exists($audioPath)) {
            return response()->file(Storage::disk('public')->path($audioPath), [
                'Content-Type' => 'audio/mpeg',
                'Cache-Control' => 'private, max-age=7200',
            ]);
        }

        abort(404, 'Physical audio file not found on any disk.');
    }
}
