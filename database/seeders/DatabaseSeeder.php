<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat satu kelas dummy
        $kelas = Kelas::create([
            'nama' => 'Bahasa Jepang N4 - Reguler'
        ]);

        // Akun Admin
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@lpk.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Akun Guru
        User::create([
            'name' => 'Sensei Yamada',
            'email' => 'guru@lpk.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        // Akun Murid 1
        User::create([
            'name' => 'Siswa Budi',
            'email' => 'murid1@lpk.com',
            'password' => Hash::make('password'),
            'role' => 'murid',
            'kelas_id' => $kelas->id,
        ]);

        // Akun Murid 2
        User::create([
            'name' => 'Siswa Ani',
            'email' => 'murid2@lpk.com',
            'password' => Hash::make('password'),
            'role' => 'murid',
            'kelas_id' => $kelas->id,
        ]);
    }
}
