@extends('layouts.guru')

@section('header')
<div class="flex justify-between items-center w-full">
    <span>Log Kecurangan — Ujian Saya</span>
</div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 shadow-sm rounded-r-lg">
    <div class="flex"><div class="ml-3"><p class="text-sm text-green-700 font-medium">{{ session('success') }}</p></div></div>
</div>
@endif

{{-- Info Banner --}}
<div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6 flex items-start space-x-3 shadow-sm">
    <svg class="h-6 w-6 text-amber-500 flex-shrink-0 animate-pulse mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <div>
        <h3 class="text-sm font-semibold text-amber-800">Deteksi Tab-Switch Aktif</h3>
        <p class="mt-1 text-sm text-amber-700">
            Log di bawah hanya menampilkan kejadian dari <strong>ujian yang Anda buat</strong>.
            Klik <strong>Izinkan</strong> untuk membuka kembali ujian murid secara instan (via
            <span class="font-mono bg-amber-100 px-1 rounded text-xs">WebSocket</span>).
            Klik <strong>Tolak</strong> jika pelanggaran dianggap disengaja (ujian terkunci permanen).
        </p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Peserta</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Ujian</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Keputusan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50 transition-colors {{ $log->status === 'pending' ? 'bg-red-50/30' : '' }}">

                    {{-- Waktu --}}
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="font-semibold text-slate-800">{{ $log->created_at->format('H:i:s') }}</span><br>
                        <span class="text-slate-500 text-xs">{{ $log->created_at->format('d M Y') }}</span>
                    </td>

                    {{-- Peserta --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-slate-900">{{ optional($log->ujianPeserta->user)->name ?? '—' }}</div>
                        <div class="text-xs text-slate-500">{{ optional($log->ujianPeserta->user)->email ?? '' }}</div>
                    </td>

                    {{-- Ujian --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-slate-800 max-w-[200px] truncate">
                            {{ optional($log->ujianPeserta->ujian)->judul ?? '—' }}
                        </div>
                    </td>

                    {{-- Status Badge --}}
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($log->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200 animate-pulse">
                                Menunggu Review
                            </span>
                        @elseif($log->status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                ✓ Diizinkan
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                ✗ Ditolak
                            </span>
                        @endif
                        @if($log->status !== 'pending' && $log->approvedBy)
                            <div class="text-[10px] text-slate-400 mt-1">oleh {{ explode(' ', $log->approvedBy->name)[0] }}</div>
                        @endif
                    </td>

                    {{-- Tombol                     <td id="row-action-{{ $log->id }}" class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        @if($log->status === 'pending')
                        <div class="flex justify-center space-x-2">
                            {{-- Izinkan --}}
                            <form action="{{ route('guru.cheat-logs.approve', $log) }}" method="POST" class="inline ajax-form" data-id="{{ $log->id }}" data-status="approved">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700 transition-colors">
                                    <svg class="mr-1 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Izinkan
                                </button>
                            </form>
 
                            {{-- Tolak --}}
                            <form action="{{ route('guru.cheat-logs.approve', $log) }}" method="POST" class="inline ajax-form" data-id="{{ $log->id }}" data-status="rejected">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-red-600 hover:bg-red-700 transition-colors">
                                    <svg class="mr-1 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Tolak
                                </button>
                            </form>
                        </div>
                        @else
                            <span class="text-slate-300 text-xs">Selesai</span>
                        @endif
                    </td>
 
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-14 text-center">
                        <div class="mx-auto w-14 h-14 bg-green-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-7 w-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="block text-sm font-medium text-slate-900 mb-1">Tidak Ada Pelanggaran</span>
                        <span class="block text-xs text-slate-500">Ujian-ujian Anda berjalan bersih tanpa aktivitas mencurigakan.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
 
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $logs->links() }}
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ajaxForms = document.querySelectorAll('.ajax-form');
    
    ajaxForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const logId = this.dataset.id;
            const status = this.dataset.status;
            const actionUrl = this.getAttribute('action');
            const token = this.querySelector('[name="_token"]').value;
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if(!confirm(status === 'approved' ? 'Izinkan peserta melanjutkan?' : 'Tandai sebagai pelanggaran berat?')) {
                return;
            }
            
            // Loading state
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            
            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update Action Cell
                    document.getElementById('row-action-' + logId).innerHTML = '<span class="text-slate-300 text-xs">Selesai</span>';
                    
                    // Optional: Update Row decoration (remove red glow)
                    const row = document.getElementById('row-action-' + logId).closest('tr');
                    row.classList.remove('bg-red-50/30');
                    
                    // Update Status Badge via DOM navigation (assuming table structure)
                    const statusCell = row.querySelectorAll('td')[3];
                    if (status === 'approved') {
                        statusCell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">✓ Diizinkan</span>';
                    } else {
                        statusCell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">✗ Ditolak</span>';
                    }
                } else {
                    alert('Gagal memproses data.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            });
        });
    });
});
</script>
@endsectionendsection
