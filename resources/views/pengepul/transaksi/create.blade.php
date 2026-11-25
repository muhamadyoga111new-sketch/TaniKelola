@extends('layouts.app')

@section('title', 'Catat Transaksi Panen')
@section('page_title', 'Catat Pembelian Hasil Panen')
@section('page_subtitle', 'Timbang hasil panen petani, hitung potongan kewajiban, dan kirim rincian nota via WhatsApp')

@section('content')
<style>
    /* HP (<768px): tabel komoditas berubah jadi kartu bertumpuk, satu baris = satu kartu.
       Input dan id-nya tetap sama, jadi JS hitungan tidak berubah sama sekali. */
    @media (max-width: 767px) {
        #itemsTable thead { display: none; }
        #itemsTable, #itemsTable tbody, #itemsTable tr, #itemsTable td { display: block; width: 100%; }
        #itemsTable tbody tr {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            margin-bottom: 0.75rem;
            padding: 0.75rem;
            background: #fff;
        }
        #itemsTable td { padding: 0.25rem 0 !important; border: 0; }
        #itemsTable td::before {
            content: attr(data-label);
            display: block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 0.25rem;
        }
        #itemsTable td[data-label=""]::before { display: none; }
        #itemsTable td:last-child { text-align: right; padding-top: 0.5rem !important; }
    }
</style>
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8">
        <form action="{{ route('pengepul.transaksi.store') }}" method="POST" id="transaksiForm" class="space-y-6">
            @csrf

            <!-- Header Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-b border-slate-100 pb-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Transaksi *</label>
                    <input type="text" name="nomor_transaksi" value="{{ old('nomor_transaksi', $nomorOtomatis) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#15803d] focus:border-transparent bg-slate-50 font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Transaksi Panen *</label>
                    <input type="date" name="tanggal_transaksi" value="{{ old('tanggal_transaksi', date('Y-m-d')) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#15803d] focus:border-transparent">
                </div>
            </div>

            <!-- 1. Petani Selection & Live Debt Status -->
            <div>
                <h4 class="font-bold text-sm text-slate-900 mb-3"><span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#14532d] text-white text-[11px] font-bold mr-1.5">1</span> Petani Penjual Panen</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-2xl bg-[#f7faf7] border border-slate-200">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Petani *</label>
                        <select name="petani_id" id="petaniSelect" required onchange="updatePetaniInfo()"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#15803d] focus:border-transparent bg-white">
                            <option value="">-- Pilih Petani Binaan --</option>
                            @foreach($petanis as $p)
                                <option value="{{ $p->id }}"
                                    data-kewajiban="{{ $p->sisa_kewajiban }}"
                                    data-wa="{{ $p->no_wa }}"
                                    {{ (old('petani_id', $selectedPetaniId) == $p->id) ? 'selected' : '' }}>
                                    {{ $p->nama_petani }} - WA: {{ $p->no_wa }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-3 bg-white rounded-xl border border-slate-200 border-l-4 border-l-rose-500 flex flex-col justify-center">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Sisa Utang Petani:</span>
                        <span id="displaySisaKewajiban" class="text-lg font-black text-rose-600">Rp 0</span>
                        <span id="displayWaPetani" class="text-[11px] text-slate-500 truncate">Pilih petani terlebih dahulu</span>
                    </div>
                </div>
            </div>

            <!-- 2. Multi-Row Vegetable Harvest Items -->
            <div>
                <div class="flex flex-col items-stretch gap-2 sm:flex-row sm:items-center sm:justify-between mb-3">
                    <div class="min-w-0">
                        <h4 class="font-bold text-sm text-slate-900"><span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#14532d] text-white text-[11px] font-bold mr-1.5">2</span> Rincian Komoditas Sayuran</h4>
                        <p class="text-xs text-slate-500 mt-1">Pilih sayuran, masukkan timbangan kg dan harga beli saat transaksi.</p>
                    </div>
                    <button type="button" onclick="addRow()" class="w-full sm:w-auto justify-center shrink-0 whitespace-nowrap inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-[#14532d]/10 hover:bg-[#14532d]/20 text-[#14532d] font-bold text-xs transition">
                        <span>+ Tambah Baris Sayur</span>
                    </button>
                    <p id="rowHint" class="hidden text-[11px] text-rose-600">Isi dulu komoditas di baris terakhir sebelum menambah baris baru.</p>
                </div>

                <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                    <table class="w-full text-left text-sm" id="itemsTable">
                        <thead class="bg-[#14532d] text-[11px] font-bold uppercase tracking-wider text-white border-b-2 border-[#eab308]">
                            <tr>
                                <th class="py-2.5 px-3">Komoditas Sayuran</th>
                                <th class="py-2.5 px-3 w-32">Berat / Timbangan</th>
                                <th class="py-2.5 px-3 w-40">Harga Beli / Satuan</th>
                                <th class="py-2.5 px-3 w-44 text-right">Subtotal</th>
                                <th class="py-2.5 px-3 w-12 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody" class="divide-y divide-slate-100">
                            <!-- Dynamic Rows will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Calculation and Debt Deduction Box -->
            <div>
                <h4 class="font-bold text-sm text-slate-900 mb-3"><span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-[#14532d] text-white text-[11px] font-bold mr-1.5">3</span> Hitungan dan Potongan</h4>
                <div class="p-5 rounded-2xl bg-[#14532d] border-b-4 border-[#eab308] text-white shadow-md space-y-3">
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="text-white/80 leading-snug">Total Nilai Penjualan Panen (Bruto):</span>
                        <span id="labelTotalBruto" class="font-extrabold text-xl text-white">Rp 0</span>
                        <input type="hidden" name="hidden_total_bruto" id="hiddenTotalBruto" value="0">
                    </div>

                    <div class="pt-3 border-t border-white/15 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <label for="potonganInput" class="text-xs font-bold text-white flex flex-wrap items-center gap-1">
                                Potongan Kewajiban / Cicilan Modal:
                                <span class="ml-2 text-[10px] px-2 py-0.5 rounded bg-white/15 text-white">Disepakati</span>
                                <button type="button" onclick="resetPotonganAuto()" class="ml-2 text-[10px] px-2 py-0.5 rounded bg-[#eab308] text-[#1a2e22] font-bold">Potong penuh</button>
                            </label>
                            <span class="text-[11px] text-white/70 block">Otomatis = total panen, dibatasi sisa kewajiban. Bisa diubah manual.</span>
                        </div>
                        <div class="w-full sm:w-56">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-[#b45309] font-bold text-xs">Rp</span>
                                <input type="number" name="potongan_kewajiban" id="potonganInput" value="{{ old('potongan_kewajiban', 0) }}" min="0" step="1"
                                    oninput="onPotonganInput()"
                                    class="w-full pl-9 pr-3 py-2 rounded-xl bg-white border border-white text-slate-900 font-black text-right text-sm focus:ring-2 focus:ring-[#eab308] focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-white/15 flex items-center justify-between gap-3 text-base">
                        <div class="min-w-0">
                            <span class="font-bold text-[#eab308] text-sm block">UANG BERSIH DITERIMA PETANI:</span>
                            <span class="text-[11px] text-white/70">Total Panen dikurangi Potongan Modal</span>
                        </div>
                        <span id="labelUangDiterima" class="font-black text-2xl text-[#eab308]">Rp 0</span>
                    </div>

                    <div class="pt-2 border-t border-white/10 flex items-center justify-between text-xs text-white/80">
                        <span>Estimasi Sisa Kewajiban Petani Setelah Transaksi:</span>
                        <span id="labelSisaSetelahnya" class="font-extrabold text-rose-300 text-sm">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan Transaksi</label>
                <textarea name="catatan" rows="2" placeholder="Contoh: Kondisi cabe petikan pagi, kualitas grade A mulus"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#15803d] focus:border-transparent">{{ old('catatan') }}</textarea>
            </div>

            <!-- WhatsApp Notification Option -->
            <div class="p-4 rounded-2xl bg-white border border-slate-200 border-l-4 border-l-[#eab308] flex items-start space-x-3">
                <input type="checkbox" name="kirim_wa" value="1" id="kirimWa" checked class="mt-1 rounded border-slate-300 text-[#14532d] focus:ring-[#15803d]">
                <label for="kirimWa" class="text-xs text-slate-700 cursor-pointer">
                    <strong class="block font-bold text-slate-900">Kirim Nota via WhatsApp</strong>
                    <span class="text-slate-500">Mode simulasi &mdash; pesan hanya tercatat di log, belum terkirim ke petani. WhatsApp live dipasang di tahap akhir.</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('pengepul.transaksi.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#eab308] hover:bg-[#facc15] text-[#1a2e22] text-xs font-extrabold shadow-md transition">
                    Simpan Transaksi &amp; Terbitkan Nota Panen
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Commodities Data for JS -->
<script>
    const komoditasData = @json($komoditasList);
    let currentPetaniDebt = 0;
    let rowIndex = 0;
    let potonganManual = false;
    // Rupiah: 25.000 / 25,000 / Rp 25.000 dibaca sebagai 25000, bukan 25.
    // parseFloat('25.000') = 25, itu penyebab 4 kg x 25.000 jadi 100.
    function parseRupiah(v) {
        let s = String(v ?? '').trim();
        if (!s) return 0;
        s = s.split(',')[0];
        s = s.replace(/[^0-9]/g, '');
        return parseFloat(s) || 0;
    }
    // Berat kg: 4,5 dan 4.5 sama-sama dibaca 4.5
    function parseDecimal(v) {
        let s = String(v ?? '').trim();
        if (!s) return 0;
        if (s.includes(',')) {
            s = s.replace(/\./g, '').replace(',', '.');
        }
        return parseFloat(s) || 0;
    }


    function updatePetaniInfo() {
        potonganManual = false;
        const select = document.getElementById('petaniSelect');
        const opt = select.options[select.selectedIndex];
        if (opt && opt.value) {
            currentPetaniDebt = parseFloat(opt.getAttribute('data-kewajiban')) || 0;
            const wa = opt.getAttribute('data-wa');
            document.getElementById('displaySisaKewajiban').innerText = 'Rp ' + currentPetaniDebt.toLocaleString('id-ID');
            document.getElementById('displayWaPetani').innerText = 'WA: ' + wa;
        } else {
            currentPetaniDebt = 0;
            document.getElementById('displaySisaKewajiban').innerText = 'Rp 0';
            document.getElementById('displayWaPetani').innerText = 'Pilih petani terlebih dahulu';
        }
        calculateAll();
    }

    function addRow() {
        // Jangan tambah baris baru kalau baris terakhir masih kosong (belum pilih komoditas),
        // supaya tidak menumpuk baris kosong. Arahkan fokus ke baris terakhir itu.
        const hint = document.getElementById('rowHint');
        if (rowIndex > 0) {
            const lastRow = document.getElementById('row-' + (rowIndex - 1));
            const lastSelect = lastRow ? lastRow.querySelector('select') : null;
            if (lastSelect && !lastSelect.value) {
                if (hint) hint.classList.remove('hidden');
                lastSelect.focus();
                return;
            }
        }
        if (hint) hint.classList.add('hidden');
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.id = 'row-' + rowIndex;
        tr.className = 'hover:bg-[#f7faf7]';

        let optionsHtml = '<option value="">-- Pilih Sayuran --</option>';
        komoditasData.forEach(k => {
            optionsHtml += `<option value="${k.id}" data-harga="${k.harga_pasar_default}" data-satuan="${k.satuan}">${k.nama_komoditas} (${k.kategori})</option>`;
        });

        tr.innerHTML = `
            <td class="p-2.5" data-label="Komoditas Sayuran">
                <select name="items[${rowIndex}][komoditas_id]" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-[#15803d]" required onchange="onKomoditasChange(${rowIndex})">
                    ${optionsHtml}
                </select>
                <input type="text" name="items[${rowIndex}][keterangan]" placeholder="Keterangan mutu (opsional)" class="w-full mt-1 px-2 py-1 rounded border border-slate-100 text-[11px] text-slate-500">
            </td>
            <td class="p-2.5" data-label="Berat / Timbangan">
                <div class="relative">
                    <input type="number" step="0.1" min="0.1" name="items[${rowIndex}][jumlah_panen]" id="qty-${rowIndex}" required placeholder="0" oninput="calculateRow(${rowIndex})"
                        class="w-full pr-7 pl-2 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-800">
                    <span id="satuan-${rowIndex}" class="absolute right-2 top-2 text-[10px] text-slate-400">kg</span>
                </div>
            </td>
            <td class="p-2.5" data-label="Harga Beli / Satuan">
                <div class="relative">
                    <span class="absolute left-2 top-2 text-[10px] text-slate-400 font-bold">Rp</span>
                    <input type="number" step="1" min="100" name="items[${rowIndex}][harga_beli]" id="harga-${rowIndex}" required placeholder="0" oninput="calculateRow(${rowIndex})"
                        class="w-full pl-7 pr-2 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-[#14532d]">
                </div>
            </td>
            <td class="p-2.5 text-right font-black text-slate-800 text-xs" data-label="Subtotal">
                <span id="subtotal-${rowIndex}">Rp 0</span>
                <input type="hidden" id="raw-subtotal-${rowIndex}" value="0">
            </td>
            <td class="p-2.5 text-center" data-label="">
                <button type="button" onclick="removeRow(${rowIndex})" class="text-rose-500 hover:text-rose-700 text-xs p-1">Hapus</button>
            </td>
        `;

        tbody.appendChild(tr);
        rowIndex++;
    }

    function onKomoditasChange(idx) {
        const hint = document.getElementById('rowHint');
        if (hint) hint.classList.add('hidden');
        const row = document.getElementById('row-' + idx);
        if (!row) return;
        const select = row.querySelector('select[name="items[' + idx + '][komoditas_id]"]');
        if (!select) return;
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) return;
        const harga = parseFloat(opt.getAttribute('data-harga')) || 0;
        const satuan = opt.getAttribute('data-satuan') || 'kg';
        const hargaInput = document.getElementById('harga-' + idx);
        if (hargaInput && harga > 0) {
            hargaInput.value = harga;
        }
        const satuanEl = document.getElementById('satuan-' + idx);
        if (satuanEl) {
            satuanEl.innerText = satuan;
        }
        calculateRow(idx);
    }

    function calculateRow(idx) {
        const qty = parseDecimal(document.getElementById(`qty-${idx}`).value);
        const harga = parseRupiah(document.getElementById(`harga-${idx}`).value);
        const subtotal = qty * harga;

        document.getElementById(`subtotal-${idx}`).innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById(`raw-subtotal-${idx}`).value = subtotal;

        calculateAll();
    }

    function removeRow(idx) {
        const row = document.getElementById('row-' + idx);
        if (!row) return;
        // Minta konfirmasi hanya kalau barisnya sudah terisi, supaya tidak sengaja
        // menghapus komoditas yang sudah dipilih. Baris kosong langsung dihapus.
        const select = row.querySelector('select');
        const qty = document.getElementById('qty-' + idx);
        const harga = document.getElementById('harga-' + idx);
        const terisi = (select && select.value) || (qty && parseDecimal(qty.value) > 0) || (harga && parseRupiah(harga.value) > 0);
        if (terisi && !confirm('Hapus baris komoditas ini?')) return;
        row.remove();
        calculateAll();
    }

    function onPotonganInput() {
        potonganManual = true;
        calculateAll();
    }
    function resetPotonganAuto() {
        potonganManual = false;
        calculateAll();
    }

    function calculateAll() {
        let totalBruto = 0;
        document.querySelectorAll('[id^="raw-subtotal-"]').forEach(el => {
            totalBruto += parseFloat(el.value) || 0;
        });

        document.getElementById('labelTotalBruto').innerText = 'Rp ' + totalBruto.toLocaleString('id-ID');
        document.getElementById('hiddenTotalBruto').value = totalBruto;

        const potonganInputEl = document.getElementById('potonganInput');
        let potongan;
        if (!potonganManual) {
            // Otomatis: seluruh hasil panen ditahan untuk membayar kewajiban,
            // dibatasi sisa kewajiban. Petani baru menerima uang kalau total panen melebihi sisa.
            potongan = Math.min(totalBruto, currentPetaniDebt);
            if (potonganInputEl) potonganInputEl.value = potongan;
        } else {
            potongan = parseRupiah(potonganInputEl.value);
            if (potongan > totalBruto) {
                potongan = totalBruto;
            }
            if (currentPetaniDebt > 0 && potongan > currentPetaniDebt) {
                potongan = currentPetaniDebt;
            }
            if (potonganInputEl) potonganInputEl.value = potongan;
        }

        const uangDiterima = Math.max(0, totalBruto - potongan);
        const sisaKewajibanBaru = Math.max(0, currentPetaniDebt - potongan);

        document.getElementById('labelUangDiterima').innerText = 'Rp ' + uangDiterima.toLocaleString('id-ID');
        document.getElementById('labelSisaSetelahnya').innerText = 'Rp ' + sisaKewajibanBaru.toLocaleString('id-ID');
    }

    // Normalisasi sebelum kirim ke server, supaya 25.000 terkirim sebagai 25000 bukan 25
    const transaksiForm = document.getElementById('transaksiForm');
    if (transaksiForm) {
        transaksiForm.addEventListener('submit', function () {
            document.querySelectorAll('[id^="harga-"]').forEach(function (el) { el.value = parseRupiah(el.value); });
            document.querySelectorAll('[id^="qty-"]').forEach(function (el) { el.value = parseDecimal(el.value); });
            const pot = document.getElementById('potonganInput');
            if (pot) { pot.value = parseRupiah(pot.value); }
        });
    }

    // Initialize with 1 row on load
    addRow();
    updatePetaniInfo();
</script>
@endsection
