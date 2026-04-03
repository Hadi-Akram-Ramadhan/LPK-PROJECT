<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

echo "CheatLog Count: " . \App\Models\CheatLog::count() . "\n\n";

$logs = \App\Models\CheatLog::with('ujianPeserta')->latest()->get();
foreach ($logs as $log) {
    echo "Log ID: {$log->id}\n";
    echo "Status: {$log->status}\n";
    echo "Peserta ID: {$log->ujian_peserta_id}\n";
    echo "Peserta Status: " . ($log->ujianPeserta ? $log->ujianPeserta->status : 'NULL') . "\n";
    echo "Created At: {$log->timestamp}\n";
    echo "------------------\n";
}
