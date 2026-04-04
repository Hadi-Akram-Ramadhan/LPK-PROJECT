<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\UjianPeserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();
        
        // Total Soal buatan guru ini
        $totalSoal = Soal::where('guru_id', $user->id)->count();
        
        // Ujian yang dibuat oleh guru ini
        $totalUjian = Ujian::where('guru_id', $user->id)->count();
        
        // Sedang Ujian (Ujian yang sedang berlangsung milik guru ini)
        $sedangUjianCount = UjianPeserta::whereHas('ujian', function($q) use ($user) {
            $q->where('guru_id', $user->id);
        })->where('status', 'pengerjaan')->count();
        
        // Ujian mendatang milik guru ini (Ambil 5 yang paling dekat)
        $upcomingExams = Ujian::where('guru_id', $user->id)
            ->where('mulai', '>', now())
            ->orderBy('mulai', 'asc')
            ->take(5)
            ->get();

        // Ujian Terbaru (Paling baru dibuat)
        $latestExams = Ujian::where('guru_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Ujian Terbaru milik guru ini
        $ujianTerbaru = Ujian::where('guru_id', $user->id)->latest()->take(5)->get();

        // Perlu Dinilai (Essay yang belum ada nilainya di ujian milik guru ini)
        $perluDinilai = UjianPeserta::whereHas('ujian', function($q) use ($user) {
            $q->where('guru_id', $user->id);
        })->where('status', 'selesai')->whereNull('skor')->count();

        return view('guru.dashboard', compact(
            'totalSoal', 
            'totalUjian', 
            'sedangUjianCount', 
<<<<<<< HEAD
            'upcomingExams',
            'latestExams',
            'perluDinilai'
=======
            'upcomingUjian',
            'perluDinilai',
            'ujianTerbaru'
>>>>>>> b90f6ff449e2a3fa195860fdcf2abb6ecdd92807
        ));
    }
}
