<x-layout :title="'Edit Kendaraan — ' . $car->fullName()">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-red-600">Pembaruan Data Kendaraan</span>
                <h1 class="text-2xl font-extrabold text-slate-900">Edit Profil: {{ $car->fullName() }}</h1>
            </div>
            <a href="{{ route('cars.show', $car) }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">&larr; Kembali ke Detail Mobil</a>
        </div>

        <form action="{{ route('cars.update', $car) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-200 pb-2 mb-4">
                    A. Parameter Utama Kendaraan
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="brand" class="block text-xs font-bold text-slate-700 mb-1">Merek Kendaraan *</label>
                        <input type="text" id="brand" name="brand" value="{{ old('brand', $car->brand) }}" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="model" class="block text-xs font-bold text-slate-700 mb-1">Model / Tipe Varian *</label>
                        <input type="text" id="model" name="model" value="{{ old('model', $car->model) }}" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 outline-none">
                    </div>

                    <div>
                        <label for="year" class="block text-xs font-bold text-slate-700 mb-1">Tahun Pembuatan *</label>
                        <input type="number" id="year" name="year" value="{{ old('year', $car->year) }}" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 outline-none">
                    </div>

                    <div>
                        <label for="license_plate" class="block text-xs font-bold text-slate-700 mb-1">Nomor Polisi (Plat) *</label>
                        <input type="text" id="license_plate" name="license_plate" value="{{ old('license_plate', $car->license_plate) }}" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-mono uppercase focus:border-red-600 outline-none">
                    </div>

                    <div>
                        <label for="transmission" class="block text-xs font-bold text-slate-700 mb-1">Transmisi *</label>
                        <select id="transmission" name="transmission" required
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 outline-none">
                            @foreach (['Automatic', 'CVT', 'Manual', 'DCT'] as $trans)
                                <option value="{{ $trans }}" @selected(old('transmission', $car->transmission) === $trans)>{{ $trans }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="mileage" class="block text-xs font-bold text-slate-700 mb-1">Jarak Tempuh / Odometer (km) *</label>
                        <input type="number" id="mileage" name="mileage" value="{{ old('mileage', $car->mileage) }}" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="listing_price" class="block text-xs font-bold text-slate-700 mb-1">Harga Taksiran / Penawaran Awal (Rp) *</label>
                        <input type="number" id="listing_price" name="listing_price" value="{{ old('listing_price', (float) $car->listing_price) }}" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-bold text-slate-900 focus:border-red-600 outline-none">
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-200 pb-2 mb-4">
                    B. Detail Identitas & Dokumen Kendaraan
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="color" class="block text-xs font-bold text-slate-700 mb-1">Warna Eksterior / Interior</label>
                        <input type="text" id="color" name="color" value="{{ old('color', $car->color) }}"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                    </div>
                    <div>
                        <label for="body_type" class="block text-xs font-bold text-slate-700 mb-1">Tipe Body</label>
                        <input type="text" id="body_type" name="body_type" value="{{ old('body_type', $car->body_type) }}"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                    </div>
                    <div>
                        <label for="fuel_type" class="block text-xs font-bold text-slate-700 mb-1">Bahan Bakar</label>
                        <input type="text" id="fuel_type" name="fuel_type" value="{{ old('fuel_type', $car->fuel_type) }}"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                    </div>
                    <div>
                        <label for="engine_capacity" class="block text-xs font-bold text-slate-700 mb-1">Isi Silinder (CC)</label>
                        <input type="text" id="engine_capacity" name="engine_capacity" value="{{ old('engine_capacity', $car->engine_capacity) }}"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                    </div>
                    <div>
                        <label for="vin_number" class="block text-xs font-bold text-slate-700 mb-1">Nomor Rangka (VIN)</label>
                        <input type="text" id="vin_number" name="vin_number" value="{{ old('vin_number', $car->vin_number) }}"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-mono uppercase focus:border-red-600 outline-none">
                    </div>
                    <div>
                        <label for="engine_number" class="block text-xs font-bold text-slate-700 mb-1">Nomor Mesin</label>
                        <input type="text" id="engine_number" name="engine_number" value="{{ old('engine_number', $car->engine_number) }}"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm font-mono uppercase focus:border-red-600 outline-none">
                    </div>
                    <div>
                        <label for="ownership_type" class="block text-xs font-bold text-slate-700 mb-1">Status Kepemilikan</label>
                        <input type="text" id="ownership_type" name="ownership_type" value="{{ old('ownership_type', $car->ownership_type) }}"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                    </div>
                    <div>
                        <label for="tax_expiry_date" class="block text-xs font-bold text-slate-700 mb-1">Pajak Berlaku Sampai</label>
                        <input type="date" id="tax_expiry_date" name="tax_expiry_date" value="{{ old('tax_expiry_date', $car->tax_expiry_date?->format('Y-m-d')) }}"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                    </div>
                    <div>
                        <label for="document_status" class="block text-xs font-bold text-slate-700 mb-1">Status Dokumen</label>
                        <input type="text" id="document_status" name="document_status" value="{{ old('document_status', $car->document_status) }}"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="photo" class="block text-xs font-bold text-slate-700 mb-1">Perbarui Foto Utama Kendaraan (Opsional)</label>
                        <input type="file" id="photo" name="photo" accept="image/*"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs text-slate-600">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('cars.show', $car) }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-bold shadow-xs transition cursor-pointer">
                    Simpan Perubahan & Hitung Ulang Risiko
                </button>
            </div>
        </form>
    </div>
</x-layout>
