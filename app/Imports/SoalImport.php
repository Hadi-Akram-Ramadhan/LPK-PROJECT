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
    private int $paketSoalId;

    public function __construct(int $paketSoalId)
    {
        $this->paketSoalId = $paketSoalId;
    }

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
            if (Str::contains($tipeRaw, 'ganda audio')) {
                $tipeEnum = 'pilihan_ganda_audio';
            } elseif (Str::contains($tipeRaw, 'ganda gambar')) {
                $tipeEnum = 'pilihan_ganda_gambar';
            } elseif (Str::contains($tipeRaw, 'multiple')) {
                $tipeEnum = 'multiple_choice';
            } elseif (Str::contains($tipeRaw, 'short') || Str::contains($tipeRaw, 'singkat')) {
                $tipeEnum = 'short_answer';
            } elseif (Str::contains($tipeRaw, 'essay') || Str::contains($tipeRaw, 'esai')) {
                $tipeEnum = 'essay';
            } elseif (Str::contains($tipeRaw, 'audio') || Str::contains($tipeRaw, 'choukai')) {
                $tipeEnum = 'audio';
            }

            // Kolom:
            // 0=Tipe, 1=Pertanyaan, 2=Gambar Soal, 3=Audio Soal,
            // 4=Opsi A, 5=Media A, 6=Opsi B, 7=Media B, 8=Opsi C, 9=Media C, 10=Opsi D, 11=Media D, 12=Opsi E, 13=Media E,
            // 14=Jawaban Benar, 15=Poin
            
            $poin         = max(1, (int) ($row[15] ?? 10));
            $gambarRaw    = trim($row[2] ?? '');
            $audioRaw     = trim($row[3] ?? '');
            $jawabanKunci = trim($row[14] ?? ''); // Column O: used as kunci for short_answer

            $gambarPath = $gambarRaw !== '' ? 'gambar/' . $gambarRaw : null;
            $audioPath  = $audioRaw !== '' ? 'audio/' . $audioRaw : null;

            DB::beginTransaction();
            try {
                $soal = Soal::create([
                    'guru_id'       => auth()->id(),
                    'paket_soal_id' => $this->paketSoalId,
                    'tipe'          => $tipeEnum,
                    'pertanyaan'    => $pertanyaan,
                    'poin'          => $poin,
                    'audio_path'    => $audioPath,
                    'gambar_path'   => $gambarPath,
                    'jawaban_kunci' => $tipeEnum === 'short_answer' ? $jawabanKunci : null,
                ]);

                // Only create PilihanJawaban for types that use them (not essay or short_answer)
                if (!in_array($tipeEnum, ['essay', 'short_answer'])) {
                    $opsi = [
                        'A' => ['teks' => trim($row[4] ?? ''),  'media' => trim($row[5] ?? '')],
                        'B' => ['teks' => trim($row[6] ?? ''),  'media' => trim($row[7] ?? '')],
                        'C' => ['teks' => trim($row[8] ?? ''),  'media' => trim($row[9] ?? '')],
                        'D' => ['teks' => trim($row[10] ?? ''), 'media' => trim($row[11] ?? '')],
                        'E' => ['teks' => trim($row[12] ?? ''), 'media' => trim($row[13] ?? '')],
                    ];

                    $jawabanBenarRaw   = Str::upper(trim($row[14] ?? ''));
                    $jawabanBenarArray = array_map('trim', explode(',', $jawabanBenarRaw));

                    foreach ($opsi as $huruf => $dataOpsi) {
                        $teksOpsi = $dataOpsi['teks'];
                        $mediaRaw = $dataOpsi['media'];
                        
                        if ($teksOpsi !== '' || $mediaRaw !== '') {
                            $mediaTipe = null;
                            $mediaPath = null;
                            
                            if ($mediaRaw !== '') {
                                $mediaTipe = ($tipeEnum === 'pilihan_ganda_audio') ? 'audio' : 'gambar';
                                $mediaPath = $mediaTipe . '/' . $mediaRaw;
                            }
                            
                            PilihanJawaban::create([
                                'soal_id'    => $soal->id,
                                'teks'       => $teksOpsi,
                                'media_path' => $mediaPath,
                                'media_tipe' => $mediaTipe,
                                'is_benar'   => in_array($huruf, $jawabanBenarArray),
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
