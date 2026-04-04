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
        
        // Ujian mendatang milik guru ini (Ambil 1 yang paling dekat)
        $upcomingUjian = Ujian::where('guru_id', $user->id)
            ->where('mulai', '>', now())
            ->orderBy('mulai', 'asc')
            ->first();

        // Perlu Dinilai (Essay yang belum ada nilainya di ujian milik guru ini)
        // Kita hitung dari UjianPeserta yang statusnya 'selesai' namun belum ada grading
        // Note: Implementasi grading spesifik mungkin berbeda tergantung skema LPK ini
        // Untuk sekarang, kita hitung UjianPeserta 'selesai' yang dikelola guru ini
        $perluDinilai = UjianPeserta::whereHas('ujian', function($q) use ($user) {
            $q->where('guru_id', $user->id);
        })->where('status', 'selesai')->whereNull('skor')->count();

        return view('guru.dashboard', compact(
            'totalSoal', 
            'totalUjian', 
            'sedangUjianCount', 
            'upcomingUjian',
            'perluDinilai'
        ));
    }
}
