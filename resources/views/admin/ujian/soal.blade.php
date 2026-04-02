@extends('layouts.admin')

@section('header', 'Manajemen Soal Ujian')
@section('header-sub', $ujian->judul)

@section('content')
<div class="card p-8">
    <form action="{{ route('admin.ujian.updateSoal', $ujian) }}" method="POST">
        @csrf
        <div class="flex-between mb-6">
            <h3 class="text-xl font-bold text-slate-800">Daftar Bank Soal</h3>
            <button type="submit" class="btn btn-primary">Simpan Pilihan Soal</button>
        </div>

        <div style="overflow-x: auto;">
            <table class="w-full text-left" style="border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #f1f5f9;">
                        <th class="py-4 px-2" style="width: 50px;">Pilih</th>
                        <th class="py-4 px-4">Pertanyaan</th>
                        <th class="py-4 px-4">Tipe</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($soals as $s)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                        <td class="py-4 px-2 text-center">
                            <input type="checkbox" name="soal_ids[]" value="{{ $s->id }}" {{ in_array($s->id, $ujianSoalIds) ? 'checked' : '' }} style="width: 18px; height: 18px; cursor: pointer;">
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-medium text-slate-800">{!! Str::limit(strip_tags($s->pertanyaan), 100) !!}</div>
                        </td>
                        <td class="py-4 px-4">
                            <span style="padding: 4px 10px; background: #f1f5f9; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #64748b;">
                                {{ $s->tipe }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-10 text-center text-slate-400 italic">Belum ada soal di bank soal. Silakan tambah soal terlebih dahulu.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 flex-between">
            <a href="{{ route('admin.ujian.index') }}" class="btn btn-outline">Kembali</a>
            <button type="submit" class="btn btn-primary">Simpan Pilihan Soal</button>
        </div>
    </form>
</div>
@endsection
