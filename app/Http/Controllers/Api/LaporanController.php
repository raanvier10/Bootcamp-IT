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
        
        $laporans = Laporan::with(['kategori', 'wilayah', 'gambar', 'ulasan'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Laporan',
            'data' => $laporans
        ], 200);
    }

    /**
     * Mendapatkan daftar laporan publik untuk Peta Laporan Sekitar
     */
    public function publik()
    {
        $laporans = Laporan::with(['kategori', 'wilayah', 'gambar', 'ulasan'])
            ->orderBy('created_at', 'desc')
            ->take(200)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Laporan Publik',
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

        // Buat Kode Laporan
        $kode_laporan = Laporan::buatKode();

        $prioritas = 'Sedang'; // Default
        if ($request->kategori_id == 4) { // Limbah Medis
            $prioritas = 'Mendesak';
        } elseif ($request->kategori_id == 7) { // Sampah Besar
            $prioritas = 'Tinggi';
        }

        $laporan = Laporan::create([
            'kode_laporan' => $kode_laporan,
            'user_id' => $user->id,
            'wilayah_id' => $request->wilayah_id,
            'kategori_id' => $request->kategori_id,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'lintang' => $request->lintang,
            'bujur' => $request->bujur,
            'alamat' => $request->alamat,
            'prioritas' => $prioritas,
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
            'user_id' => $user->id,
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

    /**
     * Memberikan ulasan/rating untuk laporan yang sudah selesai
     */
    public function storeUlasan(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();
        $laporan = Laporan::where('id', $id)->where('user_id', $user->id)->first();

        if (!$laporan) {
            return response()->json(['success' => false, 'message' => 'Laporan tidak ditemukan.'], 404);
        }

        if (!in_array(strtolower($laporan->status), ['selesai', 'ditutup'])) {
            return response()->json(['success' => false, 'message' => 'Hanya laporan yang selesai yang dapat diberikan rating.'], 400);
        }

        $ulasan = \App\Models\Ulasan::updateOrCreate(
            ['laporan_id' => $laporan->id],
            [
                'user_id' => $user->id,
                'nilai' => $request->nilai,
                'komentar' => $request->komentar,
                'dibuat_pada' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih atas penilaian Anda!',
            'data' => $ulasan
        ]);
    }

    /**
     * Konfirmasi laporan oleh pelapor (dari Menunggu Konfirmasi menjadi Selesai)
     */
    public function konfirmasi(Request $request, $id)
    {
        $user = Auth::user();
        $laporan = Laporan::where('id', $id)->where('user_id', $user->id)->first();

        if (!$laporan) {
            return response()->json(['success' => false, 'message' => 'Laporan tidak ditemukan.'], 404);
        }

        if (strtolower($laporan->status) !== 'menunggu konfirmasi') {
            return response()->json(['success' => false, 'message' => 'Laporan tidak dalam status Menunggu Konfirmasi.'], 400);
        }

        $laporan->update(['status' => 'Selesai']);

        \App\Models\RiwayatStatusLaporan::create([
            'laporan_id' => $laporan->id,
            'status' => 'Selesai',
            'catatan' => 'Pelapor telah mengonfirmasi bahwa pembersihan telah selesai.',
            'diubah_oleh' => $user->id,
            'dibuat_pada' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dikonfirmasi selesai.',
            'data' => $laporan
        ]);
    }
}
