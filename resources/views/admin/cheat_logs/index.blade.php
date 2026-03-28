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
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        @if($log->status === 'pending')
                        <div class="flex justify-center space-x-2">
                            <!-- Approve Button (Tidak Sengaja) -->
                            <form action="{{ route('admin.cheat-logs.approve', $log) }}" method="POST" class="inline" onsubmit="return confirm('Izinkan peserta ini melanjutkan ujian?');">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded shadow-sm text-white bg-green-600 hover:bg-green-700 transition-colors">
                                    <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Izinkan
                                </button>
                            </form>
                            
                            <!-- Reject Button (Sengaja/Pelanggaran Berat) -->
                            <form action="{{ route('admin.cheat-logs.approve', $log) }}" method="POST" class="inline" onsubmit="return confirm('Tandai sebagai pelanggaran berat? Ujian akan dikunci permanen untuk peserta ini.');">
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
@endsection
