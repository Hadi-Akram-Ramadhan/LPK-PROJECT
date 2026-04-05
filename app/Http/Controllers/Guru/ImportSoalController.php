<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Imports\SoalImport;
use App\Models\PaketSoal;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ImportSoalController extends Controller
{
    public function index()
    {
        $paketSoals = PaketSoal::where('guru_id', auth()->id())->latest()->get();
        return view('guru.import.index', compact('paketSoals'));
    }

    // ── Download Template ─────────────────────────────────────
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Soal');

        // Header row
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

        // Header style
        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'wrapText'   => true,
            ],
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

        // Contoh baris 7 — Short Answer (NEW)
        $sheet->fromArray([
            'Short Answer', 'Apa nama ibukota Jepang?', '', '',
            '', '', '', '', '', '', '', '', '', '',
            'Tokyo|Tokio|東京', '15',
        ], null, 'A7');

        // Zebra row styling for example rows
        $sheet->getStyle('A2:P7')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);
        // Highlight short_answer row
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
            ['4. Sheet Panduan ini TIDAK dibaca oleh sistem. Sistem hanya membaca sheet pertama.'],
        ];

        foreach ($guideRows as $i => $rowData) {
            $guide->fromArray($rowData, null, 'A' . ($i + 1));
        }

        // Style guide sheet
        $guide->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '4F46E5']],
        ]);
        foreach (['A3', 'A17', 'A27', 'A47'] as $hCell) {
            $guide->getStyle($hCell)->applyFromArray([
                'font' => ['bold' => true, 'size' => 12],
            ]);
        }
        $guide->getStyle('A4:C4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
        ]);
        $guide->getStyle('A18:C18')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
        ]);
        $guide->getStyle('A22:C22')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
            'font' => ['bold' => true, 'color' => ['rgb' => '4F46E5']],
        ]);
        $guide->getColumnDimension('A')->setWidth(25);
        $guide->getColumnDimension('B')->setWidth(55);
        $guide->getColumnDimension('C')->setWidth(70);

        $spreadsheet->setActiveSheetIndex(0);

        // Tulis ke temp file
        $tmpFile  = tempnam(sys_get_temp_dir(), 'lpk_template_') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $writer->save($tmpFile);

        $fileName = 'Template_Import_Soal_LPK_' . date('Y-m-d') . '.xlsx';

        return response()->download($tmpFile, $fileName)->deleteFileAfterSend(true);
    }



    public function store(Request $request)
    {
        $request->validate([
            'paket_soal_id' => 'required|exists:paket_soals,id',
            'file_excel'    => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'paket_soal_id.required' => 'Pilih paket soal tujuan.',
            'file_excel.required'    => 'File Excel wajib diunggah.',
            'file_excel.mimes'       => 'Format harus xlsx, xls, atau csv.',
            'file_excel.max'         => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            // Simpan file ke temp path agar bisa dibaca PhpSpreadsheet
            $tmpPath = $request->file('file_excel')->getRealPath();

            $import = new SoalImport($request->paket_soal_id);
            $import->import($tmpPath);

            $summary = $import->getSummary();

            if ($summary['sukses'] > 0) {
                $msg = "{$summary['sukses']} soal berhasil diimport.";
                if ($summary['gagal'] > 0) {
                    $msg .= " {$summary['gagal']} baris diabaikan (format tidak valid).";
                }
                return redirect()->route('guru.soal.index')->with('success', $msg);
            }

            return back()->with('error', 'Tidak ada soal yang berhasil diimport. Periksa format file Anda.');

        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat membaca file: ' . $e->getMessage());
        }
    }
}
