@props(['title' => 'CarCheck — Sistem Inspeksi & Evaluasi Mobil Bekas'])
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | CarCheck</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 flex flex-col selection:bg-red-600 selection:text-white">
    <!-- Top Accent Bar (Otospector / Automotive Precision Red & Slate Theme) -->
    <div class="bg-slate-900 text-slate-300 text-xs border-b border-slate-800 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-semibold bg-red-600/20 text-red-400 border border-red-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                    CarCheck Precision v1.0
                </span>
                <span class="hidden md:inline text-slate-400">Sistem Inspeksi Multi-Komponen, Kalkulasi Skor Risiko & Estimasi Biaya Restorasi Mobil Bekas</span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-slate-400">Kelompok 1 — Laravel Fundamental</span>
                @auth
                    <span class="text-emerald-400 font-medium">● Aktif: {{ auth()->user()->name }} ({{ auth()->user()->role ?? 'Inspektur' }})</span>
                @else
                    <form action="{{ route('demo.login') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-amber-400 hover:text-amber-300 font-semibold underline cursor-pointer">
                            Login Instan Demo (Inspektur)
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                <!-- Brand Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-600 to-rose-700 flex items-center justify-center text-white shadow-md shadow-red-600/20 group-hover:scale-105 transition">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xl font-extrabold tracking-tight text-slate-900">CAR<span class="text-red-600">CHECK</span></span>
                            <span class="text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">ID</span>
                        </div>
                        <p class="text-[11px] text-slate-500 -mt-0.5">Vehicle Inspection & Risk Engine</p>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('dashboard') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('dashboard', 'home') ? 'bg-red-50 text-red-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('cars.index') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('cars.*') ? 'bg-red-50 text-red-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Data Mobil
                    </a>
                    <a href="{{ route('inspections.index') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('inspections.index', 'inspections.show', 'inspections.edit') ? 'bg-red-50 text-red-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Laporan Inspeksi
                    </a>
                    <a href="{{ route('compare.index') }}"
                       class="px-3.5 py-2 rounded-lg text-sm font-semibold transition {{ request()->routeIs('compare.*') ? 'bg-red-50 text-red-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        Komparator Unit
                    </a>
                </nav>

                <!-- Action CTA & Auth Controls -->
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('inspections.create') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs sm:text-sm font-bold bg-red-600 text-white hover:bg-red-700 shadow-xs transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Mulai Inspeksi</span>
                    </a>

                    @auth
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-2 rounded-lg text-xs sm:text-sm font-semibold text-slate-600 hover:text-red-700 hover:bg-slate-100 border border-slate-200 transition cursor-pointer">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-100 border border-slate-200 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="hidden sm:inline-flex px-3 py-2 rounded-lg text-xs sm:text-sm font-semibold bg-slate-900 text-white hover:bg-slate-800 transition">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Mobile Navigation Bar -->
            <div class="flex md:hidden items-center gap-1 overflow-x-auto py-2 border-t border-slate-100 text-xs font-semibold">
                <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-md shrink-0 {{ request()->routeIs('dashboard', 'home') ? 'bg-red-50 text-red-700' : 'text-slate-600' }}">Dashboard</a>
                <a href="{{ route('cars.index') }}" class="px-3 py-1.5 rounded-md shrink-0 {{ request()->routeIs('cars.*') ? 'bg-red-50 text-red-700' : 'text-slate-600' }}">Data Mobil</a>
                <a href="{{ route('inspections.index') }}" class="px-3 py-1.5 rounded-md shrink-0 {{ request()->routeIs('inspections.*') ? 'bg-red-50 text-red-700' : 'text-slate-600' }}">Laporan Inspeksi</a>
                <a href="{{ route('compare.index') }}" class="px-3 py-1.5 rounded-md shrink-0 {{ request()->routeIs('compare.*') ? 'bg-red-50 text-red-700' : 'text-slate-600' }}">Komparator</a>
            </div>
        </div>
    </header>

    <!-- Flash Notifications -->
    <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 mt-4 no-print">
        @if (session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-start justify-between gap-3 text-emerald-900 shadow-2xs">
                <div class="flex items-center gap-2.5 text-sm font-medium">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="mb-4 rounded-xl bg-blue-50 border border-blue-200 p-4 flex items-center gap-2.5 text-sm font-medium text-blue-900">
                <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-4 text-red-900">
                <div class="flex items-center gap-2 font-bold text-sm mb-1.5">
                    <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Terdapat kesalahan pada input formulir:</span>
                </div>
                <ul class="list-disc list-inside text-xs sm:text-sm space-y-1 text-red-800">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content Slot -->
    <main class="grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 border-t border-slate-800 mt-16 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2.5 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-red-600 flex items-center justify-center text-white font-extrabold">C</div>
                        <span class="text-lg font-extrabold text-white tracking-tight">CAR<span class="text-red-500">CHECK</span></span>
                    </div>
                    <p class="text-xs leading-relaxed text-slate-400">
                        Aplikasi web inspeksi dan evaluasi mobil bekas presisi tinggi. Memfasilitasi pencatatan multi-komponen (6 area utama), penilaian risiko otomatis (Risk Index), dan kalkulasi biaya restorasi secara real-time.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white mb-3">Navigasi Cepat</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('dashboard') }}" class="hover:text-white transition">Dashboard Analitik & Matriks</a></li>
                        <li><a href="{{ route('cars.create') }}" class="hover:text-white transition">Tambah Profil Kendaraan Baru</a></li>
                        <li><a href="{{ route('inspections.create') }}" class="hover:text-white transition">Formulir Checklist 6 Area Komponen</a></li>
                        <li><a href="{{ route('compare.index') }}" class="hover:text-white transition">Komparator Mobil Bekas (Side-by-Side)</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white mb-3">Tim Pengembang — Kelompok 1</h4>
                    <ul class="space-y-1.5 text-xs text-slate-300">
                        <li class="flex justify-between"><span>Tedy Darmawan (Ketua)</span> <span class="font-mono text-slate-400">K3524037</span></li>
                        <li class="flex justify-between"><span>Galang Nur Seto</span> <span class="font-mono text-slate-400">K3524053</span></li>
                        <li class="flex justify-between"><span>Afifah Zahrotul Khoir Sarifudin</span> <span class="font-mono text-slate-400">K3524019</span></li>
                        <li class="flex justify-between"><span>Adelia Rosa Permata</span> <span class="font-mono text-slate-400">K3524001</span></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800 mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
                <p>&copy; {{ date('Y') }} CarCheck — Sistem Inspeksi dan Evaluasi Mobil Bekas. Standar Laporan Inspeksi Independen.</p>
                <p>Dibangun dengan Laravel & Tailwind CSS</p>
            </div>
        </div>
    </footer>
</body>
</html>
