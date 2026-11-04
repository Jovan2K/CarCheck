<x-layout title="Registrasi Akun Baru">
    <div class="max-w-lg mx-auto my-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-extrabold text-slate-900">Registrasi Pengguna CarCheck</h1>
                <p class="text-xs text-slate-500 mt-1">Langkah 1 Alur Sistem: Buat akun untuk mengelola kendaraan & inspeksi</p>
            </div>

            <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 focus:ring-2 focus:ring-red-600/20 outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alamat Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="budi@email.com" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 focus:ring-2 focus:ring-red-600/20 outline-none">
                    </div>
                    <div>
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nomor Telepon / WA</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="081234567890"
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 focus:ring-2 focus:ring-red-600/20 outline-none">
                    </div>
                </div>

                <div>
                    <label for="role" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Peran Pengguna (Target PRD)</label>
                    <select id="role" name="role" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 focus:ring-2 focus:ring-red-600/20 outline-none">
                        <option value="Calon Pembeli" @selected(old('role') === 'Calon Pembeli')>End-Buyer (Calon Pembeli Pribadi)</option>
                        <option value="Inspektur Independen" @selected(old('role') === 'Inspektur Independen')>Inspektur Independen / Mekanik Lepas</option>
                        <option value="Showroom / Dealer" @selected(old('role') === 'Showroom / Dealer')>Pedagang Mobil Bekas (Showroom)</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kata Sandi</label>
                        <input type="password" id="password" name="password" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 focus:ring-2 focus:ring-red-600/20 outline-none">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Konfirmasi Sandi</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 focus:ring-2 focus:ring-red-600/20 outline-none">
                    </div>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-xs transition cursor-pointer">
                    Daftarkan Akun & Masuk ke Dashboard
                </button>
            </form>

            <p class="mt-6 text-center text-xs text-slate-500">
                Sudah memiliki akun? <a href="{{ route('login') }}" class="font-bold text-red-600 hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</x-layout>
