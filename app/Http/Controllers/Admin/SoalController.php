<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Soal;
use App\Models\PaketSoal;
use App\Models\PilihanJawaban;
use App\Imports\SoalImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SoalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Redirect ke daftar paket soal
        return redirect()->route('admin.paket-soal.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $paketSoal = null;
        if ($request->filled('paket')) {
            $paketSoal = PaketSoal::findOrFail($request->paket);
        }

        $audioFiles = collect(Storage::disk('public')->files('audio'))->map(function($file) {
            return basename($file);
        });

        return view('admin.soal.create', compact('audioFiles', 'paketSoal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'paket_soal_id' => 'required|exists:paket_soals,id',
            'tipe'          => 'required|in:pilihan_ganda,multiple_choice,essay,audio',
            'pertanyaan'    => 'required|string',
            'poin'          => 'required|integer|min:1',
            'audio_path'    => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $soal = Soal::create([
                'guru_id'       => auth()->id(),
                'paket_soal_id' => $request->paket_soal_id,
                'tipe'          => $request->tipe,
                'pertanyaan'    => $request->pertanyaan,
                'poin'          => $request->poin,
                'audio_path'    => $request->audio_path,
            ]);

            if (in_array($request->tipe, ['pilihan_ganda', 'multiple_choice', 'audio'])) {
                if ($request->has('pilihan') && is_array($request->pilihan)) {
                    foreach ($request->pilihan as $index => $teks) {
                        if (!empty($teks)) {
                            $isBenar = false;
                            if ($request->tipe == 'pilihan_ganda' || $request->tipe == 'audio') {
                                $isBenar = ($request->jawaban_benar == $index);
                            } else if ($request->tipe == 'multiple_choice') {
                                $isBenar = (is_array($request->jawaban_benar) && in_array($index, $request->jawaban_benar));
                            }
                            PilihanJawaban::create([
                                'soal_id'  => $soal->id,
                                'teks'     => $teks,
                                'is_benar' => $isBenar,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.paket-soal.show', $request->paket_soal_id)
                ->with('success', 'Soal berhasil disimpan ke paket.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Soal $soal)
    {
        $soal->load('pilihanJawabans');
        $audioFiles = collect(Storage::disk('public')->files('audio'))->map(function($file) {
            return basename($file);
        });
        return view('admin.soal.edit', compact('soal', 'audioFiles'));
    }

    public function update(Request $request, Soal $soal)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'poin' => 'required|integer|min:1',
            'audio_path' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $soal->update([
                'pertanyaan' => $request->pertanyaan,
                'poin' => $request->poin,
                'audio_path' => $request->audio_path,
            ]);

            if (in_array($soal->tipe, ['pilihan_ganda', 'multiple_choice', 'audio'])) {
                $soal->pilihanJawabans()->delete();
                if ($request->has('pilihan') && is_array($request->pilihan)) {
                    foreach ($request->pilihan as $index => $teks) {
                        if (!empty($teks)) {
                            $isBenar = false;
                            if ($soal->tipe == 'pilihan_ganda' || $soal->tipe == 'audio') {
                                $isBenar = ($request->jawaban_benar == $index);
                            } else if ($soal->tipe == 'multiple_choice') {
                                $isBenar = (is_array($request->jawaban_benar) && in_array($index, $request->jawaban_benar));
                            }
                            PilihanJawaban::create([
                                'soal_id' => $soal->id,
                                'teks' => $teks,
                                'is_benar' => $isBenar,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.paket-soal.show', $soal->paket_soal_id)
                ->with('success', 'Soal berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Soal $soal)
    {
        $paketId = $soal->paket_soal_id;
        $soal->pilihanJawabans()->delete();
        $soal->delete();
        return redirect()->route('admin.paket-soal.show', $paketId)
            ->with('success', 'Soal berhasil dihapus.');
    }

    // ── Import Soal ───────────────────────────────────────────
    public function import()
    {
        return view('admin.soal.import');
    }

    public function storeImport(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $tmpPath = $request->file('file_excel')->getRealPath();
            $import = new SoalImport();
            $import->import($tmpPath);
            $summary = $import->getSummary();

            if ($summary['sukses'] > 0) {
                return redirect()->route('admin.soal.index')->with('success', "{$summary['sukses']} soal berhasil diimport.");
            }
            return back()->with('error', 'Tidak ada soal yang berhasil diimport.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Soal');

        $headers = [
            'A1' => 'Tipe Soal (Pilihan Ganda / Multiple Choice / Essay / Audio)',
            'B1' => 'Teks Pertanyaan',
            'C1' => 'Opsi A',
            'D1' => 'Opsi B',
            'E1' => 'Opsi C',
            'F1' => 'Opsi D',
            'G1' => 'Opsi E (Opsional)',
            'H1' => 'Jawaban Benar (A / B / C / pisah koma jika Multiple)',
            'I1' => 'File Audio (nama.mp3 — kosongkan jika bukan Choukai)',
            'J1' => 'Poin / Bobot',
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563eb']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(40);

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $tmpFile = tempnam(sys_get_temp_dir(), 'lpk_template_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpFile);

        return response()->download($tmpFile, 'Template_Import_Soal_Admin.xlsx')->deleteFileAfterSend(true);
    }
}
