@extends('layouts.murid')

@section('content')
<div class="min-h-[85vh] flex flex-col items-center justify-center p-4">
    
    <div class="w-full max-w-2xl bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden animate-fade-in relative">
        <div class="absolute inset-0 bg-blue-50/50 backdrop-blur-sm z-0"></div>
        
        <div class="relative z-10 p-8 md:p-12">
            <!-- Header Progress -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-black text-slate-800 tracking-tight">Sistem Cerdas</h2>
                    <p class="text-sm font-semibold text-blue-600 tracking-wider uppercase mt-1">Tes Buta Warna Terintegrasi</p>
                </div>
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center border-4 border-white shadow-sm">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-slate-200 rounded-full h-2.5 mb-8 overflow-hidden">
                <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-500 shadow-sm" style="width: 0%"></div>
            </div>

            <form id="colorBlindForm" action="{{ route('murid.exam.buta_warna.submit', $ujian_peserta) }}" method="POST">
                @csrf
                
                <div id="slides-container" class="relative overflow-hidden w-full" style="height: 450px;">
                    
                    <!-- Intro Slide -->
                    <div id="slide-intro" class="slide-item w-full transition-all duration-500 absolute inset-0 flex flex-col justify-center translate-x-0 opacity-100 z-20">
                        <h3 class="text-xl font-bold text-slate-800 mb-4 text-center">Instruksi Pengerjaan</h3>
                        <p class="text-slate-600 text-center mb-6 leading-relaxed">
                            Sebagai tahap akhir ujian, Anda diwajibkan melalui pemeriksaan buta warna. Anda akan ditampilkan beberapa plat warna Ishihara secara acak. <strong>Tuliskan angka</strong> yang Anda lihat di setiap gambar tersebut.
                        </p>
                        <button type="button" onclick="nextSlide(0)" class="mx-auto w-full max-w-xs flex justify-center items-center py-4 px-6 border border-transparent rounded-xl shadow-md text-base font-bold text-white bg-blue-600 hover:bg-blue-700 hover:-translate-y-1 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Mulai Tes
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>

                    <!-- Question Slides -->
                    @foreach($soals as $index => $soal)
                    <div id="slide-{{ $index + 1 }}" class="slide-item w-full transition-all duration-500 absolute inset-0 flex flex-col items-center justify-center translate-x-full opacity-0 pointer-events-none">
                        
                        <div class="w-48 h-48 md:w-56 md:h-56 bg-slate-100 rounded-full border-8 border-white p-1 shadow-lg mb-8 overflow-hidden flex items-center justify-center bg-white group">
                            <img src="{{ asset('storage/' . $soal->gambar_path) }}" alt="Ishihara Plate {{ $index+1 }}" class="w-full h-full object-cover rounded-full group-hover:scale-105 transition-transform duration-500">
                        </div>

                        <div class="w-full max-w-sm text-center">
                            <label class="block text-sm font-bold text-slate-700 mb-3">Angka apa yang Anda lihat?</label>
                            <input type="text" 
                                name="answers[{{ $soal->id }}]" 
                                id="input-{{ $index + 1 }}"
                                class="ans-input block w-full text-center text-3xl font-black text-slate-800 py-4 border-2 border-slate-300 rounded-xl focus:ring-0 focus:border-blue-500 placeholder-slate-300 transition-colors shadow-sm"
                                placeholder="..."
                                autocomplete="off"
                                onkeypress="handleEnter(event, {{ $index + 1 }})">
                            
                            <button type="button" onclick="nextSlide({{ $index + 1 }})" class="mt-6 w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-xl shadow-md text-base font-bold text-white bg-slate-800 hover:bg-slate-900 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900">
                                Lanjut
                            </button>
                        </div>
                    </div>
                    @endforeach

                    <!-- Final Slide -->
                    <div id="slide-out" class="slide-item w-full transition-all duration-500 absolute inset-0 flex flex-col justify-center items-center translate-x-full opacity-0 pointer-events-none">
                        <div class="w-20 h-20 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-6">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-800 mb-2">Terima Kasih!</h3>
                        <p class="text-slate-500 mb-8 text-center max-w-xs">Data Anda akan disimpan sebagai lampiran profil medis di dalam rekap ujian.</p>
                        
                        <button type="submit" id="btn-submit-final" class="w-full max-w-xs flex justify-center items-center py-4 px-6 border border-transparent rounded-xl shadow-md text-base font-bold text-white bg-blue-600 hover:bg-blue-700 transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Lihat Menu Hasil Ujian
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>

<script>
    const totalQuestions = {{ $soals->count() }};
    
    function nextSlide(currentIndex) {
        // Validation if it's an answer slide
        if(currentIndex > 0 && currentIndex <= totalQuestions) {
            const input = document.getElementById('input-' + currentIndex);
            if(input.value.trim() === '') {
                // Shake effect on empty input
                input.classList.add('border-red-500', 'bg-red-50');
                input.style.transform = 'translate(-5px, 0)';
                setTimeout(() => { input.style.transform = 'translate(5px, 0)'; }, 50);
                setTimeout(() => { input.style.transform = 'translate(-5px, 0)'; }, 100);
                setTimeout(() => { input.style.transform = 'translate(5px, 0)'; }, 150);
                setTimeout(() => { 
                    input.style.transform = 'translate(0, 0)'; 
                    input.classList.remove('border-red-500', 'bg-red-50');
                }, 200);
                input.focus();
                return;
            }
        }

        const currentSlide = currentIndex === 0 ? document.getElementById('slide-intro') : document.getElementById('slide-' + currentIndex);
        let nextSlideId = currentIndex === totalQuestions ? 'slide-out' : 'slide-' + (currentIndex + 1);
        const targetSlide = document.getElementById(nextSlideId);
        
        // Progress update
        if(currentIndex >= 0 && currentIndex <= totalQuestions) {
            const progress = ((currentIndex + 1) / (totalQuestions + 1)) * 100;
            document.getElementById('progress-bar').style.width = progress + '%';
        }

        // Animate Out
        currentSlide.classList.remove('translate-x-0', 'opacity-100');
        currentSlide.classList.add('-translate-x-full', 'opacity-0');
        setTimeout(() => {
            currentSlide.classList.add('pointer-events-none');
            currentSlide.classList.remove('z-20');
        }, 500);

        // Animate In
        targetSlide.classList.remove('translate-x-full', 'opacity-0', 'pointer-events-none');
        targetSlide.classList.add('translate-x-0', 'opacity-100', 'z-20');

        // Focus logic
        setTimeout(() => {
            if(currentIndex < totalQuestions) {
                document.getElementById('input-' + (currentIndex + 1)).focus();
            } else {
                document.getElementById('btn-submit-final').focus();
            }
        }, 100);
    }

    function handleEnter(e, currentIndex) {
        if(e.key === 'Enter') {
            e.preventDefault();
            nextSlide(currentIndex);
        }
    }
</script>
@endsection
