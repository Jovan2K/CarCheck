<x-layout :title="'Profil & Riwayat Inspeksi — ' . $car->fullName()">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('cars.index') }}" class="hover:text-red-600 font-semibold">Data Mobil</a>
                <span>/</span>
                <span>{{ $car->license_plate }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $car->fullName() }}</h1>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('inspections.create', ['car_id' => $car->id]) }}"
               class="px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-bold shadow-xs transition">
                + Mulai Sesi Inspeksi Baru
            </a>
            <a href="{{ route('cars.edit', $car) }}"
               class="px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-bold transition">
                Edit Profil Mobil
            </a>
            <form action="{{ route('cars.destroy', $car) }}" method="POST" onsubmit="return confirm('Hapus kendaraan ini beserta seluruh riwayat inspeksinya?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-xs sm:text-sm font-bold transition cursor-pointer">
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Vehicle Identity Matrix -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-2xs mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
            <!-- Photo / Visual Placeholder -->
            <div class="rounded-2xl bg-slate-900 text-white p-6 flex flex-col justify-between min-h-52 relative overflow-hidden">
                @if ($car->photo_url)
                    <img src="{{ asset('storage/' . $car->photo_url) }}" alt="{{ $car->fullName() }}" class="absolute inset-0 w-full h-full object-cover opacity-55">
                @endif
                <div class="relative z-10 flex items-center justify-between">
                    <span class="px-3 py-1 rounded-lg bg-white text-slate-900 font-mono font-extrabold text-sm shadow">
                        {{ $car->license_plate }}
                    </span>
                    <span class="px-2.5 py-1 rounded-md bg-red-600 text-white text-xs font-bold">
                        {{ $car->year }}
                    </span>
                </div>
                <div class="relative z-10 mt-8">
                    <p class="text-xs uppercase tracking-widest text-slate-300 font-bold">{{ $car->brand }}</p>
                    <p class="text-xl font-extrabold">{{ $car->model }}</p>
                    <p class="mt-2 text-lg font-extrabold text-emerald-400">
                        Rp {{ number_format((float) $car->listing_price, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Technical Specs Column 1 -->
            <div class="space-y-2.5 text-sm border-t lg:border-t-0 lg:border-l border-slate-200 pt-4 lg:pt-0 lg:pl-6">
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Transmisi</span>
                    <span class="font-bold text-slate-900">{{ $car->transmission }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Jarak Tempuh (Odometer)</span>
                    <span class="font-bold text-slate-900">{{ number_format($car->mileage, 0, ',', '.') }} km</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Tipe Body / Isi Silinder</span>
                    <span class="font-semibold text-slate-800">{{ $car->body_type ?? '-' }} / {{ $car->engine_capacity ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Warna & Bahan Bakar</span>
                    <span class="font-semibold text-slate-800">{{ $car->color ?? '-' }} ({{ $car->fuel_type ?? 'Bensin' }})</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Didaftarkan Oleh</span>
                    <span class="font-semibold text-slate-800">{{ $car->user?->name ?? '-' }}</span>
                </div>
            </div>

            <!-- Technical Specs Column 2 -->
            <div class="space-y-2.5 text-sm border-t lg:border-t-0 lg:border-l border-slate-200 pt-4 lg:pt-0 lg:pl-6">
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">No. Rangka (VIN)</span>
                    <span class="font-mono font-bold text-slate-800 text-xs">{{ $car->vin_number ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">No. Mesin</span>
                    <span class="font-mono font-bold text-slate-800 text-xs">{{ $car->engine_number ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Kepemilikan</span>
                    <span class="font-semibold text-slate-800">{{ $car->ownership_type ?? 'Pribadi' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Masa Berlaku Pajak</span>
                    <span class="font-semibold text-slate-800">{{ $car->tax_expiry_date?->format('d M Y') ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Kelengkapan Dokumen</span>
                    <span class="font-semibold text-emerald-700 text-xs">{{ $car->document_status ?? 'Lengkap' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Inspection Sessions History for this Vehicle (Cardinality 1:N) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Riwayat Sesi Inspeksi Kendaraan Ini ({{ $car->inspections->count() }})</h2>
                <p class="text-xs text-slate-500">Relasi 1:N — Satu mobil dapat memiliki satu atau lebih riwayat inspeksi berkala</p>
            </div>
            @if ($car->latestInspection)
                <a href="{{ route('cars.report', $car->slug) }}" class="text-xs font-bold text-red-600 hover:underline">
                    Buka URI Dinamis: /cars/{{ $car->slug }}/report &rarr;
                </a>
            @endif
        </div>

        <div class="divide-y divide-slate-200">
            @forelse ($car->inspections as $inspection)
                <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4 hover:bg-slate-50 transition">
                    <div class="flex items-center gap-4">
                        <x-grade-circle :grade="$inspection->overall_grade" size="md" />
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <a href="{{ route('inspections.show', $inspection) }}" class="font-mono font-extrabold text-red-600 hover:underline">
                                    Laporan #{{ $inspection->reference_number }}
                                </a>
                                <x-risk-badge :level="$inspection->overall_risk_level" size="sm" />
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Tanggal: <strong>{{ $inspection->inspection_date->format('d F Y') }}</strong> &bull;
                                Pemeriksa: <strong>{{ $inspection->inspector?->name ?? '-' }}</strong> &bull;
                                {{ $inspection->items->count() }} titik komponen diperiksa
                            </p>
                            @if ($inspection->general_notes)
                                <p class="text-xs text-slate-600 mt-1.5 line-clamp-2 max-w-2xl">{{ $inspection->general_notes }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 md:justify-end">
                        <div class="text-left md:text-right">
                            <span class="block text-[10px] uppercase font-bold text-slate-400">Total Estimasi Perbaikan</span>
                            <span class="text-base font-extrabold text-red-600">Rp {{ number_format((float) $inspection->total_repair_cost, 0, ',', '.') }}</span>
                            <span class="block text-[11px] text-slate-500">Rasio: {{ $inspection->repairRatioPercentage() }}% dari harga jual</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('inspections.show', $inspection) }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-red-600 text-white text-xs font-bold transition">
                                Buka Laporan
                            </a>
                            @if (auth()->check() && auth()->user()->role === 'Inspektur Independen')
                                <a href="{{ route('inspections.edit', $inspection) }}" class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition">
                                    Revisi
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-slate-500">
                    Belum ada riwayat inspeksi untuk mobil ini.
                    @if (auth()->check() && auth()->user()->role === 'Inspektur Independen')
                        <a href="{{ route('inspections.create', ['car_id' => $car->id]) }}" class="text-red-600 font-bold underline ml-1">Mulai Inspeksi Pertama</a>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
</x-layout>
