<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Extend ENUM to include 'short_answer'
        DB::statement("ALTER TABLE soals MODIFY COLUMN tipe ENUM('pilihan_ganda', 'multiple_choice', 'essay', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar', 'short_answer') NOT NULL");

        // Add jawaban_kunci column to store the correct answer key(s)
        // Multiple accepted answers are separated by '|' character
        Schema::table('soals', function (Blueprint $table) {
            $table->text('jawaban_kunci')->nullable()->after('gambar_path')
                  ->comment('Correct answer for short_answer type. Separate multiple accepted answers with |');
        });
    }

    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->dropColumn('jawaban_kunci');
        });

        DB::statement("ALTER TABLE soals MODIFY COLUMN tipe ENUM('pilihan_ganda', 'multiple_choice', 'essay', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar') NOT NULL");
    }
};
