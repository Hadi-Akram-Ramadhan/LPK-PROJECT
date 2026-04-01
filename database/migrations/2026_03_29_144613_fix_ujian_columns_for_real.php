<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            // Already renamed in partial run:
            // $table->renameColumn('nama', 'judul');
            // $table->renameColumn('durasi_menit', 'durasi');
            
            // Only add deskripsi since this is what failed before
            $table->text('deskripsi')->nullable()->after('judul');
        });
    }

    public function down(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            $table->renameColumn('judul', 'nama');
            $table->renameColumn('durasi', 'durasi_menit');
            $table->dropColumn('deskripsi');
        });
    }
};
