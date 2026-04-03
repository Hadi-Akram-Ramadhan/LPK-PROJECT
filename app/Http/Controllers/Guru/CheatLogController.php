<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\CheatLog;
use App\Models\Ujian;
use Illuminate\Http\Request;

class CheatLogController extends Controller
{
    /**
     * Guru hanya bisa melihat cheat log ujian yang ia buat sendiri.
     */
    public function index()
    {
        $ujianIds = Ujian::where('guru_id', auth()->id())->pluck('id');

        $logs = CheatLog::with(['ujianPeserta.user', 'ujianPeserta.ujian', 'approvedBy'])
            ->whereHas('ujianPeserta', function ($q) use ($ujianIds) {
                $q->whereIn('ujian_id', $ujianIds);
            })
            ->latest()
            ->paginate(20);

        return view('guru.cheat_logs.index', compact('logs'));
    }

    /**
     * Guru dapat approve cheat log ujian miliknya.
     */
    public function approve(Request $request, CheatLog $cheatLog)
    {
        // Pastikan log ini milik ujian yang dibuat guru ini
        $ujianIds       = Ujian::where('guru_id', auth()->id())->pluck('id');
        $pesertaUjianId = optional($cheatLog->ujianPeserta)->ujian_id;

        if (!$ujianIds->contains($pesertaUjianId)) {
            abort(403, 'Anda tidak berhak memproses log ini.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        try {
            $cheatLog->update([
                'status'      => $request->status,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            if ($request->status === 'approved') {
                $peserta = $cheatLog->ujianPeserta;
                if ($peserta && $peserta->status === 'diblokir') {
                    $peserta->update(['status' => 'mengerjakan']);
                    event(new \App\Events\CheatLogApproved($peserta));
                }
            }

            return back()->with('success', 'Murid berhasil dibuka blokirnya.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }
}
