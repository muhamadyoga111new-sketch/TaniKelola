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

        // Transaksi panen terbaru
        $transaksiTerbaru = TransaksiPanen::with(['petani', 'details.komoditas'])
            ->orderByDesc('tanggal_transaksi')->orderByDesc('created_at')->orderByDesc('id')
            ->take(5)
            ->get();


        // Modal terbaru
        $modalTerbaru = Modal::with('petani')
            ->orderByDesc('tanggal_pemberian')->orderByDesc('created_at')->orderByDesc('id')
            ->take(5)
            ->get();

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
            'petaniKewajiban',
            'waPending',
            'waSukses'
        ));
    }
}
