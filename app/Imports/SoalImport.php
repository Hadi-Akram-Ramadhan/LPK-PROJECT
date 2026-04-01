<?php

namespace App\Imports;

use App\Models\Soal;
use App\Models\PilihanJawaban;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SoalImport
{
    private int $sukses = 0;
    private int $gagal  = 0;

    /**
     * Loop tiap baris dari file Excel yang diberikan (path sementara).
     */
    public function import(string $filePath): void
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet       = $spreadsheet->getActiveSheet();
        $rows        = $sheet->toArray(null, true, true, false); // 0-indexed array

        // Baris 0 = header, mulai dari baris 1
        foreach (array_slice($rows, 1) as $row) {
            // Kolom: 0=Tipe, 1=Pertanyaan, 2=OpsiA, 3=B, 4=C, 5=D, 6=E, 7=Jawaban, 8=Audio, 9=Poin
            $tipeRaw    = Str::lower(trim($row[0] ?? ''));
            $pertanyaan = trim($row[1] ?? '');

            // Skip baris kosong
            if (empty($pertanyaan) || empty($tipeRaw)) {
                continue;
            }

            // Tentukan tipe enum
            $tipeEnum = 'pilihan_ganda';
            if (Str::contains($tipeRaw, 'multiple'))                              $tipeEnum = 'multiple_choice';
            elseif (Str::contains($tipeRaw, 'essay') || Str::contains($tipeRaw, 'esai')) $tipeEnum = 'essay';
            elseif (Str::contains($tipeRaw, 'audio') || Str::contains($tipeRaw, 'choukai')) $tipeEnum = 'audio';

            $poin      = max(1, (int) ($row[9] ?? 10));
            $audioFile = trim($row[8] ?? '');
            $audioPath = $audioFile !== '' ? 'audio/' . $audioFile : null;

            DB::beginTransaction();
            try {
                $soal = Soal::create([
                    'guru_id'    => auth()->id(),
                    'tipe'       => $tipeEnum,
                    'pertanyaan' => $pertanyaan,
                    'poin'       => $poin,
                    'audio_path' => $audioPath,
                ]);

                if ($tipeEnum !== 'essay') {
                    $opsi = [
                        'A' => trim($row[2] ?? ''),
                        'B' => trim($row[3] ?? ''),
                        'C' => trim($row[4] ?? ''),
                        'D' => trim($row[5] ?? ''),
                        'E' => trim($row[6] ?? ''),
                    ];

                    $jawabanBenarRaw   = Str::upper(trim($row[7] ?? ''));
                    $jawabanBenarArray = array_map('trim', explode(',', $jawabanBenarRaw));

                    foreach ($opsi as $huruf => $teksOpsi) {
                        if ($teksOpsi !== '') {
                            PilihanJawaban::create([
                                'soal_id'  => $soal->id,
                                'teks'     => $teksOpsi,
                                'is_benar' => in_array($huruf, $jawabanBenarArray),
                            ]);
                        }
                    }
                }

                DB::commit();
                $this->sukses++;
            } catch (\Throwable $e) {
                DB::rollBack();
                $this->gagal++;
            }
        }
    }

    public function getSummary(): array
    {
        return ['sukses' => $this->sukses, 'gagal' => $this->gagal];
    }
}
