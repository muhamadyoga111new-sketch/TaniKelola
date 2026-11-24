@extends('layouts.app')

@section('title', 'Dashboard Pengepul')
@section('page_title', 'Dashboard Pengepul')
@section('page_subtitle', 'Bajuin, Tanah Laut')

@section('content')
<div class="space-y-8">

    {{-- Catatan pengepul: angka yang paling penting ditaruh paling atas,
         bukan empat kartu yang sama besar. --}}
    <section class="border-b-2 border-[#1d3a2a] pb-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-[#8a5a12]">Pengepul &middot; Bpk. Mohtar</p>
                <p class="mt-1 text-xs text-slate-500">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                <h2 class="mt-3 text-sm font-semibold text-slate-600">Sisa utang petani, semua petani</h2>
                <p class="mt-1 text-3xl font-extrabold tracking-tight text-[#1d3a2a] sm:text-4xl">Rp {{ number_format($totalSisaKewajiban, 0, ',', '.') }}</p>
                <p class="mt-2 max-w-md text-xs leading-relaxed text-slate-500">
                    Sudah dibayar Rp {{ number_format($totalPembayaranKewajiban, 0, ',', '.') }} lewat potongan panen dan bayar tunai.
                    {{ $totalPetani }} petani aktif.
                </p>
            </div>
            <div class="flex shrink-0 flex-col gap-2 sm:items-end">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('pengepul.transaksi.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-md bg-[#1d3a2a] text-white text-sm font-bold hover:bg-[#162e21] transition">Catat Panen</a>
                    <a href="{{ route('pengepul.modal.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-md border border-[#1d3a2a] text-[#1d3a2a] text-sm font-bold hover:bg-[#1d3a2a]/5 transition">Beri Modal</a>
                </div>
                <a href="{{ route('pengepul.pembayaran.create') }}" class="text-xs font-semibold text-[#8a5a12] hover:underline">Bayar tunai &rarr;</a>
            </div>
        </div>
    </section>

    {{-- Tiga angka pendukung, dipisah garis saja, bukan kartu. --}}
    <section class="grid grid-cols-1 divide-y divide-slate-200 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
        <div class="py-1 sm:px-5 sm:first:pl-0">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Modal keluar</p>
            <p class="mt-1 text-lg font-bold text-slate-900">Rp {{ number_format($totalModalDiberikan, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500">Pupuk, bibit, obat, dan upah</p>
        </div>
        <div class="py-3 sm:py-1 sm:px-5">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Panen dibeli</p>
            <p class="mt-1 text-lg font-bold text-slate-900">Rp {{ number_format($totalPembelianPanen, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500">Total nilai panen, sebelum potongan</p>
        </div>
        <div class="py-3 sm:py-1 sm:px-5 sm:last:pr-0">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Petani aktif</p>
            <p class="mt-1 text-lg font-bold text-slate-900">{{ $totalPetani }} orang</p>
            <p class="text-xs text-slate-500">Mitra di Desa Bajuin</p>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

        {{-- Aktivitas terkini: bentuk buku catatan, bukan kartu. --}}
        <section class="lg:col-span-2">
            <div class="flex items-baseline justify-between border-b border-slate-300 pb-2">
                <h3 class="text-base font-bold text-slate-900">Aktivitas Terkini</h3>
                <a href="{{ route('pengepul.transaksi.index') }}" class="text-xs font-semibold text-[#1d3a2a] hover:underline">Lihat semua transaksi &rarr;</a>
            </div>
            <p class="mt-2 text-xs text-slate-500">Panen, pemberian modal, dan bayar tunai terbaru.</p>

            {{-- Tabel, layar sedang ke atas --}}
            <div class="mt-3 hidden md:block">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                            <th class="py-2 pr-3">Tanggal</th>
                            <th class="py-2 pr-3">Petani</th>
                            <th class="py-2 pr-3">Keterangan</th>
                            <th class="py-2 text-right">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($aktivitasTerbaru as $item)
                            <tr class="hover:bg-[#f7f5ef] transition">
                                <td class="py-3 pr-3 text-xs text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('d M Y') }}</td>
                                <td class="py-3 pr-3">
                                    <a href="{{ $item['url'] }}" class="text-sm font-bold text-slate-900 hover:text-[#1d3a2a] hover:underline">{{ $item['petani'] }}</a>
                                    <span class="block text-[11px] font-semibold uppercase tracking-wider {{ $item['jenis'] === 'panen' ? 'text-[#1d3a2a]' : ($item['jenis'] === 'modal' ? 'text-[#8a5a12]' : 'text-slate-500') }}">{{ $item['label'] }}</span>
                                </td>
                                <td class="py-3 pr-3 text-xs leading-relaxed text-slate-600">
                                    {{ $item['detail'] }}
                                    <span class="block text-[11px] text-slate-400">{{ $item['sub'] }}</span>
                                </td>
                                <td class="py-3 text-right text-sm font-bold text-slate-900 whitespace-nowrap">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-sm text-slate-400">Belum ada aktivitas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Daftar baris, layar kecil --}}
            <div class="mt-1 md:hidden divide-y divide-slate-200">
                @forelse($aktivitasTerbaru as $item)
                    <a href="{{ $item['url'] }}" class="block py-3">
                        <div class="flex items-baseline justify-between gap-3">
                            <span class="text-sm font-bold text-slate-900">{{ $item['petani'] }}</span>
                            <span class="text-xs font-bold text-slate-900 whitespace-nowrap">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</span>
                        </div>
                        <p class="mt-0.5 text-xs text-slate-600">{{ $item['detail'] }}</p>
                        <p class="mt-0.5 text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('d M Y') }} &middot; {{ $item['label'] }} &middot; {{ $item['sub'] }}</p>
                    </a>
                @empty
                    <p class="py-8 text-center text-sm text-slate-400">Belum ada aktivitas.</p>
                @endforelse
            </div>
        </section>

        {{-- Sisa utang terbesar + WA, tanpa kartu dan tanpa badge "Aktif". --}}
        <aside class="space-y-8">
            <section>
                <div class="flex items-baseline justify-between border-b border-slate-300 pb-2">
                    <h3 class="text-base font-bold text-slate-900">Sisa Utang Terbesar</h3>
                    <a href="{{ route('pengepul.petani.index') }}" class="text-xs font-semibold text-[#1d3a2a] hover:underline">Semua petani &rarr;</a>
                </div>
                <ol class="mt-1 divide-y divide-slate-200">
                    @forelse($petaniKewajiban as $index => $ptn)
                        <li class="flex items-baseline gap-3 py-3">
                            <span class="w-6 shrink-0 text-xs font-bold text-slate-400">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('pengepul.petani.show', $ptn) }}" class="block truncate text-sm font-bold text-slate-900 hover:text-[#1d3a2a] hover:underline">{{ $ptn->nama_petani }}</a>
                                <span class="block truncate text-xs text-slate-500">{{ $ptn->komoditas_utama ?? 'Petani Bajuin' }}</span>
                            </div>
                            <span class="shrink-0 text-sm font-bold text-slate-900 whitespace-nowrap">Rp {{ number_format($ptn->sisa_kewajiban, 0, ',', '.') }}</span>
                        </li>
                    @empty
                        <li class="py-8 text-center text-sm text-slate-400">Semua utang sudah lunas.</li>
                    @endforelse
                </ol>
            </section>

            <section class="border-t border-slate-200 pt-5">
                <h3 class="text-base font-bold text-slate-900">Pesan WhatsApp</h3>
                <p class="mt-2 text-xs leading-relaxed text-slate-500">{{ $waSukses }} pesan tercatat, {{ $waPending }} menunggu. Pengiriman masih tahap simulasi sampai Fonnte diaktifkan di akhir.</p>
                <a href="{{ route('pengepul.wa_log.index') }}" class="mt-3 inline-block text-xs font-semibold text-[#1d3a2a] hover:underline">Lihat riwayat WA &rarr;</a>
            </section>
        </aside>

    </div>

</div>
@endsection
