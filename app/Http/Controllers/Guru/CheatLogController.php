<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\CheatLog;
use App\Models\Ujian;
use Illuminate\Http\Request;

class CheatLogController extends Controller
{
    /**
     * Semua guru bisa melihat semua cheat log dari semua ujian.
     */
    public function index()
    {
        $logs = CheatLog::with(['ujianPeserta.user', 'ujianPeserta.ujian', 'approvedBy'])
            ->latest()
            ->paginate(20);

        return view('guru.cheat_logs.index', compact('logs'));
    }

    /**
     * Semua guru dapat approve/reject cheat log dari ujian manapun.
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

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Status pelanggaran berhasil diperbarui.']);
            }

            return back()->with('success', 'Murid berhasil dibuka blokirnya.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }
}
