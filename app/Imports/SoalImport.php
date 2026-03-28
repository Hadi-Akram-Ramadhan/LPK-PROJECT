<?php

namespace App\Imports;

use App\Models\Soal;
use App\Models\PilihanJawaban;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SoalImport implements ToCollection
{
    private $sukses = 0;
    private $gagal = 0;

    public function collection(Collection $rows)
    {
        // Skip header baris ke-1, tp karena gak pakai WithHeadingRow gpp kita manually skip baris pertama
        $isHeader = true;

        foreach ($rows as $row) {
            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            // A: Tipe Soal
            // B: Pertanyaan
            // C: Opsi A
            // D: Opsi B
            // E: Opsi C
            // F: Opsi D
            // G: Opsi E
            // H: Jawaban Benar (A/B/C/D/E)
            // I: Audio File
            // J: Poin

            $tipeRaw = Str::lower(trim($row[0] ?? ''));
            $pertanyaan = trim($row[1] ?? '');
            
            // Skip baris kosong
            if (empty($pertanyaan) || empty($tipeRaw)) {
                continue;
            }

            // Tentukan Tipe
            $tipeEnum = 'pilihan_ganda';
            if (Str::contains($tipeRaw, 'multiple')) $tipeEnum = 'multiple_choice';
            elseif (Str::contains($tipeRaw, 'essay') || Str::contains($tipeRaw, 'esai')) $tipeEnum = 'essay';
            elseif (Str::contains($tipeRaw, 'audio') || Str::contains($tipeRaw, 'choukai')) $tipeEnum = 'audio';

            $poin = (int) ($row[9] ?? 10);
            $poin = $poin > 0 ? $poin : 10;

            $audioFile = trim($row[8] ?? '');
            $audioPath = empty($audioFile) ? null : 'audio/' . $audioFile;

            DB::beginTransaction();
            try {
                $soal = Soal::create([
                    'guru_id' => auth()->id(),
                    'tipe' => $tipeEnum,
                    'pertanyaan' => $pertanyaan,
                    'poin' => $poin,
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

                    $jawabanBenarRaw = Str::upper(trim($row[7] ?? ''));
                    $jawabanBenarArray = array_map('trim', explode(',', $jawabanBenarRaw));

                    foreach ($opsi as $huruf => $teksOpsi) {
                        if (!empty($teksOpsi)) {
                            PilihanJawaban::create([
                                'soal_id' => $soal->id,
                                'teks' => $teksOpsi,
                                'is_benar' => in_array($huruf, $jawabanBenarArray),
                            ]);
                        }
                    }
                }

                DB::commit();
                $this->sukses++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->gagal++;
            }
        }
    }

    public function getSummary()
    {
        return [
            'sukses' => $this->sukses,
            'gagal' => $this->gagal,
        ];
    }
}
