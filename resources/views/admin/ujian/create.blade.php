@extends('layouts.admin')

@section('header', 'Tambah Ujian Baru')
@section('header-sub', 'Buat paket ujian dan tentukan durasi pengerjaan')

@section('content')
<div class="card p-8" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ route('admin.ujian.store') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label class="block text-sm font-bold mb-2 text-slate-700">Judul Ujian</label>
            <input type="text" name="judul" class="w-full px-4 py-3 border border-slate-200 rounded-lg outline-none focus:border-blue-500" placeholder="Contoh: UTS Bahasa Korea Dasar" required>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-bold mb-2 text-slate-700">Deskripsi Singkat</label>
            <textarea name="deskripsi" class="w-full px-4 py-3 border border-slate-200 rounded-lg outline-none focus:border-blue-500" placeholder="Tuliskan keterangan singkat mengenai ujian ini..." rows="3"></textarea>
        </div>

        <div class="grid-2 mb-6">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-700">Durasi (Menit)</label>
                <input type="number" name="durasi" class="w-full px-4 py-3 border border-slate-200 rounded-lg outline-none focus:border-blue-500" value="60" required>
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-700">Target Kelas</label>
                <select name="kelas_id" class="w-full px-4 py-3 border border-slate-200 rounded-lg outline-none focus:border-blue-500" required>
                    <option value="" disabled selected>Pilih Kelas...</option>
                    @foreach($kelases as $k)
                        <option value="{{ $k->id }}">{{ $k->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid-2 mb-8">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-700">Waktu Mulai</label>
                <input type="datetime-local" name="mulai" class="w-full px-4 py-3 border border-slate-200 rounded-lg outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-700">Waktu Berakhir</label>
                <input type="datetime-local" name="selesai" class="w-full px-4 py-3 border border-slate-200 rounded-lg outline-none focus:border-blue-500">
            </div>
        </div>

        <div style="border-top: 2px solid #f1f5f9; padding-top: 32px; margin-top: 32px;">
            <h3 class="text-xl font-bold text-slate-800 mb-6">Pilih Soal (Pilih minimal satu)</h3>
            <div style="max-height: 400px; overflow-y: auto; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 10px;">
                <table class="w-full text-left">
                    <thead>
                        <tr style="border-bottom: 2px solid #f1f5f9;">
                            <th class="py-3 px-2" style="width: 50px;">Pilih</th>
                            <th class="py-3 px-2">Pertanyaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($soals as $s)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td class="py-3 px-2 text-center">
                                <input type="checkbox" name="soal_ids[]" value="{{ $s->id }}" style="width: 17px; height: 17px;">
                            </td>
                            <td class="py-3 px-2 text-sm text-slate-700">
                                {!! Str::limit(strip_tags($s->pertanyaan), 80) !!}
                                <div style="font-size: 10px; color: #94a3b8; font-weight: 600; text-transform: uppercase;">{{ $s->tipe }}</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex-between mt-10" style="border-top: 1px solid #f1f5f9; padding-top: 24px;">
            <a href="{{ route('admin.ujian.index') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan & Daftarkan Ujian</button>
        </div>
    </form>
</div>
@endsection
