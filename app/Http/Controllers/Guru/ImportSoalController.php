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
            'A1' => 'Tipe Soal (Wajib)',
            'B1' => 'Tuliskan Pertanyaan / Soalnya',
            'C1' => 'Nama File Gambar (Opsional)',
            'D1' => 'Nama File Audio/Suara (Opsional)',
            'E1' => 'Jawaban A (Maks 300)',
            'F1' => 'Media A (Opsional)',
            'G1' => 'Jawaban B (Maks 300)',
            'H1' => 'Media B (Opsional)',
            'I1' => 'Jawaban C (Maks 300)',
            'J1' => 'Media C (Opsional)',
            'K1' => 'Jawaban D (Maks 300)',
            'L1' => 'Media D (Opsional)',
            'M1' => 'Jawaban E (Maks 300)',
            'N1' => 'Media E (Opsional)',
            'O1' => 'Kunci Jawaban Benar',
            'P1' => 'Nilai Point (Angka)',
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
        
        // Contoh baris 6 - Pilihan Ganda Gambar
        $sheet->fromArray([
            'Pilihan Ganda Gambar', 'Manakah dari gambar berikut yang menunjukkan stasiun?', 'gambar-tanya.jpg', '',
            '', 'stasiun.jpg', '', 'kantor.png', '', 'pasar.jpg', '', 'mall.png', '', '',
            'A', '10',
        ], null, 'A6');

        // Contoh baris 7 - Pilihan Ganda Audio (NEW)
        $sheet->fromArray([
            'Pilihan Ganda Audio', 'Dengarkan suara dan pilih benda yang dimaksud!', '', 'soal-instruksi.mp3',
            '', 'suara-a.mp3', '', 'suara-b.mp3', '', 'suara-c.mp3', '', 'suara-d.mp3', '', '',
            'B', '15',
        ], null, 'A7');

        // Contoh baris 8 — Short Answer (NEW)
        $sheet->fromArray([
            'Short Answer', 'Apa nama ibukota Korea Selatan?', '', '',
            '', '', '', '', '', '', '', '', '', '',
            'Seoul|Seol|서울', '15',
        ], null, 'A8');

        // Contoh baris 9 — Matching (NEW)
        $sheet->fromArray([
            'Matching', 'Jodohkan bahasa Indonesia dengan Korea!', '', '',
            'Pagi', 'Achim', 'Siang', 'Jeomsim', 'Malam', 'Jeonyeok', 'Besok', 'Naeil', 'Lusa', 'Morae',
            '', '15',
        ], null, 'A9');

        // Zebra row styling for example rows
        $sheet->getStyle('A2:P9')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);
        // Highlight short_answer row
        $sheet->getStyle('A8:P9')->applyFromArray([
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
            ['Kolom B (Pertanyaan)', 'WAJIB DIISI! Maks 2000 karakter. Jika lebih akan otomatis terpotong saat diimport.'],
            ['Kolom C & D (Gambar / Audio)', 'OPSIONAL. Jika soal bergambar atau bersuara, ketik *Nama File*-nya secara persis. Contoh: "soal1.jpg" atau "suara2.mp3". Ingat! Anda HARUS sudah meng-upload file zip audionya ke sistem melalui dashboard.'],
            ['Kolom E, G, I, K, M', 'Maks 300 karakter per opsi. Untuk Matching: Maks 200 karakter.'],
            ['Kolom F, H, J, L, N', 'OPSIONAL. Matching Sisi Kanan: Maks 200 karakter.'],
            ['Kolom O (Kunci Jawaban)', 'Ketik HURUF dari jawaban yang benar (misal: A, B, atau C).'],
            ['Kolom P (Poin Nilai)', 'WAJIB DIISI! Ketik angka saja tanpa huruf (contoh: 10 atau 20).'],
            [''],
            ['--- PILIHAN KATA UNTUK KOLOM "TIPE SOAL" (Kolom A) ---'],
            ['→ Ketik "Pilihan Ganda" jika soal biasa (Hanya ada 1 jawaban benar). Kunci Jawaban: A, B, C, D, atau E.'],
            ['→ Ketik "Multiple Choice" jika ada banyak jawaban yang diklik. Kunci Jawaban isi berjejer pisah koma: A,B,C'],
            ['→ Ketik "Essay" jika jawaban bebas dari murid dan dinilai guru manual. Kunci Jawaban: KOSONGKAN/HAPUS.'],
            ['→ Ketik "Short Answer" jika isian singkat yang dinilai sistem otomatis. Kunci Jawaban: lihat bantuan di bawah.'],
            ['→ Ketik "Matching" jika soal menjodohkan. Kunci Jawaban: KOSONGKAN. Pasangan diisi berdampingan (Kolom E dg F, G dg H, I dg J).'],
            ['→ Ketik "Audio" jika ini soal Listening. Kolom D wajib diisi nama file mp3. Kunci Jawaban: A, B, C, D, atau E.'],
            ['→ Ketik "Pilihan Ganda Gambar" jika opsi A/B/C/D isinya gambar semua. Kunci Jawaban: A, B, C, D, atau E.'],
            ['→ Ketik "Pilihan Ganda Audio" jika opsi A/B/C/D isinya suara semua. Kunci Jawaban: A, B, C, D, atau E.'],
            [''],
            ['--- BANTUAN UNTUK SOAL ISIAN SINGKAT (Short Answer) ---'],
            ['Terkadang anak bisa typo/salah ketik! Oleh karena itu di Isian Singkat: '],
            ['1. Kalau hurufnya besar / kecil tidak akan disalahkan (contoh murid ketik seoul atau SEOUL tetap benar).'],
            ['2. Kalau murid beda 1-2 huruf tok, misal "Seol" padahal kuncinya "Seoul", maka sistem masih menganggapnya BENAR otomatis.'],
            ['3. Agar lebih aman, jika Anda punya banyak versi jawaban, MENGHUBUNGKANNYA cukup pakai garis lurus bertingkat | (ada di atas tombol enter).'],
            ['   Contoh ketik di Kolom O Kunci Jawaban: Seoul|Seol|서울'],
            [''],
            ['--- CATATAN AKHIR ---'],
            ['1. Jangan lupa hapus baris yang berwarna abu-abu (baris 2 sampai 9) sebelum ditaruh data soal asli.'],
            ['2. Huruf besar-kecil pada penamaan file gambar/audio "SANGAT BERPENGARUH". "mobil.jpg" beda dengan "Mobil.jpg" atau "mobil.png". Pada Matching, jika isian berakhiran .jpg/.png otomatis diubah jadi gambar.'],
            ['3. SATU FILE EXCEL UNTUK SATU UJIAN. Jangan campur soal Ujian Seoul dan soal Ujian Busan dalam satu file Excel yang sama.'],
        ];

        foreach ($guideRows as $i => $rowData) {
            $guide->fromArray($rowData, null, 'A' . ($i + 1));
        }

        // Style guide sheet
        $guide->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'E11D48']],
        ]);
        foreach (['A3', 'A12', 'A20', 'A27'] as $hCell) {
            $guide->getStyle($hCell)->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F172A']],
            ]);
        }
        $guide->getStyle('A4:A10')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
        ]);
        $guide->getColumnDimension('A')->setWidth(35);
        $guide->getColumnDimension('B')->setWidth(75);

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
            'file_excel'    => 'required|max:5120|mimes:xlsx,xls,csv,zip|mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,text/plain,application/csv,application/x-csv,application/zip,application/octet-stream',
        ], [
            'paket_soal_id.required' => 'Pilih paket soal tujuan.',
            'file_excel.required'    => 'File Excel wajib diunggah.',
            'file_excel.mimes'       => 'Format tidak valid. Pastikan file berakhiran .xlsx, .xls, atau .csv.',
            'file_excel.mimetypes'   => 'Tipe berkas tidak didukung atau terdeteksi salah oleh sistem.',
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
            return back()->with('error', 'Terjadi kesalahan saat mengimport soal. Pastikan format file benar.');
        }
    }
}
