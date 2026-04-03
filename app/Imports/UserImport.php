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
            // Kolom: 0=Nama, 1=Email, 2=Password, 3=NIS, 4=IDKelas
            $name     = trim($row[0] ?? '');
            $email    = trim($row[1] ?? '');
            $password = trim($row[2] ?? '');
            $nis      = trim($row[3] ?? '');
            $kelasId  = $row[4] ?? null;

            // Skip baris kosong atau tidak lengkap
            if (empty($name) || empty($email) || empty($password)) {
                continue;
            }

            // Validasi format email sederhana
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !Str::contains($email, '@')) {
                // If it's just a username, we'll try to use it as is if it's unique
            }

            DB::beginTransaction();
            try {
                // Check if user already exists
                $existing = User::where('email', $email)->first();
                if ($existing) {
                    throw new \Exception("Email already exists");
                }

                User::create([
                    'name'     => $name,
                    'email'    => $email,
                    'password' => Hash::make($password),
                    'role'     => 'murid',
                    'kelas_id' => $kelasId ? (int)$kelasId : null,
                    'nis'      => $nis ?: null,
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
        return ['sukses' => $this->sukses, 'gagal' => $this->gagal];
    }
}
