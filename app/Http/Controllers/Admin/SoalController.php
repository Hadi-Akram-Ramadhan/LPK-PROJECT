<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HtmlSanitizer;
use App\Http\Controllers\Controller;
use App\Models\Soal;
use App\Models\PaketSoal;
use App\Models\PilihanJawaban;
use App\Imports\SoalImport;
use App\Traits\ImageCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SoalController extends Controller
{
    use ImageCompressor;
    const TIPE_VALID = [
        'pilihan_ganda', 'multiple_choice', 'essay', 'audio',
        'pilihan_ganda_audio', 'pilihan_ganda_gambar', 'short_answer', 'matching'
    ];
    const TIPE_LISTENING = ['audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return redirect()->route('admin.paket-soal.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $paketSoal = null;
        if ($request->filled('paket')) {
            $paketSoal = PaketSoal::findOrFail($request->paket);
        }

        $audioFiles = collect(Storage::disk('local')->files('audio'))->map(fn($f) => basename($f));
        $imageFiles = collect(Storage::disk('public')->files('gambar'))->map(fn($f) => basename($f));

        return view('admin.soal.create', compact('audioFiles', 'imageFiles', 'paketSoal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'paket_soal_id'  => 'required|exists:paket_soals,id',
            'tipe'           => 'required|in:' . implode(',', self::TIPE_VALID),
            'pertanyaan'     => 'required|string|max:2000',
            'poin'           => 'required|numeric|min:0.01|max:1000',
            'audio_path'     => 'nullable|string|max:255',
            'gambar_path'    => 'nullable|string|max:255',
            'audio_max_play' => 'nullable|integer|min:1|max:99',
            'jawaban_kunci'  => 'nullable|string|max:300',
            'pilihan.*'      => 'nullable|string|max:300',
            'pasang_kiri.*'  => 'nullable|string|max:200',
            'pasang_kanan.*' => 'nullable|string|max:200',
        ]);

        DB::beginTransaction();
        try {
            $soal = Soal::create([
                'guru_id'        => auth()->id(),
                'paket_soal_id'  => $request->paket_soal_id,
                'tipe'           => $request->tipe,
                'pertanyaan'     => HtmlSanitizer::clean($request->pertanyaan),
                'poin'           => $request->poin,
                'audio_path'     => $request->audio_path,
                'gambar_path'    => $request->gambar_path,
                'jawaban_kunci'  => $request->tipe === 'short_answer' ? $request->jawaban_kunci : null,
                'audio_max_play' => in_array($request->tipe, self::TIPE_LISTENING) ? $request->audio_max_play : null,
            ]);

            if ($request->tipe === 'matching') {
                $this->saveMatchingPairs($soal->id, $request);
            } elseif (in_array($request->tipe, ['pilihan_ganda', 'multiple_choice', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
                $this->savePilihanJawaban($soal, $request);
            }

            DB::commit();
            return redirect()->route('admin.paket-soal.show', $request->paket_soal_id)
                ->with('success', 'Soal berhasil disimpan ke paket.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan soal: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Soal $soal)
    {
        $soal->load('pilihanJawabans');
        $audioFiles = collect(Storage::disk('local')->files('audio'))->map(fn($f) => basename($f));
        $imageFiles = collect(Storage::disk('public')->files('gambar'))->map(fn($f) => basename($f));
        return view('admin.soal.edit', compact('soal', 'audioFiles', 'imageFiles'));
    }

    public function update(Request $request, Soal $soal)
    {
        $request->validate([
            'pertanyaan'     => 'required|string|max:2000',
            'poin'           => 'required|numeric|min:0.01|max:1000',
            'audio_path'     => 'nullable|string|max:255',
            'gambar_path'    => 'nullable|string|max:255',
            'audio_max_play' => 'nullable|integer|min:1|max:99',
            'jawaban_kunci'  => 'nullable|string|max:300',
            'pilihan.*'      => 'nullable|string|max:300',
            'pasang_kiri.*'  => 'nullable|string|max:200',
            'pasang_kanan.*' => 'nullable|string|max:200',
        ]);

        DB::beginTransaction();
        try {
            $updateData = [
                'pertanyaan'  => HtmlSanitizer::clean($request->pertanyaan),
                'poin'        => $request->poin,
                'audio_path'  => $request->audio_path,
                'gambar_path' => $request->gambar_path,
            ];
            if ($request->filled('tipe')) {
                $updateData['tipe'] = $request->tipe;
            }
            $effectiveTipe = $updateData['tipe'] ?? $soal->tipe;
            $updateData['jawaban_kunci']  = $effectiveTipe === 'short_answer' ? $request->jawaban_kunci : null;
            $updateData['audio_max_play'] = in_array($effectiveTipe, self::TIPE_LISTENING) ? $request->audio_max_play : null;

            $soal->update($updateData);

            if ($effectiveTipe === 'matching') {
                $soal->pilihanJawabans()->delete();
                $this->saveMatchingPairs($soal->id, $request);
            } elseif (in_array($effectiveTipe, ['pilihan_ganda', 'multiple_choice', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
                $soal->pilihanJawabans()->delete();
                $this->savePilihanJawaban($soal, $request);
            } else {
                // essay, short_answer: hapus pilihan jika ada
                $soal->pilihanJawabans()->delete();
            }

            DB::commit();
            return redirect()->route('admin.paket-soal.show', $soal->paket_soal_id)
                ->with('success', 'Soal berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Soal $soal)
    {
        $paketId = $soal->paket_soal_id;
        $soal->pilihanJawabans()->delete();
        $soal->delete();
        return redirect()->route('admin.paket-soal.show', $paketId)
            ->with('success', 'Soal berhasil dihapus.');
    }

    // ── Upload Media Langsung (AJAX) ─────────────────────────────
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file'  => 'required|file|max:51200',
            'jenis' => 'required|in:gambar,audio',
        ]);

        $file = $request->file('file');
        $jenis = $request->jenis;

        if ($jenis === 'gambar') {
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $allowedExt)) {
                return response()->json(['success' => false, 'message' => 'Format gambar tidak valid. Gunakan JPG, PNG, atau WEBP.'], 422);
            }
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $ext;
            $targetDir = storage_path('app/public/gambar');
            if (!file_exists($targetDir)) mkdir($targetDir, 0755, true);

            // Compress & save
            $targetPath = $targetDir . '/' . $filename;
            $this->compressAndSaveImage($file->getRealPath(), $targetPath);
            return response()->json(['success' => true, 'path' => 'gambar/' . $filename, 'filename' => $filename]);
        } else {
            $allowedExt = ['mp3', 'mpeg', 'mpga', 'wav', 'ogg'];
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $allowedExt)) {
                return response()->json(['success' => false, 'message' => 'Format audio tidak valid. Gunakan MP3, WAV, atau OGG.'], 422);
            }
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $ext;
            $file->storeAs('audio', $filename, 'local');
            return response()->json(['success' => true, 'path' => 'audio/' . $filename, 'filename' => $filename]);
        }
    }

    // ── Helpers ──────────────────────────────────────────────────
    private function saveMatchingPairs(int $soalId, Request $request): void
    {
        $kiriList  = $request->input('pasang_kiri', []);
        $kananList = $request->input('pasang_kanan', []);
        $kiriGambar  = $request->input('pasang_kiri_gambar', []);
        $kananGambar = $request->input('pasang_kanan_gambar', []);

        foreach ($kiriList as $idx => $kiriTeks) {
            $kananTeks = $kananList[$idx] ?? '';

            // Determine konten kiri (teks ATAU gambar)
            $kiriVal    = !empty($kiriGambar[$idx]) ? $kiriGambar[$idx] : trim($kiriTeks);
            $kananVal   = !empty($kananGambar[$idx]) ? $kananGambar[$idx] : trim($kananTeks);
            $kiriIsGambar  = !empty($kiriGambar[$idx]);
            $kananIsGambar = !empty($kananGambar[$idx]);

            if (empty($kiriVal) && empty($kananVal)) continue;

            if ($kiriIsGambar && $kananIsGambar) {
                $mediaTipe = 'matching_gambar_keduanya';
            } elseif ($kiriIsGambar) {
                $mediaTipe = 'matching_gambar_kiri';
            } elseif ($kananIsGambar) {
                $mediaTipe = 'matching_gambar_kanan';
            } else {
                $mediaTipe = 'matching_teks';
            }

            PilihanJawaban::create([
                'soal_id'    => $soalId,
                'teks'       => $kiriVal,
                'media_path' => $kananVal,
                'media_tipe' => $mediaTipe,
                'is_benar'   => true,
            ]);
        }
    }

    private function savePilihanJawaban(Soal $soal, Request $request): void
    {
        if ($request->has('pilihan') && is_array($request->pilihan)) {
            foreach ($request->pilihan as $index => $teks) {
                $mediaPath = $request->pilihan_media[$index] ?? null;
                $audioMaxPlay = null;

                if (!empty($teks) || !empty($mediaPath)) {
                    $isBenar = false;
                    if (in_array($soal->tipe, ['pilihan_ganda', 'audio', 'pilihan_ganda_audio', 'pilihan_ganda_gambar'])) {
                        $isBenar = ($request->jawaban_benar == $index);
                    } elseif ($soal->tipe == 'multiple_choice') {
                        $isBenar = (is_array($request->jawaban_benar) && in_array($index, $request->jawaban_benar));
                    }

                    $mediaTipe = null;
                    if (!empty($mediaPath)) {
                        $mediaTipe = ($soal->tipe === 'pilihan_ganda_audio') ? 'audio' : 'gambar';
                        if ($mediaTipe === 'audio') {
                            $audioMaxPlay = $request->input('pilihan_audio_max_play.' . $index) ?: null;
                        }
                    }

                    PilihanJawaban::create([
                        'soal_id'       => $soal->id,
                        'teks'          => $teks ?? '',
                        'media_path'    => $mediaPath,
                        'media_tipe'    => $mediaTipe,
                        'is_benar'      => $isBenar,
                        'audio_max_play'=> $audioMaxPlay,
                    ]);
                }
            }
        }
    }

    // ── Import Soal ───────────────────────────────────────────
    public function import()
    {
        $paketSoals = PaketSoal::orderBy('nama')->get();
        return view('admin.soal.import', compact('paketSoals'));
    }

    public function storeImport(Request $request)
    {
        $request->validate([
            'paket_soal_id' => 'required|exists:paket_soals,id',
            'file_excel'    => 'required|max:5120|mimes:xlsx,xls,csv,zip|mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv,text/plain,application/csv,application/x-csv,application/zip,application/octet-stream',
        ], [
            'file_excel.required' => 'File Excel wajib diunggah.',
            'file_excel.mimes'    => 'Format tidak valid. Pastikan file berakhiran .xlsx, .xls, atau .csv.',
            'file_excel.mimetypes' => 'Tipe berkas tidak didukung atau terdeteksi salah oleh sistem.',
            'file_excel.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            $tmpPath = $request->file('file_excel')->getRealPath();
            $import = new SoalImport($request->paket_soal_id);
            $import->import($tmpPath);
            $summary = $import->getSummary();

            if ($summary['sukses'] > 0) {
                return redirect()->route('admin.soal.index')->with('success', "{$summary['sukses']} soal berhasil diimport.");
            }
            return back()->with('error', 'Tidak ada soal yang berhasil diimport.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengimport soal. Pastikan format file benar.');
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Soal');

        $headers = [
            'A1' => 'Tipe Soal (Wajib)',
            'B1' => 'Tuliskan Pertanyaan / Soalnya',
            'C1' => 'Nama File Gambar (Opsional)',
            'D1' => 'Nama File Audio/Suara (Opsional)',
            'E1' => 'Jawaban A (Maks 300)',
            'F1' => 'Media A (Opsional)',
            'G1' => 'Jawaban B (Maks 300)',
            'H1' => 'Media B (Opsional)',
            'I1' => 'Jawaban C (Maks 300)',
            'J1' => 'Media C (Opsional)',
            'K1' => 'Jawaban D (Maks 300)',
            'L1' => 'Media D (Opsional)',
            'M1' => 'Jawaban E (Maks 300)',
            'N1' => 'Media E (Opsional)',
            'O1' => 'Kunci Jawaban Benar',
            'P1' => 'Nilai Point (Angka)',
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A1:P1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563eb']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(40);
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Contoh baris 2 — Pilihan Ganda
        $sheet->fromArray(['Pilihan Ganda', 'Apa arti kata "Hakgyo"?', '', '', 'Rumah Tangga', '', 'Sekolah', '', 'Stasiun', '', 'Kantor', '', '', '', 'B', '10'], null, 'A2');
        // Contoh baris 3 — Multiple Choice
        $sheet->fromArray(['Multiple Choice', 'Manakah yang termasuk kata benda?', '', '', 'Mokta (먹다)', '', 'Chaek (책)', '', 'Yeonpil (연필)', '', 'Masida (마시다)', '', '', '', 'B,C', '15'], null, 'A3');
        // Contoh baris 4 — Essay
        $sheet->fromArray(['Essay', 'Sebutkan 3 alat transportasi dalam bahasa Korea!', '', '', '', '', '', '', '', '', '', '', '', '', '', '20'], null, 'A4');
        // Contoh baris 5 — Audio
        $sheet->fromArray(['Audio', 'Dengarkan audio dan pilih jawaban yang tepat.', '', 'suara-soal-10.mp3', 'Pilih A', '', 'Pilih B', '', 'Pilih C', '', 'Pilih D', '', '', '', 'C', '10'], null, 'A5');
        // Contoh baris 6 — Pilihan Ganda Gambar (Bisa Soal Audio juga)
        $sheet->fromArray(['Listening Gambar', 'Manakah gambar yang menunjukkan stasiun?', '', 'suara-soal.mp3', '', 'stasiun.jpg', '', 'kantor.png', '', 'pasar.jpg', '', 'mall.png', '', '', 'A', '10'], null, 'A6');
        // Contoh baris 7 — Short Answer
        $sheet->fromArray(['Short Answer', 'Apa nama ibukota Korea Selatan?', '', '', '', '', '', '', '', '', '', '', '', '', 'Seoul|Seol|서울', '15'], null, 'A7');
        // Contoh baris 8 — Matching
        $sheet->fromArray(['Matching', 'Pasangkan kata Korea dengan artinya!', '', '', '사과', 'Apel', '포도', 'Anggur', '수박', 'Semangka', '딸기', 'Stroberi', '', '', '-', '20'], null, 'A8');

        // Zebra styling
        $sheet->getStyle('A2:P8')->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']]]);
        $sheet->getStyle('A7:P7')->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']], 'font' => ['color' => ['rgb' => '1D4ED8']]]);
        $sheet->getStyle('A8:P8')->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']], 'font' => ['color' => ['rgb' => '166534']]]);

        // ── Sheet 2: PANDUAN ──────────────────────────────────────
        $guide = $spreadsheet->createSheet();
        $guide->setTitle('CARA BACA PANDUAN');

        $guideRows = [
            ['BACA INI DULU SEBELUM MENGISI SOAL!'],
            [''],
            ['CARA MENGISI SETIAP KOLOM (Sangat Penting):'],
            ['Kolom A (Tipe Soal)', 'WAJIB DIISI! Pilih dari daftar valid di bawah.'],
            ['Kolom B (Pertanyaan)', 'WAJIB DIISI! Maks 2000 karakter. Jika lebih akan otomatis terpotong.'],
            ['Kolom C & D (Gambar / Audio)', 'OPSIONAL. Jika soal bergambar/bersuara, ketik nama file persis. Anda HARUS sudah upload file ke sistem.'],
            ['Kolom E, G, I, K, M', 'Maks 300 karakter per opsi. Untuk Matching: Maks 200 karakter.'],
            ['Kolom F, H, J, L, N', 'OPSIONAL. Matching Sisi Kanan: Maks 200 karakter.'],
            ['Kolom O (Kunci Jawaban)', 'Untuk PG: huruf A/B/C. Untuk Multiple Choice: A,B,C. Untuk Matching: isi tanda minus (-). Untuk Short Answer: jawaban|alternatif.'],
            ['Kolom P (Poin Nilai)', 'WAJIB. Ketik angka (contoh: 10 atau 2.5).'],
            [''],
            ['--- DAFTAR TIPE SOAL YANG VALID (Kolom A) ---'],
            ['→ Pilihan Ganda', 'Satu jawaban benar. Kunci: A, B, C, D, atau E.'],
            ['→ Multiple Choice', 'Banyak jawaban benar. Kunci: A,B,C (pisah koma).'],
            ['→ Essay', 'Jawaban bebas, dinilai manual guru. Kolom O: kosong.'],
            ['→ Short Answer', 'Isian singkat, dinilai otomatis. Kolom O: kunci|alternatif.'],
            ['→ Audio', 'Soal listening. Kolom D wajib diisi nama file mp3.'],
            ['→ Pilihan Ganda Gambar / Listening Gambar', 'Opsi jawaban berupa gambar. Isi Kolom F/H/J/L/N dengan nama file gambar. Khusus Listening Gambar, Anda juga bisa mengisi Kolom D dengan audio soal.'],
            ['→ Pilihan Ganda Audio', 'Opsi jawaban berupa audio. Isi Kolom F/H/J/L/N dengan nama file mp3.'],
            ['→ Matching', 'Pasangkan. Kolom E/F = Pasang 1 (kiri/kanan), G/H = Pasang 2, dst. Kolom O: isi tanda - (minus).'],
            [''],
            ['--- CATATAN PENTING ---'],
            ['1. Hapus baris contoh abu-abu sebelum memasukkan data soal asli.'],
            ['2. Nama file gambar/audio CASE SENSITIVE. "mobil.jpg" beda dengan "Mobil.jpg".'],
            ['3. SATU FILE EXCEL = SATU PAKET SOAL. Jangan campur paket berbeda.'],
            ['4. Untuk Matching dengan gambar: isi kolom kiri/kanan dengan nama file gambar (misal: apel.jpg). Sistem otomatis mendeteksi.'],
            ['5. PENGAMANAN AUDIO: Jumlah putar audio kini dicatat ketat oleh server. Siswa tidak bisa mencurangi jatah putar.'],
            ['6. SANITASI HTML: Hanya tag dasar (b, i, u, br, p) yang diizinkan di teks pertanyaan. Semua atribut gaya/warna akan dihapus demi keamanan.'],
        ];

        foreach ($guideRows as $i => $rowData) {
            $guide->fromArray($rowData, null, 'A' . ($i + 1));
        }

        $guide->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'E11D48']]]);
        foreach (['A3', 'A12', 'A21'] as $headerCell) {
            $guide->getStyle($headerCell)->applyFromArray(['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0F172A']]]);
        }
        $guide->getStyle('A4:A10')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
        ]);
        $guide->getColumnDimension('A')->setWidth(30);
        $guide->getColumnDimension('B')->setWidth(80);

        $spreadsheet->setActiveSheetIndex(0);

        $tmpFile = tempnam(sys_get_temp_dir(), 'lpk_template_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpFile);

        return response()->download($tmpFile, 'Template_Import_Soal_Admin.xlsx')->deleteFileAfterSend(true);
    }
}
