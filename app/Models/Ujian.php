<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kelas;

/**
 * App\Models\Ujian
 *
 * @property int $id
 * @property int $guru_id
 * @property string $judul
 * @property string|null $deskripsi
 * @property \Illuminate\Support\Carbon $mulai
 * @property \Illuminate\Support\Carbon $selesai
 * @property int $durasi
 * @property bool $acak_soal
 * @property bool $lihat_hasil
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $guru
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Soal[] $soals
 * @property-read int|null $soals_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UjianPeserta[] $pesertas
 * @property-read int|null $pesertas_count
 */
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
                    ->orderBy('ujian_soal.urutan');
    }

    public function pesertas()
    {
        return $this->hasMany(UjianPeserta::class, 'ujian_id');
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'ujian_kelas', 'ujian_id', 'kelas_id')->withTimestamps();
    }
}
