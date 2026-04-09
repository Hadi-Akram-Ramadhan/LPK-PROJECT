<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use App\Models\UjianPeserta;
use App\Models\JawabanMurid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitorUjianController extends Controller
{
    public function index()
    {
        $ujians = Ujian::where('guru_id', auth()->id())
            ->with(['guru'])
            ->withCount('pesertas')
            ->latest()
            ->paginate(10);
            
        return view('guru.monitor.index', compact('ujians'));
    }

    public function show(Ujian $ujian)
    {
        if ($ujian->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $pesertas = UjianPeserta::with(['user', 'cheatLogs'])
            ->where('ujian_id', $ujian->id)
            ->latest()
            ->paginate(20);

        // Menghitung jumlah soal essay pada ujian ini
        $essayCount = $ujian->soals()->where('tipe', 'essay')->count();
        
        return view('guru.monitor.show', compact('ujian', 'pesertas', 'essayCount'));
    }

    public function grade(UjianPeserta $ujian_peserta)
    {
        $ujian = $ujian_peserta->ujian;

        if ($ujian->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Ambil soal essay saja
        $soalEssays = $ujian->soals()->where('tipe', 'essay')->get();
        
        $jawabans = JawabanMurid::where('ujian_peserta_id', $ujian_peserta->id)
            ->whereIn('soal_id', $soalEssays->pluck('id'))
            ->get()
            ->keyBy('soal_id');

        // Hitung skor Pilihan Ganda saja (semua soal kecuali essay)
        $skorPG = JawabanMurid::where('ujian_peserta_id', $ujian_peserta->id)
            ->whereNotIn('soal_id', $soalEssays->pluck('id'))
            ->sum('poin_didapat');
            
        return view('guru.monitor.grade', compact('ujian_peserta', 'ujian', 'soalEssays', 'jawabans', 'skorPG'));
    }

    public function storeGrade(Request $request, UjianPeserta $ujian_peserta)
    {
        $ujian = $ujian_peserta->ujian;

        if ($ujian->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'poin' => 'required|array',
            'poin.*' => 'numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->poin as $soalId => $poin) {
                JawabanMurid::updateOrCreate(
                    [
                        'ujian_peserta_id' => $ujian_peserta->id,
                        'soal_id' => $soalId
                    ],
                    [
                        'poin_didapat' => $poin
                    ]
                );
            }

            // Hitung ulang total skor (PG + Essay)
            $totalSkor = JawabanMurid::where('ujian_peserta_id', $ujian_peserta->id)->sum('poin_didapat');
            $ujian_peserta->update(['skor' => $totalSkor]);

            DB::commit();
            
            return redirect()->route('guru.monitor.show', $ujian)->with('success', 'Skor essay berhasil disimpan dan nilai total telah diupdate.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    public function export(Ujian $ujian)
    {
        if ($ujian->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        // Eager load jawabanMurids.soal to calculate scores without N+1
        $pesertas = UjianPeserta::with(['user.kelas', 'jawabanMurids.soal'])
            ->where('ujian_id', $ujian->id)
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nilai Peserta');

        // Header Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']], // Tailwind Emerald-600
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        // Headers (Added Listening & Reading)
        $headers = ['No', 'Nama Murid', 'Kelas', 'Waktu Mulai', 'Waktu Selesai', 'Status', 'Listening', 'Reading', 'Total Skor'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $sheet->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Data
        $row = 2;
        foreach ($pesertas as $index => $p) {
            // Hitung rincian nilai
            $listening = $p->jawabanMurids->filter(function($j) {
                return $j->soal && $j->soal->tipe === 'audio';
            })->sum('poin_didapat');

            $reading = $p->jawabanMurids->filter(function($j) {
                return $j->soal && $j->soal->tipe !== 'audio';
            })->sum('poin_didapat');

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $p->user->name);
            $sheet->setCellValue('C' . $row, $p->user->kelas->nama ?? '-');
            $sheet->setCellValue('D' . $row, $p->mulai_at);
            $sheet->setCellValue('E' . $row, $p->selesai_at);
            $sheet->setCellValue('F' . $row, strtoupper($p->status));
            $sheet->setCellValue('G' . $row, $listening);
            $sheet->setCellValue('H' . $row, $reading);
            $sheet->setCellValue('I' . $row, $p->skor);
            
            // Alignments
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row . ':I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $row++;
        }

        // Borders
        $sheet->getStyle('A1:I' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Sanitize the filename for safe delivery
        $sanitizedJudul = preg_replace('/[^A-Za-z0-9_\-]/', '_', $ujian->judul);
        $filename = "Nilai_Ujian_" . $sanitizedJudul . "_" . date('Y-m-d') . ".xlsx";
        $tempPath = storage_path('app/public/' . $filename);

        // Aggressive buffer clearing
        while (ob_get_level()) {
            ob_end_clean();
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempPath);

        if (!file_exists($tempPath)) {
             return "Gagal membuat file export.";
        }

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Transfer-Encoding' => 'binary',
        ])->deleteFileAfterSend(true);
    }
}
