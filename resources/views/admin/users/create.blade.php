@extends('layouts.app')

@section('title', 'Tambah Akun Pengguna')
@section('page_title', 'Tambah Akun Baru')
@section('page_subtitle', 'Buat akun Admin, Pengepul, atau Petani baru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
            <h3 class="font-bold text-slate-800">Formulir Akun Baru</h3>
            <p class="text-xs text-slate-500 mt-0.5">Isi data akun pengguna yang ingin ditambahkan ke sistem.</p>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <!-- Role Selection -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Role / Peran <span class="text-rose-500">*</span></label>
                <select name="role" id="roleSelect" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                    <option value="">-- Pilih Role --</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Administrator)</option>
                    <option value="pengepul" {{ old('role') === 'pengepul' ? 'selected' : '' }}>Pengepul</option>
                    <option value="petani" {{ old('role') === 'petani' ? 'selected' : '' }}>Petani Mitra</option>
                </select>
                @error('role') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Nama Lengkap -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    placeholder="Contoh: Bapak Ahmad"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                @error('name') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Username -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Username <span class="text-rose-500">*</span></label>
                <input type="text" name="username" value="{{ old('username') }}" required
                    placeholder="Contoh: ahmad123"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                @error('username') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="email@domain.com"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                    @error('email') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        placeholder="08xxxxxxxxxx"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                    @error('phone') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Password -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Password <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required
                        placeholder="Minimal 6 karakter"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                    @error('password') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Konfirmasi Password <span class="text-rose-500">*</span></label>
                    <input type="password" name="password_confirmation" required
                        placeholder="Ulangi password"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                </div>
            </div>

            <!-- Petani-specific fields (shown only when role=petani) -->
            <div id="petaniFields" class="hidden space-y-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Data Profil Petani</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kode Petani <span class="text-rose-500">*</span></label>
                        <input type="text" name="kode_petani" value="{{ old('kode_petani') }}"
                            placeholder="Contoh: PTN-004"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent">
                        @error('kode_petani') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">NIK</label>
                        <input type="text" name="nik" value="{{ old('nik') }}"
                            placeholder="Nomor Induk Kependudukan"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Alamat</label>
                    <textarea name="alamat" rows="2" placeholder="Alamat lengkap petani"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent">{{ old('alamat') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Luas Lahan</label>
                        <input type="text" name="luas_lahan" value="{{ old('luas_lahan') }}"
                            placeholder="Contoh: 0.5 Hektar"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Komoditas Utama</label>
                        <input type="text" name="komoditas_utama" value="{{ old('komoditas_utama') }}"
                            placeholder="Contoh: Cabai Rawit & Tomat"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">
                    ← Kembali
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow-md transition">
                    Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const roleSelect = document.getElementById('roleSelect');
    const petaniFields = document.getElementById('petaniFields');

    function togglePetaniFields() {
        if (roleSelect.value === 'petani') {
            petaniFields.classList.remove('hidden');
        } else {
            petaniFields.classList.add('hidden');
        }
    }

    roleSelect.addEventListener('change', togglePetaniFields);
    // Run on load in case of old() value
    togglePetaniFields();
</script>
@endpush
@endsection
