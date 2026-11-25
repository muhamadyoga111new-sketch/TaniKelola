@extends('layouts.app')

@section('title', 'Catat Pembayaran Tunai')
@section('page_title', 'Catat Pembayaran Tunai Kewajiban')
@section('page_subtitle', 'Pencatatan uang tunai langsung dari petani untuk mencicil atau melunasi sisa kewajiban modal')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('pengepul.pembayaran.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Kwitansi Pembayaran *</label>
                    <input type="text" name="kode_pembayaran" value="{{ old('kode_pembayaran', $kodeOtomatis) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 bg-slate-50 font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Pembayaran *</label>
                    <input type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Petani Pembayar *</label>
                <select name="petani_id" id="petaniSelect" required onchange="updateDebtInfo()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500">
                    <option value="">Pilih Petani yang Memiliki Kewajiban</option>
                    @foreach($petanis as $p)
                        <option value="{{ $p->id }}" data-debt="{{ $p->sisa_kewajiban }}" {{ (old('petani_id', $selectedPetaniId) == $p->id) ? 'selected' : '' }}>
                            {{ $p->nama_petani }} - Sisa Utang: Rp {{ number_format($p->sisa_kewajiban, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
                <div class="mt-2 p-3 rounded-xl bg-teal-50/60 border border-teal-100 flex items-center justify-between text-xs">
                    <span class="text-teal-800 font-medium">Sisa Utang Petani Saat Ini:</span>
                    <span id="displayDebt" class="font-extrabold text-teal-900 text-sm">Rp 0</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Uang Pembayaran (Rp) *</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 font-bold text-sm">Rp</span>
                    <input type="text" name="jumlah_bayar_display" id="bayarDisplay" value="{{ old('jumlah_bayar_display') }}" required placeholder="0" oninput="formatRupiahBayar(this)" class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 text-base font-extrabold text-slate-900 focus:ring-2 focus:ring-teal-500" />
                    <input type="hidden" name="jumlah_bayar" id="bayarInput" value="{{ old('jumlah_bayar') }}" />
                </div>
                <div class="flex justify-between items-center text-[11px] text-slate-500 mt-1">
                    <span>Estimasi sisa kewajiban setelah bayar:</span>
                    <strong id="displayNewDebt" class="text-rose-600 font-bold">Rp 0</strong>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Metode Pembayaran *</label>
                <input type="text" name="metode_bayar" value="{{ old('metode_bayar', 'Tunai / Cash Langsung') }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                <textarea name="catatan" rows="2" placeholder="Contoh: Pembayaran dititipkan di rumah pengepul"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500">{{ old('catatan') }}</textarea>
            </div>

            <!-- WhatsApp Notify -->
            <div class="p-4 rounded-2xl bg-teal-50 border border-teal-200 flex items-start space-x-3">
                <input type="checkbox" name="kirim_wa" value="1" id="kirimWa" checked class="mt-1 rounded text-teal-600 focus:ring-teal-500">
                <label for="kirimWa" class="text-xs text-teal-900 cursor-pointer">
                    <strong class="block font-bold">Kirim Kwitansi WhatsApp Otomatis ke Petani</strong>
                    <span class="text-teal-700">Petani akan menerima konfirmasi pelunasan/cicilan beserta sisa saldo kewajiban terkini via WhatsApp.</span>
                </label>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('pengepul.pembayaran.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold shadow-md shadow-teal-600/20 transition">
                    Simpan Pembayaran Tunai
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentDebt = 0;
    function updateDebtInfo() {
        const select = document.getElementById('petaniSelect');
        const opt = select.options[select.selectedIndex];
        if (opt && opt.value) {
            currentDebt = parseFloat(opt.getAttribute('data-debt')) || 0;
            document.getElementById('displayDebt').innerText = 'Rp ' + currentDebt.toLocaleString('id-ID');
        } else {
            currentDebt = 0;
            document.getElementById('displayDebt').innerText = 'Rp 0';
        }
        calcRemaining();
    }

    function calcRemaining() {
        const bayar = parseFloat(document.getElementById('bayarInput').value) || 0;
        const newDebt = Math.max(0, currentDebt - bayar);
        document.getElementById('displayNewDebt').innerText = 'Rp ' + newDebt.toLocaleString('id-ID');
    }

    window.addEventListener('DOMContentLoaded', updateDebtInfo);
</script>
<script>
function formatRupiahBayar(elem) {
    let clean = elem.value.replace(/[^0-9]/g, '');
    if (clean === '') {
        document.getElementById('bayarInput').value = '';
        return;
    }
    const number = parseInt(clean, 10);
    document.getElementById('bayarInput').value = number;
    elem.value = number.toLocaleString('id-ID');
    calcRemaining();
}
</script>
@endsection
