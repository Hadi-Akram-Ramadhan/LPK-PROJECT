<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianPeserta extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'mulai_at'   => 'datetime',
        'selesai_at' => 'datetime',
    ];

    public function ujian()
    {
        return $this->belongsTo(Ujian::class, 'ujian_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jawabanMurids()
    {
        return $this->hasMany(JawabanMurid::class, 'ujian_peserta_id');
    }

    public function cheatLogs()
    {
        return $this->hasMany(CheatLog::class, 'ujian_peserta_id');
    }
}
