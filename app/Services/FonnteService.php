<?php

namespace App\Services;

use App\Models\Petani;
use App\Models\TransaksiPanen;
use App\Models\Modal;
use App\Models\PembayaranKewajiban;
use App\Models\WaLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected ?string $token;
    protected string $url;

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN');
        $this->url = env('FONNTE_URL', 'https://api.fonnte.com/send_message');
    }

    /**
     * Format nomor HP agar standar Indonesia (62xxx)
     */
    public function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }

    /**
     * Format Rupiah helper
     */
    public function rupiah($number): string
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }

    /**
     * Kirim Pesan via Fonnte API
     */
    public function sendMessage(string $targetPhone, string $recipientName, string $message, string $notificationType = 'umum', ?int $petaniId = null): array
    {
        $target = $this->formatPhoneNumber($targetPhone);

        // Jika token kosong atau placeholder, gunakan mode simulasi yang aman & transparan
        if (empty($this->token) || in_array(strtolower($this->token), ['dummy', 'mock', 'token_fonnte_anda'])) {
            $log = WaLog::create([
                'petani_id' => $petaniId,
                'tujuan_nomor' => $target,
                'penerima_nama' => $recipientName,
                'tipe_notifikasi' => $notificationType,
                'pesan' => $message,
                'status' => 'simulasi',
                'response_api' => json_encode(['info' => 'Terkirim dalam mode simulasi (masukkan FONNTE_TOKEN di .env untuk pengiriman WA asli)']),
            ]);

            return [
                'status' => true,
                'mode' => 'simulasi',
                'message' => 'Notifikasi tercatat dalam mode simulasi (Token Fonnte belum diisi).',
                'log_id' => $log->id,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->timeout(10)->post($this->url, [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);

            $result = $response->json();
            $status = ($response->successful() && isset($result['status']) && $result['status'] == true) ? 'sukses' : 'gagal';

            $log = WaLog::create([
                'petani_id' => $petaniId,
                'tujuan_nomor' => $target,
                'penerima_nama' => $recipientName,
                'tipe_notifikasi' => $notificationType,
                'pesan' => $message,
                'status' => $status,
                'response_api' => json_encode($result),
            ]);

            return [
                'status' => $status === 'sukses',
                'mode' => 'live',
                'message' => $status === 'sukses' ? 'WhatsApp berhasil terkirim.' : 'Gagal mengirim pesan WhatsApp.',
                'response' => $result,
                'log_id' => $log->id,
            ];
        } catch (\Exception $e) {
            Log::error('Fonnte WhatsApp Error: ' . $e->getMessage());

            WaLog::create([
                'petani_id' => $petaniId,
                'tujuan_nomor' => $target,
                'penerima_nama' => $recipientName,
                'tipe_notifikasi' => $notificationType,
                'pesan' => $message,
                'status' => 'gagal',
                'response_api' => json_encode(['error' => $e->getMessage()]),
            ]);

            return [
                'status' => false,
                'mode' => 'live',
                'message' => 'Koneksi ke gateway WhatsApp terganggu: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim notifikasi transaksi panen sesuai spesifikasi proposal
     */
    public function sendTransaksiPanenNotification(TransaksiPanen $transaksi): array
    {
        $petani = $transaksi->petani;
        $tanggal = \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d F Y');

        $rincianItem = "";
        $no = 1;
        foreach ($transaksi->details as $item) {
            $subtotal = $this->rupiah($item->subtotal);
            $harga = $this->rupiah($item->harga_beli);
            $rincianItem .= "  {$no}. {$item->komoditas->nama_komoditas}: {$item->jumlah_panen} {$item->komoditas->satuan} @ {$harga} = {$subtotal}\n";
            $no++;
        }

        $totalBruto = $this->rupiah($transaksi->total_bruto);
        $potongan = $this->rupiah($transaksi->potongan_kewajiban);
        $uangDiterima = $this->rupiah($transaksi->uang_diterima_petani);
        $sisaKewajiban = $this->rupiah($transaksi->sisa_kewajiban_sesudah);

        $pesan = "*NOTA TRANSAKSI HASIL PANEN*\n";
        $pesan .= "*TaniKelola - Pengepul Bpk. Mohtar*\n";
        $pesan .= "-------------------------------------------\n";
        $pesan .= "No. Transaksi : {$transaksi->nomor_transaksi}\n";
        $pesan .= "Tanggal       : {$tanggal}\n";
        $pesan .= "Nama Petani   : {$petani->nama_petani}\n\n";
        $pesan .= "*Rincian Panen:*\n{$rincianItem}\n";
        $pesan .= "-------------------------------------------\n";
        $pesan .= "Total Nilai Panen   : *{$totalBruto}*\n";
        $pesan .= "Potongan Modal/Utang: -{$potongan}\n";
        $pesan .= "-------------------------------------------\n";
        $pesan .= "💰 *UANG DITERIMA*  : *{$uangDiterima}*\n";
        $pesan .= "-------------------------------------------\n";
        $pesan .= "📌 *Sisa Kewajiban* : *{$sisaKewajiban}*\n\n";
        $pesan .= "Terima kasih atas kerja samanya. Bukti ini sah diterbitkan otomatis melalui Sistem Informasi TaniKelola.";

        $result = $this->sendMessage($petani->no_wa, $petani->nama_petani, $pesan, 'transaksi_panen', $petani->id);

        $statusWa = $result['mode'] === 'simulasi' ? 'simulasi' : ($result['status'] ? 'terkirim' : 'gagal');
        $transaksi->update(['status_wa' => $statusWa]);

        return $result;
    }

    /**
     * Kirim notifikasi pemberian modal
     */
    public function sendModalNotification(Modal $modal): array
    {
        $petani = $modal->petani;
        $tanggal = \Carbon\Carbon::parse($modal->tanggal_pemberian)->translatedFormat('d F Y');
        $nominal = $this->rupiah($modal->nominal);
        $sisaKewajiban = $this->rupiah($petani->sisa_kewajiban);

        $pesan = "*BUKTI PEMBERIAN MODAL TANI*\n";
        $pesan .= "*TaniKelola - Pengepul Bpk. Mohtar*\n";
        $pesan .= "-------------------------------------------\n";
        $pesan .= "No. Bukti    : {$modal->kode_modal}\n";
        $pesan .= "Tanggal      : {$tanggal}\n";
        $pesan .= "Nama Petani  : {$petani->nama_petani}\n";
        $pesan .= "Jenis Modal  : " . ucfirst($modal->jenis_modal) . " ({$modal->kategori_kebutuhan})\n";
        if ($modal->rincian_barang) {
            $pesan .= "Rincian      : {$modal->rincian_barang}\n";
        }
        $pesan .= "Nominal Modal: *{$nominal}*\n";
        $pesan .= "-------------------------------------------\n";
        $pesan .= "📌 *Total Sisa Kewajiban*: *{$sisaKewajiban}*\n\n";
        $pesan .= "Catatan ini telah tercatat dalam buku kewajiban Sistem TaniKelola.";

        return $this->sendMessage($petani->no_wa, $petani->nama_petani, $pesan, 'pemberian_modal', $petani->id);
    }

    /**
     * Kirim notifikasi pembayaran tunai langsung
     */
    public function sendPembayaranTunaiNotification(PembayaranKewajiban $bayar): array
    {
        $petani = $bayar->petani;
        $tanggal = \Carbon\Carbon::parse($bayar->tanggal_bayar)->translatedFormat('d F Y');
        $jumlahBayar = $this->rupiah($bayar->jumlah_bayar);
        $sisaKewajiban = $this->rupiah($bayar->sisa_kewajiban_sesudah);

        $pesan = "*BUKTI PEMBAYARAN KEWAJIBAN*\n";
        $pesan .= "*TaniKelola - Pengepul Bpk. Mohtar*\n";
        $pesan .= "-------------------------------------------\n";
        $pesan .= "No. Kwitansi : {$bayar->kode_pembayaran}\n";
        $pesan .= "Tanggal      : {$tanggal}\n";
        $pesan .= "Nama Petani  : {$petani->nama_petani}\n";
        $pesan .= "Jumlah Bayar : *{$jumlahBayar}*\n";
        $pesan .= "Metode       : {$bayar->metode_bayar}\n";
        $pesan .= "-------------------------------------------\n";
        $pesan .= "📌 *Sisa Kewajiban Baru*: *{$sisaKewajiban}*\n\n";
        $pesan .= "Pembayaran tunai Anda telah berhasil diverifikasi dan dicatat.";

        $result = $this->sendMessage($petani->no_wa, $petani->nama_petani, $pesan, 'pembayaran_tunai', $petani->id);

        $statusWa = $result['mode'] === 'simulasi' ? 'simulasi' : ($result['status'] ? 'terkirim' : 'gagal');
        $bayar->update(['status_wa' => $statusWa]);

        return $result;
    }
}
