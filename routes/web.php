<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Murid Routes
    Route::middleware('role:murid')->group(function () {
        Route::get('/dashboard', function () {
            return view('murid.dashboard');
        })->name('dashboard');
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('kelas', \App\Http\Controllers\Admin\KelasController::class);
        
        // Ujian Monitoring
        Route::get('/exams', [\App\Http\Controllers\Admin\ExamMonitorController::class, 'index'])->name('exams.index');
        
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
        Route::get('/dashboard', function () {
            return view('guru.dashboard');
        })->name('dashboard');
    });

    // Profile Routes (All Authenticated Users)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
