<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Handle mobile login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::with('peran')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->kata_sandi)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password salah.'
            ], 401);
        }

        $roleName = $user->peran ? strtolower($user->peran->nama) : '';

        // Cek apakah role diizinkan login lewat mobile (Admin sebaiknya tidak usah di mobile)
        if ($roleName === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admin harus login melalui Web Dashboard.'
            ], 403);
        }

        // Buat Token Sanctum
        $token = $user->createToken('mobile_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'token' => $token,
                'role' => $roleName // penting untuk routing flutter (pengguna / petugas)
            ]
        ], 200);
    }

    /**
     * Handle mobile logout.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ], 200);
    }

    /**
     * Handle mobile registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:pengguna',
            'telepon' => 'required|string|max:20',
            'password' => 'required|min:8',
        ]);

        $peranPengguna = \App\Models\Peran::where('nama', 'Pengguna')->first();

        $user = User::create([
            'peran_id' => $peranPengguna->id,
            'nama' => $request->nama,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'kata_sandi' => Hash::make($request->password),
            'aktif' => true,
            'email_diverifikasi_pada' => now(),
        ]);

        $token = $user->createToken('mobile_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'user' => $user,
                'token' => $token,
                'role' => 'pengguna'
            ]
        ], 201);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:pengguna,email,' . $user->id,
            'telepon' => 'required|string|max:20',
            'foto_profil' => 'nullable|image|max:2048',
        ]);

        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->telepon = $request->telepon;
        
        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')->store('profil', 'public');
            $user->foto_profil = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => $user
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->password_lama, $user->kata_sandi)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.'
            ], 400);
        }

        $user->kata_sandi = Hash::make($request->password_baru);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui.'
        ]);
    }
}
