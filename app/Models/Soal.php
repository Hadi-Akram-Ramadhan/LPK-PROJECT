<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Soal
 *
 * @property int $id
 * @property int $guru_id
 * @property int $paket_soal_id
 * @property string $tipe
 * @property string $pertanyaan
 * @property int $poin
 * @property string|null $audio_path
 * @property string|null $gambar_path
 * @property string|null $jawaban_kunci
 * @property int|null $audio_max_play
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $guru
 * @property-read \App\Models\PaketSoal $paketSoal
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PilihanJawaban[] $pilihanJawabans
 * @property-read int|null $pilihan_jawabans_count
 */
class Soal extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function paketSoal()
    {
        return $this->belongsTo(PaketSoal::class, 'paket_soal_id');
    }

    public function pilihanJawabans()
    {
        return $this->hasMany(PilihanJawaban::class, 'soal_id');
    }

    public function ujians()
    {
        return $this->belongsToMany(Ujian::class, 'ujian_soal', 'soal_id', 'ujian_id')
                    ->withPivot('urutan')
                    ->withTimestamps();
    }
}
