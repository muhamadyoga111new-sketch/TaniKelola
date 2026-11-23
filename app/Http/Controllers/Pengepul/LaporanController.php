<?php

namespace App\Http\Controllers\Pengepul;

use App\Http\Controllers\Controller;
use App\Models\Modal;
use App\Models\Petani;
use App\Models\TransaksiPanen;
use App\Models\PembayaranKewajiban;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $jenisLaporan = $request->query('jenis', 'kewajiban');
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        $petaniId = $request->query('petani_id');

        $petanis = Petani::orderBy('nama_petani')->get();

        $dataLaporan = [];

        if ($jenisLaporan === 'kewajiban') {
            $query = Petani::with(['modals', 'transaksiPanens', 'pembayaranKewajibans']);
            if ($petaniId) {
                $query->where('id', $petaniId);
            }
            $dataLaporan = $query->orderBy('nama_petani')->get();
        } elseif ($jenisLaporan === 'transaksi') {
            $query = TransaksiPanen::with(['petani', 'details.komoditas'])
                ->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            if ($petaniId) {
                $query->where('petani_id', $petaniId);
            }
            $dataLaporan = $query->latest('tanggal_transaksi')->get();
        } elseif ($jenisLaporan === 'modal') {
            $query = Modal::with('petani')
                ->whereBetween('tanggal_pemberian', [$startDate, $endDate]);
            if ($petaniId) {
                $query->where('petani_id', $petaniId);
            }
            $dataLaporan = $query->latest('tanggal_pemberian')->get();
        }

        return view('pengepul.laporan.index', compact(
            'jenisLaporan',
            'startDate',
            'endDate',
            'petaniId',
            'petanis',
            'dataLaporan'
        ));
    }

    public function cetak(Request $request)
    {
        $jenisLaporan = $request->query('jenis', 'kewajiban');
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        $petaniId = $request->query('petani_id');

        $dataLaporan = [];
        $selectedPetani = $petaniId ? Petani::find($petaniId) : null;

        if ($jenisLaporan === 'kewajiban') {
            $query = Petani::with(['modals', 'transaksiPanens', 'pembayaranKewajibans']);
            if ($petaniId) {
                $query->where('id', $petaniId);
            }
            $dataLaporan = $query->orderBy('nama_petani')->get();
        } elseif ($jenisLaporan === 'transaksi') {
            $query = TransaksiPanen::with(['petani', 'details.komoditas'])
                ->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
            if ($petaniId) {
                $query->where('petani_id', $petaniId);
            }
            $dataLaporan = $query->latest('tanggal_transaksi')->get();
        } elseif ($jenisLaporan === 'modal') {
            $query = Modal::with('petani')
                ->whereBetween('tanggal_pemberian', [$startDate, $endDate]);
            if ($petaniId) {
                $query->where('petani_id', $petaniId);
            }
            $dataLaporan = $query->latest('tanggal_pemberian')->get();
        }

        return view('pengepul.laporan.cetak', compact(
            'jenisLaporan',
            'startDate',
            'endDate',
            'selectedPetani',
            'dataLaporan'
        ));
    }
}
