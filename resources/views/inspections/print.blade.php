<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Laporan Inspeksi #{{ $inspection->reference_number }} — CarCheck</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        @page { size: A4; margin: 12mm; }
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 py-6 print:py-0 print:bg-white">
    @php $car = $inspection->car; @endphp

    <!-- Floating Print Controls -->
    <div class="max-w-4xl mx-auto mb-4 px-4 flex items-center justify-between no-print">
        <a href="{{ route('inspections.show', $inspection) }}" class="px-4 py-2 rounded-xl bg-white border border-slate-300 text-xs font-bold text-slate-700 hover:bg-slate-50">
            &larr; Kembali ke Detail Laporan
        </a>
        <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-extrabold shadow-md cursor-pointer">
            Cetak / Simpan sebagai PDF Sekarang
        </button>
    </div>

    <!-- Printable A4 Report Sheet -->
    <div class="max-w-4xl mx-auto bg-white border border-slate-300 print:border-0 p-6 sm:p-8 shadow-sm print:shadow-none">
        <!-- Header Logo -->
        <div class="flex items-center justify-between pb-4 border-b-2 border-red-800">
            <div>
                <div class="text-2xl font-black italic tracking-tight">CAR<span class="text-red-700">CHECK</span></div>
                <p class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">INDONESIA'S #1 PRECISION CAR INSPECTION & RISK EVALUATION</p>
            </div>
            <div class="text-right">
                <x-risk-badge :level="$inspection->overall_risk_level" size="md" />
            </div>
        </div>

        <!-- Crimson Title Bar -->
        <div class="bg-red-800 text-white px-4 py-2 my-3 flex items-center justify-between text-xs sm:text-sm font-bold">
            <span>Laporan Kondisi Kendaraan No. {{ $inspection->reference_number }}</span>
            <span>Tanggal inspeksi: {{ $inspection->inspection_date->format('d / m / Y') }}</span>
        </div>

        <!-- Identity Grid -->
        <div class="bg-slate-100 p-4 grid grid-cols-2 gap-x-8 gap-y-1.5 text-xs mb-5">
            <div class="flex justify-between"><span>No. Polisi</span> <strong>: {{ $car?->license_plate }}</strong></div>
            <div class="flex justify-between"><span>Warna Eksterior/Interior</span> <strong>: {{ $car?->color ?? 'Putih/Hitam' }}</strong></div>
            <div class="flex justify-between"><span>Merk</span> <strong>: {{ $car?->brand }}</strong></div>
            <div class="flex justify-between"><span>Bahan bakar</span> <strong>: {{ $car?->fuel_type ?? 'Bensin' }}</strong></div>
            <div class="flex justify-between"><span>Model/Tipe</span> <strong>: {{ $car?->model }}</strong></div>
            <div class="flex justify-between"><span>Odometer</span> <strong>: {{ number_format((int) ($car?->mileage ?? 0), 0, ',', '.') }} km</strong></div>
            <div class="flex justify-between"><span>Transmisi</span> <strong>: {{ $car?->transmission }}</strong></div>
            <div class="flex justify-between"><span>Pajak Berlaku Sampai</span> <strong>: {{ $car?->tax_expiry_date?->format('d/m/Y') ?? '-' }}</strong></div>
            <div class="flex justify-between"><span>Tahun / CC</span> <strong>: {{ $car?->year }} ({{ $car?->engine_capacity ?? '-' }})</strong></div>
            <div class="flex justify-between"><span>Harga Penawaran</span> <strong>: Rp {{ number_format((float) ($car?->listing_price ?? 0), 0, ',', '.') }}</strong></div>
            <div class="flex justify-between"><span>No. Rangka</span> <strong class="font-mono">: {{ $car?->vin_number ?? '-' }}</strong></div>
            <div class="flex justify-between"><span>Total Estimasi Perbaikan</span> <strong class="text-red-700">: Rp {{ number_format((float) $inspection->total_repair_cost, 0, ',', '.') }} ({{ $inspection->repairRatioPercentage() }}%)</strong></div>
        </div>

        <!-- Score Summary -->
        <div class="grid grid-cols-5 gap-3 items-center text-center border border-slate-200 rounded-xl p-4 mb-5">
            <x-grade-circle :grade="$breakdown['Interior']['grade'] ?? 'B'" size="sm" label="Interior" />
            <x-grade-circle :grade="$breakdown['Struktur Bodi']['grade'] ?? 'B'" size="sm" label="Eksterior" />
            <x-grade-circle :grade="$breakdown['Mesin']['grade'] ?? 'B'" size="sm" label="Mesin & Transmisi" />
            <x-grade-circle :grade="$breakdown['Kaki-Kaki']['grade'] ?? 'B'" size="sm" label="Kaki-Kaki & Rem" />
            <div class="border-l border-slate-200 pl-3">
                <x-grade-circle :grade="$inspection->overall_grade" size="md" label="Skor Keseluruhan" />
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-slate-100 p-3.5 rounded-lg text-xs mb-5">
            <strong>Catatan Inspector:</strong> {{ $inspection->general_notes }}
            <div class="mt-2 pt-2 border-t border-slate-300 flex items-center justify-between font-bold">
                <span>Status Banjir: {{ $inspection->flood_free ? 'BEBAS TERENDAM BANJIR' : 'INDIKASI BANJIR' }}</span>
                <span>Status Tabrakan: {{ $inspection->accident_free ? 'BEBAS TABRAKAN BESAR' : 'INDIKASI TABRAKAN' }}</span>
                <span>Harga Negosiasi Ideal: Rp {{ number_format($inspection->recommendedNegotiationPrice(), 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- 6 Areas Checklist Summary -->
        <div class="space-y-4">
            @foreach ($breakdown as $catKey => $catData)
                <div class="border border-slate-200 rounded-lg p-3">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-1.5 mb-2 text-xs font-extrabold">
                        <span>{{ $catData['title'] }} (Grade {{ $catData['grade'] }})</span>
                        <span class="text-red-700">Subtotal Perbaikan: Rp {{ number_format($catData['subtotal_cost'], 0, ',', '.') }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-x-6 gap-y-1 text-[11px]">
                        @foreach ($catData['items'] as $item)
                            <div class="flex items-center justify-between">
                                <span>{{ $item->item_name }}</span>
                                <span class="font-bold {{ $item->condition === 'Good' ? 'text-emerald-700' : ($item->condition === 'Warning' ? 'text-amber-600' : 'text-red-700') }}">
                                    ● {{ $item->condition }} {{ $item->estimated_cost > 0 ? '(Rp ' . number_format((float) $item->estimated_cost, 0, ',', '.') . ')' : '' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <p class="mt-6 text-[10px] text-slate-500 italic text-center">
            *Laporan inspeksi ini dihasilkan secara otomatis oleh Sistem CarCheck Precision pada {{ now()->format('d/m/Y H:i') }}.
        </p>
    </div>
</body>
</html>
