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
            'A1' => 'Tipe Soal (Wajib)',
            'B1' => 'Tuliskan Pertanyaan / Soalnya',
            'C1' => 'Nama File Gambar (Opsional)',
            'D1' => 'Nama File Audio/Suara (Opsional)',
            'E1' => 'Jawaban A (Teks)',
            'F1' => 'Media Jawaban A (Opsional)',
            'G1' => 'Jawaban B (Teks)',
            'H1' => 'Media Jawaban B (Opsional)',
            'I1' => 'Jawaban C (Teks)',
            'J1' => 'Media Jawaban C (Opsional)',
            'K1' => 'Jawaban D (Teks)',
            'L1' => 'Media Jawaban D (Opsional)',
            'M1' => 'Jawaban E (Teks)',
            'N1' => 'Media Jawaban E (Opsional)',
            'O1' => 'Kunci Jawaban Benar',
            'P1' => 'Nilai Point (Angka)',
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
            'Pilihan Ganda', 'Apa arti kata "Hakgyo"?', '', '',
            'Rumah Tangga', '', 'Sekolah', '', 'Stasiun', '', 'Kantor', '', '', '',
            'B', '10',
        ], null, 'A2');

        // Contoh baris 3 — Multiple Choice
        $sheet->fromArray([
            'Multiple Choice', 'Manakah yang termasuk kata benda dalam bahasa Korea?', '', '',
            'Mokta (먹다)', '', 'Chaek (책)', '', 'Yeonpil (연필)', '', 'Masida (마시다)', '', '', '',
            'B,C', '15',
        ], null, 'A3');

        // Contoh baris 4 — Essay
        $sheet->fromArray([
            'Essay', 'Sebutkan 3 alat transportasi dalam bahasa Korea!', '', '',
            '', '', '', '', '', '', '', '', '', '',
            '', '20',
        ], null, 'A4');

        // Contoh baris 5 — Audio/Choukai
        $sheet->fromArray([
            'Audio', 'Dengarkan audio dan pilih jawaban yang tepat.', '', 'suara-soal-10.mp3',
            'Pilih A', '', 'Pilih B', '', 'Pilih C', '', 'Pilih D', '', '', '',
            'C', '10',
        ], null, 'A5');

        // Contoh baris 6 — Pilihan Ganda Gambar
        $sheet->fromArray([
            'Pilihan Ganda Gambar', 'Manakah dari gambar berikut yang menunjukkan stasiun?', 'gambar-tanya.jpg', '',
            '', 'stasiun.jpg', '', 'kantor.png', '', 'pasar.jpg', '', 'mall.png', '', '',
            'A', '10',
        ], null, 'A6');

        // Contoh baris 7 — Short Answer
        $sheet->fromArray([
            'Short Answer', 'Apa nama ibukota Korea Selatan?', '', '',
            '', '', '', '', '', '', '', '', '', '',
            'Seoul|Seol|서울', '15',
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
        $guide->setTitle('CARA BACA PANDUAN');

        $guideRows = [
            ['BACA INI DULU SEBELUM MENGISI SOAL!'],
            [''],
            ['CARA MENGISI SETIAP KOLOM (Sangat Penting):'],
            ['Kolom A (Tipe Soal)', 'WAJIB DIISI! Ketik sesuai tipe yang dimau (contoh: Pilihan Ganda, Essay, Audio, dll). Lihat daftar valid di bawah.'],
            ['Kolom B (Pertanyaan)', 'Ketik pertanyaan soal di sini. Boleh ditarik memanjang.'],
            ['Kolom C & D (Gambar / Audio)', 'OPSIONAL. Jika soal bergambar atau bersuara, ketik *Nama File*-nya secara persis. Contoh: "soal1.jpg" atau "suara2.mp3". Ingat! Anda HARUS sudah meng-upload file zip audionya ke sistem melalui dashboard.'],
            ['Kolom E, G, I, K, M', 'Tuliskan teks jawaban A, B, C, D, E.'],
            ['Kolom F, H, J, L, N', 'OPSIONAL. Isikan *Nama File* hanya jika opsi jawabannya berupa gambar/audio.'],
            ['Kolom O (Kunci Jawaban)', 'Ketik HURUF dari jawaban yang benar (misal: A, B, atau C).'],
            ['Kolom P (Poin Nilai)', 'WAJIB DIISI! Ketik angka saja tanpa huruf (contoh: 10 atau 20).'],
            [''],
            ['--- PILIHAN KATA UNTUK KOLOM "TIPE SOAL" (Kolom A) ---'],
            ['→ Ketik "Pilihan Ganda" jika soal biasa (Hanya ada 1 jawaban benar). Kunci Jawaban: A, B, C, D, atau E.'],
            ['→ Ketik "Multiple Choice" jika ada banyak jawaban yang diklik. Kunci Jawaban isi berjejer pisah koma: A,B,C'],
            ['→ Ketik "Essay" jika jawaban bebas dari murid dan dinilai guru manual. Kunci Jawaban: KOSONGKAN/HAPUS.'],
            ['→ Ketik "Short Answer" jika isian singkat yang dinilai sistem otomatis. Kunci Jawaban: lihat bantuan di bawah.'],
            ['→ Ketik "Audio" jika ini soal Listening. Kolom D wajib diisi nama file mp3. Kunci Jawaban: A, B, C, D, atau E.'],
            ['→ Ketik "Pilihan Ganda Gambar" jika opsi A/B/C/D isinya gambar semua. Kunci Jawaban: A, B, C, D, atau E.'],
            [''],
            ['--- BANTUAN UNTUK SOAL ISIAN SINGKAT (Short Answer) ---'],
            ['Terkadang anak bisa typo/salah ketik! Oleh karena itu di Isian Singkat: '],
            ['1. Kalau hurufnya besar / kecil tidak akan disalahkan (contoh murid ketik seoul atau SEOUL tetap benar).'],
            ['2. Kalau murid beda 1-2 huruf tok, misal "Seol" padahal kuncinya "Seoul", maka sistem masih menganggapnya BENAR otomatis.'],
            ['3. Agar lebih aman, jika Anda punya banyak versi jawaban, MENGHUBUNGKANNYA cukup pakai garis lurus bertingkat | (ada di atas tombol enter).'],
            ['   Contoh ketik di Kolom O Kunci Jawaban: Seoul|Seol|서울'],
            [''],
            ['--- CATATAN AKHIR ---'],
            ['1. Jangan lupa hapus baris yang berwarna abu-abu (baris 2 sampai 7) sebelum ditaruh data soal asli.'],
            ['2. Huruf besar-kecil pada penamaan file gambar/audio "SANGAT BERPENGARUH". "mobil.jpg" beda dengan "Mobil.jpg".'],
        ];

        foreach ($guideRows as $i => $rowData) {
            $guide->fromArray($rowData, null, 'A' . ($i + 1));
        }

        // Style guide sheet headers
        $guide->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'E11D48']],
        ]);
        foreach (['A3', 'A12', 'A20', 'A27'] as $headerCell) {
            $guide->getStyle($headerCell)->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F172A']],
            ]);
        }
        // Style table header row
        $guide->getStyle('A4:A10')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
        ]);

        $guide->getColumnDimension('A')->setWidth(35);
        $guide->getColumnDimension('B')->setWidth(75);

        $spreadsheet->setActiveSheetIndex(0);

        $tmpFile = tempnam(sys_get_temp_dir(), 'lpk_template_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpFile);

        return response()->download($tmpFile, 'Template_Import_Soal_Admin.xlsx')->deleteFileAfterSend(true);
    }
}
