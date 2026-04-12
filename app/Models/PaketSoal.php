<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PaketSoal
 *
 * @property int $id
 * @property int $guru_id
 * @property string $nama
 * @property string|null $deskripsi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $guru
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Soal[] $soals
 * @property-read int|null $soals_count
 * @property-read int $total_soal
 * @property-read int $total_poin
 */
class PaketSoal extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function soals()
    {
        return $this->hasMany(Soal::class, 'paket_soal_id');
    }

    public function getTotalSoalAttribute()
    {
        return $this->soals()->count();
    }

    public function getTotalPoinAttribute()
    {
        return $this->soals()->sum('poin');
    }
}
