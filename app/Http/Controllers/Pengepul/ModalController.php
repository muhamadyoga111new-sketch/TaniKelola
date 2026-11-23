<?php

namespace App\Http\Controllers\Pengepul;

use App\Http\Controllers\Controller;
use App\Models\Modal;
use App\Models\Petani;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModalController extends Controller
{
    protected FonnteService $waService;

    public function __construct(FonnteService $waService)
    {
        $this->waService = $waService;
    }

    public function index(Request $request)
    {
        $petaniId = $request->query('petani_id');
        $jenisModal = $request->query('jenis_modal');

        $query = Modal::with('petani');

        if ($petaniId) {
            $query->where('petani_id', $petaniId);
        }
        if ($jenisModal) {
            $query->where('jenis_modal', $jenisModal);
        }

        $modals = $query->orderByDesc('tanggal_pemberian')->orderByDesc('created_at')->orderByDesc('id')->paginate(10)->withQueryString();
        $petanis = Petani::orderBy('nama_petani')->get();

        $totalNominalModal = Modal::sum('nominal');

        return view('pengepul.modal.index', compact('modals', 'petanis', 'totalNominalModal', 'petaniId', 'jenisModal'));
    }

    public function create(Request $request)
    {
        $selectedPetaniId = $request->query('petani_id');
        $petanis = Petani::where('status', 'aktif')->orderBy('nama_petani')->get();

        $countToday = Modal::whereDate('created_at', now()->toDateString())->count();
        $kodeOtomatis = 'MDL-' . now()->format('Ym') . '-' . str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

        return view('pengepul.modal.create', compact('petanis', 'kodeOtomatis', 'selectedPetaniId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_modal' => ['required', 'string', 'unique:modal,kode_modal'],
            'petani_id' => ['required', 'exists:petani,id'],
            'jenis_modal' => ['required', 'in:barang,uang'],
            'kategori_kebutuhan' => ['required', 'string', 'max:100'],
            'rincian_barang' => ['nullable', 'string', 'max:255'],
            'nominal' => ['required', 'numeric', 'min:1000'],
            'tanggal_pemberian' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
            'kirim_wa' => ['nullable', 'boolean'],
        ]);

        $modal = DB::transaction(function () use ($validated) {
            $modal = Modal::create([
                'kode_modal' => $validated['kode_modal'],
                'petani_id' => $validated['petani_id'],
                'jenis_modal' => $validated['jenis_modal'],
                'kategori_kebutuhan' => $validated['kategori_kebutuhan'],
                'rincian_barang' => $validated['rincian_barang'] ?? null,
                'nominal' => $validated['nominal'],
                'tanggal_pemberian' => $validated['tanggal_pemberian'],
                'catatan' => $validated['catatan'] ?? null,
                'pengguna_id' => auth()->id(),
            ]);

            return $modal;
        });

        $waMsg = "";
        if ($request->boolean('kirim_wa', true)) {
            $waRes = $this->waService->sendModalNotification($modal);
            $waMsg = " " . ($waRes['mode'] === 'simulasi' ? '(Notifikasi WA tercatat dalam simulasi)' : ($waRes['status'] ? '(WhatsApp terkirim)' : '(Gagal kirim WA)'));
        }

        return redirect()->route('pengepul.modal.index')
            ->with('success', "Pemberian modal sebesar Rp " . number_format($modal->nominal, 0, ',', '.') . " berhasil dicatat.{$waMsg}");
    }

    public function show(Modal $modal)
    {
        $modal->load(['petani', 'user']);
        return view('pengepul.modal.show', compact('modal'));
    }

    public function destroy(Modal $modal)
    {
        $petani = $modal->petani;
        $modal->delete();

        return redirect()->route('pengepul.modal.index')
            ->with('success', 'Catatan pemberian modal berhasil dihapus.');
    }
}
