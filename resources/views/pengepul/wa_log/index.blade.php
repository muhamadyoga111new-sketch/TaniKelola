@extends('layouts.app')

@section('title', 'WhatsApp Gateway')
@section('page_title', 'WhatsApp Gateway')
@section('page_subtitle', 'Monitoring pengiriman pesan otomatis ke petani')

@section('content')
<div class="space-y-6">

    <!-- Fonnte Status Banner -->
    <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-slate-900 text-white border border-slate-800 shadow-md">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-start space-x-3">
                <div class="w-10 h-10 rounded-xl bg-green-500/20 text-green-400 border border-green-500/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="font-bold text-sm sm:text-base text-white">WhatsApp Gateway</h3>
                        @if($tokenConfigured)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-500/20 text-green-300 border border-green-500/40">AKTIF</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/40">SIMULASI</span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-300 mt-1 max-w-xl hidden sm:block">
                        @if($tokenConfigured)
                            Token Fonnte aktif. Notifikasi akan dikirim otomatis ke petani.
                        @else
                            Mode Simulasi aktif. Semua notifikasi dicatat ke log tanpa kirim pesan asli.
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-2 text-xs">
                <div class="bg-white/5 p-2.5 sm:p-3 rounded-xl border border-white/10 text-center">
                    <span class="text-slate-400 block text-[10px]">Total</span>
                    <strong class="text-white text-sm">{{ $totalLogs }}</strong>
                </div>
                <div class="bg-white/5 p-2.5 sm:p-3 rounded-xl border border-white/10 text-center">
                    <span class="text-green-400 block text-[10px]">Terkirim</span>
                    <strong class="text-green-300 text-sm">{{ $totalSukses }}</strong>
                </div>
                <div class="bg-white/5 p-2.5 sm:p-3 rounded-xl border border-white/10 text-center">
                    <span class="text-blue-400 block text-[10px]">Simulasi</span>
                    <strong class="text-blue-300 text-sm">{{ $totalSimulasi }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Test Sender Card -->
    <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs">
        <h4 class="font-bold text-sm text-slate-900 mb-1">Uji Coba Kirim Pesan</h4>
        <p class="text-xs text-slate-500 mb-3 hidden sm:block">Kirim pesan uji coba untuk memvalidasi gateway.</p>

        <form action="{{ route('pengepul.wa_log.test') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp Tujuan *</label>
                <input type="text" name="target_phone" placeholder="Contoh: 08123456789" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Penerima *</label>
                <input type="text" name="recipient_name" placeholder="Contoh: Pak Penguji" required
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Isi Pesan Uji Coba *</label>
                <div class="flex space-x-2">
                    <input type="text" name="message" value="Halo, ini adalah pesan uji coba dari Sistem Informasi TaniKelola Bajuin." required
                        class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white font-bold text-xs shrink-0 transition">
                        Kirim Tes
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Log Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h4 class="font-bold text-sm text-slate-900">Riwayat Pengiriman Pesan WhatsApp</h4>
            <div class="flex items-center space-x-2">
                <a href="{{ route('pengepul.wa_log.index') }}" class="px-2.5 py-1 rounded-lg text-xs {{ !$status ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600' }}">Semua</a>
                <a href="{{ route('pengepul.wa_log.index', ['status' => 'simulasi']) }}" class="px-2.5 py-1 rounded-lg text-xs {{ $status === 'simulasi' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600' }}">Simulasi</a>
                <a href="{{ route('pengepul.wa_log.index', ['status' => 'sukses']) }}" class="px-2.5 py-1 rounded-lg text-xs {{ $status === 'sukses' ? 'bg-green-600 text-white' : 'bg-slate-100 text-slate-600' }}">Sukses</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="py-3 px-6">Waktu</th>
                        <th class="py-3 px-6">Penerima & Nomor</th>
                        <th class="py-3 px-6">Tipe Notifikasi</th>
                        <th class="py-3 px-6">Isi Pesan WhatsApp</th>
                        <th class="py-3 px-6 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-6 font-mono text-xs text-slate-400">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="font-bold text-slate-800 block">{{ $log->penerima_nama }}</span>
                                <span class="text-xs text-slate-500 font-mono">{{ $log->tujuan_nomor }}</span>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700">
                                    {{ str_replace('_', ' ', strtoupper($log->tipe_notifikasi)) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-600 max-w-md">
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 whitespace-pre-line font-mono text-[11px] leading-relaxed">
                                    {{ $log->pesan }}
                                </div>
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                @if($log->status === 'sukses')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700">Terkirim</span>
                                @elseif($log->status === 'simulasi')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">Simulasi</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">Gagal</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-sm">
                                Belum ada log pesan WhatsApp tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
