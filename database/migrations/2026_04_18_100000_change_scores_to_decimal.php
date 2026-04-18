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
        Schema::table('soals', function (Blueprint $table) {
            $table->decimal('poin', 8, 2)->default(10.00)->change();
        });

        Schema::table('jawaban_murids', function (Blueprint $table) {
            $table->decimal('poin_didapat', 8, 2)->nullable()->change();
        });

        Schema::table('ujian_pesertas', function (Blueprint $table) {
            $table->decimal('skor', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('soals', function (Blueprint $table) {
            $table->integer('poin')->default(10)->change();
        });

        Schema::table('jawaban_murids', function (Blueprint $table) {
            $table->integer('poin_didapat')->nullable()->change();
        });

        Schema::table('ujian_pesertas', function (Blueprint $table) {
            $table->integer('skor')->nullable()->change();
        });
    }
};
