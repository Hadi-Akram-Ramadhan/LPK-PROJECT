<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audio_playback_logs', function (Blueprint $col) {
            $col->id();
            $col->foreignId('ujian_peserta_id')->constrained('ujian_pesertas')->onDelete('cascade');
            $col->foreignId('soal_id')->constrained('soals')->onDelete('cascade');
            // If it's an option audio, store the option ID
            $col->foreignId('pilihan_jawaban_id')->nullable()->constrained('pilihan_jawabans')->onDelete('cascade');
            $col->integer('play_count')->default(0);
            $col->timestamps();

            // Index for fast lookup
            $col->index(['ujian_peserta_id', 'soal_id', 'pilihan_jawaban_id'], 'audio_log_unique_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audio_playback_logs');
    }
};
