<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            if (!Schema::hasColumn('ujians', 'acak_jawaban')) {
                $table->boolean('acak_jawaban')->default(false)->after('acak_soal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            if (Schema::hasColumn('ujians', 'acak_jawaban')) {
                $table->dropColumn('acak_jawaban');
            }
        });
    }
};
