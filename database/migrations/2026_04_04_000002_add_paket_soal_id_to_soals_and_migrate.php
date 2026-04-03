<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom paket_soal_id ke soals (nullable dulu)
        Schema::table('soals', function (Blueprint $table) {
            $table->foreignId('paket_soal_id')->nullable()->after('guru_id')->constrained('paket_soals')->nullOnDelete();
        });

        // 2. Buat paket default "Soal Lama (Tak Berpaket)" per guru
        $adminId = DB::table('users')->where('role', 'admin')->value('id');
        if (!$adminId) {
            $adminId = DB::table('users')->first()?->id ?? 1;
        }

        // Ambil semua guru_id unik dari soals yang belum punya paket
        $guruIds = DB::table('soals')->whereNull('paket_soal_id')->pluck('guru_id')->unique();

        foreach ($guruIds as $guruId) {
            // Buat paket untuk setiap guru
            $paketId = DB::table('paket_soals')->insertGetId([
                'guru_id'    => $guruId,
                'nama'       => 'Soal Lama (Tak Berpaket)',
                'deskripsi'  => 'Kumpulan soal yang dibuat sebelum sistem Paket Soal.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Masukkan semua soal milik guru ini ke paket tersebut
            DB::table('soals')
                ->where('guru_id', $guruId)
                ->whereNull('paket_soal_id')
                ->update(['paket_soal_id' => $paketId]);
        }
    }

    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->dropForeign(['paket_soal_id']);
            $table->dropColumn('paket_soal_id');
        });
    }
};
