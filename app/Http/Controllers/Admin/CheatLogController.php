<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheatLog;
use Illuminate\Http\Request;

class CheatLogController extends Controller
{
    public function index()
    {
        $logs = CheatLog::with(['ujianPeserta.user', 'ujianPeserta.ujian', 'approvedBy'])
            ->latest()
            ->paginate(15);
            
        return view('admin.cheat_logs.index', compact('logs'));
    }

    public function approve(Request $request, CheatLog $cheatLog)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string|max:500'
        ]);

        $cheatLog->update([
            'status' => $request->status,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->notes,
        ]);
        
        if ($request->status === 'approved') {
            $peserta = $cheatLog->ujianPeserta;
            if ($peserta->status === 'diblokir') {
                $peserta->update(['status' => 'mengerjakan']);
                event(new \App\Events\CheatLogApproved($peserta));
            }
        }
        
        return back()->with('success', 'Status pelanggaran berhasil diperbarui.');
    }
}
