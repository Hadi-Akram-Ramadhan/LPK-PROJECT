<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Imports\UserImport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    /**
     * Display a listing of all users (guru + siswa combined).
     */
    public function index(Request $request)
    {
        // Guru & Admin data
        $guruQuery = User::whereIn('role', ['guru', 'admin']);
        if ($request->filled('search_guru')) {
            $guruQuery->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_guru . '%')
                  ->orWhere('email', 'like', '%' . $request->search_guru . '%');
            });
        }
        $guruUsers = $guruQuery->latest()->get();
        $totalGuru = User::where('role', 'guru')->count();
        $totalAdmin = User::where('role', 'admin')->count();

        // Siswa data
        $siswaQuery = User::with('kelas')->where('role', 'murid');
        if ($request->filled('search_siswa')) {
            $siswaQuery->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search_siswa . '%')
                  ->orWhere('email', 'like', '%' . $request->search_siswa . '%');
            });
        }
        $siswaUsers = $siswaQuery->latest()->paginate(10);
        $totalSiswa = User::where('role', 'murid')->count();

        $kelas = Kelas::all();

        return view('admin.users.index', compact(
            'guruUsers', 'totalGuru', 'totalAdmin',
            'siswaUsers', 'totalSiswa', 'kelas'
        ));
    }

    /**
     * Display listing of guru & admin users (legacy route).
     */
    public function staff(Request $request)
    {
        $query = User::whereIn('role', ['guru', 'admin']);
        
        $users = $query->latest()->paginate(15);
        $totalGuru = User::where('role', 'guru')->count();
        $totalAdmin = User::where('role', 'admin')->count();

        return view('admin.users.staff', compact('users', 'totalGuru', 'totalAdmin'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::all();
        return view('admin.users.create', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,guru,murid'],
            'kelas_id' => ['nullable', 'exists:kelas,id', 'required_if:role,murid'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'kelas_id' => $request->role === 'murid' ? $request->kelas_id : null,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $kelas = Kelas::all();
        return view('admin.users.edit', compact('user', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'role' => ['required', 'in:admin,guru,murid'],
            'kelas_id' => ['nullable', 'exists:kelas,id', 'required_if:role,murid'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'kelas_id' => $request->role === 'murid' ? $request->kelas_id : null,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    // ── Import Users ───────────────────────────────────────────

    public function import()
    {
        return view('admin.users.import');
    }

    public function storeImport(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $tmpPath = $request->file('file_excel')->getRealPath();
            $import = new UserImport();
            $import->import($tmpPath);
            $summary = $import->getSummary();

            if ($summary['sukses'] > 0) {
                return redirect()->route('admin.users.index')->with('success', "{$summary['sukses']} siswa berhasil diimport.");
            }
            return back()->with('error', 'Tidak ada data siswa yang berhasil diimport.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        
        // --- Sheet 1: Template ---
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        $headers = [
            'A1' => 'Nama Siswa (Wajib)',
            'B1' => 'Email (Wajib & Unik)',
            'C1' => 'Password (Wajib, Min 8 Karakter)',
            'D1' => 'ID Kelas (Opsional, Lihat Sheet Bantuan)',
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Header Styling
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10b981']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);
        
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Sample Row
        $sheet->setCellValue('A2', 'Andi Siswan');
        $sheet->setCellValue('B2', 'andi@lpk.com');
        $sheet->setCellValue('C2', 'lpk123456');
        $sheet->setCellValue('D2', '1'); // Contoh ID kelas 1

        // --- Sheet 2: Reference (ID Kelas) ---
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('Bantuan ID Kelas');
        
        $refSheet->setCellValue('A1', 'ID');
        $refSheet->setCellValue('B1', 'Nama Kelas');
        
        $refSheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3b82f6']],
        ]);

        $kelas = \App\Models\Kelas::all();
        $rowNum = 2;
        foreach ($kelas as $k) {
            $refSheet->setCellValue('A' . $rowNum, $k->id);
            $refSheet->setCellValue('B' . $rowNum, $k->nama);
            $rowNum++;
        }
        $refSheet->getColumnDimension('A')->setAutoSize(true);
        $refSheet->getColumnDimension('B')->setAutoSize(true);

        $spreadsheet->setActiveSheetIndex(0); // Go back to first sheet

        $tmpFile = tempnam(sys_get_temp_dir(), 'lpk_template_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpFile);

        return response()->download($tmpFile, 'Template_Import_Siswa_LPK.xlsx')->deleteFileAfterSend(true);
    }
}
