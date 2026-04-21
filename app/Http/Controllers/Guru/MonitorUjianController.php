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
            abort(404);
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
            abort(404);
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

    public function pesertaDetail(Ujian $ujian, UjianPeserta $ujian_peserta)
    {
        if ($ujian->guru_id !== auth()->id() || $ujian_peserta->ujian_id !== $ujian->id) {
            abort(404);
        }

        $ujian_peserta->load(['user.kelas', 'jawabanMurids.soal']);

        $listening = $ujian_peserta->jawabanMurids->filter(function($j) {
            return $j->soal && in_array($j->soal->tipe, ['audio', 'pilihan_ganda_audio']);
        });

        $reading = $ujian_peserta->jawabanMurids->filter(function($j) {
            return $j->soal && !in_array($j->soal->tipe, ['audio', 'pilihan_ganda_audio']);
        });

        $skorListening = $listening->sum('poin_didapat');
        $skorReading = $reading->sum('poin_didapat');

        // Untuk view, kita butuhkan $ujian, $ujian_peserta, $listening(jawabans), $reading(jawabans), scores
        return view('guru.monitor.peserta_detail', compact('ujian', 'ujian_peserta', 'listening', 'reading', 'skorListening', 'skorReading'));
    }

    public function storeGrade(Request $request, UjianPeserta $ujian_peserta)
    {
        $ujian = $ujian_peserta->ujian;

        if ($ujian->guru_id !== auth()->id()) {
            abort(404);
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
            abort(404);
        }
        
        $pesertas = UjianPeserta::with(['user.kelas', 'jawabanMurids.soal'])
            ->where('ujian_id', $ujian->id)
            ->get();

        // Ambil semua soal terkait ujian ini, diurutkan agar konsisten dengan tampilan
        $soals = $ujian->soals()->orderBy('id')->get();
        // Pemetaan ID soal ke Nomor urut Soal
        $soalMap = [];
        foreach ($soals as $idx => $s) {
            $soalMap[$s->id] = $idx + 1;
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        // ── SHEET 1: RINGKASAN NILAI ──
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Summary Nilai');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        $headers1 = ['No', 'Nama Murid', 'Kelas', 'Waktu Mulai', 'Waktu Selesai', 'Status', 'Listening', 'Reading', 'Total Skor', 'Tes Buta Warna'];
        $column = 'A';
        foreach ($headers1 as $header) {
            $sheet1->setCellValue($column . '1', $header);
            $sheet1->getColumnDimension($column)->setAutoSize(true);
            $column++;
        }
        $sheet1->getStyle('A1:J1')->applyFromArray($headerStyle);

        $row1 = 2;
        foreach ($pesertas as $index => $p) {
            $listening = $p->jawabanMurids->filter(function($j) {
                return $j->soal && in_array($j->soal->tipe, ['audio', 'pilihan_ganda_audio']);
            })->sum('poin_didapat');

            $reading = $p->jawabanMurids->filter(function($j) {
                return $j->soal && !in_array($j->soal->tipe, ['audio', 'pilihan_ganda_audio']);
            })->sum('poin_didapat');

            $sheet1->setCellValue('A' . $row1, $index + 1);
            $sheet1->setCellValue('B' . $row1, $p->user->name ?? '-');
            $sheet1->setCellValue('C' . $row1, $p->user->kelas->nama ?? '-');
            $sheet1->setCellValue('D' . $row1, $p->mulai_at);
            $sheet1->setCellValue('E' . $row1, $p->selesai_at);
            $sheet1->setCellValue('F' . $row1, strtoupper($p->status));
            $sheet1->setCellValue('G' . $row1, $listening);
            $sheet1->setCellValue('H' . $row1, $reading);
            $sheet1->setCellValue('I' . $row1, $p->skor);
            $sheet1->setCellValue('J' . $row1, $p->hasil_buta_warna ?? '-');
            
            $sheet1->getStyle('A' . $row1)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet1->getStyle('F' . $row1 . ':J' . $row1)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $row1++;
        }
        $sheet1->getStyle('A1:J' . ($row1 - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);


        // ── SHEET 2: DETAIL JAWABAN PER SOAL ──
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Detail Jawaban');

        $detailHeaderStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']], // Blue
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        // Baris 1: Header (No, Nama, Soal 1..N)
        $sheet2->setCellValue('A1', 'No');
        $sheet2->setCellValue('B1', 'Nama Murid');
        $sheet2->getColumnDimension('A')->setWidth(5);
        $sheet2->getColumnDimension('B')->setWidth(30);

        $col2 = 'C';
        foreach ($soals as $idx => $s) {
            $sheet2->setCellValue($col2 . '1', 'Soal ' . ($idx + 1));
            // Tambahkan tooltip/keterangan tipe jika perlu
            $sheet2->getColumnDimension($col2)->setWidth(10);
            $col2++;
        }
        // Style header baris 1
        $lastCol = $col2;
        $sheet2->getStyle('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($soals) + 2) . '1')->applyFromArray($detailHeaderStyle);

        // Baris 2+: Data murid dan poin
        $row2 = 2;
        foreach ($pesertas as $index => $p) {
            $sheet2->setCellValue('A' . $row2, $index + 1);
            $sheet2->setCellValue('B' . $row2, $p->user->name ?? '-');

            // Susun array jawaban berdasar ID soal 
            $ansBySoal = [];
            foreach ($p->jawabanMurids as $jaw) {
                $ansBySoal[$jaw->soal_id] = $jaw;
            }

            $currentColIdx = 3; // Mulai dari C
            foreach ($soals as $s) {
                $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($currentColIdx);
                
                $poin = 0;
                $isBenar = false;
                if (isset($ansBySoal[$s->id])) {
                    $jawabanData = $ansBySoal[$s->id];
                    $poin = $jawabanData->poin_didapat;
                    $isBenar = $poin == $s->poin;
                }

                $sheet2->setCellValue($colStr . $row2, floatval($poin));
                $cellStyle = $sheet2->getStyle($colStr . $row2);
                $cellStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Warna cell: Hijau muda (benar penuh), kuning (sebagian), merah muda (salah / 0)
                $fillColor = 'FFD1D5'; // Merah muda default (salah)
                if ($poin > 0 && $poin < $s->poin) {
                    $fillColor = 'FEF08A'; // Kuning (partial)
                } elseif ($poin == $s->poin) {
                    $fillColor = 'BBF7D0'; // Hijau muda (benar penuh)
                }

                $cellStyle->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($fillColor);

                $currentColIdx++;
            }
            $row2++;
        }
        // Border
        $sheet2->getStyle('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($soals) + 2) . ($row2 - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        $spreadsheet->setActiveSheetIndex(0);

        // Sanitize
        $sanitizedJudul = preg_replace('/[^A-Za-z0-9_\-]/', '_', $ujian->judul);
        $filename = "Nilai_Ujian_" . $sanitizedJudul . "_" . date('Y-m-d') . ".xlsx";
        $tempPath = storage_path('app/public/' . $filename);

        while (ob_get_level()) { ob_end_clean(); }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempPath);

        if (!file_exists($tempPath)) { return "Gagal membuat file export."; }

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Transfer-Encoding' => 'binary',
        ])->deleteFileAfterSend(true);
    }
}
