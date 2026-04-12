<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PilihanJawaban
 *
 * @property int $id
 * @property int $soal_id
 * @property string $teks
 * @property string|null $media_path
 * @property string|null $media_tipe
 * @property bool $is_benar
 * @property int|null $audio_max_play
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Soal $soal
 */
class PilihanJawaban extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }
}
