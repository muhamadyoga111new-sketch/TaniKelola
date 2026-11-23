<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Petani;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Tampilkan daftar semua akun pengguna.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $roleFilter = $request->query('role');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        if ($roleFilter && in_array($roleFilter, ['admin', 'pengepul', 'petani'])) {
            $query->where('peran', $roleFilter);
        }

        $users = $query->orderBy('nama')->paginate(15)->withQueryString();

        $totalAdmin = User::where('peran', 'admin')->count();
        $totalPengepul = User::where('peran', 'pengepul')->count();
        $totalPetani = User::where('peran', 'petani')->count();

        return view('admin.users.index', compact('users', 'search', 'roleFilter', 'totalAdmin', 'totalPengepul', 'totalPetani'));
    }

    /**
     * Form tambah akun baru (Admin / Pengepul).
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Simpan akun baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', 'unique:pengguna,username'],
            'email' => ['nullable', 'email', 'unique:pengguna,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:admin,pengepul,petani'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            // Field petani (conditional)
            'kode_petani' => ['required_if:role,petani', 'nullable', 'string', 'max:20', 'unique:petani,kode_petani'],
            'nik' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'luas_lahan' => ['nullable', 'string', 'max:50'],
            'komoditas_utama' => ['nullable', 'string', 'max:100'],
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'kode_petani.required_if' => 'Kode petani wajib diisi untuk akun Petani.',
            'kode_petani.unique' => 'Kode petani sudah terdaftar.',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'nama' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? $validated['username'] . '@tanikelola.id',
                'no_hp' => $validated['phone'] ?? null,
                'peran' => $validated['role'],
                'password' => Hash::make($validated['password']),
            ]);

            // Jika role petani, buat juga data profil petani
            if ($validated['role'] === 'petani') {
                Petani::create([
                    'pengguna_id' => $user->id,
                    'kode_petani' => $validated['kode_petani'],
                    'nama_petani' => $validated['name'],
                    'nik' => $validated['nik'] ?? null,
                    'no_wa' => $validated['phone'] ?? '',
                    'alamat' => $validated['alamat'] ?? null,
                    'luas_lahan' => $validated['luas_lahan'] ?? null,
                    'komoditas_utama' => $validated['komoditas_utama'] ?? null,
                    'total_modal' => 0,
                    'total_terbayar' => 0,
                    'sisa_kewajiban' => 0,
                    'status' => 'aktif',
                ]);
            }
        });

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun ' . ucfirst($validated['role']) . ' "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    /**
     * Form edit akun.
     */
    public function edit(User $user)
    {
        $user->load('petani');
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update akun.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:50', Rule::unique('pengguna', 'username')->ignore($user->id)],
            'email' => ['nullable', 'email', Rule::unique('pengguna', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:admin,pengepul,petani'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $userData = [
            'nama' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'no_hp' => $validated['phone'],
            'peran' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // Update profil petani jika ada
        if ($user->petani) {
            $user->petani->update([
                'nama_petani' => $validated['name'],
                'no_wa' => $validated['phone'] ?? $user->petani->no_wa,
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun "' . $validated['name'] . '" berhasil diperbarui.');
    }

    /**
     * Hapus akun.
     */
    public function destroy(User $user)
    {
        // Jangan izinkan menghapus akun sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $nama = $user->name;

        DB::transaction(function () use ($user) {
            // Hapus data petani terkait jika ada
            if ($user->petani) {
                $user->petani->delete();
            }
            $user->delete();
        });

        return redirect()->route('admin.users.index')
            ->with('success', "Akun \"{$nama}\" berhasil dihapus dari sistem.");
    }
}
