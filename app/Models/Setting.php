<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';
    protected $keyType    = 'string';
    public $incrementing  = false;

    protected $fillable = ['key', 'value', 'description'];

    /**
     * Ambil nilai setting berdasarkan key, dengan default fallback.
     */
    public static function get(string $key, $default = null)
    {
        $row = static::find($key);
        return $row ? $row->value : $default;
    }

    /**
     * Simpan atau update nilai setting.
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
