<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ujians', function (Blueprint $table) {
            $table->enum('jenis_ujian', ['reguler', 'tryout'])->default('reguler')->after('judul');
        });
    }

    public function down()
    {
        Schema::table('ujians', function (Blueprint $table) {
            $table->dropColumn('jenis_ujian');
        });
    }
};
