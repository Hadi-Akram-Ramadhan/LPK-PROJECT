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
        
        $imageFiles = collect(Storage::disk('public')->files('gambar'))->map(function($file) {
            return basename($file);
        });

        return view('admin.soal.create', compact('audioFiles', 'imageFiles', 'paketSoal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'paket_soal_id' => 'required|exists:paket_soals,id',
            'tipe'          => 'required|in:pilihan_ganda,multiple_choice,essay,audio,pilihan_ganda_audio,pilihan_ganda_gambar',
            'pertanyaan'    => 'required|string',
            'poin'          => 'required|integer|min:1',
            'audio_path'    => 'nullable|string',
            'gambar_path'   => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $soal = Soal::create([
                'guru_id'       => auth()->id(), // Admin is technically "guru" structurally in Soal
                'paket_soal_id' => $request->paket_soal_id,
                'tipe'          => $request->tipe,
                'pertanyaan'    => $request->pertanyaan,
                'poin'          => $request->poin,
                'audio_path'    => $request->audio_path,
                'gambar_path'   => $request->gambar_path,
            ]);

            if (in_array($request->tipe, ['pilihan_ganda', 'multiple_choice', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
                if ($request->has('pilihan') && is_array($request->pilihan)) {
                    foreach ($request->pilihan as $index => $teks) {
                        $mediaPath = $request->pilihan_media[$index] ?? null;

                        if (!empty($teks) || !empty($mediaPath)) {
                            $isBenar = false;
                            if (in_array($request->tipe, ['pilihan_ganda', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
                                $isBenar = ($request->jawaban_benar == $index);
                            } else if ($request->tipe == 'multiple_choice') {
                                $isBenar = (is_array($request->jawaban_benar) && in_array($index, $request->jawaban_benar));
                            }

                            $mediaTipe = null;
                            if (!empty($mediaPath)) {
                                $mediaTipe = ($request->tipe === 'pilihan_ganda_audio') ? 'audio' : 'gambar';
                            }

                            PilihanJawaban::create([
                                'soal_id'    => $soal->id,
                                'teks'       => $teks ?? '',
                                'media_path' => $mediaPath,
                                'media_tipe' => $mediaTipe,
                                'is_benar'   => $isBenar,
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
        $imageFiles = collect(Storage::disk('public')->files('gambar'))->map(function($file) {
            return basename($file);
        });
        return view('admin.soal.edit', compact('soal', 'audioFiles', 'imageFiles'));
    }

    public function update(Request $request, Soal $soal)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'poin' => 'required|integer|min:1',
            'audio_path' => 'nullable|string',
            'gambar_path' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'pertanyaan' => $request->pertanyaan,
                'poin' => $request->poin,
                'audio_path' => $request->audio_path,
                'gambar_path' => $request->gambar_path,
            ];

            if ($request->filled('tipe')) {
                $updateData['tipe'] = $request->tipe;
            }

            $soal->update($updateData);

            if (in_array($soal->tipe, ['pilihan_ganda', 'multiple_choice', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
                $soal->pilihanJawabans()->delete();
                if ($request->has('pilihan') && is_array($request->pilihan)) {
                    foreach ($request->pilihan as $index => $teks) {
                        $mediaPath = $request->pilihan_media[$index] ?? null;

                        if (!empty($teks) || !empty($mediaPath)) {
                            $isBenar = false;
                            if (in_array($soal->tipe, ['pilihan_ganda', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
                                $isBenar = ($request->jawaban_benar == $index);
                            } else if ($soal->tipe == 'multiple_choice') {
                                $isBenar = (is_array($request->jawaban_benar) && in_array($index, $request->jawaban_benar));
                            }

                            $mediaTipe = null;
                            if (!empty($mediaPath)) {
                                $mediaTipe = ($soal->tipe === 'pilihan_ganda_audio') ? 'audio' : 'gambar';
                            }

                            PilihanJawaban::create([
                                'soal_id'    => $soal->id,
                                'teks'       => $teks ?? '',
                                'media_path' => $mediaPath,
                                'media_tipe' => $mediaTipe,
                                'is_benar'   => $isBenar,
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
        $paketSoals = PaketSoal::orderBy('nama')->get();
        return view('admin.soal.import', compact('paketSoals'));
    }

    public function storeImport(Request $request)
    {
        $request->validate([
            'paket_soal_id' => 'required|exists:paket_soals,id',
            'file_excel'    => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $tmpPath = $request->file('file_excel')->getRealPath();
            $import = new SoalImport($request->paket_soal_id);
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
            'A1' => 'Tipe Soal (Pilihan Ganda / Multiple Choice / Essay / Audio / Pilihan Ganda Audio / Pilihan Ganda Gambar)',
            'B1' => 'Teks Pertanyaan',
            'C1' => 'Gambar Soal (Opsional, nama file ex: gambar.jpg)',
            'D1' => 'Audio Soal (Opsional, nama file ex: choukai.mp3)',
            'E1' => 'Opsi A (Teks)',
            'F1' => 'Media Opsi A (Opsional)',
            'G1' => 'Opsi B (Teks)',
            'H1' => 'Media Opsi B (Opsional)',
            'I1' => 'Opsi C (Teks)',
            'J1' => 'Media Opsi C (Opsional)',
            'K1' => 'Opsi D (Teks)',
            'L1' => 'Media Opsi D (Opsional)',
            'M1' => 'Opsi E (Teks opsional)',
            'N1' => 'Media Opsi E (Opsional)',
            'O1' => 'Jawaban Benar (A/B/C/D/E, pisah koma jika Multiple)',
            'P1' => 'Poin / Bobot',
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563eb']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(40);

        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Contoh baris 2 — Pilihan Ganda
        $sheet->fromArray([
            'Pilihan Ganda', 'Apa arti kata "Gakkou"?', '', '',
            'Rumah Tangga', '', 'Sekolah', '', 'Stasiun', '', 'Kantor', '', '', '',
            'B', '10',
        ], null, 'A2');

        // Contoh baris 3 — Multiple Choice
        $sheet->fromArray([
            'Multiple Choice', 'Manakah yang termasuk kata benda dalam bahasa Jepang?', '', '',
            'Nomi (飲む)', '', 'Hon (本)', '', 'Enpitsu (鉛筆)', '', 'Taberu (食べる)', '', '', '',
            'B,C', '15',
        ], null, 'A3');

        // Contoh baris 4 — Essay
        $sheet->fromArray([
            'Essay', 'Sebutkan 3 alat transportasi dalam bahasa Jepang!', '', '',
            '', '', '', '', '', '', '', '', '', '',
            '', '20',
        ], null, 'A4');

        // Contoh baris 5 — Audio/Choukai
        $sheet->fromArray([
            'Audio', 'Dengarkan audio dan pilih jawaban yang tepat.', '', 'choukai.mp3',
            'Pilih A', '', 'Pilih B', '', 'Pilih C', '', 'Pilih D', '', '', '',
            'C', '10',
        ], null, 'A5');
        
        // Contoh baris 6 - Pilihan Ganda Gambar
        $sheet->fromArray([
            'Pilihan Ganda Gambar', 'Manakah dari gambar berikut yang menunjukkan stasiun?', 'tanya.jpg', '',
            '', 'stasiun.jpg', '', 'kantor.png', '', 'pasar.jpg', '', 'mall.png', '', '',
            'A', '10',
        ], null, 'A6');

        // Zebra row styling for example rows
        $sheet->getStyle('A2:P6')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);

        $tmpFile = tempnam(sys_get_temp_dir(), 'lpk_template_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpFile);

        return response()->download($tmpFile, 'Template_Import_Soal_Admin.xlsx')->deleteFileAfterSend(true);
    }
}
