<?php

namespace App\Http\Controllers\Pengepul;

use App\Http\Controllers\Controller;
use App\Models\Petani;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PetaniController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $query = Petani::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_petani', 'like', "%{$search}%")
                  ->orWhere('kode_petani', 'like', "%{$search}%")
                  ->orWhere('no_wa', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $petanis = $query->orderBy('nama_petani')->paginate(10)->withQueryString();

        $totalPetani = Petani::count();
        $totalKewajibanSemua = Petani::sum('sisa_kewajiban');

        return view('pengepul.petani.index', compact('petanis', 'totalPetani', 'totalKewajibanSemua', 'search'));
    }

    public function create()
    {
        // Generate kode petani otomatis, misal PTN-004
        $lastPetani = Petani::latest('id')->first();
        $nextNumber = $lastPetani ? ($lastPetani->id + 1) : 1;
        $suggestedCode = 'PTN-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return view('pengepul.petani.create', compact('suggestedCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_petani' => ['required', 'string', 'max:20', 'unique:petani,kode_petani'],
            'nama_petani' => ['required', 'string', 'max:150'],
            'nik' => ['nullable', 'string', 'max:30'],
            'no_wa' => ['required', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'luas_lahan' => ['nullable', 'string', 'max:50'],
            'komoditas_utama' => ['nullable', 'string', 'max:100'],
            'username' => ['nullable', 'string', 'max:50', 'unique:pengguna,username'],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'kode_petani.unique' => 'Kode petani sudah terdaftar.',
            'no_wa.required' => 'Nomor WhatsApp wajib diisi untuk notifikasi.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // Buat akun login untuk petani
            $username = $validated['username'] ?? strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $validated['nama_petani'])) . rand(10, 99);
            $password = !empty($validated['password']) ? $validated['password'] : 'password';

            $user = User::create([
                'nama' => $validated['nama_petani'],
                'username' => $username,
                'email' => $username . '@tanikelola.id',
                'no_hp' => $validated['no_wa'],
                'peran' => 'petani',
                'password' => Hash::make($password),
            ]);

            Petani::create([
                'pengguna_id' => $user->id,
                'kode_petani' => $validated['kode_petani'],
                'nama_petani' => $validated['nama_petani'],
                'nik' => $validated['nik'] ?? null,
                'no_wa' => $validated['no_wa'],
                'alamat' => $validated['alamat'] ?? null,
                'luas_lahan' => $validated['luas_lahan'] ?? null,
                'komoditas_utama' => $validated['komoditas_utama'] ?? null,
                'total_modal' => 0,
                'total_terbayar' => 0,
                'sisa_kewajiban' => 0,
                'status' => 'aktif',
            ]);
        });

        return redirect()->route('pengepul.petani.index')
            ->with('success', 'Data Petani dan Akun Login Petani berhasil ditambahkan.');
    }

    public function show(Petani $petani)
    {
        $petani->load([
            'modals' => fn($q) => $q->latest('tanggal_pemberian'),
            'transaksiPanens' => fn($q) => $q->with('details.komoditas')->latest('tanggal_transaksi'),
            'pembayaranKewajibans' => fn($q) => $q->latest('tanggal_bayar'),
            'user'
        ]);

        return view('pengepul.petani.show', compact('petani'));
    }

    public function edit(Petani $petani)
    {
        $petani->load('user');
        return view('pengepul.petani.edit', compact('petani'));
    }

    public function update(Request $request, Petani $petani)
    {
        $validated = $request->validate([
            'nama_petani' => ['required', 'string', 'max:150'],
            'nik' => ['nullable', 'string', 'max:30'],
            'no_wa' => ['required', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'luas_lahan' => ['nullable', 'string', 'max:50'],
            'komoditas_utama' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $petani->update([
            'nama_petani' => $validated['nama_petani'],
            'nik' => $validated['nik'],
            'no_wa' => $validated['no_wa'],
            'alamat' => $validated['alamat'],
            'luas_lahan' => $validated['luas_lahan'],
            'komoditas_utama' => $validated['komoditas_utama'],
            'status' => $validated['status'],
        ]);

        if ($petani->user) {
            $userUpdate = [
                'name' => $validated['nama_petani'],
                'phone' => $validated['no_wa'],
            ];
            if (!empty($validated['password'])) {
                $userUpdate['password'] = Hash::make($validated['password']);
            }
            $petani->user->update($userUpdate);
        }

        return redirect()->route('pengepul.petani.show', $petani)
            ->with('success', 'Data Petani berhasil diperbarui.');
    }

    public function destroy(Petani $petani)
    {
        $nama = $petani->nama_petani;
        if ($petani->user) {
            $petani->user->delete();
        }
        $petani->delete();

        return redirect()->route('pengepul.petani.index')
            ->with('success', "Petani {$nama} berhasil dihapus dari sistem.");
    }
}
