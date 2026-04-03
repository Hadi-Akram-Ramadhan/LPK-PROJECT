<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

$affected = \App\Models\UjianPeserta::whereNotIn('status', ['mengerjakan', 'selesai', 'diblokir', 'belum_mulai'])->update(['status' => 'mengerjakan']);
echo "Updated $affected weird records. All statuses now:\n";
$pesertas = \App\Models\UjianPeserta::all();
foreach($pesertas as $p) {
    echo "ID " . $p->id . " Status: '" . $p->status . "'\n";
}
