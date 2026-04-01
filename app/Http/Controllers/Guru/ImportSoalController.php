<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Imports\SoalImport;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ImportSoalController extends Controller
{
    public function index()
    {
        return view('guru.import.index');
    }

    // ── Download Template ─────────────────────────────────────
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Soal');

        // Header row
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

        // Header style
        $sheet->getStyle('A1:J1')->applyFromArray([
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

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Contoh baris 2 — Pilihan Ganda
        $sheet->fromArray([
            'Pilihan Ganda', 'Apa arti kata "Gakkou"?',
            'Rumah Tangga', 'Sekolah', 'Stasiun', 'Kantor', '',
            'B', '', '10',
        ], null, 'A2');

        // Contoh baris 3 — Multiple Choice
        $sheet->fromArray([
            'Multiple Choice', 'Manakah yang termasuk kata benda dalam bahasa Jepang?',
            'Nomi (飲む)', 'Hon (本)', 'Enpitsu (鉛筆)', 'Taberu (食べる)', '',
            'B,C', '', '15',
        ], null, 'A3');

        // Contoh baris 4 — Essay
        $sheet->fromArray([
            'Essay', 'Sebutkan 3 alat transportasi dalam bahasa Jepang!',
            '', '', '', '', '', '', '', '20',
        ], null, 'A4');

        // Contoh baris 5 — Audio/Choukai
        $sheet->fromArray([
            'Audio', 'Dengarkan audio dan pilih jawaban yang tepat.',
            'Pilih A', 'Pilih B', 'Pilih C', 'Pilih D', '',
            'C', 'choukai-n4-part1.mp3', '10',
        ], null, 'A5');

        // Zebra row styling for example rows
        $sheet->getStyle('A2:J5')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);

        // Tulis ke temp file
        $tmpFile  = tempnam(sys_get_temp_dir(), 'lpk_template_') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $writer->save($tmpFile);

        $fileName = 'Template_Import_Soal_LPK_' . date('Y-m-d') . '.xlsx';

        return response()->download($tmpFile, $fileName)->deleteFileAfterSend(true);
    }

    // ── Process Upload ────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file_excel.required' => 'File Excel wajib diunggah.',
            'file_excel.mimes'    => 'Format harus xlsx, xls, atau csv.',
            'file_excel.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            // Simpan file ke temp path agar bisa dibaca PhpSpreadsheet
            $tmpPath = $request->file('file_excel')->getRealPath();

            $import = new SoalImport();
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
