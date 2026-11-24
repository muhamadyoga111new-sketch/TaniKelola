<?php

namespace App\Http\Controllers\Pengepul;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use App\Models\Modal;
use App\Models\Petani;
use App\Models\TransaksiPanen;
use App\Models\PembayaranKewajiban;
use App\Models\WaLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPetani = Petani::where('status', 'aktif')->count();
        $totalModalDiberikan = Modal::sum('nominal');
        $totalPembayaranKewajiban = TransaksiPanen::sum('potongan_kewajiban') + PembayaranKewajiban::sum('jumlah_bayar');
        $totalSisaKewajiban = Petani::sum('sisa_kewajiban');
        $totalPembelianPanen = TransaksiPanen::sum('total_bruto');

        // Transaksi panen terbaru (tetap dipakai kalau ada view lain yang butuh)
        $transaksiTerbaru = TransaksiPanen::with(['petani', 'details.komoditas'])
            ->orderByDesc('tanggal_transaksi')->orderByDesc('created_at')->orderByDesc('id')
            ->take(5)
            ->get();

        // Modal terbaru (tetap dipakai kalau ada view lain yang butuh)
        $modalTerbaru = Modal::with('petani')
            ->orderByDesc('tanggal_pemberian')->orderByDesc('created_at')->orderByDesc('id')
            ->take(5)
            ->get();

        // Aktivitas Terkini: gabungan panen + modal + bayar tunai, 8 terbaru.
        // Diambil 8 dari tiap jenis dulu, baru digabung dan diurutkan lagi,
        // supaya satu jenis yang ramai tidak menenggelamkan jenis lain.
        $aktivitasTerbaru = collect()
            ->merge(TransaksiPanen::with(['petani', 'details.komoditas'])
                ->orderByDesc('tanggal_transaksi')->orderByDesc('created_at')->orderByDesc('id')
                ->take(8)->get()->map(fn ($trx) => [
                    'jenis' => 'panen',
                    'label' => 'Panen',
                    'tanggal' => $trx->tanggal_transaksi,
                    'created_at' => $trx->created_at,
                    'id' => $trx->id,
                    'petani' => $trx->petani->nama_petani ?? '-',
                    'detail' => $trx->details->map(fn ($d) => ($d->komoditas->nama_komoditas ?? '-') . ': ' . $d->jumlah_panen . ' ' . ($d->komoditas->satuan ?? ''))->join(', '),
                    'nominal' => $trx->total_bruto,
                    'sub' => 'Potong Rp ' . number_format($trx->potongan_kewajiban, 0, ',', '.') . ' • Diterima Rp ' . number_format($trx->uang_diterima_petani, 0, ',', '.'),
                    'url' => route('pengepul.transaksi.show', $trx),
                ]))
            ->merge(Modal::with('petani')
                ->orderByDesc('tanggal_pemberian')->orderByDesc('created_at')->orderByDesc('id')
                ->take(8)->get()->map(fn ($modal) => [
                    'jenis' => 'modal',
                    'label' => 'Modal',
                    'tanggal' => $modal->tanggal_pemberian,
                    'created_at' => $modal->created_at,
                    'id' => $modal->id,
                    'petani' => $modal->petani->nama_petani ?? '-',
                    'detail' => collect([$modal->jenis_modal, $modal->kategori_kebutuhan, $modal->rincian_barang])->filter()->join(' • '),
                    'nominal' => $modal->nominal,
                    'sub' => $modal->catatan ?: 'Pemberian modal',
                    'url' => route('pengepul.modal.show', $modal),
                ]))
            ->merge(PembayaranKewajiban::with('petani')
                ->orderByDesc('tanggal_bayar')->orderByDesc('created_at')->orderByDesc('id')
                ->take(8)->get()->map(fn ($bayar) => [
                    'jenis' => 'bayar',
                    'label' => 'Bayar Tunai',
                    'tanggal' => $bayar->tanggal_bayar,
                    'created_at' => $bayar->created_at,
                    'id' => $bayar->id,
                    'petani' => $bayar->petani->nama_petani ?? '-',
                    'detail' => $bayar->metode_bayar ?: 'Tunai',
                    'nominal' => $bayar->jumlah_bayar,
                    'sub' => 'Sisa Rp ' . number_format($bayar->sisa_kewajiban_sesudah, 0, ',', '.'),
                    'url' => route('pengepul.pembayaran.show', $bayar),
                ]))
            ->sortByDesc(fn ($a) => ($a['tanggal'] ? $a['tanggal']->format('Y-m-d') : '0000-00-00') . '_' . ($a['created_at'] ? $a['created_at']->timestamp : 0) . '_' . str_pad($a['id'], 10, '0', STR_PAD_LEFT))
            ->values()
            ->take(8);

        // Petani dengan sisa kewajiban tertinggi
        $petaniKewajiban = Petani::where('sisa_kewajiban', '>', 0)
            ->orderByDesc('sisa_kewajiban')
            ->take(5)
            ->get();

        // Rekap status WA log
        $waPending = WaLog::where('status', 'pending')->count();
        $waSukses = WaLog::whereIn('status', ['sukses', 'simulasi'])->count();

        return view('pengepul.dashboard', compact(
            'totalPetani',
            'totalModalDiberikan',
            'totalPembayaranKewajiban',
            'totalSisaKewajiban',
            'totalPembelianPanen',
            'transaksiTerbaru',
            'modalTerbaru',
            'aktivitasTerbaru',
            'petaniKewajiban',
            'waPending',
            'waSukses'
        ));
    }
}
