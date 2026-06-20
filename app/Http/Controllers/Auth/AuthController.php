<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Peran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Tampilkan form login
     */
    public function showLogin()
    {
        return view('guest.login');
    }

    /**
     * Tampilkan form login khusus admin/petugas
     */
    public function showAdminLogin()
    {
        return view('guest.admin-login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cari pengguna berdasarkan email
        $pengguna = User::where('email', $request->email)->first();

        if ($pengguna && Hash::check($request->password, $pengguna->kata_sandi)) {
            Auth::login($pengguna, $request->boolean('remember'));
            $request->session()->regenerate();

            // Redirect berdasarkan peran
            if ($pengguna->isAdmin()) {
                return redirect('/admin/dashboard');
            } elseif ($pengguna->isPetugas()) {
                return redirect('/officer/dashboard');
            }

            return redirect('/user/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Tampilkan form register
     */
    public function showRegister()
    {
        return view('guest.register');
    }

    /**
     * Proses registrasi
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:pengguna'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $peranPengguna = Peran::where('nama', 'Pengguna')->first();

        $pengguna = User::create([
            'peran_id' => $peranPengguna->id,
            'nama' => $request->name,
            'email' => $request->email,
            'telepon' => $request->phone,
            'kata_sandi' => Hash::make($request->password),
            'aktif' => true,
            'email_diverifikasi_pada' => now(),
        ]);

        Auth::login($pengguna);

        return redirect('/user/dashboard');
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
