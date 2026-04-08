<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserImport
{
    private int $sukses    = 0;
    private int $terlewati = 0;
    private int $gagal     = 0;

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
            // Kolom: 0=Nama, 1=Email, 2=Password, 3=IDKelas
            $name     = trim($row[0] ?? '');
            $email    = trim($row[1] ?? '');
            $password = trim($row[2] ?? '');
            $kelasId  = $row[3] ?? null;

            // Skip baris yang benar-benar kosong (biasanya baris sisa di Excel)
            if (empty($name) && empty($email) && empty($password)) {
                continue;
            }

            // Jika ada yang kosong tapi tidak semua (data tidak lengkap), baru hitung Gagal
            if (empty($name) || empty($email) || empty($password)) {
                $this->gagal++;
                continue;
            }

            // Validasi format email wajib!
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->gagal++;
                continue;
            }

            // Cek apakah user sudah terdaftar di sistem (untuk skip daripada error)
            $existing = User::where('email', $email)->first();
            if ($existing) {
                $this->terlewati++;
                continue; // Lanjut ke user berikutnya tanpa error
            }

            DB::beginTransaction();
            try {
                User::create([
                    'name'     => $name,
                    'email'    => $email,
                    'password' => Hash::make($password),
                    'role'     => 'murid',
                    'kelas_id' => $kelasId ? (int)$kelasId : null,
                ]);

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
        return [
            'sukses'    => $this->sukses,
            'terlewati' => $this->terlewati,
            'gagal'     => $this->gagal
        ];
    }
}
