<?php

namespace App\Http\Controllers\Pengepul;

use App\Http\Controllers\Controller;
use App\Models\Komoditas;
use Illuminate\Http\Request;

class KomoditasController extends Controller
{
    public function index()
    {
        $komoditas = Komoditas::orderBy('kategori')->orderBy('nama_komoditas')->get();
        return view('pengepul.komoditas.index', compact('komoditas'));
    }

    public function create()
    {
        return view('pengepul.komoditas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_komoditas' => ['required', 'string', 'max:100', 'unique:komoditas,nama_komoditas'],
            'kategori' => ['required', 'string', 'max:50'],
            'satuan' => ['required', 'string', 'max:20'],
            'harga_pasar_default' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Komoditas::create($validated);

        return redirect()->route('pengepul.komoditas.index')
            ->with('success', 'Komoditas sayuran berhasil ditambahkan.');
    }

    public function edit(Komoditas $komodita)
    {
        return view('pengepul.komoditas.edit', ['komoditas' => $komodita]);
    }

    public function update(Request $request, Komoditas $komodita)
    {
        $validated = $request->validate([
            'nama_komoditas' => ['required', 'string', 'max:100', 'unique:komoditas,nama_komoditas,' . $komodita->id],
            'kategori' => ['required', 'string', 'max:50'],
            'satuan' => ['required', 'string', 'max:20'],
            'harga_pasar_default' => ['required', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $komodita->update($validated);

        return redirect()->route('pengepul.komoditas.index')
            ->with('success', 'Harga dan data komoditas berhasil diperbarui.');
    }

    public function destroy(Komoditas $komodita)
    {
        $nama = $komodita->nama_komoditas;
        $komodita->delete();

        return redirect()->route('pengepul.komoditas.index')
            ->with('success', "Komoditas {$nama} berhasil dihapus.");
    }
}
