<?php

namespace App\Imports;

use App\Helpers\HtmlSanitizer;
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
            } elseif (Str::contains($tipeRaw, 'matching') || Str::contains($tipeRaw, 'pasang')) {
                $tipeEnum = 'matching';
            } elseif (Str::contains($tipeRaw, 'audio') || Str::contains($tipeRaw, 'choukai')) {
                $tipeEnum = 'audio';
            }

            // Kolom:
            // 0=Tipe, 1=Pertanyaan, 2=Gambar Soal, 3=Audio Soal,
            // 4=Opsi A/Pasang Kiri 1, 5=Media A/Pasang Kanan 1, 6=Opsi B/Pasang Kiri 2, 7=Media B/Pasang Kanan 2, ...
            // 14=Jawaban Benar, 15=Poin
            
            $poin         = max(1, (int) ($row[15] ?? 10));
            $gambarRaw    = trim($row[2] ?? '');
            $audioRaw     = trim($row[3] ?? '');
            $jawabanKunci = trim($row[14] ?? '');

            $gambarPath = $gambarRaw !== '' ? 'gambar/' . $gambarRaw : null;
            $audioPath  = $audioRaw !== '' ? 'audio/' . $audioRaw : null;

            DB::beginTransaction();
            try {
                $soal = Soal::create([
                    'guru_id'       => auth()->id(),
                    'paket_soal_id' => $this->paketSoalId,
                    'tipe'          => $tipeEnum,
                    'pertanyaan'    => Str::limit(HtmlSanitizer::clean($pertanyaan), 2000, ''),
                    'poin'          => $poin,
                    'audio_path'    => $audioPath,
                    'gambar_path'   => $gambarPath,
                    'jawaban_kunci' => $tipeEnum === 'short_answer' ? Str::limit($jawabanKunci, 300, '') : null,
                ]);

                if ($tipeEnum === 'matching') {
                    // Kolom E/F = Pasang 1, G/H = Pasang 2, I/J = Pasang 3, K/L = Pasang 4, M/N = Pasang 5
                    $pasangKoloms = [
                        ['kiri' => trim($row[4] ?? ''),  'kanan' => trim($row[5] ?? '')],
                        ['kiri' => trim($row[6] ?? ''),  'kanan' => trim($row[7] ?? '')],
                        ['kiri' => trim($row[8] ?? ''),  'kanan' => trim($row[9] ?? '')],
                        ['kiri' => trim($row[10] ?? ''), 'kanan' => trim($row[11] ?? '')],
                        ['kiri' => trim($row[12] ?? ''), 'kanan' => trim($row[13] ?? '')],
                    ];

                    foreach ($pasangKoloms as $pasang) {
                        if (empty($pasang['kiri']) && empty($pasang['kanan'])) continue;

                        // Cek apakah kiri/kanan adalah file gambar
                        $kiriIsGambar  = !empty($pasang['kiri']) && preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $pasang['kiri']);
                        $kananIsGambar = !empty($pasang['kanan']) && preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $pasang['kanan']);

                        $kiriVal  = $kiriIsGambar  ? 'gambar/' . $pasang['kiri']  : $pasang['kiri'];
                        $kananVal = $kananIsGambar ? 'gambar/' . $pasang['kanan'] : $pasang['kanan'];

                        if ($kiriIsGambar && $kananIsGambar) {
                            $mediaTipe = 'matching_gambar_keduanya';
                        } elseif ($kiriIsGambar) {
                            $mediaTipe = 'matching_gambar_kiri';
                        } elseif ($kananIsGambar) {
                            $mediaTipe = 'matching_gambar_kanan';
                        } else {
                            $mediaTipe = 'matching_teks';
                        }

                        PilihanJawaban::create([
                            'soal_id'    => $soal->id,
                            'teks'       => Str::limit($kiriVal, 200, ''),
                            'media_path' => Str::limit($kananVal, 200, ''),
                            'media_tipe' => $mediaTipe,
                            'is_benar'   => true,
                        ]);
                    }
                } elseif (!in_array($tipeEnum, ['essay', 'short_answer'])) {
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
                                'teks'       => Str::limit($teksOpsi, 300, ''),
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
