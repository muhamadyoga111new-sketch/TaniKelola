<?php

namespace App\Http\Controllers\Pengepul;

use App\Http\Controllers\Controller;
use App\Models\DetailTransaksiPanen;
use App\Models\Komoditas;
use App\Models\Petani;
use App\Models\TransaksiPanen;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiPanenController extends Controller
{
    protected FonnteService $waService;

    public function __construct(FonnteService $waService)
    {
        $this->waService = $waService;
    }

    public function index(Request $request)
    {
        $petaniId = $request->query('petani_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = TransaksiPanen::with(['petani', 'details.komoditas']);

        if ($petaniId) {
            $query->where('petani_id', $petaniId);
        }
        if ($startDate) {
            $query->whereDate('tanggal_transaksi', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('tanggal_transaksi', '<=', $endDate);
        }

        $transaksis = $query->orderByDesc('tanggal_transaksi')->orderByDesc('created_at')->orderByDesc('id')->paginate(10)->withQueryString();
        $petanis = Petani::orderBy('nama_petani')->get();

        $totalNilaiPanen = TransaksiPanen::sum('total_bruto');
        $totalPotonganUtang = TransaksiPanen::sum('potongan_kewajiban');
        $totalUangDiterima = TransaksiPanen::sum('uang_diterima_petani');

        return view('pengepul.transaksi.index', compact(
            'transaksis',
            'petanis',
            'totalNilaiPanen',
            'totalPotonganUtang',
            'totalUangDiterima',
            'petaniId',
            'startDate',
            'endDate'
        ));
    }

    public function create(Request $request)
    {
        $selectedPetaniId = $request->query('petani_id');
        $petanis = Petani::where('status', 'aktif')->orderBy('nama_petani')->get();
        $komoditasList = Komoditas::orderBy('nama_komoditas')->get();

        $countToday = TransaksiPanen::whereDate('created_at', now()->toDateString())->count();
        $nomorOtomatis = 'TRX-' . now()->format('Ym') . '-' . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

        return view('pengepul.transaksi.create', compact('petanis', 'komoditasList', 'nomorOtomatis', 'selectedPetaniId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_transaksi' => ['required', 'string', 'unique:transaksi_panen,nomor_transaksi'],
            'petani_id' => ['required', 'exists:petani,id'],
            'tanggal_transaksi' => ['required', 'date'],
            'potongan_kewajiban' => ['required', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string'],
            'kirim_wa' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.komoditas_id' => ['required', 'exists:komoditas,id'],
            'items.*.jumlah_panen' => ['required', 'numeric', 'min:0.01'],
            'items.*.harga_beli' => ['required', 'numeric', 'min:100'],
            'items.*.keterangan' => ['nullable', 'string'],
        ], [
            'items.required' => 'Minimal tambahkan 1 jenis komoditas sayuran.',
            'items.*.jumlah_panen.min' => 'Berat/jumlah panen harus lebih dari 0.',
        ]);

        $petani = Petani::findOrFail($validated['petani_id']);

        // Hitung total bruto
        $totalBruto = 0;
        $itemsData = [];
        foreach ($validated['items'] as $item) {
            $subtotal = $item['jumlah_panen'] * $item['harga_beli'];
            $totalBruto += $subtotal;
            $itemsData[] = [
                'komoditas_id' => $item['komoditas_id'],
                'jumlah_panen' => $item['jumlah_panen'],
                'harga_beli' => $item['harga_beli'],
                'subtotal' => $subtotal,
                'keterangan' => $item['keterangan'] ?? null,
            ];
        }

        $potongan = min($validated['potongan_kewajiban'], $totalBruto);
        // Pastikan potongan tidak melebihi sisa utang berjalan
        $potongan = min($potongan, $petani->sisa_kewajiban);

        $uangDiterima = $totalBruto - $potongan;
        $sisaSebelum = $petani->sisa_kewajiban;
        $sisaSesudah = max(0, $sisaSebelum - $potongan);

        $transaksi = DB::transaction(function () use ($validated, $petani, $totalBruto, $potongan, $uangDiterima, $sisaSebelum, $sisaSesudah, $itemsData) {
            $trx = TransaksiPanen::create([
                'nomor_transaksi' => $validated['nomor_transaksi'],
                'petani_id' => $petani->id,
                'tanggal_transaksi' => $validated['tanggal_transaksi'],
                'total_bruto' => $totalBruto,
                'potongan_kewajiban' => $potongan,
                'uang_diterima_petani' => $uangDiterima,
                'sisa_kewajiban_sebelum' => $sisaSebelum,
                'sisa_kewajiban_sesudah' => $sisaSesudah,
                'status_wa' => 'pending',
                'catatan' => $validated['catatan'] ?? null,
                'pengguna_id' => auth()->id(),
            ]);

            foreach ($itemsData as $item) {
                $trx->details()->create($item);
            }

            return $trx;
        });

        // Kirim WhatsApp Notification
        $waMsg = "";
        if ($request->boolean('kirim_wa', true)) {
            $transaksi->load(['petani', 'details.komoditas']);
            $waRes = $this->waService->sendTransaksiPanenNotification($transaksi);
            $waMsg = " " . ($waRes['mode'] === 'simulasi' ? '(WhatsApp tercatat dalam simulasi)' : ($waRes['status'] ? '(WhatsApp otomatis terkirim)' : '(Gagal kirim WhatsApp)'));
        }

        return redirect()->route('pengepul.transaksi.show', $transaksi)
            ->with('success', "Transaksi panen {$transaksi->nomor_transaksi} berhasil dicatat.{$waMsg}");
    }

    public function show(TransaksiPanen $transaksi)
    {
        $transaksi->load(['petani', 'details.komoditas', 'user']);
        return view('pengepul.transaksi.show', compact('transaksi'));
    }

    public function cetak(TransaksiPanen $transaksi)
    {
        $transaksi->load(['petani', 'details.komoditas', 'user']);

        // Kalau Dompdf sudah terpasang (composer require barryvdh/laravel-dompdf),
        // unduh langsung sebagai file PDF. Kalau belum, jatuh ke halaman
        // cetak lama (window.print, lalu pilih Save as PDF di dialog browser).
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pengepul.transaksi.cetak_pdf', compact('transaksi'));
            $pdf->setPaper('a4', 'portrait');
            return $pdf->download('Nota-' . $transaksi->nomor_transaksi . '.pdf');
        }

        return view('pengepul.transaksi.cetak', compact('transaksi'));
    }

    public function kirimWa(TransaksiPanen $transaksi)
    {
        $transaksi->load(['petani', 'details.komoditas']);
        $result = $this->waService->sendTransaksiPanenNotification($transaksi);

        $msg = $result['mode'] === 'simulasi'
            ? 'Notifikasi berhasil diuji (Mode Simulasi tercatat di WA Log).'
            : ($result['status'] ? 'WhatsApp berhasil dikirim ke nomor petani.' : 'Gagal mengirim WhatsApp: ' . ($result['message'] ?? 'Error gateway'));

        return back()->with('info', $msg);
    }

    public function destroy(TransaksiPanen $transaksi)
    {
        $petani = $transaksi->petani;
        $nomor = $transaksi->nomor_transaksi;
        $transaksi->delete();

        return redirect()->route('pengepul.transaksi.index')
            ->with('success', "Transaksi {$nomor} berhasil dihapus dan kewajiban disinkronkan kembali.");
    }
}
