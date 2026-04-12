@extends('layouts.guru')

@section('header')
<div class="flex items-center space-x-4">
    <a href="{{ route('guru.paket-soal.show', $soal->paket_soal_id) }}" class="text-slate-500 hover:text-slate-700 transition-colors">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    </a>
    <span>Edit Soal: {{ strip_tags(substr($soal->pertanyaan, 0, 40)) }}...</span>
</div>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden max-w-4xl mx-auto">
    <form action="{{ route('guru.soal.update', $soal) }}" method="POST" class="p-8" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
        @endif

        @include('_partials.soal_form_fields', [
            'soal'       => $soal,
            'audioFiles' => $audioFiles,
            'imageFiles' => $imageFiles,
            'baseRoute'  => 'guru',
            'uploadRoute' => 'guru.soal.uploadMedia',
        ])

        <div class="pt-10 flex justify-end border-t border-slate-100 mt-8 gap-4">
            <a href="{{ route('guru.paket-soal.show', $soal->paket_soal_id) }}"
               class="bg-white border border-slate-300 rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                class="ml-3 inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-accent-600 hover:bg-accent-700 transition-colors">
                Perbarui Soal
            </button>
        </div>
    </form>
</div>

@include('_partials.soal_form_js', [
    'soal'       => $soal,
    'audioFiles' => $audioFiles,
    'imageFiles' => $imageFiles,
    'uploadRoute' => 'guru.soal.uploadMedia',
])
@endsection
