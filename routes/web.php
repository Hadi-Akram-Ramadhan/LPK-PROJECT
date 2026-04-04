<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Murid Routes
    Route::middleware('role:murid')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Murid\ExamController::class, 'index'])->name('murid.dashboard');
        
        Route::post('/exam/{ujian_peserta}/start', [\App\Http\Controllers\Murid\ExamController::class, 'start'])->name('murid.exam.start');
        Route::get('/exam/{ujian_peserta}/show', [\App\Http\Controllers\Murid\ExamController::class, 'show'])->name('murid.exam.show');
        Route::post('/exam/{ujian_peserta}/auto-save', [\App\Http\Controllers\Murid\ExamController::class, 'storeAnswer'])->name('murid.exam.autoSave');
        Route::post('/exam/{ujian_peserta}/finish', [\App\Http\Controllers\Murid\ExamController::class, 'finish'])->name('murid.exam.finish');
        Route::get('/exam/{ujian_peserta}/finish', [\App\Http\Controllers\Murid\ExamController::class, 'finish']); // Fallback untuk refresh/GET
        Route::get('/exam/{ujian_peserta}/result', [\App\Http\Controllers\Murid\ExamController::class, 'result'])->name('murid.exam.result');
        
        // Anti-Cheat Endpoints
        Route::post('/exam/{ujian_peserta}/report-tab-switch', [\App\Http\Controllers\Murid\ExamController::class, 'reportCheat'])->name('murid.exam.reportCheat');
        Route::get('/exam/{ujian_peserta}/blocked', [\App\Http\Controllers\Murid\ExamController::class, 'blocked'])->name('murid.exam.blocked');
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::get('/import-users', [\App\Http\Controllers\Admin\UserController::class, 'import'])->name('users.import');
        Route::post('/import-users', [\App\Http\Controllers\Admin\UserController::class, 'storeImport'])->name('users.storeImport');
        Route::get('/import-users/template', [\App\Http\Controllers\Admin\UserController::class, 'downloadTemplate'])->name('users.template');

        Route::get('/staff', [\App\Http\Controllers\Admin\UserController::class, 'staff'])->name('staff.index');
        Route::resource('kelas', \App\Http\Controllers\Admin\KelasController::class);
        Route::resource('ujian', \App\Http\Controllers\Admin\UjianController::class);
        Route::get('/ujian/{ujian}/soal', [\App\Http\Controllers\Admin\UjianController::class, 'manajemenSoal'])->name('ujian.soal');
        Route::post('/ujian/{ujian}/soal', [\App\Http\Controllers\Admin\UjianController::class, 'updateSoal'])->name('ujian.updateSoal');
        
        // Paket Soal (Bank Soal terkelompok)
        Route::resource('paket-soal', \App\Http\Controllers\Admin\PaketSoalController::class);

        // Individual Soal (dalam konteks paket)
        Route::resource('soal', \App\Http\Controllers\Admin\SoalController::class);
        Route::get('/import-soal', [\App\Http\Controllers\Admin\SoalController::class, 'import'])->name('soal.import');
        Route::post('/import-soal', [\App\Http\Controllers\Admin\SoalController::class, 'storeImport'])->name('soal.storeImport');
        Route::get('/import-soal/template', [\App\Http\Controllers\Admin\SoalController::class, 'downloadTemplate'])->name('soal.template');
        
        // Ujian Monitoring
        Route::get('/monitor', [\App\Http\Controllers\Admin\ExamMonitorController::class, 'index'])->name('monitor.index');
        Route::get('/monitor/{ujian}', [\App\Http\Controllers\Admin\ExamMonitorController::class, 'show'])->name('monitor.show');
        Route::get('/monitor/{ujian}/export', [\App\Http\Controllers\Admin\ExamMonitorController::class, 'export'])->name('monitor.export');
        
        // Cheat Logs Monitoring
        Route::get('/cheat-logs', [\App\Http\Controllers\Admin\CheatLogController::class, 'index'])->name('cheat-logs.index');
        Route::post('/cheat-logs/{cheatLog}/approve', [\App\Http\Controllers\Admin\CheatLogController::class, 'approve'])->name('cheat-logs.approve');
        
        // Audio Explorer
        Route::get('/audio', [\App\Http\Controllers\Admin\AudioController::class, 'index'])->name('audio.index');
        Route::post('/audio', [\App\Http\Controllers\Admin\AudioController::class, 'store'])->name('audio.store');
        Route::delete('/audio', [\App\Http\Controllers\Admin\AudioController::class, 'destroy'])->name('audio.destroy');
    });

    // Guru Routes
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', \App\Http\Controllers\Guru\DashboardController::class)->name('dashboard');
        
        // Paket Soal (Bank Soal terkelompok)
        Route::resource('paket-soal', \App\Http\Controllers\Guru\PaketSoalController::class);

        // Individual Soal Guru
        Route::resource('soal', \App\Http\Controllers\Guru\SoalController::class);
        
        // Manajemen Ujian
        Route::resource('ujian', \App\Http\Controllers\Guru\UjianController::class);
        
        // Import Soal
        Route::get('/import-soal', [\App\Http\Controllers\Guru\ImportSoalController::class, 'index'])->name('import.index');
        Route::post('/import-soal', [\App\Http\Controllers\Guru\ImportSoalController::class, 'store'])->name('import.store');
        
        // Endpoint download template
        Route::get('/import-soal/template', [\App\Http\Controllers\Guru\ImportSoalController::class, 'downloadTemplate'])->name('import.template');

        // Monitor Ujian & Penilaian
        Route::get('/monitor', [\App\Http\Controllers\Guru\MonitorUjianController::class, 'index'])->name('monitor.index');
        Route::get('/monitor/{ujian}', [\App\Http\Controllers\Guru\MonitorUjianController::class, 'show'])->name('monitor.show');
        Route::get('/monitor/{ujian}/export', [\App\Http\Controllers\Guru\MonitorUjianController::class, 'export'])->name('monitor.export');
        Route::get('/monitor/grade/{ujian_peserta}', [\App\Http\Controllers\Guru\MonitorUjianController::class, 'grade'])->name('monitor.grade');
        Route::post('/monitor/grade/{ujian_peserta}', [\App\Http\Controllers\Guru\MonitorUjianController::class, 'storeGrade'])->name('monitor.storeGrade');

        // Audio Explorer (Guru)
        Route::get('/audio', [\App\Http\Controllers\Guru\AudioController::class, 'index'])->name('audio.index');
        Route::post('/audio', [\App\Http\Controllers\Guru\AudioController::class, 'store'])->name('audio.store');
        Route::delete('/audio', [\App\Http\Controllers\Guru\AudioController::class, 'destroy'])->name('audio.destroy');

        // Cheat Logs (Guru — hanya ujian milik guru ini)
        Route::get('/cheat-logs', [\App\Http\Controllers\Guru\CheatLogController::class, 'index'])->name('cheat-logs.index');
        Route::post('/cheat-logs/{cheatLog}/approve', [\App\Http\Controllers\Guru\CheatLogController::class, 'approve'])->name('cheat-logs.approve');
    });

    // Profile Routes (All Authenticated Users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
