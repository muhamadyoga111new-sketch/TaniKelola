@extends('layouts.app')

@section('title', 'Pembayaran Tunai')
@section('page_title', 'Pembayaran Tunai')
@section('page_subtitle', 'Pencatatan pelunasan/cicilan utang modal secara tunai')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Filter & Add Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs space-y-3 sm:space-y-0 sm:flex sm:items-center sm:justify-between sm:gap-4">
        <form method="GET" action="{{ route('pengepul.pembayaran.index') }}" class="flex flex-wrap items-center gap-2 sm:gap-3">
            <select name="petani_id" class="w-full sm:w-auto px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500">
                <option value="">Semua Petani</option>
                @foreach($petanis as $p)
                    <option value="{{ $p->id }}" {{ $petaniId == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_petani }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white font-semibold text-xs hover:bg-slate-700 transition">Filter</button>
        </form>

        <a href="{{ route('pengepul.pembayaran.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Catat Pembayaran Baru</span>
        </a>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="py-3 px-4">No. Kwitansi</th>
                        <th class="py-3 px-4">Petani</th>
                        <th class="py-3 px-4">Metode</th>
                        <th class="py-3 px-4 text-right">Jumlah Bayar</th>
                        <th class="py-3 px-4 text-right">Sisa Kewajiban</th>
                        <th class="py-3 px-4 text-center">WA</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pembayarans as $b)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4">
                                <a href="{{ route('pengepul.pembayaran.show', $b) }}" class="text-teal-700 hover:underline font-semibold text-xs">{{ $b->kode_pembayaran }}</a>
                                <span class="block text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($b->tanggal_bayar)->translatedFormat('d M Y') }}</span>
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <span class="font-bold text-slate-800">{{ $b->petani->nama_petani }}</span>
                            </td>
                            <td class="py-3 px-4 text-xs font-medium text-slate-700">{{ $b->metode_bayar }}</td>
                            <td class="py-3 px-4 text-right font-bold text-teal-700">Rp {{ number_format($b->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-medium text-slate-700">Rp {{ number_format($b->sisa_kewajiban_sesudah, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($b->status_wa === 'terkirim')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">Terkirim</span>
                                @elseif($b->status_wa === 'simulasi')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">Simulasi</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Menunggu</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('pengepul.pembayaran.show', $b) }}" class="p-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-800 text-xs font-semibold">Kwitansi</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-sm">Belum ada pembayaran tunai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pembayarans->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $pembayarans->links() }}</div>
        @endif
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-3">
        @forelse($pembayarans as $b)
            <a href="{{ route('pengepul.pembayaran.show', $b) }}" class="block bg-white rounded-xl border border-slate-200/80 shadow-xs p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-teal-700">{{ $b->kode_pembayaran }}</span>
                    <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($b->tanggal_bayar)->translatedFormat('d M Y') }}</span>
                </div>
                <p class="text-xs font-medium text-slate-800">{{ $b->petani->nama_petani }}</p>
                <p class="text-[10px] text-slate-500 mt-0.5">Metode: {{ $b->metode_bayar }}</p>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100">
                    <div class="text-[10px] text-slate-500">
                        Sisa: <span class="font-semibold">Rp {{ number_format($b->sisa_kewajiban_sesudah, 0, ',', '.') }}</span>
                    </div>
                    <span class="text-sm font-bold text-teal-700">Rp {{ number_format($b->jumlah_bayar, 0, ',', '.') }}</span>
                </div>
            </a>
        @empty
            <div class="py-12 text-center text-slate-400 text-sm bg-white rounded-xl border border-slate-200/80">Belum ada pembayaran tunai.</div>
        @endforelse
        @if($pembayarans->hasPages())
            <div class="p-3">{{ $pembayarans->links() }}</div>
        @endif
    </div>

</div>
@endsection
