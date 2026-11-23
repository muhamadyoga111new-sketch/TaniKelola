<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - TaniKelola</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { background: white !important; font-size: 11px; }
            .no-print { display: none !important; }
            @page { margin: 15mm; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 p-6 text-xs">

    <div class="no-print max-w-4xl mx-auto mb-4 flex justify-between items-center">
        <button onclick="window.history.back()" class="px-3 py-1.5 rounded-lg bg-slate-200 text-slate-800 font-semibold">
            &larr; Kembali
        </button>
        <button onclick="window.print()" class="px-4 py-1.5 rounded-lg bg-emerald-600 text-white font-bold shadow">
            🖨️ Cetak / Ekspor PDF
        </button>
    </div>

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200 print:border-none print:shadow-none print:p-0">

        <!-- Kop Surat -->
        <div class="border-b-2 border-slate-900 pb-3 mb-4 text-center">
            <h1 class="text-lg font-black tracking-tight text-slate-900 uppercase">SISTEM INFORMASI TANIKELOLA</h1>
            <h2 class="text-sm font-bold text-slate-800 uppercase">PENGELOLAAN MODAL DAN TRANSAKSI HASIL PANEN PETANI - PENGEPUL</h2>
            <p class="text-[11px] text-slate-500">Mitra Pengepul: Bpk. Mohtar &bull; Desa Bajuin, Kec. Bajuin, Kab. Tanah Laut, Kalimantan Selatan</p>
        </div>

        <div class="my-4 text-center">
            <h3 class="font-extrabold text-sm uppercase underline">
                @if($jenisLaporan === 'kewajiban')
                    LAPORAN REKAPITULASI KEWAJIBAN & SISA MODAL PETANI
                @elseif($jenisLaporan === 'transaksi')
                    LAPORAN TRANSAKSI PEMBELIAN HASIL PANEN
                @else
                    LAPORAN PEMBERIAN MODAL KEBUTUHAN BUDIDAYA
                @endif
            </h3>
            <p class="text-[11px] text-slate-500 mt-1">
                @if($jenisLaporan !== 'kewajiban')
                    Periode: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                @else
                    Status Data per: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                @endif
                @if($selectedPetani) &bull; Petani: {{ $selectedPetani->nama_petani }} @endif
            </p>
        </div>

        <!-- Content Table -->
        @if($jenisLaporan === 'kewajiban')
            <table class="w-full text-left border-collapse border border-slate-300 my-4 text-[11px]">
                <thead>
                    <tr class="bg-slate-100 border border-slate-300 font-bold uppercase text-center">
                        <th class="p-2 border border-slate-300">No.</th>
                        <th class="p-2 border border-slate-300 text-left">Nama Petani</th>
                        <th class="p-2 border border-slate-300">No. WhatsApp</th>
                        <th class="p-2 border border-slate-300 text-right">Total Modal</th>
                        <th class="p-2 border border-slate-300 text-right">Potongan Panen</th>
                        <th class="p-2 border border-slate-300 text-right">Bayar Tunai</th>
                        <th class="p-2 border border-slate-300 text-right">Total Terbayar</th>
                        <th class="p-2 border border-slate-300 text-right">Sisa Kewajiban</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumModal=0; $sumPot=0; $sumTun=0; $sumSisa=0; @endphp
                    @foreach($dataLaporan as $i => $row)
                        @php
                            $pot = $row->transaksiPanens->sum('potongan_kewajiban');
                            $tun = $row->pembayaranKewajibans->sum('jumlah_bayar');
                            $sumModal += $row->total_modal;
                            $sumPot += $pot;
                            $sumTun += $tun;
                            $sumSisa += $row->sisa_kewajiban;
                        @endphp
                        <tr class="border border-slate-300">
                            <td class="p-2 text-center border border-slate-300">{{ $i + 1 }}</td>
                            <td class="p-2 font-bold border border-slate-300">{{ $row->nama_petani }}</td>
                            <td class="p-2 text-center border border-slate-300">{{ $row->no_wa }}</td>
                            <td class="p-2 text-right border border-slate-300">Rp {{ number_format($row->total_modal, 0, ',', '.') }}</td>
                            <td class="p-2 text-right border border-slate-300">Rp {{ number_format($pot, 0, ',', '.') }}</td>
                            <td class="p-2 text-right border border-slate-300">Rp {{ number_format($tun, 0, ',', '.') }}</td>
                            <td class="p-2 text-right font-bold border border-slate-300">Rp {{ number_format($pot + $tun, 0, ',', '.') }}</td>
                            <td class="p-2 text-right font-black border border-slate-300">Rp {{ number_format($row->sisa_kewajiban, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-100 font-bold border-t-2 border-slate-900">
                    <tr>
                        <td colspan="3" class="p-2 text-center border border-slate-300">TOTAL:</td>
                        <td class="p-2 text-right border border-slate-300">Rp {{ number_format($sumModal, 0, ',', '.') }}</td>
                        <td class="p-2 text-right border border-slate-300">Rp {{ number_format($sumPot, 0, ',', '.') }}</td>
                        <td class="p-2 text-right border border-slate-300">Rp {{ number_format($sumTun, 0, ',', '.') }}</td>
                        <td class="p-2 text-right border border-slate-300">Rp {{ number_format($sumPot + $sumTun, 0, ',', '.') }}</td>
                        <td class="p-2 text-right border border-slate-300">Rp {{ number_format($sumSisa, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @elseif($jenisLaporan === 'transaksi')
            <table class="w-full text-left border-collapse border border-slate-300 my-4 text-[11px]">
                <thead>
                    <tr class="bg-slate-100 border border-slate-300 font-bold uppercase text-center">
                        <th class="p-2 border border-slate-300">No. TRX & Tanggal</th>
                        <th class="p-2 border border-slate-300 text-left">Nama Petani</th>
                        <th class="p-2 border border-slate-300 text-left">Rincian Sayur & Timbangan</th>
                        <th class="p-2 border border-slate-300 text-right">Nilai Bruto</th>
                        <th class="p-2 border border-slate-300 text-right">Potongan Utang</th>
                        <th class="p-2 border border-slate-300 text-right">Uang Diterima Petani</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumB=0; $sumP=0; $sumNet=0; @endphp
                    @foreach($dataLaporan as $trx)
                        @php $sumB += $trx->total_bruto; $sumP += $trx->potongan_kewajiban; $sumNet += $trx->uang_diterima_petani; @endphp
                        <tr class="border border-slate-300">
                            <td class="p-2 border border-slate-300 font-mono">
                                {{ $trx->nomor_transaksi }}<br>
                                <span class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') }}</span>
                            </td>
                            <td class="p-2 font-bold border border-slate-300">{{ $trx->petani->nama_petani }}</td>
                            <td class="p-2 border border-slate-300 text-[10px]">
                                @foreach($trx->details as $d)
                                    <div>&bull; {{ $d->komoditas->nama_komoditas }}: {{ $d->jumlah_panen }} {{ $d->komoditas->satuan }} @ Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</div>
                                @endforeach
                            </td>
                            <td class="p-2 text-right border border-slate-300">Rp {{ number_format($trx->total_bruto, 0, ',', '.') }}</td>
                            <td class="p-2 text-right border border-slate-300 text-rose-700">-Rp {{ number_format($trx->potongan_kewajiban, 0, ',', '.') }}</td>
                            <td class="p-2 text-right font-bold border border-slate-300">Rp {{ number_format($trx->uang_diterima_petani, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-100 font-bold border-t-2 border-slate-900">
                    <tr>
                        <td colspan="3" class="p-2 text-center border border-slate-300">TOTAL:</td>
                        <td class="p-2 text-right border border-slate-300">Rp {{ number_format($sumB, 0, ',', '.') }}</td>
                        <td class="p-2 text-right border border-slate-300">-Rp {{ number_format($sumP, 0, ',', '.') }}</td>
                        <td class="p-2 text-right border border-slate-300">Rp {{ number_format($sumNet, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <table class="w-full text-left border-collapse border border-slate-300 my-4 text-[11px]">
                <thead>
                    <tr class="bg-slate-100 border border-slate-300 font-bold uppercase text-center">
                        <th class="p-2 border border-slate-300">No. Bukti & Tanggal</th>
                        <th class="p-2 border border-slate-300 text-left">Nama Petani</th>
                        <th class="p-2 border border-slate-300">Bentuk Modal</th>
                        <th class="p-2 border border-slate-300 text-left">Rincian Kebutuhan</th>
                        <th class="p-2 border border-slate-300 text-right">Nominal Modal</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumM=0; @endphp
                    @foreach($dataLaporan as $m)
                        @php $sumM += $m->nominal; @endphp
                        <tr class="border border-slate-300">
                            <td class="p-2 border border-slate-300 font-mono">
                                {{ $m->kode_modal }}<br>
                                <span class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($m->tanggal_pemberian)->format('d/m/Y') }}</span>
                            </td>
                            <td class="p-2 font-bold border border-slate-300">{{ $m->petani->nama_petani }}</td>
                            <td class="p-2 text-center border border-slate-300">{{ ucfirst($m->jenis_modal) }}</td>
                            <td class="p-2 border border-slate-300">
                                <strong>{{ $m->kategori_kebutuhan }}</strong>
                                <span class="block text-[10px] text-slate-600">{{ $m->rincian_barang ?? '-' }}</span>
                            </td>
                            <td class="p-2 text-right font-bold border border-slate-300">Rp {{ number_format($m->nominal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-100 font-bold border-t-2 border-slate-900">
                    <tr>
                        <td colspan="4" class="p-2 text-center border border-slate-300">TOTAL:</td>
                        <td class="p-2 text-right border border-slate-300">Rp {{ number_format($sumM, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <!-- Signatures -->
        <div class="grid grid-cols-2 gap-8 mt-12 text-center text-xs">
            <div>
                <p class="text-slate-500">Mengetahui,</p>
                <div class="h-16"></div>
                <p class="font-bold underline text-slate-900">Muhammad Yoga</p>
                <p class="text-[10px] text-slate-400">NIM: 2401301003 (Peneliti)</p>
            </div>
            <div>
                <p class="text-slate-500">Bajuin, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Pengepul Mitra,</p>
                <div class="h-16"></div>
                <p class="font-bold underline text-slate-900">Bapak Mohtar</p>
                <p class="text-[10px] text-slate-400">Pengepul Hasil Tani Bajuin</p>
            </div>
        </div>

    </div>

</body>
</html>
