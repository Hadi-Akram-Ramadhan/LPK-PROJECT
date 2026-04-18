<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioPlaybackLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ujian_peserta_id',
        'soal_id',
        'pilihan_jawaban_id',
        'play_count',
    ];

    public function ujianPeserta()
    {
        return $this->belongsTo(UjianPeserta::class);
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }

    public function pilihanJawaban()
    {
        return $this->belongsTo(PilihanJawaban::class);
    }
}
