<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $petugasId = Auth::id();
        $query = \App\Models\Laporan::with(['wilayah', 'kategori', 'gambarSebelum'])
            ->where('petugas_id', $petugasId);

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'Baru') {
                $query->whereIn('status', ['Terverifikasi', 'Ditugaskan']);
            } elseif ($status === 'Diproses') {
                $query->whereIn('status', ['Dalam Perjalanan', 'Sedang Dibersihkan']);
            } elseif ($status === 'Selesai') {
                $query->whereIn('status', ['Selesai', 'Ditutup']);
            }
        }

        $tasks = $query->latest('updated_at')->paginate(10);

        return view('officer.tasks', compact('tasks'));
    }

    public function show($id)
    {
        $tugas = \App\Models\Laporan::with([
            'wilayah', 
            'kategori', 
            'gambarSebelum',
            'gambarSesudah',
            'user'
        ])
        ->where('petugas_id', Auth::id())
        ->findOrFail($id);

        return view('officer.task_detail', compact('tugas'));
    }

    public function update(Request $request, $id)
    {
        $laporan = \App\Models\Laporan::where('petugas_id', Auth::id())->findOrFail($id);

        $action = $request->input('action');

        if ($action === 'menuju_lokasi') {
            $laporan->update(['status' => 'Dalam Perjalanan']);
            \App\Models\RiwayatStatusLaporan::create([
                'laporan_id'  => $laporan->id,
                'status'      => 'Dalam Perjalanan',
                'catatan'     => 'Petugas sedang menuju lokasi.',
                'diubah_oleh' => Auth::id(),
            ]);
            \App\Models\Notifikasi::create([
                'user_id' => $laporan->user_id,
                'judul' => "Petugas Menuju Lokasi",
                'pesan' => "Petugas sedang dalam perjalanan ke lokasi laporan Anda ({$laporan->kode_laporan}).",
                'tipe' => 'info',
                'sudah_dibaca' => false,
                'dibuat_pada' => now()
            ]);

            $pelapor = \App\Models\User::find($laporan->user_id);
            if ($pelapor) {
                $pelapor->sendPushNotification("Petugas Menuju Lokasi", "Petugas sedang dalam perjalanan ke lokasi laporan Anda ({$laporan->kode_laporan}).", ['laporan_id' => $laporan->id]);
            }
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
            \App\Models\Notifikasi::create([
                'user_id' => $laporan->user_id,
                'judul' => "Pembersihan Dimulai",
                'pesan' => "Petugas telah tiba di lokasi dan mulai membersihkan sampah untuk laporan Anda ({$laporan->kode_laporan}).",
                'tipe' => 'info',
                'sudah_dibaca' => false,
                'dibuat_pada' => now()
            ]);

            $pelapor = \App\Models\User::find($laporan->user_id);
            if ($pelapor) {
                $pelapor->sendPushNotification("Pembersihan Dimulai", "Petugas telah tiba di lokasi dan mulai membersihkan sampah untuk laporan Anda ({$laporan->kode_laporan}).", ['laporan_id' => $laporan->id]);
            }
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

            // Update status Laporan
            $laporan->update(['status' => 'Selesai']);

            // Buat riwayat
            \App\Models\RiwayatStatusLaporan::create([
                'laporan_id'  => $laporan->id,
                'status'      => 'Selesai',
                'catatan'     => $request->catatan_penanganan,
                'diubah_oleh' => Auth::id(),
            ]);

            \App\Models\Notifikasi::create([
                'user_id' => $laporan->user_id,
                'judul' => "Laporan Selesai",
                'pesan' => "Laporan Anda ({$laporan->kode_laporan}) telah selesai ditangani. Terima kasih atas partisipasinya!",
                'tipe' => 'success',
                'sudah_dibaca' => false,
                'dibuat_pada' => now()
            ]);

            $pelapor = \App\Models\User::find($laporan->user_id);
            if ($pelapor) {
                $pelapor->sendPushNotification("Laporan Selesai", "Laporan Anda ({$laporan->kode_laporan}) telah selesai ditangani. Terima kasih atas partisipasinya!", ['laporan_id' => $laporan->id]);
            }

            return back()->with('success', 'Tugas berhasil diselesaikan! Terima kasih atas kerja keras Anda.');
        }

        return back()->with('error', 'Aksi tidak valid.');
    }
}
