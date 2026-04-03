<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheatLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'timestamp' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function ujianPeserta()
    {
        return $this->belongsTo(UjianPeserta::class, 'ujian_peserta_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
