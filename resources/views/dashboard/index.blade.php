<x-layout title="Dashboard Utama — Inspeksi & Evaluasi Mobil Bekas">
    <!-- Hero Banner & Quick Workflow Steps -->
    <div class="rounded-3xl bg-gradient-to-r from-slate-900 via-slate-800 to-red-950 text-white p-6 sm:p-8 mb-8 shadow-lg relative overflow-hidden">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-500/20 border border-red-400/30 text-red-300 text-xs font-bold mb-3">
                    <span>SISTEM INSPEKSI & EVALUASI MOBIL BEKAS PRESISI TINGGI</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight leading-tight">
                    Evaluasi Kelayakan 6 Area Komponen, Deteksi Risiko & Kalkulasi Biaya Perbaikan Real-Time
                </h1>
                <p class="mt-2.5 text-sm text-slate-300 leading-relaxed">
                    Hindari asimetri informasi saat membeli mobil bekas. Catat kondisi Mesin, Transmisi, Kaki-Kaki, Struktur Bodi, Interior & Pengereman untuk menghasilkan laporan resmi berstandar inspektur independen.
                </p>
                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <a href="{{ route('cars.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-slate-900 hover:bg-slate-100 font-bold text-sm shadow-sm transition">
                        <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>1. Tambah Mobil Baru</span>
                    </a>
                    <a href="{{ route('inspections.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-sm shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <span>2. Mulai Sesi Inspeksi (6 Area)</span>
                    </a>
                    <a href="{{ route('compare.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-sm transition">
                        <span>3. Bandingkan Unit (Komparator)</span>
                    </a>
                </div>
            </div>

            <!-- Risk Algorithm Summary Box -->
            <div class="bg-slate-950/60 backdrop-blur-xs border border-slate-700/80 rounded-2xl p-4 sm:p-5 lg:w-80 shrink-0">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-red-400 mb-2.5">Algoritma Risk Index CarCheck</h3>
                <ul class="space-y-2 text-xs text-slate-300">
                    <li class="flex items-start gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 mt-1 shrink-0"></span>
                        <span><strong>Low Risk:</strong> Bebas cacat sasis/mesin berat, biaya perbaikan <code>&lt; 5%</code> harga jual.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 mt-1 shrink-0"></span>
                        <span><strong>Moderate Risk:</strong> Aus pemakaian wajar (kaki-kaki/servis), biaya <code>5% - 15%</code> harga jual.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500 mt-1 shrink-0"></span>
                        <span><strong>High Risk:</strong> Indikasi sasis bengkok, banjir, turun mesin, atau biaya <code>&gt; 15%</code> harga jual.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Matrix Summary Cards (PRD Section 2.2.D) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-stat-card
            title="Total Sesi Inspeksi"
            :value="$totalInspections . ' Laporan'"
            :subtitle="$totalCars . ' Unit kendaraan terdaftar di database'"
            accent="blue" />

        <x-stat-card
            title="Rata-Rata Skor Kelayakan"
            :value="$averageHealthScore . ' / 100'"
            subtitle="Indeks kesehatan teknis lintas 6 area komponen"
            accent="emerald" />

        <x-stat-card
            title="Rekapitulasi Estimasi Biaya"
            :value="'Rp ' . number_format($totalRepairCostAll, 0, ',', '.')"
            subtitle="Total akumulasi biaya restorasi seluruh unit"
            accent="amber" />

        <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-2xs border-l-4 border-l-red-600">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Distribusi Skor Risiko</p>
            <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                <a href="{{ route('dashboard', ['risk' => 'Low']) }}" class="p-2 rounded-xl bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 transition">
                    <span class="block text-lg font-extrabold text-emerald-700">{{ $riskCounts['Low'] }}</span>
                    <span class="text-[11px] font-bold text-emerald-800">Low</span>
                </a>
                <a href="{{ route('dashboard', ['risk' => 'Moderate']) }}" class="p-2 rounded-xl bg-amber-50 border border-amber-200 hover:bg-amber-100 transition">
                    <span class="block text-lg font-extrabold text-amber-700">{{ $riskCounts['Moderate'] }}</span>
                    <span class="text-[11px] font-bold text-amber-800">Moderate</span>
                </a>
                <a href="{{ route('dashboard', ['risk' => 'High']) }}" class="p-2 rounded-xl bg-red-50 border border-red-200 hover:bg-red-100 transition">
                    <span class="block text-lg font-extrabold text-red-700">{{ $riskCounts['High'] }}</span>
                    <span class="text-[11px] font-bold text-red-800">High</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar + Recent Inspections Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden mb-10">
        <div class="p-5 sm:p-6 border-b border-slate-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900">Daftar Laporan Inspeksi & Status Kelayakan Unit</h2>
                <p class="text-xs text-slate-500 mt-0.5">Klik "Lihat Laporan Resmi" untuk membuka halaman detail laporan URI dinamis atau mencetak PDF</p>
            </div>

            <form action="{{ route('dashboard') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari merk, model, plat, no laporan..."
                       class="rounded-xl border border-slate-300 px-3.5 py-2 text-xs sm:text-sm w-56 sm:w-64 focus:border-red-600 outline-none">
                <select name="risk" class="rounded-xl border border-slate-300 px-3 py-2 text-xs sm:text-sm focus:border-red-600 outline-none">
                    <option value="">Semua Tingkat Risiko</option>
                    <option value="Low" @selected($riskFilter === 'Low')>Low Risk (&lt; 5%)</option>
                    <option value="Moderate" @selected($riskFilter === 'Moderate')>Moderate Risk (5% - 15%)</option>
                    <option value="High" @selected($riskFilter === 'High')>High Risk (&gt; 15% / Sasis)</option>
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs sm:text-sm font-bold hover:bg-slate-800 transition cursor-pointer">
                    Filter
                </button>
                @if ($search !== '' || $riskFilter)
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-extrabold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4">No. Laporan & Tanggal</th>
                        <th class="py-3.5 px-4">Unit Kendaraan</th>
                        <th class="py-3.5 px-4 text-center">Grade</th>
                        <th class="py-3.5 px-4">Status Risiko (Risk Index)</th>
                        <th class="py-3.5 px-4 text-right">Harga Penawaran</th>
                        <th class="py-3.5 px-4 text-right">Estimasi Perbaikan</th>
                        <th class="py-3.5 px-4 text-center">Indikator Utama</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-sm">
                    @forelse ($recentInspections as $inspection)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-4 px-4 align-middle">
                                <a href="{{ route('inspections.show', $inspection) }}" class="font-mono font-bold text-red-600 hover:underline text-xs block">
                                    #{{ $inspection->reference_number }}
                                </a>
                                <span class="text-xs text-slate-500">{{ $inspection->inspection_date->format('d M Y') }}</span>
                                <span class="block text-[11px] text-slate-400">Inspektur: {{ $inspection->inspector?->name ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-4 align-middle">
                                @if ($inspection->car)
                                    <a href="{{ route('cars.show', $inspection->car) }}" class="font-bold text-slate-900 hover:text-red-600 transition block">
                                        {{ $inspection->car->fullName() }}
                                    </a>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-1 text-xs text-slate-500">
                                        <span class="px-2 py-0.5 rounded bg-slate-100 font-mono font-semibold text-slate-700">{{ $inspection->car->license_plate }}</span>
                                        <span>&bull; {{ $inspection->car->transmission }}</span>
                                        <span>&bull; {{ number_format($inspection->car->mileage, 0, ',', '.') }} km</span>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-4 align-middle text-center">
                                <x-grade-circle :grade="$inspection->overall_grade" size="sm" />
                            </td>
                            <td class="py-4 px-4 align-middle">
                                <x-risk-badge :level="$inspection->overall_risk_level" size="sm" />
                                <span class="block text-[11px] text-slate-500 mt-1">
                                    Skor Kelayakan: <strong>{{ $inspection->healthScore() }}%</strong>
                                </span>
                            </td>
                            <td class="py-4 px-4 align-middle text-right font-semibold text-slate-800">
                                Rp {{ number_format((float) ($inspection->car?->listing_price ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 align-middle text-right">
                                <span class="font-extrabold {{ $inspection->total_repair_cost > 0 ? 'text-red-600' : 'text-emerald-600' }} block">
                                    Rp {{ number_format((float) $inspection->total_repair_cost, 0, ',', '.') }}
                                </span>
                                <span class="text-[11px] font-semibold text-slate-500">
                                    Rasio: {{ $inspection->repairRatioPercentage() }}% dari harga
                                </span>
                            </td>
                            <td class="py-4 px-4 align-middle text-center">
                                <div class="inline-flex flex-col gap-1 text-[11px] font-semibold">
                                    @if ($inspection->flood_free)
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">✓ Bebas Banjir</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-200">✕ Indikasi Banjir</span>
                                    @endif

                                    @if ($inspection->accident_free)
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">✓ Bebas Tabrakan</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-200">✕ Indikasi Tabrakan</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-4 align-middle text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('inspections.show', $inspection) }}"
                                       class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-red-600 text-white text-xs font-bold transition">
                                        Lihat Laporan
                                    </a>
                                    <a href="{{ route('inspections.print', $inspection) }}" target="_blank"
                                       class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:text-red-600 hover:border-red-200 transition" title="Cetak / Download PDF">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-500">
                                Belum ada sesi inspeksi yang sesuai filter. <a href="{{ route('inspections.create') }}" class="text-red-600 font-bold underline">Mulai Inspeksi Baru</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($recentInspections->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $recentInspections->links() }}
            </div>
        @endif
    </div>

    <!-- Registered Vehicles Quick Cards -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-lg font-extrabold text-slate-900">Unit Mobil Terdaftar Terbaru</h2>
            <p class="text-xs text-slate-500">Akses cepat ke profil kendaraan, riwayat pemeriksaan, atau mulai inspeksi baru</p>
        </div>
        <a href="{{ route('cars.index') }}" class="text-xs font-bold text-red-600 hover:underline">Lihat Semua Mobil &rarr;</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach ($cars as $car)
            @php $latest = $car->latestInspection; @endphp
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div>
                            <span class="inline-block px-2.5 py-0.5 rounded-md bg-slate-900 text-white font-mono text-xs font-bold mb-1.5">
                                {{ $car->license_plate }}
                            </span>
                            <h3 class="font-extrabold text-slate-900 text-base leading-snug">
                                <a href="{{ route('cars.show', $car) }}" class="hover:text-red-600 transition">{{ $car->fullName() }}</a>
                            </h3>
                        </div>
                        @if ($latest)
                            <x-grade-circle :grade="$latest->overall_grade" size="sm" />
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 bg-slate-50 rounded-xl p-3 border border-slate-100 mb-3">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Harga Penawaran</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format((float) $car->listing_price, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Odometer & Transmisi</span>
                            <span class="font-semibold text-slate-800">{{ number_format($car->mileage, 0, ',', '.') }} km &bull; {{ $car->transmission }}</span>
                        </div>
                    </div>

                    @if ($latest)
                        <div class="flex items-center justify-between text-xs mb-4">
                            <x-risk-badge :level="$latest->overall_risk_level" size="sm" />
                            <span class="font-bold text-red-600">Perbaikan: Rp {{ number_format((float) $latest->total_repair_cost, 0, ',', '.') }}</span>
                        </div>
                    @else
                        <div class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 mb-4">
                            Belum ada sesi inspeksi untuk kendaraan ini.
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between gap-2 pt-3 border-t border-slate-100">
                    <a href="{{ route('cars.show', $car) }}" class="text-xs font-bold text-slate-700 hover:text-red-600">
                        Detail & Riwayat
                    </a>
                    <div class="flex items-center gap-2">
                        @if ($latest)
                            <a href="{{ route('cars.report', $car->slug) }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                                Laporan URI Slug
                            </a>
                        @endif
                        <a href="{{ route('inspections.create', ['car_id' => $car->id]) }}" class="px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition">
                            + Inspeksi
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-layout>
