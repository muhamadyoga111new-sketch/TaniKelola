<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Transaksi - {{ $transaksi->nomor_transaksi }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
            @page { margin: 15mm; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 p-4 sm:p-10 text-xs">

    <div class="no-print max-w-2xl mx-auto mb-4 flex justify-between items-center">
        <button onclick="window.history.back()" class="px-3 py-1.5 rounded-lg bg-slate-200 text-slate-800 font-semibold">
            &larr; Kembali
        </button>
        <button onclick="window.print()" class="px-4 py-1.5 rounded-lg bg-emerald-600 text-white font-bold shadow">
            🖨️ Cetak Dokumen / Simpan PDF
        </button>
    </div>

    <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200 print:border-none print:shadow-none print:p-0">

        <!-- Header -->
        <div class="border-b-2 border-slate-900 pb-4 mb-4 flex justify-between items-start">
            <div>
                <h1 class="text-xl font-black tracking-tight text-slate-900">TANIKELOLA</h1>
                <p class="font-bold text-xs text-slate-700">PENGEPUL HASIL TANI & SAYURAN DESA BAJUIN</p>
                <p class="text-[11px] text-slate-500">Jl. Atilam, Desa Bajuin, Kab. Tanah Laut, Kalimantan Selatan</p>
                <p class="text-[11px] text-slate-500">Pimpinan: Bpk. Mohtar &bull; WhatsApp: 0812-3456-7890</p>
            </div>
            <div class="text-right">
                <span class="font-mono font-bold text-sm block">{{ $transaksi->nomor_transaksi }}</span>
                <span class="text-slate-500">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d F Y') }}</span>
            </div>
        </div>

        <div class="text-center my-3">
            <h2 class="font-extrabold text-sm uppercase tracking-wider underline">NOTA PENJUALAN HASIL PANEN</h2>
        </div>

        <!-- Identity -->
        <div class="grid grid-cols-2 gap-4 my-4 p-3 bg-slate-50 rounded-lg border border-slate-200 text-xs">
            <div>
                <span class="font-bold text-slate-500 block">PETANI PENJUAL:</span>
                <p class="font-bold text-sm text-slate-900">{{ $transaksi->petani->nama_petani }}</p>
                <p>WA: {{ $transaksi->petani->no_wa }}</p>
                <p>Alamat: {{ $transaksi->petani->alamat ?? 'Desa Bajuin' }}</p>
            </div>
            <div class="text-right">
                <span class="font-bold text-slate-500 block">KETERANGAN TRANSAKSI:</span>
                <p>Petugas Kasir: {{ $transaksi->user->name ?? 'Pengepul' }}</p>
                <p>Status Notifikasi: {{ ucfirst($transaksi->status_wa) }}</p>
                @if($transaksi->catatan)
                    <p class="italic text-slate-600">"{{ $transaksi->catatan }}"</p>
                @endif
            </div>
        </div>

        <!-- Detail Table -->
        <table class="w-full text-left my-4 border-collapse">
            <thead>
                <tr class="border-y-2 border-slate-800 text-[11px] font-bold uppercase">
                    <th class="py-2">No.</th>
                    <th class="py-2">Nama Komoditas Sayuran</th>
                    <th class="py-2 text-right">Berat / Timbangan</th>
                    <th class="py-2 text-right">Harga Beli</th>
                    <th class="py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($transaksi->details as $idx => $d)
                    <tr>
                        <td class="py-2 text-slate-500 font-mono">{{ $idx + 1 }}</td>
                        <td class="py-2">
                            <strong>{{ $d->komoditas->nama_komoditas }}</strong>
                            @if($d->keterangan) <span class="text-[10px] text-slate-500">({{ $d->keterangan }})</span> @endif
                        </td>
                        <td class="py-2 text-right font-medium">{{ $d->jumlah_panen }} {{ $d->komoditas->satuan }}</td>
                        <td class="py-2 text-right">Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                        <td class="py-2 text-right font-bold">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="border-t-2 border-slate-800 pt-3 flex justify-end">
            <div class="w-72 space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span>Total Nilai Panen Bruto:</span>
                    <span class="font-bold">Rp {{ number_format($transaksi->total_bruto, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-rose-700 font-semibold">
                    <span>Potongan Modal / Utang:</span>
                    <span>-Rp {{ number_format($transaksi->potongan_kewajiban, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between py-1.5 border-y-2 border-slate-900 font-black text-sm text-slate-900">
                    <span>UANG DITERIMA PETANI:</span>
                    <span>Rp {{ number_format($transaksi->uang_diterima_petani, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-1 text-[11px] text-slate-600">
                    <span>Sisa Kewajiban Sebelumnya:</span>
                    <span>Rp {{ number_format($transaksi->sisa_kewajiban_sebelum, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-[11px]">
                    <span>Sisa Kewajiban Sekarang:</span>
                    <span class="text-rose-700">Rp {{ number_format($transaksi->sisa_kewajiban_sesudah, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="grid grid-cols-2 gap-8 mt-12 text-center text-xs">
            <div>
                <p class="text-slate-500">Petani Penjual,</p>
                <div class="h-16"></div>
                <p class="font-bold underline text-slate-900">{{ $transaksi->petani->nama_petani }}</p>
            </div>
            <div>
                <p class="text-slate-500">Pengepul Pembeli,</p>
                <div class="h-16"></div>
                <p class="font-bold underline text-slate-900">Bapak Mohtar</p>
            </div>
        </div>

        <div class="mt-8 pt-4 border-t border-slate-200 text-center text-[10px] text-slate-400">
            Dokumen ini dicetak melalui Sistem Informasi TaniKelola &bull; Tanggal Cetak: {{ date('d/m/Y H:i') }}
        </div>
    </div>

</body>
</html>
