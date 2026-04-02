@extends('layouts.admin')

@section('header')
<div class="flex justify-between items-center w-full">
    <span>Pemantauan Kecurangan (Anti-Cheat Logs)</span>
</div>
@endsection

@section('content')

@if(session('success'))
<div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 shadow-sm">
    <div class="flex">
        <div class="ml-3">
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

<div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6 relative overflow-hidden shadow-sm">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-red-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-semibold text-red-800">Sistem Deteksi Tab-Switch Aktif</h3>
            <div class="mt-1 text-sm text-red-700">
                <p>Ketika murid berpindah tab atau meminimalkan browser saat ujian, sistem otomatis mendeteksi dan mengunci layar mereka. Administrator atau Guru wajib melakukan persetujuan ("Sengaja" / "Tidak Sengaja") agar murid bisa melanjutkan ujian.</p>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu Kejadian</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Identitas Peserta</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Ujian</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi / Keputusan</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50 transition-colors {{ $log->status === 'pending' ? 'bg-red-50/30' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="font-semibold text-slate-800">{{ $log->timestamp->format('H:i:s') }}</span>
                        <br>
                        <span class="text-slate-500 text-xs">{{ $log->timestamp->format('d M Y') }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-slate-900">{{ optional($log->ujianPeserta->user)->name ?? 'Unknown User' }}</div>
                        <div class="text-xs text-slate-500">ID Peserta: #{{ $log->ujian_peserta_id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-slate-800">{{ optional($log->ujianPeserta->ujian)->judul ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                        @if($log->status === 'pending')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200 shadow-sm animate-pulse-slow">
                                Menunggu Review
                            </span>
                        @elseif($log->status === 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                Diizinkan Lanjut
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                Ditolak (Sengaja)
                            </span>
                        @endif
                        
                        @if($log->status !== 'pending' && $log->approvedBy)
                            <div class="text-xs text-slate-400 mt-1">oleh {{ explode(' ', $log->approvedBy->name)[0] }}</div>
                        @endif
                    </td>
                    <td id="row-action-{{ $log->id }}" class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        @if($log->status === 'pending')
                        <div class="flex justify-center space-x-2">
                            <!-- Approve Button (Tidak Sengaja) -->
                            <form action="{{ route('admin.cheat-logs.approve', $log) }}" method="POST" class="inline ajax-form" data-id="{{ $log->id }}" data-status="approved">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700 transition-colors">
                                    <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Izinkan
                                </button>
                            </form>
                            
                            <!-- Reject Button (Sengaja/Pelanggaran Berat) -->
                            <form action="{{ route('admin.cheat-logs.approve', $log) }}" method="POST" class="inline ajax-form" data-id="{{ $log->id }}" data-status="rejected">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-red-600 hover:bg-red-700 transition-colors">
                                    <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Tolak
                                </button>
                            </form>
                        </div>
                        @else
                            <span class="text-slate-300">Selesai</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        <div class="mx-auto w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="block text-sm font-medium text-slate-900 mb-1">Tidak Ada Pelanggaran</span>
                        <span class="block text-xs text-slate-500">Log perpindahan tab yang kosong menandakan ujian berjalan lancar.</span>
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
                    document.getElementById('row-action-' + logId).innerHTML = '<span class="text-slate-300">Selesai</span>';
                    
                    // Optional: Update Row decoration (remove red glow)
                    const row = document.getElementById('row-action-' + logId).closest('tr');
                    row.classList.remove('bg-red-50/30');
                    
                    // Update Status Badge via DOM navigation (assuming table structure)
                    const statusCell = row.querySelectorAll('td')[3];
                    if (status === 'approved') {
                        statusCell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">✓ Diizinkan Lanjut</span>';
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
@endsection
