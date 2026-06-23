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

        // Logic foto sesudah / penyelesaian
        // Saat ini hanya mengupdate status
        $laporan = Laporan::find($penugasan->laporan_id);
        $laporan->status = 'Selesai';
        $laporan->save();

        $penugasan->diselesaikan_pada = now();
        $penugasan->save();

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil diverifikasi dan diselesaikan',
            'data' => $penugasan
        ], 200);
    }
}
