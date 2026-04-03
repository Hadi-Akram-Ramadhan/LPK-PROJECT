<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
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
