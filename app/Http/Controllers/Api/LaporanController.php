<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Laporan;
use App\Models\GambarLaporan;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    /**
     * Mendapatkan daftar laporan khusus pelapor yang login
     */
    public function index()
    {
        $user = Auth::user();
        
        $laporans = Laporan::with(['kategori', 'wilayah', 'gambar'])
            ->where('pengguna_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Laporan',
            'data' => $laporans
        ], 200);
    }

    /**
     * Membuat laporan baru (Pelapor)
     */
    public function store(Request $request)
    {
        $request->validate([
            'wilayah_id' => 'required|exists:wilayah,id',
            'kategori_id' => 'required|exists:kategori,id',
            'judul' => 'required|string|max:150',
            'deskripsi' => 'required|string|min:20|max:500',
            'lintang' => 'required|numeric',
            'bujur' => 'required|numeric',
            'alamat' => 'required|string',
            'foto' => 'required|image|max:5120',
        ]);

        $user = Auth::user();

        // Buat Kode Laporan (misal: REP-YYYYMMDD-RANDOM)
        $kode_laporan = 'REP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        $laporan = Laporan::create([
            'kode_laporan' => $kode_laporan,
            'pengguna_id' => $user->id,
            'wilayah_id' => $request->wilayah_id,
            'kategori_id' => $request->kategori_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'lintang' => $request->lintang,
            'bujur' => $request->bujur,
            'alamat' => $request->alamat,
            'prioritas' => 'Sedang', // Default, admin bisa ubah
            'status' => 'Menunggu',
            'dilaporkan_pada' => now(),
            'dibuat_pada' => now(),
            'diubah_pada' => now(),
        ]);

        // Simpan foto
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('laporan/sebelum', 'public');
            GambarLaporan::create([
                'laporan_id' => $laporan->id,
                'tipe_gambar' => 'sebelum',
                'jalur_gambar' => $path,
                'lintang' => $request->lintang,
                'bujur' => $request->bujur,
                'dibuat_pada' => now(),
            ]);
        }

        // Buat Notifikasi untuk Pengguna
        \App\Models\Notifikasi::create([
            'pengguna_id' => $user->id,
            'judul' => 'Laporan Berhasil Diterima',
            'pesan' => "Laporan Anda dengan kode $kode_laporan berhasil dikirim dan sedang menunggu verifikasi petugas.",
            'tipe' => 'info',
            'sudah_dibaca' => false,
            'dibuat_pada' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dibuat',
            'data' => $laporan
        ], 201);
    }
}
