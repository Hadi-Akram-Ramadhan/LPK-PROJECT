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
        // Update ENUM for 'tipe' in soals table using a raw query.
        DB::statement("ALTER TABLE soals MODIFY COLUMN tipe ENUM('pilihan_ganda', 'multiple_choice', 'essay', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar') NOT NULL");

        Schema::table('soals', function (Blueprint $table) {
            $table->string('gambar_path')->nullable()->after('audio_path');
        });

        Schema::table('pilihan_jawabans', function (Blueprint $table) {
            $table->string('media_path')->nullable()->after('teks');
            $table->string('media_tipe')->nullable()->after('media_path')->comment('audio, gambar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pilihan_jawabans', function (Blueprint $table) {
            $table->dropColumn(['media_path', 'media_tipe']);
        });

        Schema::table('soals', function (Blueprint $table) {
            $table->dropColumn('gambar_path');
        });

        // Revert ENUM
        DB::statement("ALTER TABLE soals MODIFY COLUMN tipe ENUM('pilihan_ganda', 'multiple_choice', 'essay', 'audio') NOT NULL");
    }
};
