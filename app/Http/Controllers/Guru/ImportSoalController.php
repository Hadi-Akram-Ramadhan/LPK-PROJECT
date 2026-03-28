<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SoalImport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

class ImportSoalController extends Controller
{
    public function index()
    {
        return view('guru.import.index');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'Tipe Soal (Pilihan Ganda/Multiple Choice/Essay/Audio)');
        $sheet->setCellValue('B1', 'Teks Pertanyaan');
        $sheet->setCellValue('C1', 'Opsi A');
        $sheet->setCellValue('D1', 'Opsi B');
        $sheet->setCellValue('E1', 'Opsi C');
        $sheet->setCellValue('F1', 'Opsi D');
        $sheet->setCellValue('G1', 'Opsi E (Opsional)');
        $sheet->setCellValue('H1', 'Jawaban Benar (A/B/C/D/E, Pisahkan koma jika Multiple Choice)');
        $sheet->setCellValue('I1', 'File Audio (Cth: choukai_part1.mp3, kosongkan jika bukan Audio)');
        $sheet->setCellValue('J1', 'Poin / Bobot Nilai');

        // Style
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        foreach(range('A','J') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Contoh Data 1 (Pilihan Ganda)
        $sheet->setCellValue('A2', 'Pilihan Ganda');
        $sheet->setCellValue('B2', 'Apa arti kosa kata "Gakkou"?');
        $sheet->setCellValue('C2', 'Rumah Tangga');
        $sheet->setCellValue('D2', 'Sekolah');
        $sheet->setCellValue('E2', 'Stasiun');
        $sheet->setCellValue('F2', 'Kantor');
        $sheet->setCellValue('H2', 'B');
        $sheet->setCellValue('J2', '10');

        // Contoh Data 2 (Essay)
        $sheet->setCellValue('A3', 'Essay');
        $sheet->setCellValue('B3', 'Sebutkan 3 macam alat transportasi dalam bahasa Jepang!');
        $sheet->setCellValue('J3', '20');

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Template_Import_Soal_LPK_' . date('Y-m-d') . '.xlsx';
        
        // Simpan sementara
        $temp_file = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($temp_file);
        
        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120'
        ], [
            'file_excel.required' => 'File Excel wajib diunggah',
            'file_excel.mimes' => 'Format harus xlsx, xls, atau csv'
        ]);

        try {
            $import = new SoalImport();
            Excel::import($import, $request->file('file_excel'));
            
            $summary = $import->getSummary();
            
            if ($summary['sukses'] > 0) {
                return redirect()->route('guru.soal.index')->with('success', "Berhasil import! {$summary['sukses']} soal ditambahkan. " . ($summary['gagal'] > 0 ? "Namun {$summary['gagal']} soal/baris gagal diimport karena format tidak valid." : 'Semua baris valid.'));
            } else {
                return back()->with('error', "Gagal import. Tidak ada baris yang memenuhi standar format atau dokumen kosong.");
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
