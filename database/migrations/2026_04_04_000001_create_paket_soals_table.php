<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_soals');
    }
};
