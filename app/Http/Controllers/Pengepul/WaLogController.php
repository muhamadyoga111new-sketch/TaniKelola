<?php

namespace App\Http\Controllers\Pengepul;

use App\Http\Controllers\Controller;
use App\Models\WaLog;
use App\Services\FonnteService;
use Illuminate\Http\Request;

class WaLogController extends Controller
{
    protected FonnteService $waService;

    public function __construct(FonnteService $waService)
    {
        $this->waService = $waService;
    }

    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = WaLog::with('petani');

        if ($status) {
            $query->where('status', $status);
        }

        $logs = $query->latest()->paginate(15)->withQueryString();

        $tokenConfigured = !empty(env('FONNTE_TOKEN')) && !in_array(strtolower(env('FONNTE_TOKEN')), ['dummy', 'mock', 'token_fonnte_anda']);
        $totalLogs = WaLog::count();
        $totalSimulasi = WaLog::where('status', 'simulasi')->count();
        $totalSukses = WaLog::where('status', 'sukses')->count();
        $totalGagal = WaLog::where('status', 'gagal')->count();

        return view('pengepul.wa_log.index', compact(
            'logs',
            'tokenConfigured',
            'totalLogs',
            'totalSimulasi',
            'totalSukses',
            'totalGagal',
            'status'
        ));
    }

    public function testSend(Request $request)
    {
        $validated = $request->validate([
            'target_phone' => ['required', 'string'],
            'recipient_name' => ['required', 'string'],
            'message' => ['required', 'string'],
        ]);

        $res = $this->waService->sendMessage(
            $validated['target_phone'],
            $validated['recipient_name'],
            $validated['message'],
            'manual_test'
        );

        if ($res['mode'] === 'simulasi') {
            return back()->with('info', 'Uji coba berhasil tercatat dalam Mode Simulasi. Token Fonnte belum diisi di .env.');
        }

        if ($res['status']) {
            return back()->with('success', 'Pesan WhatsApp berhasil dikirim ke nomor ' . $validated['target_phone']);
        }

        return back()->with('error', 'Gagal mengirim pesan: ' . ($res['message'] ?? 'Error gateway'));
    }
}
