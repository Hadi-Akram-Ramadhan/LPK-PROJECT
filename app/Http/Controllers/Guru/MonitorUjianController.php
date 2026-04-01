<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Models\UjianPeserta;
use App\Models\JawabanMurid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitorUjianController extends Controller
{
    public function index()
    {
        $ujians = Ujian::where('guru_id', auth()->id())
            ->withCount('pesertas')
            ->latest()
            ->paginate(10);
            
        return view('guru.monitor.index', compact('ujians'));
    }

    public function show(Ujian $ujian)
    {
        if ($ujian->guru_id !== auth()->id()) {
            abort(403);
        }

        $pesertas = UjianPeserta::with('user')
            ->where('ujian_id', $ujian->id)
            ->latest()
            ->paginate(20);

        // Menghitung jumlah soal essay pada ujian ini
        $essayCount = $ujian->soals()->where('tipe', 'essay')->count();
        
        return view('guru.monitor.show', compact('ujian', 'pesertas', 'essayCount'));
    }

    public function grade(UjianPeserta $ujian_peserta)
    {
        $ujian = $ujian_peserta->ujian;
        if ($ujian->guru_id !== auth()->id()) {
            abort(403);
        }

        // Ambil soal essay saja
        $soalEssays = $ujian->soals()->where('tipe', 'essay')->get();
        
        $jawabans = JawabanMurid::where('ujian_peserta_id', $ujian_peserta->id)
            ->whereIn('soal_id', $soalEssays->pluck('id'))
            ->get()
            ->keyBy('soal_id');
            
        return view('guru.monitor.grade', compact('ujian_peserta', 'ujian', 'soalEssays', 'jawabans'));
    }

    public function storeGrade(Request $request, UjianPeserta $ujian_peserta)
    {
        $ujian = $ujian_peserta->ujian;
        if ($ujian->guru_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'poin' => 'required|array',
            'poin.*' => 'numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->poin as $soalId => $poin) {
                JawabanMurid::updateOrCreate(
                    [
                        'ujian_peserta_id' => $ujian_peserta->id,
                        'soal_id' => $soalId
                    ],
                    [
                        'poin_didapat' => $poin
                    ]
                );
            }

            // Hitung ulang total skor (PG + Essay)
            $totalSkor = JawabanMurid::where('ujian_peserta_id', $ujian_peserta->id)->sum('poin_didapat');
            $ujian_peserta->update(['skor' => $totalSkor]);

            DB::commit();
            
            return redirect()->route('guru.monitor.show', $ujian)->with('success', 'Skor essay berhasil disimpan dan nilai total telah diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }
}
