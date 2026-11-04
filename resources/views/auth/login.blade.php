<x-layout title="Login Pengguna">
    <div class="max-w-md mx-auto my-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <div class="text-center mb-6">
                <div class="w-12 h-12 rounded-2xl bg-red-600 text-white flex items-center justify-center mx-auto mb-3 shadow-md shadow-red-600/20">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900">Masuk ke CarCheck</h1>
                <p class="text-xs text-slate-500 mt-1">Autentikasi Sesi Inspektur, Calon Pembeli & Showroom</p>
            </div>

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', 'tedy@carcheck.id') }}" required
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 focus:ring-2 focus:ring-red-600/20 outline-none">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kata Sandi</label>
                    <input type="password" id="password" name="password" value="password" required
                           class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm focus:border-red-600 focus:ring-2 focus:ring-red-600/20 outline-none">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="inline-flex items-center gap-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                        <span>Ingat sesi saya</span>
                    </label>
                    <a href="{{ route('register') }}" class="font-bold text-red-600 hover:underline">Belum punya akun? Daftar</a>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-xs transition cursor-pointer">
                    Masuk Sekarang
                </button>
            </form>

            <!-- Quick Demo Accounts -->
            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 text-center">Akun Pengujian Cepat (Password: password)</p>
                <div class="grid grid-cols-1 gap-2">
                    <form action="{{ route('demo.login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="tedy@carcheck.id">
                        <button type="submit" class="w-full text-left px-3.5 py-2 rounded-xl border border-slate-200 hover:border-red-300 hover:bg-red-50/40 text-xs flex items-center justify-between transition cursor-pointer">
                            <span><strong>Tedy Darmawan</strong> (Inspektur Independen)</span>
                            <span class="text-red-600 font-bold">Login 1-Klik &rarr;</span>
                        </button>
                    </form>
                    <form action="{{ route('demo.login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="afifah@carcheck.id">
                        <button type="submit" class="w-full text-left px-3.5 py-2 rounded-xl border border-slate-200 hover:border-red-300 hover:bg-red-50/40 text-xs flex items-center justify-between transition cursor-pointer">
                            <span><strong>Afifah Zahrotul</strong> (Calon Pembeli)</span>
                            <span class="text-red-600 font-bold">Login 1-Klik &rarr;</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
