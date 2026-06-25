<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            // Redirect berdasarkan peran
            if ($user->isAdmin()) {
                return redirect('/admin/dashboard');
            } elseif ($user->isPetugas()) {
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
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->letters()->numbers()->symbols()],
        ]);

        $user = User::create([
            'peran' => 'Pelapor',
            'name' => $request->name,
            'email' => $request->email,
            'telepon' => $request->phone,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect('/user/dashboard');
    }

    /**
     * Lupa Password - Tampilkan Form Email
     */
    public function showForgotPassword()
    {
        return view('guest.forgot-password');
    }

    /**
     * Lupa Password - Kirim Link (atau OTP/Token)
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        // Generate 6-digit OTP
        $otp = (string) random_int(100000, 999999);
        
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => Hash::make($otp), 'created_at' => now()]
        );

        // Kirim email OTP
        \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\ResetOtpMail($otp));
        
        return redirect()->route('password.verify.form', ['email' => $request->email])
            ->with('success', 'Kode OTP 6-angka telah dikirim ke email Anda. Silakan cek Inbox atau folder Spam.');
    }

    /**
     * Tampilkan form verifikasi OTP
     */
    public function showVerifyOtp(Request $request)
    {
        if (!$request->email) return redirect()->route('password.request');
        return view('guest.verify-otp');
    }

    /**
     * Proses verifikasi OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|numeric|digits:6'
        ]);

        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['token' => 'Kode OTP salah atau sudah kedaluwarsa.']);
        }

        // Simpan sesi untuk allow akses form ganti password
        session(['reset_email' => $request->email, 'reset_token' => $request->token]);
        
        return redirect()->route('password.reset')->with('success', 'Kode OTP benar! Silakan buat password baru.');
    }

    /**
     * Tampilkan form reset password
     */
    public function showResetPassword(Request $request)
    {
        if (!session('reset_email') || !session('reset_token')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi reset password tidak valid. Silakan ulangi dari awal.']);
        }
        return view('guest.reset-password');
    }

    /**
     * Proses reset password (Simpan Password Baru)
     */
    public function processResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->letters()->numbers()->symbols()],
            'token' => 'required|numeric|digits:6'
        ]);

        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi reset password tidak valid atau kedaluwarsa.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        session()->forget(['reset_email', 'reset_token']);

        return redirect('/login')->with('success', 'Password berhasil diubah. Silakan login kembali dengan password baru Anda.');
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
