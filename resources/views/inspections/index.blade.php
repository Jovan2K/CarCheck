<x-layout title="Daftar Sesi & Laporan Inspeksi">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Manajemen Sesi & Laporan Inspeksi</h1>
            <p class="text-xs text-slate-500 mt-1">Rekam data pemeriksaan multi-komponen, skor risiko, dan estimasi biaya restorasi</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('compare.index') }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold text-xs sm:text-sm transition">
                Komparator Unit
            </a>
            <a href="{{ route('inspections.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs sm:text-sm shadow-xs transition">
                + Mulai Inspeksi Baru
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 shadow-2xs">
        <form action="{{ route('inspections.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari No. Laporan, Merek, Model, atau Nomor Polisi..."
                   class="grow rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
            <select name="risk" class="rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                <option value="">Semua Skor Risiko</option>
                <option value="Low" @selected($risk === 'Low')>Low Risk (&lt; 5%)</option>
                <option value="Moderate" @selected($risk === 'Moderate')>Moderate Risk (5% - 15%)</option>
                <option value="High" @selected($risk === 'High')>High Risk (&gt; 15% / Sasis)</option>
            </select>
            <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 text-white text-sm font-bold hover:bg-slate-800 transition cursor-pointer">
                Terapkan Filter
            </button>
            @if ($search !== '' || $risk)
                <a href="{{ route('inspections.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Inspections List -->
    <div class="space-y-4">
        @forelse ($inspections as $inspection)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-2xs hover:shadow-md transition">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <x-grade-circle :grade="$inspection->overall_grade" size="md" label="Skor" />
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span class="px-2.5 py-0.5 rounded bg-slate-900 text-white font-mono text-xs font-bold">
                                    #{{ $inspection->reference_number }}
                                </span>
                                <x-risk-badge :level="$inspection->overall_risk_level" size="sm" />
                                <span class="text-xs text-slate-500">Tanggal: <strong>{{ $inspection->inspection_date->format('d M Y') }}</strong></span>
                            </div>

                            @if ($inspection->car)
                                <h2 class="text-lg font-extrabold text-slate-900">
                                    <a href="{{ route('inspections.show', $inspection) }}" class="hover:text-red-600 transition">
                                        {{ $inspection->car->fullName() }} — <span class="font-mono text-base text-slate-600">{{ $inspection->car->license_plate }}</span>
                                    </a>
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Harga Penawaran: <strong>Rp {{ number_format((float) $inspection->car->listing_price, 0, ',', '.') }}</strong> &bull;
                                    Odometer: {{ number_format($inspection->car->mileage, 0, ',', '.') }} km &bull;
                                    Inspektur: {{ $inspection->inspector?->name ?? '-' }}
                                </p>
                            @endif

                            @if ($inspection->general_notes)
                                <p class="mt-2 text-xs text-slate-600 bg-slate-50 rounded-xl px-3 py-2 border border-slate-100 max-w-3xl">
                                    <strong class="text-slate-800">Catatan Inspector:</strong> {{ $inspection->general_notes }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row lg:flex-col items-start sm:items-center lg:items-end justify-between gap-3 border-t lg:border-t-0 pt-3 lg:pt-0 border-slate-100 shrink-0">
                        <div class="lg:text-right">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Estimasi Perbaikan</span>
                            <span class="text-xl font-extrabold text-red-600">
                                Rp {{ number_format((float) $inspection->total_repair_cost, 0, ',', '.') }}
                            </span>
                            <span class="block text-xs font-semibold text-slate-500">
                                Rasio: {{ $inspection->repairRatioPercentage() }}% dari harga penawaran
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('inspections.show', $inspection) }}"
                               class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-red-600 text-white text-xs font-bold transition">
                                Detail Laporan
                            </a>
                            <a href="{{ route('inspections.print', $inspection) }}" target="_blank"
                               class="px-3 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-xs font-bold transition">
                                Cetak PDF
                            </a>
                            <a href="{{ route('inspections.edit', $inspection) }}"
                               class="px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-bold transition">
                                Revisi
                            </a>
                            <form action="{{ route('inspections.destroy', $inspection) }}" method="POST" onsubmit="return confirm('Hapus sesi laporan inspeksi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-2 rounded-xl border border-slate-200 hover:border-red-300 hover:text-red-600 text-slate-500 text-xs font-bold transition cursor-pointer">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-500">
                Belum ada laporan inspeksi yang tercatat.
                <a href="{{ route('inspections.create') }}" class="text-red-600 font-bold underline ml-1">Mulai Sesi Inspeksi Sekarang</a>
            </div>
        @endforelse
    </div>

    @if ($inspections->hasPages())
        <div class="mt-6">
            {{ $inspections->links() }}
        </div>
    @endif
</x-layout>
