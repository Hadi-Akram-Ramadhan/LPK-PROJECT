<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

try {
    $ujian_peserta = \App\Models\UjianPeserta::latest()->first();
    echo "Peserta ID: " . $ujian_peserta->id . "\n";
    echo "Old Status: " . $ujian_peserta->status . "\n";
    
    $ujian_peserta->update(['status' => 'diblokir']);
    echo "New Status: " . $ujian_peserta->status . "\n";

    $log = \App\Models\CheatLog::create([
        'ujian_peserta_id' => $ujian_peserta->id,
        'keterangan' => 'Terdeteksi memindahkan/menyembunyikan tab ujian.',
        'status' => 'pending',
        'timestamp' => \Carbon\Carbon::now(),
    ]);

    echo "CheatLog Created! ID: " . $log->id . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
