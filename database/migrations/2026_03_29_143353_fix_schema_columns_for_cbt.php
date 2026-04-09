<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────
        // 1. ujian_pesertas
        //    - Ubah enum status agar match dengan controller
        //    - Tambah kolom mulai_at
        // ─────────────────────────────────────────────────────────────
        Schema::table('ujian_pesertas', function (Blueprint $table) {
            // Drop enum constraint dulu (MySQL spesifik — ubah via string)
            $table->string('status')->default('belum_mulai')->change();
            $table->timestamp('mulai_at')->nullable()->after('user_id');
        });

        // Update nilai lama agar konsisten
        DB::statement("UPDATE ujian_pesertas SET status = 'belum_mulai'  WHERE status = 'belum'");
        DB::statement("UPDATE ujian_pesertas SET status = 'mengerjakan'  WHERE status = 'sedang'");
        DB::statement("UPDATE ujian_pesertas SET status = 'diblokir'     WHERE status = 'dipause'");

        // ─────────────────────────────────────────────────────────────
        // 2. jawaban_murids
        //    - Tambah pilihan_jawaban_id (untuk PG & Audio)
        //    - Tambah jawaban_multiple (untuk Multiple Choice — JSON)
        // ─────────────────────────────────────────────────────────────
        Schema::table('jawaban_murids', function (Blueprint $table) {
            $table->foreignId('pilihan_jawaban_id')
                ->nullable()
                ->after('soal_id')
                ->constrained('pilihan_jawabans')
                ->nullOnDelete();

            $table->text('jawaban_multiple')->nullable()->after('jawaban_text');
        });

        // ─────────────────────────────────────────────────────────────
        // 3. cheat_logs
        //    - Tambah status (pending/approved/rejected)
        //    - Tambah keterangan
        //    - Tambah notes (catatan dari admin/guru)
        //    - timestamp sudah ada, biarkan
        // ─────────────────────────────────────────────────────────────
        Schema::table('cheat_logs', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('tipe');
            $table->text('keterangan')->nullable()->after('status');
            $table->text('notes')->nullable()->after('approved_at');
        });

        // ─────────────────────────────────────────────────────────────
        // 4. ujians — tambah kolom acak_soal jika belum ada
        // ─────────────────────────────────────────────────────────────
        if (!Schema::hasColumn('ujians', 'acak_soal')) {
            Schema::table('ujians', function (Blueprint $table) {
                $table->boolean('acak_soal')->default(false)->after('selesai');
            });
        }
    }

    public function down(): void
    {
        Schema::table('ujian_pesertas', function (Blueprint $table) {
            $table->dropColumn('mulai_at');
        });

        Schema::table('jawaban_murids', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pilihan_jawaban_id');
            $table->dropColumn('jawaban_multiple');
        });

        Schema::table('cheat_logs', function (Blueprint $table) {
            $table->dropColumn(['status', 'keterangan', 'notes']);
        });
    }
};
