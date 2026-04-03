<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

echo "Memeriksa status server lokal...\n";

// Melompati pengecekan network eksternal


// Simulasi login untuk Admin, Guru, dan Murid perlu browser/session management.
// Karena via Guzzle/Http di CLI agak kompleks untuk menjaga session, 
// kita akan membuat request internal via framework.
echo "Menggunakan internal crawler untuk mengecek endpoint...\n";

function checkInternalRoute($email, $route, $description) {
    $user = \App\Models\User::where('email', $email)->first();
    if (!$user) {
        echo "[FAIL] User $email tidak ditemukan di database.\n";
        return;
    }
    
    // Simulate internal request
    auth()->login($user);
    $request = \Illuminate\Http\Request::create($route, 'GET');
    $response = app()->handle($request);
    
    if ($response->getStatusCode() === 200) {
        echo "[OK] $description berhasil diakses.\n";
    } else {
        echo "[ERROR] $description mengembalikan status: " . $response->getStatusCode() . "\n";
    }
    auth()->logout();
}

checkInternalRoute('admin@lpk.com', '/admin/dashboard', 'Dashboard Admin');
checkInternalRoute('admin@lpk.com', '/admin/users', 'Manajemen User');
checkInternalRoute('admin@lpk.com', '/admin/cheat-logs', 'Anti-Cheat Monitor Admin');

checkInternalRoute('guru@lpk.com', '/guru/dashboard', 'Dashboard Guru');
checkInternalRoute('guru@lpk.com', '/guru/soal', 'Bank Soal Guru');
checkInternalRoute('guru@lpk.com', '/guru/monitor', 'Monitor Ujian Guru');

checkInternalRoute('murid1@lpk.com', '/dashboard', 'Dashboard Murid (Daftar Ujian)');

echo "\nSemua pengecekan logika internal routing selesai.\n";
