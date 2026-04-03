<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            // Rename 'nama' -> 'judul' (controller/views expect 'judul')
            if (Schema::hasColumn('ujians', 'nama') && !Schema::hasColumn('ujians', 'judul')) {
                $table->renameColumn('nama', 'judul');
            }

            // Rename 'durasi_menit' -> 'durasi' (controller/views expect 'durasi')
            if (Schema::hasColumn('ujians', 'durasi_menit') && !Schema::hasColumn('ujians', 'durasi')) {
                $table->renameColumn('durasi_menit', 'durasi');
            }
        });

        // Add 'deskripsi' column if it doesn't exist (used by controller but never created)
        if (!Schema::hasColumn('ujians', 'deskripsi')) {
            Schema::table('ujians', function (Blueprint $table) {
                $table->text('deskripsi')->nullable()->after('judul');
            });
        }
    }

    public function down(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            if (Schema::hasColumn('ujians', 'judul') && !Schema::hasColumn('ujians', 'nama')) {
                $table->renameColumn('judul', 'nama');
            }
            if (Schema::hasColumn('ujians', 'durasi') && !Schema::hasColumn('ujians', 'durasi_menit')) {
                $table->renameColumn('durasi', 'durasi_menit');
            }
            if (Schema::hasColumn('ujians', 'deskripsi')) {
                $table->dropColumn('deskripsi');
            }
        });
    }
};
