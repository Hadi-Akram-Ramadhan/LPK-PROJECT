<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Extend ENUM to include 'matching'
        DB::statement("ALTER TABLE soals MODIFY COLUMN tipe ENUM(
            'pilihan_ganda',
            'multiple_choice',
            'essay',
            'audio',
            'pilihan_ganda_audio',
            'pilihan_ganda_gambar',
            'short_answer',
            'matching'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE soals MODIFY COLUMN tipe ENUM(
            'pilihan_ganda',
            'multiple_choice',
            'essay',
            'audio',
            'pilihan_ganda_audio',
            'pilihan_ganda_gambar',
            'short_answer'
        ) NOT NULL");
    }
};
