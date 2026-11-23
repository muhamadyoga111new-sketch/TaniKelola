@extends('layouts.app')

@section('title', 'Dashboard Pengepul')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan pengelolaan modal dan transaksi panen')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Quick Action Banner -->
    <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-gradient-to-r from-emerald-800 via-teal-800 to-emerald-900 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white/10 to-transparent pointer-events-none"></div>
        <div class="relative z-10">
            <p class="text-xs text-emerald-200 mb-1">Selamat Bertugas, {{ auth()->user()->name }}</p>
            <h3 class="text-lg sm:text-2xl font-bold tracking-tight">Ringkasan Pengelolaan</h3>
            <p class="text-xs text-emerald-100/80 mt-1 max-w-xl hidden sm:block">
                Pantau transaksi panen, pemberian modal, dan sisa kewajiban petani.
            </p>
            <div class="flex flex-wrap gap-2 mt-3">
                <a href="{{ route('pengepul.transaksi.create') }}" class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-lg bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs shadow-md transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Catat Panen</span>
                </a>
                <a href="{{ route('pengepul.modal.create') }}" class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/20 transition">
                    <span>Beri Modal</span>
                </a>
                <a href="{{ route('pengepul.pembayaran.create') }}" class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/20 transition">
                    <span>Bayar Tunai</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Card 1: Total Petani -->
        <div class="bg-white p-4 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider">Petani Mitra</span>
                <span class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-xl sm:text-2xl font-extrabold text-slate-900">{{ $totalPetani }}</div>
                <p class="text-[10px] sm:text-xs text-emerald-600 mt-0.5">Mitra Aktif</p>
            </div>
        </div>

        <!-- Card 2: Total Modal -->
        <div class="bg-white p-4 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Modal</span>
                <span class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-xl font-extrabold text-slate-900">Rp {{ number_format($totalModalDiberikan, 0, ',', '.') }}</div>
                <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5">Pupuk, bibit, obat & upah</p>
            </div>
        </div>

        <!-- Card 3: Total Nilai Panen -->
        <div class="bg-white p-4 sm:p-5 rounded-xl sm:rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-semibold text-slate-500 uppercase tracking-wider">Pembelian</span>
                <span class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-xl font-extrabold text-slate-900">Rp {{ number_format($totalPembelianPanen, 0, ',', '.') }}</div>
                <p class="text-[10px] sm:text-xs text-amber-600 mt-0.5">Total pembelian panen</p>
            </div>
        </div>

        <!-- Card 4: Sisa Kewajiban Petani -->
        <div class="bg-white p-4 sm:p-5 rounded-xl sm:rounded-2xl border border-rose-100 shadow-xs bg-gradient-to-br from-white to-rose-50/30">
            <div class="flex items-center justify-between">
                <span class="text-[10px] sm:text-xs font-semibold text-rose-700 uppercase tracking-wider">Sisa Piutang</span>
                <span class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-xl font-extrabold text-rose-600">Rp {{ number_format($totalSisaKewajiban, 0, ',', '.') }}</div>
                <p class="text-[10px] sm:text-xs text-slate-500 mt-0.5">Belum lunas</p>
            </div>
        </div>
    </div>

    <!-- 2 Column Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

        <!-- Transaksi Panen Terkini -->
        <div class="lg:col-span-2 bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-bold text-sm sm:text-base text-slate-900">Transaksi Terkini</h4>
                <a href="{{ route('pengepul.transaksi.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                    Lihat Semua &rarr;
                </a>
            </div>

            <!-- Desktop Table (hidden on mobile) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="py-2.5 pr-3">Tanggal</th>
                            <th class="py-2.5 pr-3">Petani</th>
                            <th class="py-2.5 pr-3">Komoditas</th>
                            <th class="py-2.5 text-right pr-3">Nilai</th>
                            <th class="py-2.5 text-right pr-3">Potong</th>
                            <th class="py-2.5 text-right pr-3">Diterima</th>
                            <th class="py-2.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transaksiTerbaru as $trx)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 pr-3 text-xs text-slate-600">
                                    <span class="font-medium text-slate-800">
                                        {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d M Y') }}
                                    </span>
                                </td>
                                <td class="py-3 pr-3 text-xs">
                                    <span class="font-bold text-slate-900">{{ $trx->petani->nama_petani }}</span>
                                </td>
                                <td class="py-3 pr-3 text-xs text-slate-600">
                                    @foreach($trx->details as $d)
                                        <span class="inline-block bg-slate-100 px-1.5 py-0.5 rounded text-[10px] font-medium mr-0.5 mb-0.5">
                                            {{ $d->komoditas->nama_komoditas }}: {{ $d->jumlah_panen }} {{ $d->komoditas->satuan }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="py-3 text-right pr-3 text-xs font-semibold text-slate-800">
                                    Rp {{ number_format($trx->total_bruto, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-right pr-3 text-xs font-semibold text-rose-600">
                                    -{{ number_format($trx->potongan_kewajiban, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-right pr-3 text-xs font-bold text-emerald-700">
                                    Rp {{ number_format($trx->uang_diterima_petani, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-center text-xs">
                                    <a href="{{ route('pengepul.transaksi.show', $trx) }}" class="inline-flex items-center px-2 py-1 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-[11px] transition">
                                        Rincian &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                    Belum ada transaksi panen.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards (shown on mobile only) -->
            <div class="md:hidden space-y-3">
                @forelse($transaksiTerbaru as $trx)
                    <a href="{{ route('pengepul.transaksi.show', $trx) }}" class="block p-3 rounded-xl border border-slate-100 hover:border-emerald-200 transition bg-slate-50/50">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-900">{{ $trx->petani->nama_petani }}</span>
                            <span class="text-[10px] text-slate-500 font-medium">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="flex flex-wrap gap-1 mt-1.5">
                            @foreach($trx->details as $d)
                                <span class="bg-slate-200/60 px-1.5 py-0.5 rounded text-[10px] font-medium text-slate-600">
                                    {{ $d->komoditas->nama_komoditas }}: {{ $d->jumlah_panen }} {{ $d->komoditas->satuan }}
                                </span>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100">
                            <div class="text-[10px] text-slate-500">
                                Bruto: <span class="font-semibold text-slate-700">Rp {{ number_format($trx->total_bruto, 0, ',', '.') }}</span>
                                <span class="text-rose-500 ml-1">-{{ number_format($trx->potongan_kewajiban, 0, ',', '.') }}</span>
                            </div>
                            <span class="text-xs font-bold text-emerald-700">Rp {{ number_format($trx->uang_diterima_petani, 0, ',', '.') }}</span>
                        </div>
                    </a>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs">
                        Belum ada transaksi panen.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Kewajiban Petani -->
        <div class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs p-4 sm:p-6 flex flex-col">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div>
                    <h4 class="font-bold text-sm sm:text-base text-slate-900">Kewajiban Petani</h4>
                    <p class="text-[10px] sm:text-xs text-slate-500">Sisa piutang modal</p>
                </div>
                <a href="{{ route('pengepul.petani.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Semua</a>
            </div>

            <div class="space-y-2 sm:space-y-3 flex-1">
                @forelse($petaniKewajiban as $ptn)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div class="min-w-0 flex-1 mr-3">
                            <p class="font-bold text-xs text-slate-900 truncate">{{ $ptn->nama_petani }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ $ptn->komoditas_utama ?? 'Mitra Bajuin' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-xs font-bold text-rose-600 block">
                                Rp {{ number_format($ptn->sisa_kewajiban, 0, ',', '.') }}
                            </span>
                            <a href="{{ route('pengepul.petani.show', $ptn) }}" class="text-[10px] font-semibold text-emerald-600 hover:underline">
                                Rincian &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400 text-xs">
                        Semua kewajiban telah lunas.
                    </div>
                @endforelse
            </div>

            <!-- WhatsApp Widget -->
            <div class="mt-4 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-bold text-slate-800">WhatsApp Gateway</span>
                    </div>
                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 font-semibold">Aktif</span>
                </div>
                <a href="{{ route('pengepul.wa_log.index') }}" class="block w-full text-center py-2 px-3 mt-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition">
                    Riwayat WA &rarr;
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
