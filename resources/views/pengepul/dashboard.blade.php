@extends('layouts.app')

@section('title', 'Dashboard Pengepul')
@section('page_title', 'Dashboard Pengepul')
@section('page_subtitle', 'Bajuin, Tanah Laut')

@section('content')
<div class="space-y-8">

    {{-- Blok utama: warna logo. Hijau daun gelap solid, garis emas di bawah,
         angka sisa utang putih besar. Tanpa gradient. --}}
    <section class="rounded-lg bg-[#14532d] border-b-4 border-[#eab308] px-5 py-6 sm:px-7">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-[#eab308]">Pengepul &middot; Bpk. Mohtar &mdash; Bajuin, Tanah Laut</p>
                <p class="mt-1 text-xs text-white/70">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                <h2 class="mt-4 text-sm font-semibold text-white/85">Sisa utang petani, semua petani</h2>
                <p class="mt-1 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Rp {{ number_format($totalSisaKewajiban, 0, ',', '.') }}</p>
                <p class="mt-2 max-w-md text-xs leading-relaxed text-white/75">
                    Sudah dibayar Rp {{ number_format($totalPembayaranKewajiban, 0, ',', '.') }} lewat potongan panen dan bayar tunai.
                    {{ $totalPetani }} petani aktif.
                </p>
            </div>
            <div class="flex shrink-0 flex-col gap-2 sm:items-end">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('pengepul.transaksi.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-md bg-[#eab308] text-[#14532d] text-sm font-extrabold hover:bg-[#facc15] transition">Catat Panen</a>
                    <a href="{{ route('pengepul.modal.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-md border border-white/60 text-white text-sm font-bold hover:bg-white/10 transition">Beri Modal</a>
                </div>
                <a href="{{ route('pengepul.pembayaran.create') }}" class="text-xs font-bold text-[#eab308] hover:underline">Bayar tunai &rarr;</a>
            </div>
        </div>
    </section>

    {{-- Tiga angka pendukung, dipisah garis warna logo. --}}
    <section class="grid grid-cols-1 divide-y divide-slate-200 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
        <div class="border-l-4 border-[#15803d] py-1 pl-4 sm:pr-5">
            <p class="text-xs font-bold uppercase tracking-wider text-[#15803d]">Modal keluar</p>
            <p class="mt-1 text-lg font-extrabold text-slate-900">Rp {{ number_format($totalModalDiberikan, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500">Pupuk, bibit, obat, dan upah</p>
        </div>
        <div class="border-l-4 border-[#eab308] py-3 pl-4 sm:py-1 sm:px-5">
            <p class="text-xs font-bold uppercase tracking-wider text-[#b45309]">Panen dibeli</p>
            <p class="mt-1 text-lg font-extrabold text-slate-900">Rp {{ number_format($totalPembelianPanen, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-500">Total nilai panen, sebelum potongan</p>
        </div>
        <div class="border-l-4 border-[#14532d] py-3 pl-4 sm:py-1 sm:px-5">
            <p class="text-xs font-bold uppercase tracking-wider text-[#14532d]">Petani aktif</p>
            <p class="mt-1 text-lg font-extrabold text-slate-900">{{ $totalPetani }} orang</p>
            <p class="text-xs text-slate-500">Mitra di Desa Bajuin</p>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

        {{-- Aktivitas terkini: buku catatan dengan kode warna sayuran. --}}
        <section class="lg:col-span-2">
            <div class="flex items-baseline justify-between border-b-2 border-[#14532d] pb-2">
                <h3 class="text-base font-extrabold text-[#14532d]">Aktivitas Terkini</h3>
                <a href="{{ route('pengepul.transaksi.index') }}" class="text-xs font-bold text-[#14532d] hover:underline">Lihat semua transaksi &rarr;</a>
            </div>
            <p class="mt-2 text-xs text-slate-500">Panen, pemberian modal, dan bayar tunai terbaru.</p>

            {{-- Tabel, layar sedang ke atas --}}
            <div class="mt-3 hidden md:block">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-200">
                            <th class="py-2 pr-3">Tanggal</th>
                            <th class="py-2 pr-3">Petani</th>
                            <th class="py-2 pr-3">Keterangan</th>
                            <th class="py-2 text-right">Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($aktivitasTerbaru as $item)
                            <tr class="hover:bg-[#f7faf7] transition">
                                <td class="py-3 pr-3 text-xs text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('d M Y') }}</td>
                                <td class="py-3 pr-3">
                                    <a href="{{ $item['url'] }}" class="text-sm font-bold text-slate-900 hover:text-[#14532d] hover:underline">{{ $item['petani'] }}</a>
                                    <span class="block text-[11px] font-extrabold uppercase tracking-wider {{ $item['jenis'] === 'panen' ? 'text-[#15803d]' : ($item['jenis'] === 'modal' ? 'text-[#b45309]' : 'text-[#b91c1c]') }}">{{ $item['label'] }}</span>
                                </td>
                                <td class="py-3 pr-3 text-xs leading-relaxed text-slate-600">
                                    {{ $item['detail'] }}
                                    <span class="block text-[11px] text-slate-400">{{ $item['sub'] }}</span>
                                </td>
                                <td class="py-3 text-right text-sm font-extrabold text-slate-900 whitespace-nowrap">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
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
                    <a href="{{ $item['url'] }}" class="block py-3 border-l-4 {{ $item['jenis'] === 'panen' ? 'border-[#15803d]' : ($item['jenis'] === 'modal' ? 'border-[#eab308]' : 'border-[#b91c1c]') }} pl-3">
                        <div class="flex items-baseline justify-between gap-3">
                            <span class="text-sm font-bold text-slate-900">{{ $item['petani'] }}</span>
                            <span class="text-xs font-extrabold text-slate-900 whitespace-nowrap">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</span>
                        </div>
                        <p class="mt-0.5 text-xs text-slate-600">{{ $item['detail'] }}</p>
                        <p class="mt-0.5 text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('d M Y') }} &middot; {{ $item['label'] }} &middot; {{ $item['sub'] }}</p>
                    </a>
                @empty
                    <p class="py-8 text-center text-sm text-slate-400">Belum ada aktivitas.</p>
                @endforelse
            </div>
        </section>

        {{-- Sisa utang terbesar + WA --}}
        <aside class="space-y-8">
            <section>
                <div class="flex items-baseline justify-between border-b-2 border-[#eab308] pb-2">
                    <h3 class="text-base font-extrabold text-[#14532d]">Sisa Utang Terbesar</h3>
                    <a href="{{ route('pengepul.petani.index') }}" class="text-xs font-bold text-[#14532d] hover:underline">Semua petani &rarr;</a>
                </div>
                <ol class="mt-1 divide-y divide-slate-200">
                    @forelse($petaniKewajiban as $index => $ptn)
                        <li class="flex items-baseline gap-3 py-3">
                            <span class="w-6 shrink-0 text-xs font-extrabold text-[#b45309]">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('pengepul.petani.show', $ptn) }}" class="block truncate text-sm font-bold text-slate-900 hover:text-[#14532d] hover:underline">{{ $ptn->nama_petani }}</a>
                                <span class="block truncate text-xs text-slate-500">{{ $ptn->komoditas_utama ?? 'Petani Bajuin' }}</span>
                            </div>
                            <span class="shrink-0 text-sm font-extrabold text-[#14532d] whitespace-nowrap">Rp {{ number_format($ptn->sisa_kewajiban, 0, ',', '.') }}</span>
                        </li>
                    @empty
                        <li class="py-8 text-center text-sm text-slate-400">Semua utang sudah lunas.</li>
                    @endforelse
                </ol>
            </section>

            <section class="rounded-lg border-2 border-[#14532d] p-4">
                <h3 class="text-base font-extrabold text-[#14532d]">Pesan WhatsApp</h3>
                <p class="mt-2 text-xs leading-relaxed text-slate-500">{{ $waSukses }} pesan tercatat, {{ $waPending }} menunggu. Pengiriman masih tahap simulasi sampai Fonnte diaktifkan di akhir.</p>
                <a href="{{ route('pengepul.wa_log.index') }}" class="mt-3 inline-block text-xs font-bold text-[#14532d] hover:underline">Lihat riwayat WA &rarr;</a>
            </section>
        </aside>

    </div>

</div>
@endsection
