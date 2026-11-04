<x-layout title="Tambah Profil Kendaraan Baru">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-red-600">Langkah 2 Alur Sistem CarCheck</span>
                <h1 class="text-2xl font-extrabold text-slate-900">Pendaftaran Profil Kendaraan Baru</h1>
                <p class="text-xs text-slate-500 mt-0.5">Masukkan parameter identitas kendaraan, spesifikasi teknis, dan harga penawaran awal</p>
            </div>
            <a href="{{ route('cars.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">&larr; Kembali ke Daftar Mobil</a>
        </div>

        <form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Primary Required Vehicle Fields (ERD Cars) -->
            <div>
                <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-200 pb-2 mb-4">
                    A. Parameter Utama Kendaraan (Wajib)
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="brand" class="block text-xs font-bold text-slate-700 mb-1">Merek Kendaraan <span class="text-red-600">*</span></label>
                        <input type="text" id="brand" name="brand" value="{{ old('brand') }}" placeholder="Contoh: Toyota, Nissan, Honda" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="model" class="block text-xs font-bold text-slate-700 mb-1">Model / Tipe Varian <span class="text-red-600">*</span></label>
                        <input type="text" id="model" name="model" value="{{ old('model') }}" placeholder="Contoh: Avanza Veloz 1.5 Q / X-Trail 2.5 T32" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 outline-none">
                    </div>

                    <div>
                        <label for="year" class="block text-xs font-bold text-slate-700 mb-1">Tahun Pembuatan <span class="text-red-600">*</span></label>
                        <input type="number" id="year" name="year" value="{{ old('year', 2021) }}" min="1980" max="{{ date('Y') + 1 }}" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 outline-none">
                    </div>

                    <div>
                        <label for="license_plate" class="block text-xs font-bold text-slate-700 mb-1">Nomor Polisi (Plat) <span class="text-red-600">*</span></label>
                        <input type="text" id="license_plate" name="license_plate" value="{{ old('license_plate') }}" placeholder="B 1234 XYZ" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-mono uppercase focus:border-red-600 outline-none">
                    </div>

                    <div>
                        <label for="transmission" class="block text-xs font-bold text-slate-700 mb-1">Transmisi <span class="text-red-600">*</span></label>
                        <select id="transmission" name="transmission" required
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 outline-none">
                            <option value="Automatic" @selected(old('transmission') === 'Automatic')>Automatic (AT)</option>
                            <option value="CVT" @selected(old('transmission') === 'CVT')>CVT</option>
                            <option value="Manual" @selected(old('transmission') === 'Manual')>Manual (MT)</option>
                            <option value="DCT" @selected(old('transmission') === 'DCT')>Dual Clutch (DCT)</option>
                        </select>
                    </div>

                    <div>
                        <label for="mileage" class="block text-xs font-bold text-slate-700 mb-1">Jarak Tempuh / Odometer (km) <span class="text-red-600">*</span></label>
                        <input type="number" id="mileage" name="mileage" value="{{ old('mileage', 45000) }}" min="0" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="listing_price" class="block text-xs font-bold text-slate-700 mb-1">Harga Taksiran / Penawaran Awal (Rp) <span class="text-red-600">*</span></label>
                        <input type="number" id="listing_price" name="listing_price" value="{{ old('listing_price', 250000000) }}" min="1000000" step="500000" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-bold text-slate-900 focus:border-red-600 outline-none">
                        <p class="text-[11px] text-slate-500 mt-1">Digunakan sebagai acuan kalkulasi persentase rasio biaya perbaikan pada Algoritma Risk Index.</p>
                    </div>
                </div>
            </div>

            <!-- Extended Otospector Identity & Document Attributes -->
            <div>
                <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-200 pb-2 mb-4">
                    B. Detail Identitas & Dokumen Kendaraan (Standar Laporan Otospector)
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="color" class="block text-xs font-bold text-slate-700 mb-1">Warna Eksterior / Interior</label>
                        <input type="text" id="color" name="color" value="{{ old('color', 'Putih/Hitam') }}" placeholder="Putih/Hitam"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                    </div>

                    <div>
                        <label for="body_type" class="block text-xs font-bold text-slate-700 mb-1">Tipe Body</label>
                        <input type="text" id="body_type" name="body_type" value="{{ old('body_type', 'SUV / MPV') }}" placeholder="SUV / MPV / Sedan"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                    </div>

                    <div>
                        <label for="fuel_type" class="block text-xs font-bold text-slate-700 mb-1">Bahan Bakar</label>
                        <select id="fuel_type" name="fuel_type" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                            <option value="Bensin" @selected(old('fuel_type') === 'Bensin')>Bensin</option>
                            <option value="Diesel" @selected(old('fuel_type') === 'Diesel')>Diesel / Solar</option>
                            <option value="Hybrid" @selected(old('fuel_type') === 'Hybrid')>Hybrid (HEV)</option>
                            <option value="Listrik (BEV)" @selected(old('fuel_type') === 'Listrik (BEV)')>Listrik (BEV)</option>
                        </select>
                    </div>

                    <div>
                        <label for="engine_capacity" class="block text-xs font-bold text-slate-700 mb-1">Isi Silinder (CC)</label>
                        <input type="text" id="engine_capacity" name="engine_capacity" value="{{ old('engine_capacity', '1,500 cc') }}" placeholder="2,500 cc"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                    </div>

                    <div>
                        <label for="vin_number" class="block text-xs font-bold text-slate-700 mb-1">Nomor Rangka (VIN)</label>
                        <input type="text" id="vin_number" name="vin_number" value="{{ old('vin_number') }}" placeholder="MNTJBAT32L00XXXXX"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-mono uppercase focus:border-red-600 outline-none">
                    </div>

                    <div>
                        <label for="engine_number" class="block text-xs font-bold text-slate-700 mb-1">Nomor Mesin</label>
                        <input type="text" id="engine_number" name="engine_number" value="{{ old('engine_number') }}" placeholder="QR25XXXXXXM"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-mono uppercase focus:border-red-600 outline-none">
                    </div>

                    <div>
                        <label for="ownership_type" class="block text-xs font-bold text-slate-700 mb-1">Status Kepemilikan</label>
                        <input type="text" id="ownership_type" name="ownership_type" value="{{ old('ownership_type', 'Pribadi (Tangan Pertama)') }}"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                    </div>

                    <div>
                        <label for="tax_expiry_date" class="block text-xs font-bold text-slate-700 mb-1">Pajak Berlaku Sampai</label>
                        <input type="date" id="tax_expiry_date" name="tax_expiry_date" value="{{ old('tax_expiry_date', now()->addMonths(8)->toDateString()) }}"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                    </div>

                    <div>
                        <label for="document_status" class="block text-xs font-bold text-slate-700 mb-1">Status Dokumen</label>
                        <input type="text" id="document_status" name="document_status" value="{{ old('document_status', 'STNK, BPKB & Faktur Lengkap') }}"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                    </div>

                    <div class="sm:col-span-3">
                        <label for="photo" class="block text-xs font-bold text-slate-700 mb-1">Unggah Foto Utama Kendaraan (Opsional — JPG/PNG maks 4MB)</label>
                        <input type="file" id="photo" name="photo" accept="image/*"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-red-50 file:text-red-700 file:font-bold">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('cars.index') }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" name="start_inspection" value="0" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold transition cursor-pointer">
                    Simpan Profil Kendaraan
                </button>
                <button type="submit" name="start_inspection" value="1" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold shadow-xs transition cursor-pointer">
                    Simpan & Langsung Mulai Inspeksi &rarr;
                </button>
            </div>
        </form>
    </div>
</x-layout>
