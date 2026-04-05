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
        $logs = CheatLog::with(['ujianPeserta.user', 'ujianPeserta.ujian', 'approvedBy'])
            ->latest()
            ->paginate(20);

        return view('guru.cheat_logs.index', compact('logs'));
    }

    /**
     * Guru dapat approve cheat log ujian miliknya.
     */
    public function approve(Request $request, CheatLog $cheatLog)
    {
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
