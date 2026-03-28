<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ujian;
use Illuminate\Http\Request;

class ExamMonitorController extends Controller
{
    public function index()
    {
        $ujians = Ujian::with(['guru', 'pesertas.user'])
            ->withCount('pesertas')
            ->latest()
            ->paginate(15);
            
        return view('admin.exams.index', compact('ujians'));
    }
}
