<?php

namespace App\Http\Controllers\Petani;

use App\Http\Controllers\Controller;
use App\Models\Petani;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $petani = Petani::where('pengguna_id', $user->id)->first();

        if (!$petani) {
            // Jika akun petani belum terhubung profil, cari berdasarkan no_wa
            $petani = Petani::where('no_wa', $user->no_hp ?? $user->phone)->first();
        }

        if (!$petani) {
            return view('petani.no_profile', compact('user'));
        }

        $petani->load([
            'modals' => fn($q) => $q->orderByDesc('tanggal_pemberian')->orderByDesc('created_at')->orderByDesc('id'),
            'transaksiPanens' => fn($q) => $q->with('details.komoditas')->orderByDesc('tanggal_transaksi')->orderByDesc('created_at')->orderByDesc('id'),
            'pembayaranKewajibans' => fn($q) => $q->orderByDesc('tanggal_bayar')->orderByDesc('created_at')->orderByDesc('id'),
        ]);

        $pengepul = User::where('peran', 'pengepul')->first();

        return view('petani.dashboard', compact('petani', 'pengepul'));
    }
}
