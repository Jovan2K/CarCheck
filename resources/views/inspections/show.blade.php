<x-layout :title="'Laporan Kondisi Kendaraan No. ' . $inspection->reference_number">
    @php $car = $inspection->car; @endphp

    <!-- Top Action Toolbar -->
    <div class="mb-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print">
        <div>
            <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-red-600 font-semibold">Dashboard</a>
                <span>/</span>
                <a href="{{ route('inspections.index') }}" class="hover:text-red-600 font-semibold">Laporan Inspeksi</a>
                <span>/</span>
                <span class="font-mono text-slate-700 font-bold">#{{ $inspection->reference_number }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900">
                Laporan Evaluasi & Kelayakan Mobil Bekas
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('inspections.print', $inspection) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-extrabold shadow-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak / Ekspor PDF Resmi</span>
            </a>

            <a href="{{ route('inspections.edit', $inspection) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 text-xs sm:text-sm font-bold transition">
                <span>Revisi Data Temuan</span>
            </a>

            <a href="{{ route('compare.index', ['ids' => [$inspection->id]]) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold transition">
                <span>Bandingkan Unit</span>
            </a>
        </div>
    </div>

    <!-- OFFICIAL OTOSPECTOR + CARCHECK REPORT SHEET -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden mb-8">
        <!-- Brand Header Row -->
        <div class="px-6 sm:px-8 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-red-600 text-white flex items-center justify-center shadow-md shadow-red-600/20">
                    <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-black tracking-tight italic text-slate-900">
                        CAR<span class="text-red-600">CHECK</span> <span class="not-italic text-xs font-bold uppercase tracking-widest text-slate-400 ml-1">INSPECTION</span>
                    </div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">
                        INDONESIA'S PRECISION CAR INSPECTION & RISK EVALUATION
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <x-risk-badge :level="$inspection->overall_risk_level" size="lg" />
            </div>
        </div>

        <!-- Crimson Report Banner (Exact Otospector Style) -->
        <div class="bg-red-800 text-white px-6 sm:px-8 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-sm font-bold">
            <span>Laporan Kondisi Kendaraan No. {{ $inspection->reference_number }}</span>
            <span>Tanggal inspeksi: {{ $inspection->inspection_date->format('d / m / Y') }}</span>
        </div>

        <!-- Vehicle Identity Grey Table (2 Columns like Otospector Page 1) -->
        <div class="bg-slate-100/90 px-6 sm:px-8 py-6 border-b border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-2 text-xs sm:text-sm">
                <div class="space-y-2">
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">No. Polisi</span>
                        <span class="col-span-7 font-bold text-slate-900">: {{ $car?->license_plate ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">Merk</span>
                        <span class="col-span-7 font-bold text-slate-900">: {{ $car?->brand ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">Model/Tipe</span>
                        <span class="col-span-7 font-bold text-slate-900">: {{ $car?->model ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">Transmisi</span>
                        <span class="col-span-7 font-semibold text-slate-900">: {{ $car?->transmission ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">Tipe body</span>
                        <span class="col-span-7 font-semibold text-slate-900">: {{ $car?->body_type ?? 'MPV / SUV' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">Tahun</span>
                        <span class="col-span-7 font-semibold text-slate-900">: {{ $car?->year ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">Isi Silinder</span>
                        <span class="col-span-7 font-semibold text-slate-900">: {{ $car?->engine_capacity ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">No. Rangka</span>
                        <span class="col-span-7 font-mono font-semibold text-slate-800">: {{ $car?->vin_number ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">No. Mesin</span>
                        <span class="col-span-7 font-mono font-semibold text-slate-800">: {{ $car?->engine_number ?? '-' }}</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">Warna Eksterior/Interior</span>
                        <span class="col-span-7 font-semibold text-slate-900">: {{ $car?->color ?? 'Putih/Hitam' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">Bahan bakar</span>
                        <span class="col-span-7 font-semibold text-slate-900">: {{ $car?->fuel_type ?? 'Bensin' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">Odometer</span>
                        <span class="col-span-7 font-bold text-slate-900">: {{ number_format((int) ($car?->mileage ?? 0), 0, ',', '.') }} km</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">Pajak Berlaku Sampai</span>
                        <span class="col-span-7 font-semibold text-slate-900">: {{ $car?->tax_expiry_date?->format('d/m/Y') ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">Kepemilikan</span>
                        <span class="col-span-7 font-semibold text-slate-900">: {{ $car?->ownership_type ?? 'Pribadi' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">Kelengkapan Dokumen</span>
                        <span class="col-span-7 font-semibold text-emerald-700">: {{ $car?->document_status ?? 'Lengkap' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">Harga Taksiran Awal</span>
                        <span class="col-span-7 font-extrabold text-slate-900">: Rp {{ number_format((float) ($car?->listing_price ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">Inspektur Pemeriksa</span>
                        <span class="col-span-7 font-semibold text-slate-900">: {{ $inspection->inspector?->name ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-12">
                        <span class="col-span-5 text-slate-600 font-medium">URI Dinamis Mobil</span>
                        <span class="col-span-7 font-mono text-xs text-red-700">: /cars/{{ $car?->slug }}/report</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visual Showcase + Otospector Pillar Grades + Overall Score Circle -->
        <div class="p-6 sm:p-8 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center border-b border-slate-200">
            <!-- Left: Vehicle Card / Photo -->
            <div class="lg:col-span-5 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white p-6 relative overflow-hidden min-h-64 flex flex-col justify-between shadow-md">
                @if ($car?->photo_url)
                    <img src="{{ asset('storage/' . $car->photo_url) }}" alt="{{ $car->fullName() }}" class="absolute inset-0 w-full h-full object-cover opacity-65">
                @endif
                <div class="relative z-10 flex items-center justify-between">
                    <span class="px-3 py-1 rounded-lg bg-red-600 text-white font-mono text-xs font-extrabold tracking-wider">
                        {{ $car?->license_plate }}
                    </span>
                    <span class="px-3 py-1 rounded-lg bg-white/15 backdrop-blur-xs text-xs font-bold">
                        Kelayakan: {{ $inspection->healthScore() }}%
                    </span>
                </div>

                <!-- Decorative Car Silhouette SVG when no custom photo -->
                @if (! $car?->photo_url)
                    <div class="relative z-10 my-4 flex justify-center text-slate-400/40">
                        <svg class="w-40 h-24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13l2.3-5.2A2 2 0 017.1 6.5h9.8a2 2 0 011.8 1.3L21 13v5a1 1 0 01-1 1h-1a1 1 0 01-1-1v-1H6v1a1 1 0 01-1 1H4a1 1 0 01-1-1v-5z" />
                            <circle cx="7.5" cy="14.5" r="1.5" />
                            <circle cx="16.5" cy="14.5" r="1.5" />
                        </svg>
                    </div>
                @endif

                <div class="relative z-10">
                    <p class="text-xs uppercase tracking-widest text-slate-300 font-bold">{{ $car?->brand }} &bull; {{ $car?->year }}</p>
                    <h2 class="text-2xl font-black tracking-tight">{{ $car?->model }}</h2>
                    <p class="text-xs text-slate-300 mt-1">{{ $car?->transmission }} &bull; {{ number_format((int) ($car?->mileage ?? 0), 0, ',', '.') }} km &bull; {{ $car?->color }}</p>
                </div>
            </div>

            <!-- Right: Otospector Sub-Scores + Overall Score Circle -->
            <div class="lg:col-span-7 flex flex-col items-center">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 w-full text-center mb-6">
                    <x-grade-circle :grade="$breakdown['Interior']['grade'] ?? 'B'" size="md" label="Interior" />
                    <x-grade-circle :grade="$breakdown['Struktur Bodi']['grade'] ?? 'B'" size="md" label="Eksterior & Bodi" />
                    <x-grade-circle :grade="$breakdown['Mesin']['grade'] ?? 'B'" size="md" label="Mesin & Transmisi" />
                    <x-grade-circle :grade="$breakdown['Kaki-Kaki']['grade'] ?? 'B'" size="md" label="Kaki-Kaki & Rem" />
                </div>

                <div class="w-full bg-slate-100 rounded-xl py-1.5 px-4 text-center text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-4">
                    Skor Keseluruhan
                </div>

                <x-grade-circle :grade="$inspection->overall_grade" size="xl" />
                <p class="mt-2 text-sm font-extrabold text-slate-800">
                    Predikat: {{ $inspection->gradeDescription() }} (Skor Teknis {{ $inspection->healthScore() }}/100)
                </p>
                <p class="mt-2 text-xs font-semibold text-slate-500 border-t border-slate-200 pt-2 w-full text-center">
                    A = Sangat Baik, B = Baik, C = Sedang, D = Kurang, E = Buruk
                </p>
            </div>
        </div>

        <!-- Financial & Risk Calculation Engine Panel (PRD Section 2.2.C) -->
        <div class="p-6 sm:p-8 bg-slate-50 border-b border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-4">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Harga Penawaran Awal</span>
                    <p class="text-xl font-extrabold text-slate-900 mt-1">
                        Rp {{ number_format((float) ($car?->listing_price ?? 0), 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-500">Acuan nilai jual penjual</span>
                </div>

                <div class="bg-white rounded-2xl border border-red-200 p-4">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-red-600">Total Akumulasi Biaya Perbaikan</span>
                    <p class="text-xl font-extrabold text-red-600 mt-1">
                        Rp {{ number_format((float) $inspection->total_repair_cost, 0, ',', '.') }}
                    </p>
                    <span class="text-xs font-semibold text-slate-600">
                        Rasio: <strong>{{ $inspection->repairRatioPercentage() }}%</strong> dari harga jual ({{ $inspection->items->whereIn('condition', ['Warning', 'Critical'])->count() }} temuan)
                    </span>
                </div>

                <div class="bg-white rounded-2xl border border-emerald-200 p-4">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Rekomendasi Harga Negosiasi Ideal</span>
                    <p class="text-xl font-extrabold text-emerald-700 mt-1">
                        Rp {{ number_format($inspection->recommendedNegotiationPrice(), 0, ',', '.') }}
                    </p>
                    <span class="text-xs text-slate-500">
                        Rentang wajar: Rp {{ number_format($advisor['fair_price_min'], 0, ',', '.') }} – Rp {{ number_format($advisor['fair_price_max'], 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Inspector Notes + Smart Advisor Verdict -->
            <div class="bg-slate-200/70 rounded-2xl p-5 text-xs sm:text-sm text-slate-800 leading-relaxed mb-6">
                <p>
                    <strong class="text-slate-900">Catatan Inspector:</strong>
                    {{ $inspection->general_notes ?: 'Tidak ada catatan tambahan dari inspektur.' }}
                </p>
                <div class="mt-3 pt-3 border-t border-slate-300/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <span><strong>Analisis Rekomendasi CarCheck:</strong> {{ $advisor['verdict_title'] }} — {{ $advisor['negotiation_advice'] }}</span>
                </div>
            </div>

            <!-- Flood-Free, Accident-Free Seals & QR Verification (Exact Otospector Footer Layout) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <div class="flex items-center justify-around col-span-2 bg-white rounded-2xl border border-slate-200 p-4">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center border-4 {{ $inspection->flood_free ? 'border-emerald-500 bg-emerald-50 text-emerald-600' : 'border-red-600 bg-red-50 text-red-600' }} mb-2">
                            @if ($inspection->flood_free)
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @else
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            @endif
                        </div>
                        <span class="text-xs sm:text-sm font-extrabold {{ $inspection->flood_free ? 'text-slate-900' : 'text-red-600' }}">
                            {{ $inspection->flood_free ? 'Bebas Terendam Banjir' : 'Terindikasi Bekas Banjir!' }}
                        </span>
                    </div>

                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center border-4 {{ $inspection->accident_free ? 'border-emerald-500 bg-emerald-50 text-emerald-600' : 'border-red-600 bg-red-50 text-red-600' }} mb-2">
                            @if ($inspection->accident_free)
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            @else
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            @endif
                        </div>
                        <span class="text-xs sm:text-sm font-extrabold {{ $inspection->accident_free ? 'text-slate-900' : 'text-red-600' }}">
                            {{ $inspection->accident_free ? 'Bebas Tabrakan Besar' : 'Terindikasi Bekas Tabrakan!' }}
                        </span>
                    </div>
                </div>

                <!-- Simulated QR Code Verification Box -->
                <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-extrabold text-slate-900">Scan QR Code</p>
                        <p class="text-xs font-bold text-slate-600">Untuk Verifikasi Laporan Inspeksi</p>
                        <p class="text-[10px] font-mono text-slate-400 mt-1">ID: #{{ $inspection->reference_number }}</p>
                    </div>
                    <div class="w-16 h-16 bg-slate-900 text-white rounded-xl p-2 flex items-center justify-center shrink-0">
                        <svg class="w-12 h-12" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8h-8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5zm13-2h3v2h-3v-2zm-3 0h2v3h-2v-3zm3 3h3v2h-3v-2zm-3 3h2v2h-2v-2zm3 0h3v2h-3v-2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6-AREA VISUAL HEALTH CHART & DETAILED CHECKLIST MATRIX (Otospector Pages 2-4 Style) -->
        <div class="p-6 sm:p-8">
            <div class="bg-red-800 text-white px-5 py-2.5 rounded-xl font-extrabold text-sm uppercase tracking-wider mb-4 flex items-center justify-between">
                <span>Titik Inspeksi 6 Area Fungsional & Rincian Biaya</span>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span> OK (Good)</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Perlu Diperhatikan (Warning)</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-400"></span> Rusak Parah (Critical)</span>
                </div>
            </div>

            <!-- Visual Progress Bars for the 6 Areas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                @foreach ($breakdown as $catKey => $catData)
                    <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50/60">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <span class="text-xs font-extrabold text-slate-900 block">{{ $catData['title'] }}</span>
                                <span class="text-[11px] text-slate-500">{{ $catData['status_label'] }}</span>
                            </div>
                            <x-grade-circle :grade="$catData['grade']" size="sm" />
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-200 overflow-hidden mb-2">
                            <div class="h-full rounded-full {{ $catData['health_percentage'] >= 85 ? 'bg-emerald-500' : ($catData['health_percentage'] >= 65 ? 'bg-amber-500' : 'bg-red-600') }}"
                                 style="width: {{ $catData['health_percentage'] }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] font-semibold text-slate-600">
                            <span>Skor: {{ $catData['health_percentage'] }}% ({{ $catData['good_count'] }}/{{ $catData['total_items'] }} OK)</span>
                            <span class="{{ $catData['subtotal_cost'] > 0 ? 'text-red-600 font-bold' : 'text-emerald-700' }}">
                                Rp {{ number_format($catData['subtotal_cost'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Detailed Otospector 2-Column Checklist per Area -->
            <div class="space-y-6">
                @foreach ($breakdown as $catKey => $catData)
                    <div class="border border-slate-200 rounded-2xl overflow-hidden">
                        <div class="bg-slate-100 px-5 py-3 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                            <h3 class="font-extrabold text-slate-900 text-sm">{{ $catData['title'] }}</h3>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="text-emerald-700 font-bold">{{ $catData['good_count'] }} OK</span>
                                <span class="text-amber-700 font-bold">{{ $catData['warning_count'] }} Warning</span>
                                <span class="text-red-700 font-bold">{{ $catData['critical_count'] }} Critical</span>
                                <span class="px-2.5 py-0.5 rounded-md bg-white border border-slate-200 font-extrabold text-slate-900">
                                    Subtotal: Rp {{ number_format($catData['subtotal_cost'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-2.5">
                                @foreach ($catData['items'] as $item)
                                    <div class="flex items-center justify-between py-1.5 border-b border-slate-100 text-xs sm:text-sm">
                                        <span class="font-medium text-slate-800 pr-2">{{ $item->item_name }}</span>
                                        <div class="flex items-center gap-3 shrink-0">
                                            @if ($item->condition !== 'Good' && $item->estimated_cost > 0)
                                                <span class="text-xs font-bold text-red-600">
                                                    Rp {{ number_format((float) $item->estimated_cost, 0, ',', '.') }}
                                                </span>
                                            @endif
                                            <x-condition-dot :condition="$item->condition" :showText="false" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Category Notes (Blue text exactly like Otospector PDF Pages 2-4!) -->
                            @php
                                $defectsInCategory = $catData['items']->whereIn('condition', ['Warning', 'Critical']);
                            @endphp
                            <div class="mt-3 pt-2 text-xs sm:text-sm font-semibold">
                                <span class="text-slate-900 font-extrabold">Catatan:</span>
                                @if ($defectsInCategory->isEmpty())
                                    <span class="text-blue-700">N/A (Seluruh komponen pada area {{ $catKey }} berfungsi normal)</span>
                                @else
                                    <span class="text-blue-700">
                                        {{ $defectsInCategory->map(fn ($d) => ($d->defect_description ?: $d->item_name) . ' (Estimasi Rp ' . number_format((float) $d->estimated_cost, 0, ',', '.') . ')')->implode(' | ') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- "PERLU DIPERHATIKAN" VISUAL DEFECT CARDS (Otospector Pages 8-9 Style) -->
        @php
            $allProblemItems = $inspection->items->whereIn('condition', ['Warning', 'Critical'])->values();
        @endphp
        <div class="p-6 sm:p-8 border-t border-slate-200 bg-slate-50/50">
            <div class="bg-red-800 text-white px-5 py-2.5 rounded-xl font-extrabold text-sm uppercase tracking-wider mb-5 flex items-center justify-between">
                <span>Perlu Diperhatikan — Rincian Temuan Kerusakan & Dokumentasi Visual ({{ $allProblemItems->count() }} Temuan)</span>
                <span class="text-xs font-normal">Total Restorasi: Rp {{ number_format((float) $inspection->total_repair_cost, 0, ',', '.') }}</span>
            </div>

            @if ($allProblemItems->isEmpty())
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 text-center text-emerald-900">
                    <p class="font-extrabold text-base">Unit Bebas Temuan Kerusakan!</p>
                    <p class="text-xs mt-1">Seluruh titik pemeriksaan pada 6 area fungsional berstatus Baik (OK).</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($allProblemItems as $defect)
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs flex flex-col justify-between">
                            <div>
                                <div class="px-4 py-3 bg-slate-100 border-b border-slate-200 flex items-center justify-between gap-2">
                                    <div class="text-xs">
                                        <span class="font-bold text-slate-500">Keterangan:</span>
                                        <span class="font-extrabold text-slate-900 ml-1">{{ $defect->item_name }}</span>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $defect->condition === 'Critical' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $defect->condition }} ({{ $defect->category }})
                                    </span>
                                </div>

                                @if ($defect->photo_url)
                                    <div class="h-48 bg-slate-900 overflow-hidden">
                                        <img src="{{ asset('storage/' . $defect->photo_url) }}" alt="{{ $defect->item_name }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="h-36 bg-gradient-to-br from-slate-800 to-slate-900 text-white p-4 flex items-center justify-between">
                                        <div>
                                            <span class="text-[10px] uppercase tracking-widest text-red-400 font-bold">Temuan Area {{ $defect->category }}</span>
                                            <p class="text-sm font-bold text-slate-100 mt-1">{{ $defect->item_name }}</p>
                                            <p class="text-xs text-amber-300 font-extrabold mt-2">
                                                Taksiran Perbaikan: Rp {{ number_format((float) $defect->estimated_cost, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center text-amber-400 shrink-0">
                                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                    </div>
                                @endif

                                <div class="p-4 text-xs sm:text-sm">
                                    <p class="text-slate-800">
                                        <strong class="text-slate-900">Catatan:</strong>
                                        {{ $defect->defect_description ?: 'Komponen memerlukan perbaikan atau penggantian suku cadang.' }}
                                    </p>
                                </div>
                            </div>

                            <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs no-print">
                                <span class="font-extrabold text-red-600">Biaya: Rp {{ number_format((float) $defect->estimated_cost, 0, ',', '.') }}</span>
                                <form action="{{ route('inspections.items.destroy', [$inspection, $defect]) }}" method="POST" onsubmit="return confirm('Hapus item temuan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-600 font-semibold cursor-pointer">Hapus Temuan</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Add Additional Component Finding Form (Granular CRUD InspectionItem + Photo Upload) -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-2xs no-print">
        <h3 class="text-base font-extrabold text-slate-900 mb-1">Tambah Temuan Komponen / Unggah Bukti Foto Tambahan</h3>
        <p class="text-xs text-slate-500 mb-4">Temuan baru akan langsung memicu kalkulasi ulang Total Biaya Perbaikan & Skor Risiko secara otomatis.</p>

        <form action="{{ route('inspections.items.store', $inspection) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Klaster Area (6 Area) *</label>
                <select name="category" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs sm:text-sm">
                    @foreach (\App\Models\InspectionItem::CATEGORIES as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Komponen *</label>
                <input type="text" name="item_name" required placeholder="Contoh: Kerapatan Head Gasket / Motor Fan"
                       class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs sm:text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Status Kondisi *</label>
                <select name="condition" required class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs sm:text-sm">
                    <option value="Warning">Warning (Perlu Perhatian)</option>
                    <option value="Critical">Critical (Rusak Parah)</option>
                    <option value="Good">Good (Baik / OK)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Estimasi Biaya (Rp) *</label>
                <input type="number" name="estimated_cost" value="750000" min="0" step="50000" required
                       class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs sm:text-sm font-bold text-red-600">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Foto Bukti (Opsional)</label>
                <input type="file" name="photo" accept="image/*"
                       class="w-full rounded-xl border border-slate-300 px-2 py-1.5 text-xs">
            </div>

            <div class="sm:col-span-2 lg:col-span-5">
                <label class="block text-xs font-bold text-slate-700 mb-1">Rincian Kecacatan / Kerusakan</label>
                <input type="text" name="defect_description" placeholder="Jelaskan kondisi kerusakan teknis yang ditemukan..."
                       class="w-full rounded-xl border border-slate-300 px-3 py-2 text-xs sm:text-sm">
            </div>

            <div>
                <button type="submit" class="w-full py-2 px-4 rounded-xl bg-slate-900 hover:bg-red-600 text-white text-xs sm:text-sm font-bold transition cursor-pointer">
                    + Tambah & Hitung
                </button>
            </div>
        </form>
    </div>
</x-layout>
