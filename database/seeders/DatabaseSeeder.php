<?php

namespace Database\Seeders;

use App\Models\Komoditas;
use App\Models\Modal;
use App\Models\Petani;
use App\Models\TransaksiPanen;
use App\Models\DetailTransaksiPanen;
use App\Models\PembayaranKewajiban;
use App\Models\User;
use App\Models\WaLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Akun Administrator
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator Sistem',
                'email' => 'admin@tanikelola.id',
                'no_hp' => '081299887766',
                'peran' => 'admin',
                'password' => Hash::make('password'),
            ]
        );

        // 1. Akun Pengepul (Bapak Mohtar - Mitra Riset Bajuin)
        $pengepul = User::firstOrCreate(
            ['username' => 'pengepul'],
            [
                'nama' => 'Bapak Mohtar (Pengepul)',
                'email' => 'mohtar@tanikelola.id',
                'no_hp' => '081234567890',
                'peran' => 'pengepul',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Akun Petani dan Profil Petani
        $petaniData = [
            [
                'name' => 'Pak Subarjo',
                'username' => 'subarjo',
                'phone' => '082155667788',
                'nik' => '6301021204750001',
                'kode_petani' => 'PTN-001',
                'alamat' => 'Jl. Atilam RT 04, Desa Bajuin',
                'luas_lahan' => '0.5 Hektar',
                'komoditas_utama' => 'Cabai Rawit & Tomat',
            ],
            [
                'name' => 'Pak Sugeng',
                'username' => 'sugeng',
                'phone' => '082233445566',
                'nik' => '6301021508800002',
                'kode_petani' => 'PTN-002',
                'alamat' => 'Dusun 2 RT 07, Desa Bajuin',
                'luas_lahan' => '0.75 Hektar',
                'komoditas_utama' => 'Terong Ungu & Jagung Manis',
            ],
            [
                'name' => 'Ibu Maryati',
                'username' => 'maryati',
                'phone' => '085299881122',
                'nik' => '6301025510820003',
                'kode_petani' => 'PTN-003',
                'alamat' => 'Dekat Sungai RT 02, Desa Bajuin',
                'luas_lahan' => '0.4 Hektar',
                'komoditas_utama' => 'Buncis & Kacang Panjang',
            ],
        ];

        $petaniModels = [];
        foreach ($petaniData as $p) {
            $userPetani = User::create([
                'nama' => $p['name'],
                'username' => $p['username'],
                'email' => $p['username'] . '@tanikelola.id',
                'no_hp' => $p['phone'],
                'peran' => 'petani',
                'password' => Hash::make('password'),
            ]);

            $petaniModel = Petani::create([
                'pengguna_id' => $userPetani->id,
                'kode_petani' => $p['kode_petani'],
                'nama_petani' => $p['name'],
                'nik' => $p['nik'],
                'no_wa' => $p['phone'],
                'alamat' => $p['alamat'],
                'luas_lahan' => $p['luas_lahan'],
                'komoditas_utama' => $p['komoditas_utama'],
                'total_modal' => 0,
                'total_terbayar' => 0,
                'sisa_kewajiban' => 0,
                'status' => 'aktif',
            ]);

            $petaniModels[$p['username']] = $petaniModel;
        }

        // 3. 14 Komoditas Sayuran Sesuai Batasan Proposal
        $komoditasList = [
            ['nama_komoditas' => 'Cabai Rawit', 'kategori' => 'Cabai', 'satuan' => 'kg', 'harga_pasar_default' => 38000, 'deskripsi' => 'Cabai rawit segar grade A/B'],
            ['nama_komoditas' => 'Cabai Merah Keriting', 'kategori' => 'Cabai', 'satuan' => 'kg', 'harga_pasar_default' => 28000, 'deskripsi' => 'Cabai keriting merah segar'],
            ['nama_komoditas' => 'Cabai Hijau Keriting', 'kategori' => 'Cabai', 'satuan' => 'kg', 'harga_pasar_default' => 20000, 'deskripsi' => 'Cabai keriting hijau'],
            ['nama_komoditas' => 'Cabai Merah Besar', 'kategori' => 'Cabai', 'satuan' => 'kg', 'harga_pasar_default' => 26000, 'deskripsi' => 'Cabai merah teropong besar'],
            ['nama_komoditas' => 'Cabai Hijau Besar', 'kategori' => 'Cabai', 'satuan' => 'kg', 'harga_pasar_default' => 18000, 'deskripsi' => 'Cabai hijau besar segar'],
            ['nama_komoditas' => 'Terong Putih', 'kategori' => 'Sayuran Buah', 'satuan' => 'kg', 'harga_pasar_default' => 8000, 'deskripsi' => 'Terong putih mulus'],
            ['nama_komoditas' => 'Terong Ungu', 'kategori' => 'Sayuran Buah', 'satuan' => 'kg', 'harga_pasar_default' => 7000, 'deskripsi' => 'Terong ungu panjang'],
            ['nama_komoditas' => 'Tomat', 'kategori' => 'Sayuran Buah', 'satuan' => 'kg', 'harga_pasar_default' => 11000, 'deskripsi' => 'Tomat buah ranum segar'],
            ['nama_komoditas' => 'Mentimun', 'kategori' => 'Sayuran Buah', 'satuan' => 'kg', 'harga_pasar_default' => 6000, 'deskripsi' => 'Mentimun lalap / sayur'],
            ['nama_komoditas' => 'Labu Kuning', 'kategori' => 'Sayuran Buah', 'satuan' => 'kg', 'harga_pasar_default' => 5000, 'deskripsi' => 'Labu manis kuning'],
            ['nama_komoditas' => 'Kacang Panjang', 'kategori' => 'Sayuran Polong', 'satuan' => 'kg', 'harga_pasar_default' => 9000, 'deskripsi' => 'Kacang panjang segar'],
            ['nama_komoditas' => 'Buncis', 'kategori' => 'Sayuran Polong', 'satuan' => 'kg', 'harga_pasar_default' => 13000, 'deskripsi' => 'Buncis manis renyah'],
            ['nama_komoditas' => 'Jagung Manis', 'kategori' => 'Palawija', 'satuan' => 'kg', 'harga_pasar_default' => 7500, 'deskripsi' => 'Jagung manis tongkol segar'],
            ['nama_komoditas' => 'Daun Bawang', 'kategori' => 'Sayuran Daun', 'satuan' => 'kg', 'harga_pasar_default' => 16000, 'deskripsi' => 'Daun bawang segar'],
        ];

        $komoditasMap = [];
        foreach ($komoditasList as $k) {
            $created = Komoditas::create($k);
            $komoditasMap[$k['nama_komoditas']] = $created;
        }

        // 4. Sample Modal Diberikan Pengepul ke Petani
        // Subarjo: 2 modal (Pupuk + Obat)
        Modal::create([
            'kode_modal' => 'MDL-202609-0001',
            'petani_id' => $petaniModels['subarjo']->id,
            'jenis_modal' => 'barang',
            'kategori_kebutuhan' => 'Pupuk & Bibit',
            'rincian_barang' => 'Pupuk NPK Mutiara 50kg, Bibit Cabai Rawit Ori 2 bungkus',
            'nominal' => 950000,
            'tanggal_pemberian' => '2026-09-01',
            'catatan' => 'Modal awal tanam musim penghujan',
            'user_id' => $pengepul->id,
            'pengguna_id' => $pengepul->id,
        ]);

        Modal::create([
            'kode_modal' => 'MDL-202609-0002',
            'petani_id' => $petaniModels['subarjo']->id,
            'jenis_modal' => 'uang',
            'kategori_kebutuhan' => 'Pengolahan Lahan',
            'rincian_barang' => 'Biaya sewa kultivator dan tenaga bedengan',
            'nominal' => 500000,
            'tanggal_pemberian' => '2026-09-05',
            'catatan' => 'Uang tunai diserahkan langsung di pondok kebun',
            'pengguna_id' => $pengepul->id,
        ]);

        // Sugeng: 1 modal
        Modal::create([
            'kode_modal' => 'MDL-202609-0003',
            'petani_id' => $petaniModels['sugeng']->id,
            'jenis_modal' => 'barang',
            'kategori_kebutuhan' => 'Obat & Insektisida',
            'rincian_barang' => 'Insektisida Regent 2 botol, Fungisida Antracol 1 kg',
            'nominal' => 450000,
            'tanggal_pemberian' => '2026-09-10',
            'catatan' => 'Pengendalian hama tanaman terong',
            'pengguna_id' => $pengepul->id,
        ]);

        // Maryati: 1 modal
        Modal::create([
            'kode_modal' => 'MDL-202609-0004',
            'petani_id' => $petaniModels['maryati']->id,
            'jenis_modal' => 'uang',
            'kategori_kebutuhan' => 'Upah Tenaga Kerja',
            'rincian_barang' => 'Upah pemasangan lanjaran bambu 2 orang 2 hari',
            'nominal' => 400000,
            'tanggal_pemberian' => '2026-09-12',
            'catatan' => 'Lanjaran kacang panjang',
            'pengguna_id' => $pengepul->id,
        ]);

        // 5. Sample Transaksi Panen Subarjo (Panen Cabai Rawit & Tomat)
        $subarjo = $petaniModels['subarjo']->fresh();
        $sisaSebelum = $subarjo->sisa_kewajiban; // 1.450.000
        $potongan = 400000;
        $totalBruto = (25 * 38000) + (50 * 11000); // 950.000 + 550.000 = 1.500.000
        $uangDiterima = $totalBruto - $potongan; // 1.100.000
        $sisaSesudah = $sisaSebelum - $potongan; // 1.050.000

        $trx1 = TransaksiPanen::create([
            'nomor_transaksi' => 'TRX-202609-0001',
            'petani_id' => $subarjo->id,
            'tanggal_transaksi' => '2026-09-25',
            'total_bruto' => $totalBruto,
            'potongan_kewajiban' => $potongan,
            'uang_diterima_petani' => $uangDiterima,
            'sisa_kewajiban_sebelum' => $sisaSebelum,
            'sisa_kewajiban_sesudah' => $sisaSesudah,
            'status_wa' => 'simulasi',
            'catatan' => 'Panen petikan ke-1 kondisi bagus',
            'pengguna_id' => $pengepul->id,
        ]);

        DetailTransaksiPanen::create([
            'transaksi_panen_id' => $trx1->id,
            'komoditas_id' => $komoditasMap['Cabai Rawit']->id,
            'jumlah_panen' => 25,
            'harga_beli' => 38000,
            'subtotal' => 950000,
            'keterangan' => 'Cabai rawit merah petikan pagi',
        ]);

        DetailTransaksiPanen::create([
            'transaksi_panen_id' => $trx1->id,
            'komoditas_id' => $komoditasMap['Tomat']->id,
            'jumlah_panen' => 50,
            'harga_beli' => 11000,
            'subtotal' => 550000,
            'keterangan' => 'Tomat merah segar keranjang',
        ]);

        // 6. Sample Pembayaran Kewajiban Tunai (Pak Subarjo bayar cash 150.000)
        $subarjo = $subarjo->fresh();
        PembayaranKewajiban::create([
            'kode_pembayaran' => 'BYR-202609-0001',
            'petani_id' => $subarjo->id,
            'tanggal_bayar' => '2026-09-28',
            'jumlah_bayar' => 150000,
            'metode_bayar' => 'Tunai / Cash',
            'sisa_kewajiban_sebelum' => 1050000,
            'sisa_kewajiban_sesudah' => 900000,
            'status_wa' => 'simulasi',
            'catatan' => 'Titip pembayaran tunai saat pertemuan tani',
            'pengguna_id' => $pengepul->id,
        ]);

        // Log sample WA
        WaLog::create([
            'petani_id' => $subarjo->id,
            'tujuan_nomor' => '6282155667788',
            'penerima_nama' => 'Pak Subarjo',
            'tipe_notifikasi' => 'transaksi_panen',
            'pesan' => "*NOTA TRANSAKSI HASIL PANEN*\nNo. Transaksi : TRX-202609-0001\nTotal Panen: Rp 1.500.000\nPotongan: Rp 400.000\nDiterima: Rp 1.100.000\nSisa Kewajiban: Rp 1.050.000",
            'status' => 'simulasi',
            'response_api' => json_encode(['info' => 'Sample simulasi']),
        ]);

        // Recalculate all petani debts
        foreach ($petaniModels as $p) {
            $p->recalculateKewajiban();
        }
    }
}
