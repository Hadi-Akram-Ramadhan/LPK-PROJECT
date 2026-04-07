<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UjianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ujians = Ujian::with(['guru'])
            ->withCount('soals')
            ->latest()
            ->paginate(10);
            
        return view('guru.ujian.index', compact('ujians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil Semua Paket Soal (termasuk milik admin dan guru lain yang tampil di sistem)
        $paketSoals = \App\Models\PaketSoal::with(['soals' => function($q) {
                $q->orderBy('id', 'asc');
            }])
            ->get();

        // Kelas untuk assign peserta
        $kelas = Kelas::all();
        
        return view('guru.ujian.create', compact('paketSoals', 'kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'durasi' => 'required|integer|min:1',
            'mulai' => 'nullable|date',
            'selesai' => 'nullable|date|after_or_equal:mulai',
            'soal_id' => 'required|array|min:1',
            'soal_id.*' => 'exists:soals,id',
            'acak_soal' => 'nullable|boolean',
            'jenis_ujian' => 'nullable|in:reguler,tryout',
        ]);

        DB::beginTransaction();
        try {
            $ujian = Ujian::create([
                'guru_id' => auth()->id(),
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'durasi' => $request->durasi,
                'mulai' => $request->mulai,
                'selesai' => $request->selesai,
                'acak_soal' => $request->has('acak_soal'),
                'jenis_ujian' => $request->jenis_ujian ?? 'reguler',
            ]);

            // Sync Soal dengan Pivot
            if ($request->has('soal_id')) {
                $urutan = 1;
                $syncData = [];
                foreach ($request->soal_id as $s_id) {
                    $syncData[$s_id] = ['urutan' => $urutan];
                    $urutan++;
                }
                $ujian->soals()->sync($syncData);
            }

            // Jika ada assign ke kelas
            if ($request->has('kelas_id') && is_array($request->kelas_id)) {
                // Di sini kita assign murid dalam kelas tersebut ke ujian_peserta
                $murids = \App\Models\User::whereIn('kelas_id', $request->kelas_id)
                                        ->where('role', 'murid')
                                        ->get();
                
                $pesertaData = [];
                foreach ($murids as $murid) {
                    $pesertaData[] = [
                        'ujian_id' => $ujian->id,
                        'user_id' => $murid->id,
                        'status' => 'belum_mulai',
                        'skor' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                \App\Models\UjianPeserta::insert($pesertaData);
            }

            DB::commit();
            return redirect()->route('guru.ujian.index')->with('success', 'Ujian berhasil dibuat dan soal telah diassign.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ujian $ujian)
    {

        $ujian->load('soals');
        $selectedSoal = $ujian->soals->pluck('id')->toArray();
        
        $paketSoals = \App\Models\PaketSoal::with(['soals' => function($q) {
                $q->orderBy('id', 'asc');
            }])
            ->get();
        
        return view('guru.ujian.edit', compact('ujian', 'paketSoals', 'selectedSoal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ujian $ujian)
    {

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'durasi' => 'required|integer|min:1',
            'mulai' => 'nullable|date',
            'selesai' => 'nullable|date|after_or_equal:mulai',
            'soal_id' => 'required|array|min:1',
            'soal_id.*' => 'exists:soals,id',
            'acak_soal' => 'nullable|boolean',
            'jenis_ujian' => 'nullable|in:reguler,tryout',
        ]);

        DB::beginTransaction();
        try {
            $ujian->update([
                'judul' => $request->judul,
                'deskripsi' => $request->deskripsi,
                'durasi' => $request->durasi,
                'mulai' => $request->mulai,
                'selesai' => $request->selesai,
                'acak_soal' => $request->has('acak_soal'),
                'jenis_ujian' => $request->jenis_ujian ?? 'reguler',
            ]);

            // Sync Soal dengan Pivot
            if ($request->has('soal_id')) {
                $urutan = 1;
                $syncData = [];
                foreach ($request->soal_id as $s_id) {
                    $syncData[$s_id] = ['urutan' => $urutan];
                    $urutan++;
                }
                $ujian->soals()->sync($syncData);
            }

            DB::commit();
            return redirect()->route('guru.ujian.index')->with('success', 'Detail ujian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ujian $ujian)
    {

        $ujian->delete();
        return redirect()->route('guru.ujian.index')->with('success', 'Ujian berhasil dihapus.');
    }
}
