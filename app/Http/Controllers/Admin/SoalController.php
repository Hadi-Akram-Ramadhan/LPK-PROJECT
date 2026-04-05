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
            'tipe'          => 'required|in:pilihan_ganda,multiple_choice,essay,audio,pilihan_ganda_audio,pilihan_ganda_gambar,short_answer',
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
                'jawaban_kunci' => $request->tipe === 'short_answer' ? $request->jawaban_kunci : null,
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

            // Determine the final tipe (either from request or existing)
            $effectiveTipe = $updateData['tipe'] ?? $soal->tipe;
            $updateData['jawaban_kunci'] = $effectiveTipe === 'short_answer' ? $request->jawaban_kunci : null;

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
            'A1' => 'Tipe Soal',
            'B1' => 'Teks Pertanyaan',
            'C1' => 'Gambar Soal (nama file, ex: tanya.jpg)',
            'D1' => 'Audio Soal (nama file, ex: dengar.mp3)',
            'E1' => 'Opsi A (Teks)',
            'F1' => 'Media Opsi A (nama file)',
            'G1' => 'Opsi B (Teks)',
            'H1' => 'Media Opsi B (nama file)',
            'I1' => 'Opsi C (Teks)',
            'J1' => 'Media Opsi C (nama file)',
            'K1' => 'Opsi D (Teks)',
            'L1' => 'Media Opsi D (nama file)',
            'M1' => 'Opsi E (Teks opsional)',
            'N1' => 'Media Opsi E (nama file)',
            'O1' => 'Jawaban Benar / Kunci Jawaban',
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

        // Contoh baris 6 — Pilihan Ganda Gambar
        $sheet->fromArray([
            'Pilihan Ganda Gambar', 'Manakah dari gambar berikut yang menunjukkan stasiun?', 'tanya.jpg', '',
            '', 'stasiun.jpg', '', 'kantor.png', '', 'pasar.jpg', '', 'mall.png', '', '',
            'A', '10',
        ], null, 'A6');

        // Contoh baris 7 — Short Answer
        $sheet->fromArray([
            'Short Answer', 'Apa nama ibukota Jepang?', '', '',
            '', '', '', '', '', '', '', '', '', '',
            'Tokyo|Tokio|東京', '15',
        ], null, 'A7');

        // Zebra row styling for example rows
        $sheet->getStyle('A2:P7')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);
        // Highlight short_answer row in blue tint
        $sheet->getStyle('A7:P7')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
            'font' => ['color' => ['rgb' => '1D4ED8']],
        ]);

        // ── Sheet 2: PANDUAN ──────────────────────────────────────
        $guide = $spreadsheet->createSheet();
        $guide->setTitle('Panduan Import');

        $guideRows = [
            ['PANDUAN LENGKAP IMPORT SOAL – LPK CBT SYSTEM'],
            [''],
            ['--- PENJELASAN KOLOM ---'],
            ['Kolom', 'Nama Kolom', 'Keterangan'],
            ['A', 'Tipe Soal', '(WAJIB) Lihat daftar tipe valid di bawah'],
            ['B', 'Teks Pertanyaan', '(WAJIB) Teks soal. Boleh berisi HTML sederhana (<b>, <br>)'],
            ['C', 'Gambar Soal', '(Opsional) Nama file gambar yg sudah diupload, ex: tanya.jpg'],
            ['D', 'Audio Soal', '(Opsional) Nama file audio yg sudah diupload, ex: dengar.mp3'],
            ['E-F', 'Opsi A (Teks + Media)', '(Opsional untuk Essay/Short Answer) Teks dan nama file media opsi A'],
            ['G-H', 'Opsi B (Teks + Media)', 'Sama seperti Opsi A'],
            ['I-J', 'Opsi C (Teks + Media)', 'Sama seperti Opsi A'],
            ['K-L', 'Opsi D (Teks + Media)', 'Sama seperti Opsi A'],
            ['M-N', 'Opsi E (Teks + Media)', '(Opsional) Opsi kelima jika diperlukan'],
            ['O', 'Jawaban Benar / Kunci', 'Lihat penjelasan per-tipe di bawah'],
            ['P', 'Poin / Bobot', '(WAJIB) Angka bulat, ex: 10, 15, 20'],
            [''],
            ['--- DAFTAR TIPE SOAL YANG VALID ---'],
            ['Tipe di Kolom A', 'Keterangan', 'Cara Isi Kolom O (Jawaban)'],
            ['Pilihan Ganda', '1 jawaban benar dari pilihan', 'Huruf opsi: A, B, C, D, atau E'],
            ['Multiple Choice', 'Lebih dari 1 jawaban benar', 'Huruf opsi dipisah koma: B,C atau A,C,D'],
            ['Essay', 'Jawaban teks bebas, dinilai manual oleh Guru', 'KOSONGKAN kolom O'],
            ['Short Answer', 'Jawaban singkat, dinilai OTOMATIS oleh sistem', 'Isi jawaban benar (lihat panduan khusus di bawah)'],
            ['Audio', 'Soal Listening. Kolom D WAJIB diisi nama file audio', 'Huruf opsi: A, B, C, D, atau E'],
            ['Pilihan Ganda Audio', 'Opsi jawaban berupa file audio (Kolom F,H,J,L,N)', 'Huruf opsi: A, B, C, D, atau E'],
            ['Pilihan Ganda Gambar', 'Opsi jawaban berupa file gambar (Kolom F,H,J,L,N)', 'Huruf opsi: A, B, C, D, atau E'],
            [''],
            ['--- PANDUAN KHUSUS TIPE SHORT ANSWER ---'],
            ['Sistem menilai jawaban murid secara OTOMATIS dengan 2 logika gabungan:'],
            [''],
            ['Logika 1: CASE-INSENSITIVE'],
            ['  Huruf besar/kecil diabaikan sepenuhnya.'],
            ['  Contoh: "tokyo", "TOKYO", "Tokyo" semuanya dianggap SAMA dan BENAR.'],
            [''],
            ['Logika 2: FUZZY MATCHING (Toleransi Typo)'],
            ['  Sistem menghitung kemiripan teks jawaban murid dengan kunci jawaban.'],
            ['  Jika kemiripan >= 85%, jawaban dianggap BENAR meskipun ada typo kecil.'],
            ['  Contoh BENAR:  "Tokio"  vs kunci "Tokyo"  => kemiripan ~89% => BENAR'],
            ['  Contoh SALAH:  "Tkoyo"  vs kunci "Tokyo"  => kemiripan ~67% => SALAH'],
            [''],
            ['KUNCI JAWABAN GANDA:'],
            ['  Pisahkan jawaban yang diterima dengan tanda | (pipe/garis tegak vertikal).'],
            ['  Kolom O contoh: Tokyo|Tokio|東京'],
            ['  Sistem akan cocokkan ke SEMUA kunci. Jika ada 1 yg cocok >= 85% => BENAR.'],
            [''],
            ['--- CATATAN PENTING ---'],
            ['1. File gambar & audio HARUS sudah diupload via menu Media di dashboard Admin/Guru.'],
            ['2. Nama file bersifat case-sensitive: "soal1.jpg" beda dengan "Soal1.jpg".'],
            ['3. Baris dengan tipe atau pertanyaan kosong akan dilewati (tidak diimport).'],
            ['4. Sheet Panduan ini TIDAK dibaca oleh sistem. Sistem hanya membaca sheet pertama (Template Soal).'],
        ];

        foreach ($guideRows as $i => $rowData) {
            $guide->fromArray($rowData, null, 'A' . ($i + 1));
        }

        // Style guide sheet headers
        $guide->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1D4ED8']],
        ]);
        foreach (['A3', 'A17', 'A27', 'A47'] as $headerCell) {
            $guide->getStyle($headerCell)->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F172A']],
            ]);
        }
        // Style table header row
        $guide->getStyle('A4:C4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
        ]);
        $guide->getStyle('A18:C18')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
        ]);
        // Highlight Short Answer row in the table
        $guide->getStyle('A22:C22')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
            'font' => ['bold' => true, 'color' => ['rgb' => '1D4ED8']],
        ]);

        foreach (['A', 'B', 'C'] as $col) {
            $guide->getColumnDimension($col)->setAutoSize(false);
        }
        $guide->getColumnDimension('A')->setWidth(25);
        $guide->getColumnDimension('B')->setWidth(55);
        $guide->getColumnDimension('C')->setWidth(70);

        $spreadsheet->setActiveSheetIndex(0);

        $tmpFile = tempnam(sys_get_temp_dir(), 'lpk_template_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpFile);

        return response()->download($tmpFile, 'Template_Import_Soal_Admin.xlsx')->deleteFileAfterSend(true);
    }
}
