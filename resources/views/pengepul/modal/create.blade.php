@extends('layouts.app')

@section('title', 'Pemberian Modal Petani')
@section('page_title', 'Catat Pemberian Modal Tani')
@section('page_subtitle', 'Mencatat pemberian bahan atau uang kebutuhan budidaya yang menambah kewajiban petani')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('pengepul.modal.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Bukti Modal *</label>
                    <input type="text" name="kode_modal" value="{{ old('kode_modal', $kodeOtomatis) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 bg-slate-50 font-mono font-bold">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Pemberian *</label>
                    <input type="date" name="tanggal_pemberian" value="{{ old('tanggal_pemberian', date('Y-m-d')) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Petani Penerima Modal *</label>
                <select name="petani_id" id="petaniSelect" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Pilih Petani Binaan --</option>
                    @foreach($petanis as $p)
                        <option value="{{ $p->id }}" {{ (old('petani_id', $selectedPetaniId) == $p->id) ? 'selected' : '' }}>
                            {{ $p->nama_petani }} - Sisa Kewajiban: Rp {{ number_format($p->sisa_kewajiban, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Bentuk Modal *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center space-x-3 p-3.5 rounded-2xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-checked:border-emerald-500 has-checked:bg-emerald-50/50">
                        <input type="radio" name="jenis_modal" value="barang" checked class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="font-bold text-xs text-slate-800 block">📦 Bahan / Barang</span>
                            <span class="text-[11px] text-slate-500">Pupuk, racun, obat, bibit sayur</span>
                        </div>
                    </label>

                    <label class="flex items-center space-x-3 p-3.5 rounded-2xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-checked:border-emerald-500 has-checked:bg-emerald-50/50">
                        <input type="radio" name="jenis_modal" value="uang" class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="font-bold text-xs text-slate-800 block">💵 Uang Tunai</span>
                            <span class="text-[11px] text-slate-500">Olah lahan, sewa traktor, upah kerja</span>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Kebutuhan Budidaya *</label>
                <input type="text" name="kategori_kebutuhan" id="kategoriInput" value="{{ old('kategori_kebutuhan', 'Pupuk & Nutrisi') }}" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                <div class="flex flex-wrap gap-1.5 mt-2">
                    <button type="button" onclick="setKategori('Pupuk & Nutrisi')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-[11px] text-slate-600 font-medium">Pupuk & Nutrisi</button>
                    <button type="button" onclick="setKategori('Obat & Pestisida')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-[11px] text-slate-600 font-medium">Obat & Pestisida</button>
                    <button type="button" onclick="setKategori('Bibit / Benih')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-[11px] text-slate-600 font-medium">Bibit / Benih</button>
                    <button type="button" onclick="setKategori('Pengolahan Lahan')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-[11px] text-slate-600 font-medium">Pengolahan Lahan</button>
                    <button type="button" onclick="setKategori('Upah Tenaga Kerja')" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-100 text-[11px] text-slate-600 font-medium">Upah Tenaga Kerja</button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Rincian Barang / Keperluan</label>
                <input type="text" name="rincian_barang" value="{{ old('rincian_barang') }}" placeholder="Contoh: Pupuk NPK Mutiara 50kg (1 sak)"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nominal Modal (Rp) *</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 font-bold text-sm">
                        Rp
                    </div>
                    <input type="text" name="nominal_display" id="nominalDisplay" value="{{ old('nominal_display') }}" required placeholder="0" oninput="formatRupiah(this)" class="w-full pl-11 pr-4 py-2.5 rounded-xl border border-slate-200 text-base font-extrabold text-slate-900 focus:ring-2 focus:ring-emerald-500" />
                    <input type="hidden" name="nominal" id="nominal" value="{{ old('nominal') }}" />
                </div>
                <span class="text-[11px] text-slate-400 mt-1 block">Nominal ini akan otomatis menambah total kewajiban petani kepada pengepul.</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                <textarea name="catatan" rows="2" placeholder="Catatan kesepakatan"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">{{ old('catatan') }}</textarea>
            </div>

            <!-- WhatsApp Notify Checkbox -->
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 flex items-start space-x-3">
                <input type="checkbox" name="kirim_wa" value="1" id="kirimWa" checked class="mt-1 rounded text-emerald-600 focus:ring-emerald-500">
                <label for="kirimWa" class="text-xs text-emerald-900 cursor-pointer">
                    <strong class="block font-bold">Kirim Notifikasi WhatsApp Otomatis ke Petani</strong>
                    <span class="text-emerald-700">Rincian bukti modal dan saldo kewajiban akan otomatis dikirimkan ke nomor WhatsApp petani via Fonnte Gateway.</span>
                </label>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('pengepul.modal.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                    Simpan Modal Tani
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function setKategori(nama) {
        document.getElementById('kategoriInput').value = nama;
    }
</script>
<script>
function formatRupiah(elem) {
    // Hapus semua karakter selain angka
    let clean = elem.value.replace(/[^0-9]/g, '');
    if (clean === '') {
        document.getElementById('nominal').value = '';
        return;
    }
    const number = parseInt(clean, 10);
    // Set nilai numerik ke hidden input
    document.getElementById('nominal').value = number;
    // Tampilkan format rupiah
    elem.value = number.toLocaleString('id-ID');
}
</script>
@endsection
