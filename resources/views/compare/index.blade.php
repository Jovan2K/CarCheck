<x-layout title="Komparator Unit Mobil Bekas (Side-by-Side Comparison)">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-red-600">Fitur Pengayaan CarCheck (PRD 2.3)</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Komparator Unit Mobil Bekas (Side-by-Side)</h1>
            <p class="text-xs text-slate-500 mt-1">Bandingkan 2 hingga 3 kandidat kendaraan secara objektif berdasarkan skor risiko, kondisi 6 area, dan biaya restorasi</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">&larr; Kembali ke Dashboard</a>
    </div>

    <!-- Vehicle Selection Form -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs mb-8">
        <form action="{{ route('compare.index') }}" method="GET">
            <p class="text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-3">
                Pilih 2 hingga 3 Laporan Unit Kendaraan untuk Dibandingkan:
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                @foreach ($allInspections as $insp)
                    <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition {{ in_array($insp->id, $selectedIds, true) ? 'border-red-600 bg-red-50/40' : 'border-slate-200 hover:bg-slate-50' }}">
                        <input type="checkbox" name="ids[]" value="{{ $insp->id }}"
                               class="mt-1 rounded border-slate-300 text-red-600 focus:ring-red-500"
                               @checked(in_array($insp->id, $selectedIds, true))>
                        <div class="text-xs">
                            <span class="font-extrabold text-slate-900 block">{{ $insp->car?->fullName() }}</span>
                            <span class="text-slate-500 font-mono text-[11px]">{{ $insp->car?->license_plate }} &bull; Grade {{ $insp->overall_grade }} ({{ $insp->overall_risk_level }})</span>
                            <span class="block text-red-600 font-bold mt-0.5">Perbaikan: Rp {{ number_format((float) $insp->total_repair_cost, 0, ',', '.') }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-red-600 text-white text-xs sm:text-sm font-bold transition cursor-pointer">
                    Bandingkan Unit Terpilih &rarr;
                </button>
            </div>
        </form>
    </div>

    @if ($comparedInspections->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-slate-500">
            Silakan pilih minimal 2 laporan inspeksi di atas untuk menampilkan matriks perbandingan berdampingan.
        </div>
    @else
        <!-- Recommended Best Choice Banner -->
        @if ($bestInspection && $comparedInspections->count() >= 2)
            <div class="mb-6 rounded-2xl bg-gradient-to-r from-emerald-900 to-slate-900 text-white p-5 shadow-md flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-slate-950 font-black flex items-center justify-center text-xl shrink-0">
                        ★
                    </div>
                    <div>
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-300">Rekomendasi Keputusan Pembelian Terbaik</span>
                        <h2 class="text-lg font-extrabold">
                            {{ $bestInspection->car?->fullName() }} ({{ $bestInspection->car?->license_plate }})
                        </h2>
                        <p class="text-xs text-slate-300 mt-0.5">
                            Skor Kelayakan Tertinggi (<strong>{{ $bestInspection->healthScore() }}% — Grade {{ $bestInspection->overall_grade }}</strong>) dengan Rasio Perbaikan Terendah (<strong>{{ $bestInspection->repairRatioPercentage() }}%</strong>).
                        </p>
                    </div>
                </div>
                <a href="{{ route('inspections.show', $bestInspection) }}" class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shrink-0 transition">
                    Lihat Laporan Lengkap &rarr;
                </a>
            </div>
        @endif

        <!-- Side-by-Side Comparison Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[720px]">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-900 text-white">
                        <th class="p-4 w-56 text-xs font-extrabold uppercase tracking-wider text-slate-300">Parameter Perbandingan</th>
                        @foreach ($comparedInspections as $insp)
                            <th class="p-4 border-l border-slate-800 align-top">
                                @if ($bestInspection && $bestInspection->id === $insp->id && $comparedInspections->count() >= 2)
                                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-emerald-500 text-slate-950 text-[10px] font-black uppercase mb-1.5">
                                        ★ Pilihan Terbaik
                                    </span>
                                @endif
                                <div class="text-base font-extrabold">{{ $insp->car?->fullName() }}</div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $insp->car?->license_plate }} &bull; #{{ $insp->reference_number }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs sm:text-sm">
                    <tr>
                        <td class="p-4 font-bold text-slate-700 bg-slate-50">Skor Keseluruhan & Risk Index</td>
                        @foreach ($comparedInspections as $insp)
                            <td class="p-4 border-l border-slate-100">
                                <div class="flex items-center gap-3">
                                    <x-grade-circle :grade="$insp->overall_grade" size="sm" />
                                    <div>
                                        <x-risk-badge :level="$insp->overall_risk_level" size="sm" />
                                        <span class="block text-xs text-slate-500 mt-1">Skor Kelayakan: <strong>{{ $insp->healthScore() }}/100</strong></span>
                                    </div>
                                </div>
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class="p-4 font-bold text-slate-700 bg-slate-50">Harga Penawaran Awal</td>
                        @foreach ($comparedInspections as $insp)
                            <td class="p-4 border-l border-slate-100 font-extrabold text-slate-900 text-base">
                                Rp {{ number_format((float) ($insp->car?->listing_price ?? 0), 0, ',', '.') }}
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class="p-4 font-bold text-slate-700 bg-slate-50">Total Estimasi Perbaikan</td>
                        @foreach ($comparedInspections as $insp)
                            <td class="p-4 border-l border-slate-100">
                                <span class="font-extrabold text-red-600 text-base block">
                                    Rp {{ number_format((float) $insp->total_repair_cost, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-slate-500">Rasio: <strong>{{ $insp->repairRatioPercentage() }}%</strong> dari harga jual</span>
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class="p-4 font-bold text-slate-700 bg-slate-50">Total Biaya Efektif (Harga + Perbaikan)</td>
                        @foreach ($comparedInspections as $insp)
                            @php $totalEffective = (float) ($insp->car?->listing_price ?? 0) + (float) $insp->total_repair_cost; @endphp
                            <td class="p-4 border-l border-slate-100 font-bold text-slate-800">
                                Rp {{ number_format($totalEffective, 0, ',', '.') }}
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class="p-4 font-bold text-slate-700 bg-slate-50">Harga Negosiasi Ideal</td>
                        @foreach ($comparedInspections as $insp)
                            <td class="p-4 border-l border-slate-100 font-extrabold text-emerald-700">
                                Rp {{ number_format($insp->recommendedNegotiationPrice(), 0, ',', '.') }}
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class="p-4 font-bold text-slate-700 bg-slate-50">Spesifikasi & Odometer</td>
                        @foreach ($comparedInspections as $insp)
                            <td class="p-4 border-l border-slate-100 text-xs space-y-1">
                                <div>Transmisi: <strong>{{ $insp->car?->transmission }}</strong></div>
                                <div>Odometer: <strong>{{ number_format((int) ($insp->car?->mileage ?? 0), 0, ',', '.') }} km</strong></div>
                                <div>Mesin / BBM: <strong>{{ $insp->car?->engine_capacity ?? '-' }} ({{ $insp->car?->fuel_type ?? 'Bensin' }})</strong></div>
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class="p-4 font-bold text-slate-700 bg-slate-50">Indikator Sasis & Banjir</td>
                        @foreach ($comparedInspections as $insp)
                            <td class="p-4 border-l border-slate-100 text-xs space-y-1">
                                <div class="{{ $insp->accident_free ? 'text-emerald-700 font-bold' : 'text-red-600 font-bold' }}">
                                    {{ $insp->accident_free ? '✓ Bebas Tabrakan Besar' : '✕ Indikasi Tabrakan Besar' }}
                                </div>
                                <div class="{{ $insp->flood_free ? 'text-emerald-700 font-bold' : 'text-red-600 font-bold' }}">
                                    {{ $insp->flood_free ? '✓ Bebas Terendam Banjir' : '✕ Indikasi Banjir' }}
                                </div>
                            </td>
                        @endforeach
                    </tr>

                    <!-- 6 Functional Areas Comparison Rows -->
                    @foreach (\App\Models\InspectionItem::CATEGORIES as $category)
                        <tr>
                            <td class="p-4 font-bold text-slate-700 bg-slate-50">Area: {{ $category }}</td>
                            @foreach ($comparedInspections as $insp)
                                @php $catStat = $breakdowns[$insp->id][$category] ?? null; @endphp
                                <td class="p-4 border-l border-slate-100 text-xs">
                                    @if ($catStat)
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="font-extrabold text-slate-900">Grade {{ $catStat['grade'] }} ({{ $catStat['health_percentage'] }}%)</span>
                                            <span class="{{ $catStat['subtotal_cost'] > 0 ? 'text-red-600 font-bold' : 'text-emerald-700 font-semibold' }}">
                                                Rp {{ number_format($catStat['subtotal_cost'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <span class="text-slate-500">
                                            {{ $catStat['good_count'] }} OK, {{ $catStat['warning_count'] }} Warning, {{ $catStat['critical_count'] }} Critical
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    <tr>
                        <td class="p-4 font-bold text-slate-700 bg-slate-50">Aksi Laporan</td>
                        @foreach ($comparedInspections as $insp)
                            <td class="p-4 border-l border-slate-100">
                                <a href="{{ route('inspections.show', $insp) }}" class="inline-block px-4 py-2 rounded-xl bg-slate-900 hover:bg-red-600 text-white text-xs font-bold transition">
                                    Buka Laporan Lengkap &rarr;
                                </a>
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</x-layout>
