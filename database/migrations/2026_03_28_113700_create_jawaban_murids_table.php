<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jawaban_murids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_peserta_id')->constrained('ujian_pesertas')->cascadeOnDelete();
            $table->foreignId('soal_id')->constrained('soals')->cascadeOnDelete();
            $table->text('jawaban_text')->nullable(); // For essay or serialized JSON for multiple choice
            $table->integer('poin_didapat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_murids');
    }
};
