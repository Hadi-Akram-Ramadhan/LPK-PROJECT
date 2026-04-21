<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach(\App\Models\Ujian::with('soals')->get() as $u) {
    if($u->soals->count() > 0) {
        echo "Ujian ID: {$u->id}, Title: {$u->judul}\n";
        $types = $u->soals->groupBy('tipe')->map->count();
        print_r($types->toArray());
        echo "-------------------\n";
    }
}
