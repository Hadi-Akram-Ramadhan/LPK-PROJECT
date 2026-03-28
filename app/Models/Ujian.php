<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'mulai' => 'datetime',
        'selesai' => 'datetime',
    ];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function soals()
    {
        return $this->belongsToMany(Soal::class, 'ujian_soal', 'ujian_id', 'soal_id')
                    ->withPivot('urutan')
                    ->withTimestamps()
                    ->orderBy('pivot_urutan');
    }

    public function pesertas()
    {
        return $this->hasMany(UjianPeserta::class, 'ujian_id');
    }
}
