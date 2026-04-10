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
        // Akun Admin Utama
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@lpk.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
