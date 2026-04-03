<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
