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
        Route::get('/exam/{ujian_peserta}/review', [\App\Http\Controllers\Murid\ExamController::class, 'review'])->name('murid.exam.review');
        
        // Anti-Cheat Endpoints
        Route::post('/exam/{ujian_peserta}/report-tab-switch', [\App\Http\Controllers\Murid\ExamController::class, 'reportCheat'])->name('murid.exam.reportCheat');
        Route::get('/exam/{ujian_peserta}/blocked', [\App\Http\Controllers\Murid\ExamController::class, 'blocked'])->name('murid.exam.blocked');
        // Tes Buta Warna (Sistem Cerdas)
        Route::get('/exam/{ujian_peserta}/buta-warna', [\App\Http\Controllers\Murid\ColorBlindTestController::class, 'show'])->name('murid.exam.buta_warna.show');
        Route::post('/exam/{ujian_peserta}/buta-warna', [\App\Http\Controllers\Murid\ColorBlindTestController::class, 'submit'])->name('murid.exam.buta_warna.submit');

        // Ganti Password
        Route::get('/password', function() {
            return view('murid.password');
        })->name('murid.password');
        
        // Media Proxy
        Route::get('/exam/media/{ujian_peserta}/{id}/{type}', [\App\Http\Controllers\Murid\AudioProxyController::class, 'stream'])->name('murid.exam.media');
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
        Route::get('/ujian/{ujian}/preview', [\App\Http\Controllers\Admin\UjianController::class, 'preview'])->name('ujian.preview');
        Route::get('/ujian/{ujian}/soal', [\App\Http\Controllers\Admin\UjianController::class, 'manajemenSoal'])->name('ujian.soal');

        Route::post('/ujian/{ujian}/soal', [\App\Http\Controllers\Admin\UjianController::class, 'updateSoal'])->name('ujian.updateSoal');
        
        // Paket Soal (Bank Soal terkelompok)
        Route::resource('paket-soal', \App\Http\Controllers\Admin\PaketSoalController::class);
        Route::post('/paket-soal/{paket_soal}/duplicate', [\App\Http\Controllers\Admin\PaketSoalController::class, 'duplicate'])->name('paket-soal.duplicate');

        // Individual Soal (dalam konteks paket)
        Route::resource('soal', \App\Http\Controllers\Admin\SoalController::class);
        Route::get('/import-soal', [\App\Http\Controllers\Admin\SoalController::class, 'import'])->name('soal.import');
        Route::post('/import-soal', [\App\Http\Controllers\Admin\SoalController::class, 'storeImport'])->name('soal.storeImport');
        Route::get('/import-soal/template', [\App\Http\Controllers\Admin\SoalController::class, 'downloadTemplate'])->name('soal.template');
        Route::post('/soal/upload-media', [\App\Http\Controllers\Admin\SoalController::class, 'uploadMedia'])->name('soal.uploadMedia');
        
        // Ujian Monitoring
        Route::get('/monitor', [\App\Http\Controllers\Admin\ExamMonitorController::class, 'index'])->name('monitor.index');
        Route::get('/monitor/{ujian}', [\App\Http\Controllers\Admin\ExamMonitorController::class, 'show'])->name('monitor.show');
        Route::get('/monitor/{ujian}/export', [\App\Http\Controllers\Admin\ExamMonitorController::class, 'export'])->name('monitor.export');
        Route::get('/monitor/{ujian}/peserta/{ujian_peserta}', [\App\Http\Controllers\Admin\ExamMonitorController::class, 'pesertaDetail'])->name('monitor.pesertaDetail');
        
        // Cheat Logs Monitoring
        Route::get('/cheat-logs', [\App\Http\Controllers\Admin\CheatLogController::class, 'index'])->name('cheat-logs.index');
        Route::post('/cheat-logs/{cheatLog}/approve', [\App\Http\Controllers\Admin\CheatLogController::class, 'approve'])->name('cheat-logs.approve');
        
        // Audio Explorer
        Route::get('/audio', [\App\Http\Controllers\Admin\AudioController::class, 'index'])->name('audio.index');
        Route::post('/audio', [\App\Http\Controllers\Admin\AudioController::class, 'store'])->name('audio.store');
        Route::post('/audio/rename', [\App\Http\Controllers\Admin\AudioController::class, 'rename'])->name('audio.rename');
        Route::get('/audio/stream', [\App\Http\Controllers\Admin\AudioController::class, 'stream'])->name('audio.stream');
        Route::delete('/audio', [\App\Http\Controllers\Admin\AudioController::class, 'destroy'])->name('audio.destroy');

        // Soal Buta Warna
        Route::get('/soal-buta-warna', [\App\Http\Controllers\Admin\SoalButaWarnaController::class, 'index'])->name('soal_buta_warna.index');
        Route::post('/soal-buta-warna', [\App\Http\Controllers\Admin\SoalButaWarnaController::class, 'store'])->name('soal_buta_warna.store');
        Route::delete('/soal-buta-warna/{soal_buta_warna}', [\App\Http\Controllers\Admin\SoalButaWarnaController::class, 'destroy'])->name('soal_buta_warna.destroy');

        // Image Explorer
        Route::get('/image', [\App\Http\Controllers\Admin\ImageController::class, 'index'])->name('image.index');
        Route::post('/image', [\App\Http\Controllers\Admin\ImageController::class, 'store'])->name('image.store');
        Route::post('/image/rename', [\App\Http\Controllers\Admin\ImageController::class, 'rename'])->name('image.rename');
        Route::delete('/image', [\App\Http\Controllers\Admin\ImageController::class, 'destroy'])->name('image.destroy');
    });

    // Guru Routes
    Route::middleware('role:guru')->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', \App\Http\Controllers\Guru\DashboardController::class)->name('dashboard');
        
        // Paket Soal (Bank Soal terkelompok)
        Route::resource('paket-soal', \App\Http\Controllers\Guru\PaketSoalController::class);
        Route::post('/paket-soal/{paket_soal}/duplicate', [\App\Http\Controllers\Guru\PaketSoalController::class, 'duplicate'])->name('paket-soal.duplicate');

        // Individual Soal Guru
        Route::resource('soal', \App\Http\Controllers\Guru\SoalController::class);
        
        // Manajemen Ujian
        Route::resource('ujian', \App\Http\Controllers\Guru\UjianController::class);
        Route::get('/ujian/{ujian}/preview', [\App\Http\Controllers\Guru\UjianController::class, 'preview'])->name('ujian.preview');

        
        // Import Soal
        Route::get('/import-soal', [\App\Http\Controllers\Guru\ImportSoalController::class, 'index'])->name('import.index');
        Route::post('/import-soal', [\App\Http\Controllers\Guru\ImportSoalController::class, 'store'])->name('import.store');
        
        // Endpoint download template
        Route::get('/import-soal/template', [\App\Http\Controllers\Guru\ImportSoalController::class, 'downloadTemplate'])->name('import.template');

        // Upload Media Langsung dari form soal
        Route::post('/soal/upload-media', [\App\Http\Controllers\Guru\SoalController::class, 'uploadMedia'])->name('soal.uploadMedia');

        // Monitor Ujian & Penilaian
        Route::get('/monitor', [\App\Http\Controllers\Guru\MonitorUjianController::class, 'index'])->name('monitor.index');
        Route::get('/monitor/{ujian}', [\App\Http\Controllers\Guru\MonitorUjianController::class, 'show'])->name('monitor.show');
        Route::get('/monitor/{ujian}/export', [\App\Http\Controllers\Guru\MonitorUjianController::class, 'export'])->name('monitor.export');
        Route::get('/monitor/{ujian}/peserta/{ujian_peserta}', [\App\Http\Controllers\Guru\MonitorUjianController::class, 'pesertaDetail'])->name('monitor.pesertaDetail');
        Route::get('/monitor/grade/{ujian_peserta}', [\App\Http\Controllers\Guru\MonitorUjianController::class, 'grade'])->name('monitor.grade');
        Route::post('/monitor/grade/{ujian_peserta}', [\App\Http\Controllers\Guru\MonitorUjianController::class, 'storeGrade'])->name('monitor.storeGrade');

        // Audio Explorer (Guru)
        Route::get('/audio', [\App\Http\Controllers\Guru\AudioController::class, 'index'])->name('audio.index');
        Route::post('/audio', [\App\Http\Controllers\Guru\AudioController::class, 'store'])->name('audio.store');
        Route::post('/audio/rename', [\App\Http\Controllers\Guru\AudioController::class, 'rename'])->name('audio.rename');
        Route::get('/audio/stream', [\App\Http\Controllers\Guru\AudioController::class, 'stream'])->name('audio.stream');
        Route::delete('/audio', [\App\Http\Controllers\Guru\AudioController::class, 'destroy'])->name('audio.destroy');

        // Image Explorer (Guru)
        Route::get('/image', [\App\Http\Controllers\Guru\ImageController::class, 'index'])->name('image.index');
        Route::post('/image', [\App\Http\Controllers\Guru\ImageController::class, 'store'])->name('image.store');
        Route::post('/image/rename', [\App\Http\Controllers\Guru\ImageController::class, 'rename'])->name('image.rename');
        Route::delete('/image', [\App\Http\Controllers\Guru\ImageController::class, 'destroy'])->name('image.destroy');

        // Cheat Logs (Guru — hanya ujian milik guru ini)
        Route::get('/cheat-logs', [\App\Http\Controllers\Guru\CheatLogController::class, 'index'])->name('cheat-logs.index');
        Route::post('/cheat-logs/{cheatLog}/approve', [\App\Http\Controllers\Guru\CheatLogController::class, 'approve'])->name('cheat-logs.approve');

        // Soal Buta Warna (Guru)
        Route::get('/soal-buta-warna', [\App\Http\Controllers\Guru\SoalButaWarnaController::class, 'index'])->name('soal_buta_warna.index');
        Route::post('/soal-buta-warna', [\App\Http\Controllers\Guru\SoalButaWarnaController::class, 'store'])->name('soal_buta_warna.store');
        Route::delete('/soal-buta-warna/{soal_buta_warna}', [\App\Http\Controllers\Guru\SoalButaWarnaController::class, 'destroy'])->name('soal_buta_warna.destroy');


        // Daftar Kelas & Murid
        Route::get('/kelas', [\App\Http\Controllers\Guru\KelasController::class, 'index'])->name('kelas.index');
        Route::get('/kelas/{kelas}', [\App\Http\Controllers\Guru\KelasController::class, 'show'])->name('kelas.show');
    });

    // Profile Routes (All Authenticated Users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Shared Media Proxy for Previews
    Route::get('/exam/media-preview/{id}/{type}', [\App\Http\Controllers\Murid\AudioProxyController::class, 'streamPreview'])->name('shared.media-preview');
});

require __DIR__.'/auth.php';
