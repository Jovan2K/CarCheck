<x-layout :title="'Revisi Data Temuan Inspeksi #' . $inspection->reference_number">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-red-600">Langkah 6 (Koreksi) — Revisi Data Temuan</span>
            <h1 class="text-2xl font-extrabold text-slate-900">
                Revisi Lembar Inspeksi #{{ $inspection->reference_number }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui kondisi komponen atau estimasi biaya perbaikan, lalu simpan untuk mengalkulasi ulang Risk Index.</p>
        </div>
        <a href="{{ route('inspections.show', $inspection) }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">&larr; Kembali ke Laporan</a>
    </div>

    <form action="{{ route('inspections.update', $inspection) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-2xs">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label for="car_id" class="block text-xs font-bold text-slate-700 mb-1">Unit Kendaraan *</label>
                    <select id="car_id" name="car_id" required
                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm font-semibold text-slate-900 focus:border-red-600 outline-none">
                        @foreach ($cars as $car)
                            <option value="{{ $car->id }}" @selected(old('car_id', $inspection->car_id) == $car->id)>
                                {{ $car->fullName() }} — [{{ $car->license_plate }}] — Rp {{ number_format((float) $car->listing_price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="inspection_date" class="block text-xs font-bold text-slate-700 mb-1">Tanggal Inspeksi *</label>
                    <input type="date" id="inspection_date" name="inspection_date"
                           value="{{ old('inspection_date', $inspection->inspection_date->format('Y-m-d')) }}" required
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 outline-none">
                </div>
            </div>
        </div>

        @php
            $existingByKey = $inspection->items->keyBy(fn ($i) => $i->category . '|' . $i->item_name);
            $itemIndex = 0;
        @endphp

        @foreach ($checklist as $categoryKey => $area)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-extrabold">{{ $area['title'] }}</h3>
                        <p class="text-xs text-slate-300">{{ $area['subtitle'] }}</p>
                    </div>
                </div>

                <div class="divide-y divide-slate-200">
                    @foreach ($area['items'] as $preset)
                        @php
                            $existing = $existingByKey->get($categoryKey . '|' . $preset['name']);
                            $cond = old("items.{$itemIndex}.condition", $existing?->condition ?? 'Good');
                            $cost = old("items.{$itemIndex}.estimated_cost", (float) ($existing?->estimated_cost ?? 0));
                            $desc = old("items.{$itemIndex}.defect_description", $existing?->defect_description ?? '');
                        @endphp
                        <div class="p-5 edit-item-row" data-default-cost="{{ $preset['default_warning_cost'] }}">
                            <input type="hidden" name="items[{{ $itemIndex }}][category]" value="{{ $categoryKey }}">
                            <input type="hidden" name="items[{{ $itemIndex }}][item_name]" value="{{ $preset['name'] }}">

                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <div>
                                    <span class="font-bold text-slate-900 text-sm">{{ $preset['name'] }}</span>
                                    <p class="text-xs text-slate-500">{{ $preset['hint'] }}</p>
                                </div>

                                <div class="flex flex-wrap items-center gap-2 shrink-0">
                                    @foreach (['Good' => 'Baik (Good)', 'Warning' => 'Perlu Perhatian', 'Critical' => 'Rusak Parah'] as $val => $label)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="items[{{ $itemIndex }}][condition]" value="{{ $val }}"
                                                   class="peer sr-only edit-condition-radio" @checked($cond === $val)>
                                            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600
                                                {{ $val === 'Good' ? 'peer-checked:bg-emerald-600 peer-checked:border-emerald-600' : ($val === 'Warning' ? 'peer-checked:bg-amber-500 peer-checked:border-amber-500' : 'peer-checked:bg-red-600 peer-checked:border-red-600') }}
                                                peer-checked:text-white transition">
                                                {{ $label }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="edit-defect-drawer mt-4 pt-3 border-t border-dashed border-slate-200 grid grid-cols-1 md:grid-cols-12 gap-3 bg-slate-50 p-3.5 rounded-xl {{ $cond === 'Good' ? 'hidden' : '' }}">
                                <div class="md:col-span-4">
                                    <label class="block text-[11px] font-bold uppercase text-slate-700 mb-1">Estimasi Biaya (Rp)</label>
                                    <input type="number" name="items[{{ $itemIndex }}][estimated_cost]" value="{{ $cost }}" min="0" step="50000"
                                           class="edit-cost-input w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-bold text-red-600">
                                </div>
                                <div class="md:col-span-5">
                                    <label class="block text-[11px] font-bold uppercase text-slate-700 mb-1">Rincian Kerusakan</label>
                                    <input type="text" name="items[{{ $itemIndex }}][defect_description]" value="{{ $desc }}"
                                           class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
                                </div>
                                <div class="md:col-span-3">
                                    <label class="block text-[11px] font-bold uppercase text-slate-700 mb-1">Ganti Foto Bukti</label>
                                    <input type="file" name="items[{{ $itemIndex }}][photo]" accept="image/*"
                                           class="w-full rounded-xl border border-slate-300 bg-white px-2.5 py-1.5 text-xs">
                                </div>
                            </div>
                        </div>
                        @php $itemIndex++; @endphp
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-2xs">
            <label for="general_notes" class="block text-sm font-extrabold uppercase tracking-wider text-slate-900 mb-2">
                Catatan Kesimpulan Umum Inspector
            </label>
            <textarea id="general_notes" name="general_notes" rows="3"
                      class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-red-600 outline-none">{{ old('general_notes', $inspection->general_notes) }}</textarea>

            <div class="mt-5 flex items-center justify-end gap-3">
                <a href="{{ route('inspections.show', $inspection) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-bold">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-extrabold cursor-pointer">
                    Simpan Revisi & Kalkulasi Ulang
                </button>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.edit-item-row').forEach(row => {
                const radios = row.querySelectorAll('.edit-condition-radio');
                const drawer = row.querySelector('.edit-defect-drawer');
                const costInput = row.querySelector('.edit-cost-input');
                const defaultCost = parseFloat(row.dataset.defaultCost || '500000');

                radios.forEach(r => {
                    r.addEventListener('change', function () {
                        if (this.value === 'Good') {
                            drawer.classList.add('hidden');
                            costInput.value = 0;
                        } else {
                            drawer.classList.remove('hidden');
                            if (parseFloat(costInput.value || '0') === 0) {
                                costInput.value = defaultCost;
                            }
                        }
                    });
                });
            });
        });
    </script>
</x-layout>
