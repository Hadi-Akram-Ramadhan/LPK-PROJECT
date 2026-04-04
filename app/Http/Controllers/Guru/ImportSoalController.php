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

        // Zebra row styling for example rows
        $sheet->getStyle('A2:P6')->applyFromArray([
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
