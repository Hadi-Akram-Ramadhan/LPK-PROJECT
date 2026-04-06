<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kelas = Kelas::withCount('users')->latest()->paginate(15);
        return view('guru.kelas.index', compact('kelas'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kela) // Note the pluralization from routes
    {
        $kela->load(['users' => function($q) {
            $q->where('role', 'murid')->latest();
        }]);
        
        return view('guru.kelas.show', [
            'kelas' => $kela,
            'students' => $kela->users
        ]);
    }
}
