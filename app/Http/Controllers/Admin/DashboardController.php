<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Ujian;
use App\Models\Soal;
use App\Models\UjianPeserta;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Stats
        $totalSiswa = User::where('role', 'murid')->count();
        
        $now = Carbon::now();
        $ujianAktifCount = Ujian::where(function($q) use ($now) {
            $q->whereNull('mulai')->orWhere('mulai', '<=', $now);
        })->where(function($q) use ($now) {
            $q->whereNull('selesai')->orWhere('selesai', '>=', $now);
        })->count();

        $totalSoal = Soal::count();

        // Rata-rata Nilai (Global)
        $avgNilai = UjianPeserta::where('status', 'selesai')
            ->whereNotNull('skor')
            ->avg('skor') ?? 0;

        // 2. Ujian Terbaru (Latest Created)
        $latestExams = Ujian::withCount(['soals', 'pesertas'])
            ->latest()
            ->take(5)
            ->get();

        // 3. Ujian Mendatang (Upcoming)
        $upcomingExams = Ujian::withCount(['soals', 'pesertas'])
            ->where('mulai', '>', $now)
            ->orderBy('mulai', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalSiswa',
            'ujianAktifCount',
            'totalSoal',
            'avgNilai',
            'latestExams',
            'upcomingExams'
        ));
    }
}
