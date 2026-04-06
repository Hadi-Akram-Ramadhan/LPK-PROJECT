<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kelas::withCount('users');
        
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        $kelas = $query->latest()->paginate(15);
        return view('admin.kelas.index', compact('kelas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.kelas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kelas,nama',
        ]);

        Kelas::create([
            'nama' => $request->nama,
        ]);

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kela)
    {
        $kela->load(['users' => function($q) {
            $q->where('role', 'murid')->latest();
        }]);
        
        return view('admin.kelas.show', ['kelas' => $kela, 'students' => $kela->users]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kela) // Note: route parameter might be $kela because of singularization
    {
        return view('admin.kelas.edit', ['kelas' => $kela]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kelas,nama,' . $kela->id,
        ]);

        $kela->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('admin.kelas.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kela)
    {
        if ($kela->users()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus kelas yang memiliki murid terdaftar.');
        }

        $kela->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}
