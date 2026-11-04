<x-layout title="Formulir Inspeksi Multi-Komponen (6 Area)">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-red-600">Langkah 3 & 4 Alur Sistem CarCheck</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Formulir Inspeksi Multi-Komponen (6 Area)</h1>
            <p class="text-xs text-slate-500 mt-1">
                Pilih kondisi tiap komponen (<strong>Good</strong>, <strong>Warning</strong>, atau <strong>Critical</strong>). Jika bermasalah, input estimasi biaya perbaikan & catatan teknis akan aktif otomatis.
            </p>
        </div>
        <a href="{{ route('inspections.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 shrink-0">&larr; Kembali ke Daftar Inspeksi</a>
    </div>

    @if ($cars->isEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
            <h2 class="text-lg font-extrabold text-amber-900">Belum Ada Kendaraan Terdaftar</h2>
            <p class="text-xs text-amber-800 mt-1 mb-4">Silakan daftarkan profil mobil terlebih dahulu sebelum memulai sesi inspeksi.</p>
            <a href="{{ route('cars.create') }}" class="inline-flex px-4 py-2.5 rounded-xl bg-red-600 text-white text-sm font-bold">
                + Tambah Profil Mobil Baru
            </a>
        </div>
    @else
        <form action="{{ route('inspections.store') }}" method="POST" enctype="multipart/form-data" id="inspection-form" class="space-y-6">
            @csrf

            <!-- Step 3: Session Header & Vehicle Selector -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-2xs">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-4">
                    <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-900">
                        Inisiasi Sesi Inspeksi & Identitas Unit
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold">
                        Status Sesi: In Progress &rarr; Auto-Evaluate
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label for="car_id" class="block text-xs font-bold text-slate-700 mb-1">Pilih Unit Kendaraan yang Diinspeksi *</label>
                        <select id="car_id" name="car_id" required
                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-semibold text-slate-900 focus:border-red-600 outline-none">
                            @foreach ($cars as $car)
                                <option value="{{ $car->id }}"
                                        data-price="{{ (float) $car->listing_price }}"
                                        @selected(old('car_id', $selectedCarId) == $car->id)>
                                    {{ $car->fullName() }} — [{{ $car->license_plate }}] — Harga: Rp {{ number_format((float) $car->listing_price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">
                            Mobil belum ada di daftar? <a href="{{ route('cars.create') }}" class="text-red-600 font-bold underline">+ Daftarkan Mobil Baru</a>
                        </p>
                    </div>

                    <div>
                        <label for="inspection_date" class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pelaksanaan Inspeksi *</label>
                        <input type="date" id="inspection_date" name="inspection_date" value="{{ old('inspection_date', now()->toDateString()) }}" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 outline-none">
                    </div>
                </div>
            </div>

            <!-- Live Real-Time Risk & Cost Calculator Bar -->
            <div class="sticky top-16 z-30 bg-slate-900 text-white rounded-2xl p-4 shadow-lg border border-slate-700 flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-6">
                    <div>
                        <span class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold">Total Estimasi Perbaikan (Real-Time)</span>
                        <span id="live-total-cost" class="text-xl font-extrabold text-amber-400">Rp 0</span>
                    </div>
                    <div class="border-l border-slate-700 pl-5">
                        <span class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold">Rasio thd Harga Jual</span>
                        <span id="live-ratio" class="text-lg font-extrabold text-white">0.00%</span>
                    </div>
                    <div class="border-l border-slate-700 pl-5">
                        <span class="block text-[10px] uppercase tracking-wider text-slate-400 font-bold">Prediksi Risk Index</span>
                        <span id="live-risk-badge" class="inline-block mt-0.5 px-3 py-0.5 rounded-full text-xs font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
                            Low Risk (&lt; 5%)
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" id="btn-mark-all-good"
                            class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition cursor-pointer">
                        Set Semua "Baik (Good)"
                    </button>
                    <button type="submit"
                            class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-500 text-white text-xs sm:text-sm font-extrabold shadow-sm transition cursor-pointer">
                        Simpan & Evaluasi Sekarang &rarr;
                    </button>
                </div>
            </div>

            <!-- 6 Functional Areas Checklist -->
            @php $itemIndex = 0; @endphp
            @foreach ($checklist as $categoryKey => $area)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                    <div class="bg-slate-900 text-white px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="text-base font-extrabold tracking-tight">{{ $area['title'] }}</h3>
                            <p class="text-xs text-slate-300">{{ $area['subtitle'] }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-white/10 text-xs font-semibold text-slate-200 shrink-0">
                            {{ count($area['items']) }} Titik Pemeriksaan
                        </span>
                    </div>

                    <div class="divide-y divide-slate-200">
                        @foreach ($area['items'] as $preset)
                            @php
                                $oldCondition = old("items.{$itemIndex}.condition", 'Good');
                                $oldCost = old("items.{$itemIndex}.estimated_cost", 0);
                                $oldDesc = old("items.{$itemIndex}.defect_description", '');
                                $isCriticalArea = in_array($categoryKey, ['Struktur Bodi', 'Mesin'], true) || $preset['is_structural_or_heavy'];
                            @endphp
                            <div class="p-5 transition item-row"
                                 data-index="{{ $itemIndex }}"
                                 data-category="{{ $categoryKey }}"
                                 data-default-cost="{{ $preset['default_warning_cost'] }}"
                                 data-heavy="{{ $isCriticalArea ? '1' : '0' }}">

                                <input type="hidden" name="items[{{ $itemIndex }}][category]" value="{{ $categoryKey }}">
                                <input type="hidden" name="items[{{ $itemIndex }}][item_name]" value="{{ $preset['name'] }}">

                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                    <div class="max-w-xl">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-900 text-sm">{{ $preset['name'] }}</span>
                                            @if ($categoryKey === 'Struktur Bodi' || $categoryKey === 'Mesin')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-red-50 text-red-700 border border-red-200">
                                                    Area Kritis Risk Index
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $preset['hint'] }}</p>
                                    </div>

                                    <!-- Condition Radio Pills (Good / Warning / Critical) -->
                                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                                        <label class="cursor-pointer">
                                            <input type="radio" name="items[{{ $itemIndex }}][condition]" value="Good"
                                                   class="peer sr-only condition-radio" @checked($oldCondition === 'Good')>
                                            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 transition">
                                                <span class="w-2 h-2 rounded-full bg-emerald-400 peer-checked:bg-white"></span>
                                                Baik (Good)
                                            </span>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio" name="items[{{ $itemIndex }}][condition]" value="Warning"
                                                   class="peer sr-only condition-radio" @checked($oldCondition === 'Warning')>
                                            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 transition">
                                                <span class="w-2 h-2 rounded-full bg-amber-400 peer-checked:bg-white"></span>
                                                Perlu Perhatian (Warning)
                                            </span>
                                        </label>

                                        <label class="cursor-pointer">
                                            <input type="radio" name="items[{{ $itemIndex }}][condition]" value="Critical"
                                                   class="peer sr-only condition-radio" @checked($oldCondition === 'Critical')>
                                            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 peer-checked:bg-red-600 peer-checked:text-white peer-checked:border-red-600 transition">
                                                <span class="w-2 h-2 rounded-full bg-red-500 peer-checked:bg-white"></span>
                                                Rusak Parah (Critical)
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Conditional Cost, Defect Note & Photo Evidence Drawer (Activated when Warning or Critical) -->
                                <div class="defect-drawer mt-4 pt-4 border-t border-dashed border-slate-200 grid grid-cols-1 md:grid-cols-12 gap-3 bg-slate-50/90 p-3.5 rounded-xl {{ $oldCondition === 'Good' ? 'hidden' : '' }}">
                                    <div class="md:col-span-4">
                                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                            Estimasi Biaya Perbaikan (Rp) *
                                        </label>
                                        <input type="number"
                                               name="items[{{ $itemIndex }}][estimated_cost]"
                                               value="{{ $oldCost }}"
                                               min="0"
                                               step="50000"
                                               class="cost-input w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-bold text-red-600 focus:border-red-600 outline-none">
                                        <span class="text-[10px] text-slate-500">Saran taksiran pasar: Rp {{ number_format($preset['default_warning_cost'], 0, ',', '.') }}</span>
                                    </div>

                                    <div class="md:col-span-5">
                                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                            Catatan Teknis / Rincian Kerusakan
                                        </label>
                                        <input type="text"
                                               name="items[{{ $itemIndex }}][defect_description]"
                                               value="{{ $oldDesc }}"
                                               placeholder="Contoh: Rembesan oli pada seal, perlu penggantian segera..."
                                               class="desc-input w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:border-red-600 outline-none">
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                                            Foto Bukti Kerusakan (Opsional)
                                        </label>
                                        <input type="file"
                                               name="items[{{ $itemIndex }}][photo]"
                                               accept="image/*"
                                               class="w-full rounded-xl border border-slate-300 bg-white px-2.5 py-1.5 text-xs text-slate-600">
                                    </div>
                                </div>
                            </div>
                            @php $itemIndex++; @endphp
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- General Inspector Conclusion Notes -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-2xs">
                <label for="general_notes" class="block text-sm font-extrabold uppercase tracking-wider text-slate-900 mb-2">
                    Catatan Kesimpulan Umum Inspector (Ditampilkan di Halaman Depan Laporan)
                </label>
                <textarea id="general_notes" name="general_notes" rows="3"
                          placeholder="Contoh: Kondisi mobil secara umum baik, tidak ditemukan indikasi tabrakan besar maupun terendam banjir. Beberapa komponen aus wajar perlu diservis..."
                          class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-red-600 outline-none">{{ old('general_notes', 'Kondisi mobil telah diperiksa menyeluruh pada 6 area utama. Lihat rincian komponen di bawah untuk daftar perbaikan yang direkomendasikan.') }}</textarea>

                <div class="mt-5 flex flex-wrap items-center justify-end gap-3">
                    <a href="{{ route('inspections.index') }}" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 text-sm font-bold hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-extrabold shadow-md shadow-red-600/20 transition cursor-pointer">
                        Simpan & Jalankan Kalkulasi Skor Risiko &rarr;
                    </button>
                </div>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const carSelect = document.getElementById('car_id');
                const rows = document.querySelectorAll('.item-row');
                const liveTotalCost = document.getElementById('live-total-cost');
                const liveRatio = document.getElementById('live-ratio');
                const liveRiskBadge = document.getElementById('live-risk-badge');
                const btnMarkAllGood = document.getElementById('btn-mark-all-good');

                function formatRupiah(num) {
                    return 'Rp ' + Math.round(num).toLocaleString('id-ID');
                }

                function updateLiveCalculation() {
                    const selectedOption = carSelect.options[carSelect.selectedIndex];
                    const listingPrice = parseFloat(selectedOption?.dataset?.price || '0');

                    let totalCost = 0;
                    let hasCriticalBodyOrEngine = false;
                    let hasAnyCritical = false;

                    rows.forEach(row => {
                        const checked = row.querySelector('.condition-radio:checked');
                        const condition = checked ? checked.value : 'Good';
                        const costInput = row.querySelector('.cost-input');
                        const category = row.dataset.category;

                        if (condition !== 'Good') {
                            totalCost += parseFloat(costInput.value || '0');
                        }

                        if (condition === 'Critical') {
                            hasAnyCritical = true;
                            if (category === 'Struktur Bodi' || category === 'Mesin') {
                                hasCriticalBodyOrEngine = true;
                            }
                        }
                    });

                    const ratio = listingPrice > 0 ? (totalCost / listingPrice) * 100 : 0;
                    liveTotalCost.textContent = formatRupiah(totalCost);
                    liveRatio.textContent = ratio.toFixed(2) + '%';

                    if (hasCriticalBodyOrEngine || ratio > 15) {
                        liveRiskBadge.textContent = 'High Risk (Risiko Tinggi)';
                        liveRiskBadge.className = 'inline-block mt-0.5 px-3 py-0.5 rounded-full text-xs font-extrabold bg-red-500/20 text-red-300 border border-red-400/40';
                    } else if (ratio >= 5 || hasAnyCritical) {
                        liveRiskBadge.textContent = 'Moderate Risk (Risiko Sedang)';
                        liveRiskBadge.className = 'inline-block mt-0.5 px-3 py-0.5 rounded-full text-xs font-extrabold bg-amber-500/20 text-amber-300 border border-amber-400/40';
                    } else {
                        liveRiskBadge.textContent = 'Low Risk (Risiko Rendah)';
                        liveRiskBadge.className = 'inline-block mt-0.5 px-3 py-0.5 rounded-full text-xs font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-400/40';
                    }
                }

                rows.forEach(row => {
                    const radios = row.querySelectorAll('.condition-radio');
                    const drawer = row.querySelector('.defect-drawer');
                    const costInput = row.querySelector('.cost-input');
                    const defaultCost = parseFloat(row.dataset.defaultCost || '500000');

                    radios.forEach(radio => {
                        radio.addEventListener('change', function () {
                            if (this.value === 'Good') {
                                drawer.classList.add('hidden');
                                costInput.value = 0;
                            } else {
                                drawer.classList.remove('hidden');
                                if (parseFloat(costInput.value || '0') === 0) {
                                    costInput.value = this.value === 'Critical' ? defaultCost * 2 : defaultCost;
                                }
                            }
                            updateLiveCalculation();
                        });
                    });

                    costInput.addEventListener('input', updateLiveCalculation);
                });

                carSelect.addEventListener('change', updateLiveCalculation);

                if (btnMarkAllGood) {
                    btnMarkAllGood.addEventListener('click', function () {
                        rows.forEach(row => {
                            const goodRadio = row.querySelector('.condition-radio[value="Good"]');
                            const drawer = row.querySelector('.defect-drawer');
                            const costInput = row.querySelector('.cost-input');
                            if (goodRadio) {
                                goodRadio.checked = true;
                                drawer.classList.add('hidden');
                                costInput.value = 0;
                            }
                        });
                        updateLiveCalculation();
                    });
                }

                updateLiveCalculation();
            });
        </script>
    @endif
</x-layout>
