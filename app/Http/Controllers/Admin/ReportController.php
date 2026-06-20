<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Laporan::with(['pengguna', 'wilayah', 'kategori']);
        
        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $query->where('status', $request->status);
        }

        $laporans = $query->latest('dilaporkan_pada')->paginate(15);
        return view('admin.reports', compact('laporans'));
    }

    public function exportPdf(Request $request)
    {
        $query = \App\Models\Laporan::with(['pengguna', 'wilayah', 'kategori', 'penugasan.petugas']);
        
        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('dilaporkan_pada', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $laporans = $query->latest('dilaporkan_pada')->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pdf.reports', compact('laporans'))
            ->setPaper('a4', 'landscape');
            
        return $pdf->download('rekap-laporan-trashreport.pdf');
    }

    public function show($id)
    {
        $laporan = \App\Models\Laporan::with(['pengguna', 'wilayah', 'kategori', 'gambarSebelum', 'gambarSesudah', 'riwayatStatus', 'riwayatStatus.pengguna'])->findOrFail($id);
        $petugas = \App\Models\User::where('peran_id', 3)->where('aktif', true)->get();
        
        // Deteksi Duplikat
        $activeReports = \App\Models\Laporan::where('id', '!=', $laporan->id)
            ->whereIn('status', ['Menunggu', 'Terverifikasi', 'Ditugaskan', 'Dalam Perjalanan', 'Sedang Dibersihkan'])
            ->get();
            
        $duplicateReports = collect();
        $radius = 50; // radius dalam meter

        foreach ($activeReports as $activeReport) {
            $earthRadius = 6371000;
            $latFrom = deg2rad($laporan->lintang);
            $lonFrom = deg2rad($laporan->bujur);
            $latTo = deg2rad($activeReport->lintang);
            $lonTo = deg2rad($activeReport->bujur);

            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
            $distance = $angle * $earthRadius;

            if ($distance <= $radius) {
                $activeReport->distance = round($distance);
                $duplicateReports->push($activeReport);
            }
        }
        $duplicateReports = $duplicateReports->sortBy('distance');

        return view('admin.report_detail', compact('laporan', 'petugas', 'duplicateReports'));
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Terverifikasi,Ditolak',
            'petugas_id' => 'required_if:status,Terverifikasi|nullable|exists:pengguna,id',
            'alasan_penolakan' => 'required_if:status,Ditolak|nullable|string',
        ]);

        $laporan = \App\Models\Laporan::findOrFail($id);
        
        if ($request->status === 'Terverifikasi') {
            $laporan->update(['status' => 'Ditugaskan']);
            
            \App\Models\Penugasan::create([
                'laporan_id' => $laporan->id,
                'petugas_id' => $request->petugas_id,
                'ditugaskan_oleh' => \Illuminate\Support\Facades\Auth::id(),
                'ditugaskan_pada' => now(),
            ]);

            \App\Models\RiwayatStatusLaporan::create([
                'laporan_id' => $laporan->id,
                'status' => 'Ditugaskan',
                'catatan' => 'Laporan diverifikasi dan ditugaskan ke petugas.',
                'diubah_oleh' => \Illuminate\Support\Facades\Auth::id()
            ]);

            \App\Models\Notifikasi::create([
                'pengguna_id' => $request->petugas_id,
                'judul' => "Tugas Baru: {$laporan->kode_laporan}",
                'pesan' => "Anda mendapat tugas baru untuk laporan di {$laporan->alamat}.",
                'tipe' => 'info',
                'sudah_dibaca' => false,
                'dibuat_pada' => now()
            ]);
            
            return redirect()->route('admin.reports')->with('success', 'Laporan berhasil diverifikasi dan ditugaskan.');
        } else {
            $laporan->update([
                'status' => 'Ditolak',
                'alasan_penolakan' => $request->alasan_penolakan
            ]);

            \App\Models\RiwayatStatusLaporan::create([
                'laporan_id' => $laporan->id,
                'status' => 'Ditolak',
                'catatan' => $request->alasan_penolakan,
                'diubah_oleh' => \Illuminate\Support\Facades\Auth::id()
            ]);

            \App\Models\Notifikasi::create([
                'pengguna_id' => $laporan->pengguna_id,
                'judul' => "Peringatan Sistem",
                'pesan' => "Laporan {$laporan->kode_laporan} ditolak karena {$request->alasan_penolakan}",
                'tipe' => 'error',
                'sudah_dibaca' => false,
                'dibuat_pada' => now()
            ]);

            return redirect()->route('admin.reports')->with('success', 'Laporan telah ditolak.');
        }
    }
}
