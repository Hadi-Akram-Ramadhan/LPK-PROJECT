<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add audio play limit to soals (for question-level audio)
        Schema::table('soals', function (Blueprint $table) {
            $table->unsignedSmallInteger('audio_max_play')
                  ->nullable()
                  ->after('jawaban_kunci')
                  ->comment('Max times audio can be played. NULL = unlimited.');
        });

        // Add audio play limit to pilihan_jawabans (for answer-option audio)
        Schema::table('pilihan_jawabans', function (Blueprint $table) {
            $table->unsignedSmallInteger('audio_max_play')
                  ->nullable()
                  ->after('media_tipe')
                  ->comment('Max play limit for this audio option. NULL = unlimited.');
        });
    }

    public function down(): void
    {
        Schema::table('pilihan_jawabans', function (Blueprint $table) {
            $table->dropColumn('audio_max_play');
        });

        Schema::table('soals', function (Blueprint $table) {
            $table->dropColumn('audio_max_play');
        });
    }
};
