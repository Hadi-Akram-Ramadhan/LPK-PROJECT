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
        // Ambil ID ujian milik guru ini
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
     * Guru dapat approve/reject cheat log ujian miliknya.
     */
    public function approve(Request $request, CheatLog $cheatLog)
    {
        // Pastikan log ini milik ujian yang dibuat guru ini
        $ujianIds = Ujian::where('guru_id', auth()->id())->pluck('id');
        $pesertaUjianId = optional($cheatLog->ujianPeserta)->ujian_id;

        if (!$ujianIds->contains($pesertaUjianId)) {
            abort(403, 'Anda tidak berhak memproses log ini.');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes'  => 'nullable|string|max:500',
        ]);

        try {
            $cheatLog->update([
                'status'      => $request->status,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'notes'       => $request->notes,
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
    
            return back()->with('success', 'Status pelanggaran berhasil diperbarui dan murid telah diberitahu.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Gagal memproses data: ' . $e->getMessage());
        }
    }
}
