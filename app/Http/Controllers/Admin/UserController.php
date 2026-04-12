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
            'name' => ['required', 'string', 'max:60'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:100', 'unique:'.User::class],
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
            'name' => ['required', 'string', 'max:60'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:100', 'unique:'.User::class.',email,'.$user->id],
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
            'file_excel' => 'required|max:5120|mimes:xlsx,xls,csv,zip|mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,text/plain,application/csv,application/x-csv,application/zip,application/octet-stream',
        ], [
            'file_excel.required' => 'File Excel wajib diunggah.',
            'file_excel.mimes'    => 'Format tidak valid. Pastikan file berakhiran .xlsx, .xls, atau .csv.',
            'file_excel.mimetypes' => 'Tipe berkas tidak didukung atau terdeteksi salah oleh sistem.',
            'file_excel.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            $tmpPath = $request->file('file_excel')->getRealPath();
            $import = new UserImport();
            $import->import($tmpPath);
            $summary = $import->getSummary();

            $msg = "Import selesai. ";
            $msg .= "Berhasil: {$summary['sukses']} siswa. ";
            if ($summary['terlewati'] > 0) {
                $msg .= "Dilewati (Sudah ada): {$summary['terlewati']}. ";
            }
            if ($summary['gagal'] > 0) {
                $msg .= "Gagal: {$summary['gagal']}. ";
            }

            if ($summary['sukses'] > 0 || $summary['terlewati'] > 0) {
                return redirect()->route('admin.users.index')->with('success', $msg);
            }
            return back()->with('error', 'Tidak ada data siswa yang berhasil diimport. ' . $msg);
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
            'A1' => 'Nama Murid (Maks 60 huruf)',
            'B1' => 'Alamat Email (Maks 100 huruf)',
            'C1' => 'Password (Min 8 huruf)',
            'D1' => 'ID Kelas (Angka)',
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Header Styling
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10b981']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => false],
        ]);
        
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Sample Row
        $sheet->setCellValue('A2', 'Budi Santoso');
        $sheet->setCellValue('B2', 'budi123@gmail.com');
        $sheet->setCellValue('C2', 'rahasia123');
        $sheet->setCellValue('D2', '1'); // Contoh ID kelas 1
        
        $sheet->setCellValue('A3', 'Siti Aminah');
        $sheet->setCellValue('B3', 'siti@yahoo.com');
        $sheet->setCellValue('C3', 'sandisiti99');
        $sheet->setCellValue('D3', ''); 

        $sheet->getStyle('A2:D3')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
        ]);

        // --- Sheet 2: Reference (Panduan & ID Kelas) ---
        $refSheet = $spreadsheet->createSheet();
        $refSheet->setTitle('CARA BACA & BANTUAN');
        
        $panduan = [
            ['PANDUAN MUDAH MENGISI DATA MURID BARU'],
            [''],
            ['--- CARA MENGISI ---'],
            ['1. Buka sheet "Template Import" yang ada di sebelah kiri bawah layar Anda.'],
            ['2. Baris yang berwarna abu-abu (Budi & Siti) HANYA CONTOH. Silakan dihapus saja.'],
            ['3. Ketik nama murid di Kolom A (Maksimal 60 karakter). Jika terlalu panjang akan otomatis terpotong.'],
            ['4. Ketik email mereka di Kolom B (Maksimal 100 karakter).'],
            ['5. Ketik password/sandi di Kolom C (wajib minimal 8 karakter).'],
            ['6. Jika Anda ingin langsung memasukkan murid ke sebuah kelas, isi angka Nomor ID Kelas di Kolom D.'],
            ['7. Lihat daftar Nomor ID Kelas resmi di bawah ini (bukan nama kelasnya, tapi ketik ANGKANYA saja).'],
            [''],
            ['--- DAFTAR NOMOR ID KELAS SAAT INI ---'],
            ['Nomor ID Kelas', 'Nama Kelas aslinya'],
        ];

        $rowNum = 1;
        foreach ($panduan as $row) {
            if (count($row) == 1) {
                $refSheet->setCellValue('A' . $rowNum, $row[0]);
            } else {
                $refSheet->setCellValue('A' . $rowNum, $row[0]);
                $refSheet->setCellValue('B' . $rowNum, $row[1]);
            }
            $rowNum++;
        }

        // Apply styles to Guide
        $refSheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'E11D48'], 'size' => 14],
        ]);
        $refSheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A'], 'size' => 12],
        ]);
        $refSheet->getStyle('A12')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '0F172A'], 'size' => 12],
        ]);
        $refSheet->getStyle('A13:B13')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
        ]);

        $kelas = \App\Models\Kelas::all();
        foreach ($kelas as $k) {
            $refSheet->setCellValue('A' . $rowNum, $k->id);
            $refSheet->setCellValue('B' . $rowNum, $k->nama);
            
            // Format center ID
            $refSheet->getStyle('A'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rowNum++;
        }
        $refSheet->getColumnDimension('A')->setWidth(35);
        $refSheet->getColumnDimension('B')->setWidth(50);

        $spreadsheet->setActiveSheetIndex(0); // Go back to first sheet

        $tmpFile = tempnam(sys_get_temp_dir(), 'lpk_template_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpFile);

        return response()->download($tmpFile, 'Template_Import_Siswa_LPK.xlsx')->deleteFileAfterSend(true);
    }
}
