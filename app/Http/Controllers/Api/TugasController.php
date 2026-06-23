<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penugasan;
use App\Models\Laporan;
use Illuminate\Support\Facades\Auth;

class TugasController extends Controller
{
    /**
     * Mendapatkan daftar tugas yang diberikan kepada Petugas yang sedang login
     */
    public function index()
    {
        $user = Auth::user();
        
        // Pastikan role petugas
        if (!$user->isPetugas()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $penugasan = Penugasan::with(['laporan.kategori', 'laporan.wilayah', 'laporan.pengguna'])
            ->where('petugas_id', $user->id)
            ->orderBy('ditugaskan_pada', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Tugas',
            'data' => $penugasan
        ], 200);
    }

    /**
     * Memverifikasi / Menyelesaikan Tugas (Eco-Cam / Upload Foto Bukti)
     */
    public function verifikasi(Request $request, $id)
    {
        $user = Auth::user();
        
        $penugasan = Penugasan::where('id', $id)
            ->where('petugas_id', $user->id)
            ->first();

        if (!$penugasan) {
            return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
        }

        $laporan = Laporan::find($penugasan->laporan_id);
        
        // Update status sesuai request
        $statusBaru = $request->input('status') ?: 'Selesai';
        
        // Cek apakah status benar-benar berubah
        if ($laporan->status != $statusBaru) {
            $laporan->status = $statusBaru;
            
            // Tambahkan ke Riwayat Status Laporan
            $catatanRiwayat = "Petugas memperbarui status menjadi {$statusBaru}";
            if (strtolower($statusBaru) == 'selesai' && $request->has('keterangan')) {
                $catatanRiwayat = $request->input('keterangan');
            }
            
            \App\Models\RiwayatStatusLaporan::create([
                'laporan_id' => $laporan->id,
                'status' => $statusBaru,
                'catatan' => $catatanRiwayat,
                'diubah_oleh' => $user->id
            ]);
        }

        // Jika tugas selesai, simpan foto bukti dan catat waktu penyelesaian
        if (strtolower($laporan->status) == 'selesai') {
            $penugasan->diselesaikan_pada = now();
            
            // Simpan foto bukti jika diunggah
            if ($request->hasFile('foto_bukti')) {
                $path = $request->file('foto_bukti')->store('laporan/sesudah', 'public');
                \App\Models\GambarLaporan::create([
                    'laporan_id' => $laporan->id,
                    'tipe_gambar' => 'sesudah',
                    'jalur_gambar' => $path,
                    'dibuat_pada' => now(),
                ]);
            }
            
            // Kirim notifikasi ke pelapor bahwa laporan selesai
            \App\Models\Notifikasi::create([
                'pengguna_id' => $laporan->pengguna_id,
                'judul' => 'Tumpukan Sampah Dibersihkan',
                'pesan' => "Laporan Anda dengan kode {$laporan->kode_laporan} telah berhasil dibersihkan oleh Petugas kami. Terima kasih atas kepedulian Anda terhadap lingkungan!",
                'tipe' => 'selesai',
                'sudah_dibaca' => false,
                'dibuat_pada' => now()
            ]);
        } elseif (strtolower($laporan->status) == 'sedang dibersihkan' || strtolower($laporan->status) == 'dalam perjalanan') {
            // Kirim notifikasi ke pelapor bahwa laporan sedang diproses
            $pesan = strtolower($laporan->status) == 'dalam perjalanan' 
                ? "Petugas sedang dalam perjalanan menuju lokasi tumpukan sampah pada laporan {$laporan->kode_laporan}."
                : "Petugas telah tiba dan sedang membersihkan tumpukan sampah pada laporan {$laporan->kode_laporan}.";
                
            \App\Models\Notifikasi::create([
                'pengguna_id' => $laporan->pengguna_id,
                'judul' => 'Petugas Bergerak',
                'pesan' => $pesan,
                'tipe' => 'proses',
                'sudah_dibaca' => false,
                'dibuat_pada' => now()
            ]);
        }

        $laporan->save();
        $penugasan->save();

        return response()->json([
            'success' => true,
            'message' => 'Status tugas berhasil diperbarui dan diproses',
            'data' => $penugasan
        ], 200);
    }
}
