<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Penugasan;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $petugasId = Auth::id();
        $query = Penugasan::with(['laporan', 'laporan.wilayah', 'laporan.kategori', 'laporan.gambarSebelum'])
            ->where('petugas_id', $petugasId);

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'Baru') {
                $query->whereHas('laporan', function ($q) {
                    $q->whereIn('status', ['Terverifikasi', 'Ditugaskan']);
                });
            } elseif ($status === 'Diproses') {
                $query->whereHas('laporan', function ($q) {
                    $q->whereIn('status', ['Dalam Perjalanan', 'Sedang Dibersihkan']);
                });
            } elseif ($status === 'Selesai') {
                $query->whereHas('laporan', function ($q) {
                    $q->whereIn('status', ['Selesai', 'Ditutup']);
                });
            }
        }

        $tasks = $query->latest('ditugaskan_pada')->paginate(10);

        return view('officer.tasks', compact('tasks'));
    }

    public function show($id)
    {
        $tugas = Penugasan::with([
            'laporan', 
            'laporan.wilayah', 
            'laporan.kategori', 
            'laporan.gambarSebelum',
            'laporan.gambarSesudah',
            'laporan.pengguna'
        ])
        ->where('petugas_id', Auth::id())
        ->findOrFail($id);

        return view('officer.task_detail', compact('tugas'));
    }

    public function update(Request $request, $id)
    {
        $penugasan = Penugasan::where('petugas_id', Auth::id())->findOrFail($id);
        $laporan = $penugasan->laporan;

        $action = $request->input('action');

        if ($action === 'menuju_lokasi') {
            $laporan->update(['status' => 'Dalam Perjalanan']);
            \App\Models\RiwayatStatusLaporan::create([
                'laporan_id'  => $laporan->id,
                'status'      => 'Dalam Perjalanan',
                'catatan'     => 'Petugas sedang menuju lokasi.',
                'diubah_oleh' => Auth::id(),
            ]);
            return back()->with('success', 'Status diperbarui: Anda sedang dalam perjalanan menuju lokasi.');
        } 
        
        elseif ($action === 'mulai_pembersihan') {
            $laporan->update(['status' => 'Sedang Dibersihkan']);
            \App\Models\RiwayatStatusLaporan::create([
                'laporan_id'  => $laporan->id,
                'status'      => 'Sedang Dibersihkan',
                'catatan'     => 'Petugas telah tiba di lokasi dan mulai membersihkan sampah.',
                'diubah_oleh' => Auth::id(),
            ]);
            return back()->with('success', 'Status diperbarui: Mulai proses pembersihan.');
        } 
        
        elseif ($action === 'selesaikan_tugas') {
            $request->validate([
                'catatan_penanganan' => ['nullable', 'string'],
                'foto_sesudah'       => ['required', 'image', 'max:5120'],
            ]);

            // Upload foto sesudah
            $path = $request->file('foto_sesudah')->store('laporan/sesudah', 'public');

            \App\Models\GambarLaporan::create([
                'laporan_id'   => $laporan->id,
                'tipe_gambar'  => 'sesudah',
                'jalur_gambar' => $path,
                'lintang'      => $laporan->lintang,
                'bujur'        => $laporan->bujur,
            ]);

            // Update status Laporan & Penugasan
            $laporan->update(['status' => 'Selesai']);
            $penugasan->update(['diselesaikan_pada' => now()]);

            // Buat riwayat
            \App\Models\RiwayatStatusLaporan::create([
                'laporan_id'  => $laporan->id,
                'status'      => 'Selesai',
                'catatan'     => $request->catatan_penanganan,
                'diubah_oleh' => Auth::id(),
            ]);

            return back()->with('success', 'Tugas berhasil diselesaikan! Terima kasih atas kerja keras Anda.');
        }

        return back()->with('error', 'Aksi tidak valid.');
    }
}
