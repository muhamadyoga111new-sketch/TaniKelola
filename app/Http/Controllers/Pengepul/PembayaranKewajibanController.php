<?php

namespace App\Http\Controllers\Pengepul;

use App\Http\Controllers\Controller;
use App\Models\PembayaranKewajiban;
use App\Models\Petani;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranKewajibanController extends Controller
{
    protected FonnteService $waService;

    public function __construct(FonnteService $waService)
    {
        $this->waService = $waService;
    }

    public function index(Request $request)
    {
        $petaniId = $request->query('petani_id');

        $query = PembayaranKewajiban::with('petani');

        if ($petaniId) {
            $query->where('petani_id', $petaniId);
        }

        $pembayarans = $query->orderByDesc('tanggal_bayar')->orderByDesc('created_at')->orderByDesc('id')->paginate(10)->withQueryString();
        $petanis = Petani::orderBy('nama_petani')->get();

        $totalBayarTunai = PembayaranKewajiban::sum('jumlah_bayar');

        return view('pengepul.pembayaran.index', compact('pembayarans', 'petanis', 'totalBayarTunai', 'petaniId'));
    }

    public function create(Request $request)
    {
        $selectedPetaniId = $request->query('petani_id');
        $petanis = Petani::where('sisa_kewajiban', '>', 0)->orderBy('nama_petani')->get();

        $countToday = PembayaranKewajiban::whereDate('created_at', now()->toDateString())->count();
        $kodeOtomatis = 'BYR-' . now()->format('Ym') . '-' . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

        return view('pengepul.pembayaran.create', compact('petanis', 'kodeOtomatis', 'selectedPetaniId'));
    }

    public function store(Request $request)
    {
        // Kode dibuat dari jumlah pembayaran hari ini. Kalau form dikirim ulang
        // (mis. setelah gagal validasi, atau tombol Simpan diklik dua kali),
        // kode yang sama akan ditolak aturan unique. Jadi kalau sudah dipakai,
        // cari kode kosong berikutnya sebelum validasi.
        $kode = $request->input('kode_pembayaran');
        if ($kode && PembayaranKewajiban::where('kode_pembayaran', $kode)->exists()) {
            $countToday = PembayaranKewajiban::whereDate('created_at', now()->toDateString())->count();
            do {
                $countToday++;
                $kode = 'BYR-' . now()->format('Ym') . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);
            } while (PembayaranKewajiban::where('kode_pembayaran', $kode)->exists());
            $request->merge(['kode_pembayaran' => $kode]);
        }

        $validated = $request->validate([
            'kode_pembayaran' => ['required', 'string', 'unique:pembayaran_kewajiban,kode_pembayaran'],
            'petani_id' => ['required', 'exists:petani,id'],
            'tanggal_bayar' => ['required', 'date'],
            'jumlah_bayar' => ['required', 'numeric', 'min:1000'],
            'metode_bayar' => ['required', 'string', 'max:50'],
            'catatan' => ['nullable', 'string'],
            'kirim_wa' => ['nullable', 'boolean'],
        ]);

        $petani = Petani::findOrFail($validated['petani_id']);

        if ($validated['jumlah_bayar'] > $petani->sisa_kewajiban) {
            return back()->withInput()->withErrors([
                'jumlah_bayar' => 'Jumlah bayar tidak boleh melebihi sisa kewajiban petani (Rp ' . number_format($petani->sisa_kewajiban, 0, ',', '.') . ').',
            ]);
        }

        $sisaSebelum = $petani->sisa_kewajiban;
        $sisaSesudah = max(0, $sisaSebelum - $validated['jumlah_bayar']);

        $bayar = DB::transaction(function () use ($validated, $petani, $sisaSebelum, $sisaSesudah) {
            return PembayaranKewajiban::create([
                'kode_pembayaran' => $validated['kode_pembayaran'],
                'petani_id' => $petani->id,
                'tanggal_bayar' => $validated['tanggal_bayar'],
                'jumlah_bayar' => $validated['jumlah_bayar'],
                'metode_bayar' => $validated['metode_bayar'],
                'sisa_kewajiban_sebelum' => $sisaSebelum,
                'sisa_kewajiban_sesudah' => $sisaSesudah,
                'status_wa' => 'pending',
                'catatan' => $validated['catatan'] ?? null,
                'pengguna_id' => auth()->id(),
            ]);
        });

        $waMsg = "";
        if ($request->boolean('kirim_wa', true)) {
            $bayar->load('petani');
            $waRes = $this->waService->sendPembayaranTunaiNotification($bayar);
            $waMsg = " " . ($waRes['mode'] === 'simulasi' ? '(WhatsApp tercatat dalam mode simulasi)' : ($waRes['status'] ? '(WhatsApp otomatis terkirim)' : '(Gagal kirim WA)'));
        }

        return redirect()->route('pengepul.pembayaran.index')
            ->with('success', "Pembayaran kewajiban sebesar Rp " . number_format($bayar->jumlah_bayar, 0, ',', '.') . " berhasil dicatat.{$waMsg}");
    }

    public function show(PembayaranKewajiban $pembayaran)
    {
        $pembayaran->load(['petani', 'user']);
        return view('pengepul.pembayaran.show', compact('pembayaran'));
    }

    public function destroy(PembayaranKewajiban $pembayaran)
    {
        $petani = $pembayaran->petani;
        $pembayaran->delete();

        return redirect()->route('pengepul.pembayaran.index')
            ->with('success', 'Catatan pembayaran tunai berhasil dihapus.');
    }
}
