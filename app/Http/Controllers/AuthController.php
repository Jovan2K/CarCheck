<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display login page.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle user authentication.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali di CarCheck, '.Auth::user()->name.'!');
        }

        return back()->withErrors([
            'email' => 'Kredensial email atau kata sandi yang dimasukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    /**
     * Display registration page.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Handle new user registration.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['nullable', 'string', Rule::in(['Inspektur Independen', 'Calon Pembeli', 'Showroom / Dealer'])],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'] ?? 'Calon Pembeli',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Akun CarCheck berhasil didaftarkan. Selamat datang, '.$user->name.'!');
    }

    /**
     * Quick demo login for evaluators/students.
     */
    public function demoLogin(Request $request): RedirectResponse
    {
        $email = $request->input('email', 'tedy@carcheck.id');
        $user = User::where('email', $email)->first() ?? User::first();

        if (! $user) {
            $user = User::create([
                'name' => 'Tedy Darmawan (K3524037)',
                'email' => 'tedy@carcheck.id',
                'phone' => '081234567037',
                'role' => 'Inspektur Independen',
                'password' => Hash::make('password'),
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Login instan berhasil sebagai '.$user->name.' ('.$user->role.').');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sesi CarCheck.');
    }
}
