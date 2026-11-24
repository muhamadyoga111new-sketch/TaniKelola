<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Transaksi - {{ $transaksi->nomor_transaksi }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; margin: 0; }
        h1 { font-size: 20px; margin: 0; }
        h2 { font-size: 13px; text-align: center; text-decoration: underline; text-transform: uppercase; margin: 12px 0; }
        .header { border-bottom: 2px solid #111; padding-bottom: 10px; }
        .header td { vertical-align: top; }
        .right { text-align: right; }
        .muted { color: #555; font-size: 11px; }
        .box { background: #f2f2f2; border: 1px solid #ddd; padding: 10px; margin: 12px 0; }
        table.items { width: 100%; border-collapse: collapse; margin: 12px 0; }
        table.items th { border-top: 2px solid #111; border-bottom: 2px solid #111; text-align: left; padding: 6px 4px; font-size: 11px; text-transform: uppercase; }
        table.items td { border-bottom: 1px solid #ddd; padding: 6px 4px; }
        .num { text-align: right; }
        table.totals { width: 280px; margin-left: auto; border-collapse: collapse; }
        table.totals td { padding: 4px 0; }
        .total-row td { border-top: 2px solid #111; border-bottom: 2px solid #111; font-weight: bold; font-size: 14px; padding: 8px 0; }
        .rose { color: #b91c1c; }
        .sign { margin-top: 40px; width: 100%; }
        .sign td { text-align: center; font-size: 11px; padding-top: 50px; }
    </style>
</head>
<body>
    <table width="100%" class="header">
        <tr>
            <td>
                <h1>TANIKELOLA</h1>
                <strong>PENGEPUL HASIL TANI &amp; SAYURAN DESA BAJUIN</strong><br>
                <span class="muted">Jl. Atilam, Desa Bajuin, Kab. Tanah Laut, Kalimantan Selatan<br>
                Pimpinan: Bpk. Mohtar</span>
            </td>
            <td class="right">
                <strong>{{ $transaksi->nomor_transaksi }}</strong><br>
                <span class="muted">Tanggal: {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d F Y') }}<br>
                Dicatat: {{ $transaksi->created_at->translatedFormat('d F Y, H:i') }} WITA</span>
            </td>
        </tr>
    </table>

    <h2>Nota Penjualan Hasil Panen</h2>

    <table width="100%" class="box">
        <tr>
            <td>
                <strong>PETANI PENJUAL:</strong><br>
                <strong>{{ $transaksi->petani->nama_petani }}</strong><br>
                WA: {{ $transaksi->petani->no_wa }}<br>
                Alamat: {{ $transaksi->petani->alamat ?? 'Desa Bajuin' }}
            </td>
            <td class="right">
                <strong>KETERANGAN TRANSAKSI:</strong><br>
                Petugas Kasir: {{ $transaksi->user->name ?? 'Pengepul' }}<br>
                Status Notifikasi: {{ ucfirst($transaksi->status_wa) }}
                @if($transaksi->catatan)<br><em>"{{ $transaksi->catatan }}"</em>@endif
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama Komoditas Sayuran</th>
                <th class="num">Berat / Timbangan</th>
                <th class="num">Harga Beli</th>
                <th class="num">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->details as $idx => $d)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td><strong>{{ $d->komoditas->nama_komoditas }}</strong>@if($d->keterangan) <span class="muted">({{ $d->keterangan }})</span>@endif</td>
                <td class="num">{{ $d->jumlah_panen }} {{ $d->komoditas->satuan }}</td>
                <td class="num">Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</td>
                <td class="num"><strong>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Total Nilai Panen Bruto:</td><td class="num"><strong>Rp {{ number_format($transaksi->total_bruto, 0, ',', '.') }}</strong></td></tr>
        <tr class="rose"><td>Potongan Modal / Utang:</td><td class="num"><strong>-Rp {{ number_format($transaksi->potongan_kewajiban, 0, ',', '.') }}</strong></td></tr>
        <tr class="total-row"><td>UANG DITERIMA PETANI:</td><td class="num">Rp {{ number_format($transaksi->uang_diterima_petani, 0, ',', '.') }}</td></tr>
        <tr><td class="muted">Sisa Kewajiban Sebelumnya:</td><td class="num muted">Rp {{ number_format($transaksi->sisa_kewajiban_sebelum, 0, ',', '.') }}</td></tr>
        <tr><td><strong>Sisa Kewajiban Sekarang:</strong></td><td class="num"><strong>Rp {{ number_format($transaksi->sisa_kewajiban_sesudah, 0, ',', '.') }}</strong></td></tr>
    </table>

    <table class="sign">
        <tr>
            <td>Petani Penjual,<br><br><br>( {{ $transaksi->petani->nama_petani }} )</td>
            <td>Pengepul,<br><br><br>( Bpk. Mohtar )</td>
        </tr>
    </table>
</body>
</html>
