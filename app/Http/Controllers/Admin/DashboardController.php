<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use App\Models\Modal;
use App\Models\Petani;
use App\Models\TransaksiPanen;
use App\Models\PembayaranKewajiban;
use App\Models\User;
use App\Models\WaLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalAdmin = User::where('peran', 'admin')->count();
        $totalPengepul = User::where('peran', 'pengepul')->count();
        $totalPetani = Petani::where('status', 'aktif')->count();
        $totalModalDiberikan = Modal::sum('nominal');
        $totalPembayaranKewajiban = TransaksiPanen::sum('potongan_kewajiban') + PembayaranKewajiban::sum('jumlah_bayar');
        $totalSisaKewajiban = Petani::sum('sisa_kewajiban');
        $totalPembelianPanen = TransaksiPanen::sum('total_bruto');

        // User terbaru
        $usersTerbaru = User::latest()->take(5)->get();

        // Transaksi panen terbaru
        $transaksiTerbaru = TransaksiPanen::with(['petani', 'details.komoditas'])
            ->latest('tanggal_transaksi')
            ->take(5)
            ->get();

        // Petani dengan sisa kewajiban tertinggi
        $petaniKewajiban = Petani::where('sisa_kewajiban', '>', 0)
            ->orderByDesc('sisa_kewajiban')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalAdmin',
            'totalPengepul',
            'totalPetani',
            'totalModalDiberikan',
            'totalPembayaranKewajiban',
            'totalSisaKewajiban',
            'totalPembelianPanen',
            'usersTerbaru',
            'transaksiTerbaru',
            'petaniKewajiban'
        ));
    }
}
