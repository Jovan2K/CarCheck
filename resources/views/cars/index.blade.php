<x-layout title="Data Kendaraan (CRUD Profil Mobil)">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Manajemen Data Kendaraan</h1>
            <p class="text-xs text-slate-500 mt-1">Kelola identitas spesifik kendaraan yang akan atau sedang diinspeksi</p>
        </div>
        <a href="{{ route('cars.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-xs transition shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Mobil Baru</span>
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 shadow-2xs">
        <form action="{{ route('cars.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="q" value="{{ $search }}" placeholder="Cari Merek, Model, Nomor Polisi, atau No. Rangka..."
                   class="grow rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
            <select name="transmission" class="rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-red-600 outline-none">
                <option value="">Semua Transmisi</option>
                <option value="Automatic" @selected($transmission === 'Automatic')>Automatic (AT)</option>
                <option value="CVT" @selected($transmission === 'CVT')>CVT</option>
                <option value="Manual" @selected($transmission === 'Manual')>Manual (MT)</option>
                <option value="DCT" @selected($transmission === 'DCT')>DCT</option>
            </select>
            <button type="submit" class="px-5 py-2 rounded-xl bg-slate-900 text-white text-sm font-bold hover:bg-slate-800 transition cursor-pointer">
                Cari
            </button>
            @if ($search !== '' || $transmission)
                <a href="{{ route('cars.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Vehicle Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($cars as $car)
            @php $latest = $car->latestInspection; @endphp
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <!-- Top Header Band -->
                    <div class="bg-slate-900 text-white px-5 py-3.5 flex items-center justify-between">
                        <span class="font-mono font-bold text-xs px-2.5 py-1 rounded bg-white/10 border border-white/15">
                            {{ $car->license_plate }}
                        </span>
                        <span class="text-xs font-semibold text-slate-300">{{ $car->year }} &bull; {{ $car->transmission }}</span>
                    </div>

                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-red-600">{{ $car->brand }}</p>
                                <h2 class="text-lg font-extrabold text-slate-900 leading-snug">
                                    <a href="{{ route('cars.show', $car) }}" class="hover:text-red-600 transition">{{ $car->model }}</a>
                                </h2>
                            </div>
                            @if ($latest)
                                <x-grade-circle :grade="$latest->overall_grade" size="sm" />
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-2.5 text-xs bg-slate-50 rounded-xl p-3.5 border border-slate-100 mb-4">
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Harga Penawaran</span>
                                <span class="font-extrabold text-slate-900 text-sm">Rp {{ number_format((float) $car->listing_price, 0, ',', '.') }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Odometer</span>
                                <span class="font-bold text-slate-800 text-sm">{{ number_format($car->mileage, 0, ',', '.') }} km</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Warna & BBM</span>
                                <span class="font-semibold text-slate-700">{{ $car->color ?? '-' }} ({{ $car->fuel_type ?? 'Bensin' }})</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Sesi Inspeksi</span>
                                <span class="font-semibold text-slate-700">{{ $car->inspections->count() }} Kali Diperiksa</span>
                            </div>
                        </div>

                        @if ($latest)
                            <div class="rounded-xl bg-slate-50/80 border border-slate-200/80 p-3 flex items-center justify-between gap-2">
                                <x-risk-badge :level="$latest->overall_risk_level" size="sm" />
                                <div class="text-right">
                                    <span class="block text-[10px] text-slate-400 uppercase font-bold">Estimasi Perbaikan</span>
                                    <span class="text-xs font-extrabold text-red-600">Rp {{ number_format((float) $latest->total_repair_cost, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @else
                            <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800 font-medium">
                                Belum ada catatan inspeksi untuk mobil ini.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('cars.show', $car) }}" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                            Detail
                        </a>
                        <a href="{{ route('cars.edit', $car) }}" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                            Edit
                        </a>
                    </div>
                    @if (auth()->check() && auth()->user()->role === 'Inspektur Independen')
                        <a href="{{ route('inspections.create', ['car_id' => $car->id]) }}"
                           class="px-3.5 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition">
                            Mulai Inspeksi &rarr;
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center">
                <p class="text-slate-500 mb-3">Belum ada data kendaraan yang terdaftar.</p>
                <a href="{{ route('cars.create') }}" class="inline-flex px-4 py-2 rounded-xl bg-red-600 text-white font-bold text-sm">
                    + Tambah Mobil Pertama
                </a>
            </div>
        @endforelse
    </div>

    @if ($cars->hasPages())
        <div class="mt-6">
            {{ $cars->links() }}
        </div>
    @endif
</x-layout>
