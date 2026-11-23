@extends('layouts.app')

@section('title', 'Catat Transaksi Panen')
@section('page_title', 'Catat Pembelian Hasil Panen')
@section('page_subtitle', 'Timbang hasil panen petani, hitung potongan kewajiban, dan kirim rincian nota via WhatsApp')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('pengepul.transaksi.store') }}" method="POST" id="transaksiForm" class="space-y-6">
            @csrf

            <!-- Header Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-b border-slate-100 pb-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Transaksi *</label>
                    <input type="text" name="nomor_transaksi" value="{{ old('nomor_transaksi', $nomorOtomatis) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 bg-slate-50 font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Transaksi Panen *</label>
                    <input type="date" name="tanggal_transaksi" value="{{ old('tanggal_transaksi', date('Y-m-d')) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Petani Selection & Live Debt Status -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-50/80 border border-slate-200/60">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Petani Penjual Panen *</label>
                    <select name="petani_id" id="petaniSelect" required onchange="updatePetaniInfo()"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 bg-white">
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

                <div class="p-3 bg-white rounded-xl border border-slate-200 flex flex-col justify-center">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Sisa Utang Petani:</span>
                    <span id="displaySisaKewajiban" class="text-lg font-black text-rose-600">Rp 0</span>
                    <span id="displayWaPetani" class="text-[11px] text-slate-500 truncate">Pilih petani terlebih dahulu</span>
                </div>
            </div>

            <!-- Multi-Row Vegetable Harvest Items -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 class="font-bold text-sm text-slate-900">Rincian Komoditas Sayuran</h4>
                        <p class="text-xs text-slate-500">Pilih sayuran, masukkan timbangan kg dan harga beli saat transaksi.</p>
                    </div>
                    <button type="button" onclick="addRow()" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs transition">
                        <span>+ Tambah Baris Sayur</span>
                    </button>
                </div>

                <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                    <table class="w-full text-left text-sm" id="itemsTable">
                        <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
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

            <!-- Calculation and Debt Deduction Box -->
            <div class="p-5 rounded-2xl bg-emerald-950 text-white shadow-md space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-emerald-200">Total Nilai Penjualan Panen (Bruto):</span>
                    <span id="labelTotalBruto" class="font-extrabold text-xl text-white">Rp 0</span>
                    <input type="hidden" name="hidden_total_bruto" id="hiddenTotalBruto" value="0">
                </div>

                <div class="pt-3 border-t border-emerald-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <label for="potonganInput" class="text-xs font-bold text-emerald-200 flex items-center">
                            Potongan Kewajiban / Cicilan Modal:
                            <span class="ml-2 text-[10px] px-2 py-0.5 rounded bg-emerald-800/80 text-emerald-300">Disepakati</span>
                        </label>
                        <span class="text-[11px] text-emerald-300/80 block">Nominal untuk mengurangi kewajiban utang modal petani</span>
                    </div>
                    <div class="w-full sm:w-56">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-rose-300 font-bold text-xs">Rp</span>
                            <input type="number" name="potongan_kewajiban" id="potonganInput" value="{{ old('potongan_kewajiban', 0) }}" min="0" step="5000"
                                oninput="calculateAll()"
                                class="w-full pl-9 pr-3 py-2 rounded-xl bg-emerald-900 border border-emerald-700 text-white font-black text-right text-sm focus:ring-2 focus:ring-amber-400">
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-emerald-800/80 flex items-center justify-between text-base">
                    <div>
                        <span class="font-bold text-amber-300 text-sm block">UANG BERSIH DITERIMA PETANI:</span>
                        <span class="text-[11px] text-emerald-300">Total Panen dikurangi Potongan Modal</span>
                    </div>
                    <span id="labelUangDiterima" class="font-black text-2xl text-amber-400">Rp 0</span>
                </div>

                <div class="pt-2 border-t border-emerald-800/50 flex items-center justify-between text-xs text-emerald-200">
                    <span>Estimasi Sisa Kewajiban Petani Setelah Transaksi:</span>
                    <span id="labelSisaSetelahnya" class="font-extrabold text-rose-300 text-sm">Rp 0</span>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan Transaksi</label>
                <textarea name="catatan" rows="2" placeholder="Contoh: Kondisi cabe petikan pagi, kualitas grade A mulus"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">{{ old('catatan') }}</textarea>
            </div>

            <!-- WhatsApp Notification Option -->
            <div class="p-4 rounded-2xl bg-green-50 border border-green-200 flex items-start space-x-3">
                <input type="checkbox" name="kirim_wa" value="1" id="kirimWa" checked class="mt-1 rounded text-green-600 focus:ring-green-500">
                <label for="kirimWa" class="text-xs text-green-900 cursor-pointer">
                    <strong class="block font-bold">Kirim Nota Otomatis via WhatsApp Gateway (Fonnte API)</strong>
                    <span class="text-green-700">Petani akan langsung menerima rincian sayur, berat timbangan, potongan modal, dan sisa kewajiban via pesan WhatsApp resmi.</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('pengepul.transaksi.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 text-xs font-extrabold shadow-md transition">
                    Simpan Transaksi & Terbitkan Nota Panen
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
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.id = 'row-' + rowIndex;
        tr.className = 'hover:bg-slate-50/50';

        let optionsHtml = '<option value="">-- Pilih Sayuran --</option>';
        komoditasData.forEach(k => {
            optionsHtml += `<option value="${k.id}" data-harga="${k.harga_pasar_default}" data-satuan="${k.satuan}">${k.nama_komoditas} (${k.kategori})</option>`;
        });

        tr.innerHTML = `
            <td class="p-2.5">
                <select name="items[${rowIndex}][komoditas_id]" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs focus:ring-1 focus:ring-emerald-500" required onchange="onKomoditasChange(${rowIndex})">
                    ${optionsHtml}
                </select>
                <input type="text" name="items[${rowIndex}][keterangan]" placeholder="Keterangan mutu (opsional)" class="w-full mt-1 px-2 py-1 rounded border border-slate-100 text-[11px] text-slate-500">
            </td>
            <td class="p-2.5">
                <div class="relative">
                    <input type="number" step="0.1" min="0.1" name="items[${rowIndex}][jumlah_panen]" id="qty-${rowIndex}" required placeholder="0" oninput="calculateRow(${rowIndex})"
                        class="w-full pr-7 pl-2 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-slate-800">
                    <span id="satuan-${rowIndex}" class="absolute right-2 top-2 text-[10px] text-slate-400">kg</span>
                </div>
            </td>
            <td class="p-2.5">
                <div class="relative">
                    <span class="absolute left-2 top-2 text-[10px] text-slate-400 font-bold">Rp</span>
                    <input type="number" step="500" min="100" name="items[${rowIndex}][harga_beli]" id="harga-${rowIndex}" required placeholder="0" oninput="calculateRow(${rowIndex})"
                        class="w-full pl-7 pr-2 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-emerald-800">
                </div>
            </td>
            <td class="p-2.5 text-right font-black text-slate-800 text-xs">
                <span id="subtotal-${rowIndex}">Rp 0</span>
                <input type="hidden" id="raw-subtotal-${rowIndex}" value="0">
            </td>
            <td class="p-2.5 text-center">
                <button type="button" onclick="removeRow(${rowIndex})" class="text-rose-500 hover:text-rose-700 text-xs p-1">✕</button>
            </td>
        `;

        tbody.appendChild(tr);
        rowIndex++;
    }

    

    function onKomoditasChange(idx) {
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
        if (row) {
            row.remove();
        }
        calculateAll();
    }

    function calculateAll() {
        let totalBruto = 0;
        document.querySelectorAll('[id^="raw-subtotal-"]').forEach(el => {
            totalBruto += parseFloat(el.value) || 0;
        });

        document.getElementById('labelTotalBruto').innerText = 'Rp ' + totalBruto.toLocaleString('id-ID');
        document.getElementById('hiddenTotalBruto').value = totalBruto;

        let potongan = parseRupiah(document.getElementById('potonganInput').value);
        
        // Batasi potongan tidak boleh melebihi total panen
        if (potongan > totalBruto) {
            potongan = totalBruto;
            document.getElementById('potonganInput').value = potongan;
        }

        // Batasi potongan tidak boleh melebihi sisa utang berjalan
        if (currentPetaniDebt > 0 && potongan > currentPetaniDebt) {
            potongan = currentPetaniDebt;
            document.getElementById('potonganInput').value = potongan;
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
