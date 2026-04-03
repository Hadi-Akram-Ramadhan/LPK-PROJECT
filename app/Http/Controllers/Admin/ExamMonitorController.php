<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use Illuminate\Http\Request;

class ExamMonitorController extends Controller
{
    public function index()
    {
        $ujians = Ujian::with(['guru', 'pesertas.user'])
            ->withCount('pesertas')
            ->latest()
            ->paginate(15);
            
        return view('admin.exams.index', compact('ujians'));
    }

    public function show(Ujian $ujian)
    {
        $pesertas = \App\Models\UjianPeserta::with('user.kelas')
            ->where('ujian_id', $ujian->id)
            ->latest()
            ->paginate(30);

        return view('admin.exams.show', compact('ujian', 'pesertas'));
    }

    public function export(Ujian $ujian)
    {
        $pesertas = \App\Models\UjianPeserta::with('user.kelas')
            ->where('ujian_id', $ujian->id)
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nilai Ujian');

        // Header Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']], // Tailwind Indigo-600
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        // Headers
        $headers = ['No', 'Nama Murid', 'Kelas', 'Waktu Mulai', 'Waktu Selesai', 'Status', 'Skor'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        // Data
        $row = 2;
        foreach ($pesertas as $index => $p) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $p->user->name);
            $sheet->setCellValue('C' . $row, $p->user->kelas->nama ?? '-');
            $sheet->setCellValue('D' . $row, $p->mulai_at);
            $sheet->setCellValue('E' . $row, $p->selesai_at);
            $sheet->setCellValue('F' . $row, strtoupper($p->status));
            $sheet->setCellValue('G' . $row, $p->skor);
            
            // Center alignment for some columns
            $sheet->getStyle('A' . $row . ':A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row . ':G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $row++;
        }

        // Final Borders
        $sheet->getStyle('A1:G' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        $filename = "Nilai_Ujian_" . str_replace(' ', '_', $ujian->judul) . "_" . date('Y-m-d') . ".xlsx";
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
