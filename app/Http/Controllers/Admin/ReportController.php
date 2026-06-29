<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Laporan::with(['user', 'wilayah', 'kategori']);
        
        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori_id') && $request->kategori_id !== 'Semua Kategori') {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('wilayah_id') && $request->wilayah_id !== 'Semua Wilayah') {
            $query->where('wilayah_id', $request->wilayah_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('dilaporkan_pada', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_laporan', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('kategori', function($qKat) use ($search) {
                      $qKat->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $laporans = $query->latest('dilaporkan_pada')->paginate(15);
        $categories = \App\Models\Kategori::all();
        $wilayahs = \App\Models\Wilayah::all();

        return view('admin.reports', compact('laporans', 'categories', 'wilayahs'));
    }

    public function exportPdf(Request $request)
    {
        $query = \App\Models\Laporan::with(['user', 'wilayah', 'kategori', 'petugas']);
        
        if ($request->filled('status') && $request->status !== 'Semua Status') {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('kategori_id') && $request->kategori_id !== 'Semua Kategori') {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('wilayah_id') && $request->wilayah_id !== 'Semua Wilayah') {
            $query->where('wilayah_id', $request->wilayah_id);
        }
        
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('dilaporkan_pada', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_laporan', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('kategori', function($qKat) use ($search) {
                      $qKat->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $laporans = $query->latest('dilaporkan_pada')->get();
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pdf.reports', compact('laporans'))
            ->setPaper('a4', 'landscape');
            
        return $pdf->download('rekap-laporan-trashreport.pdf');
    }

    public function show($id)
    {
        $laporan = \App\Models\Laporan::with(['user', 'wilayah', 'kategori', 'gambarSebelum', 'gambarSesudah', 'riwayatStatus', 'riwayatStatus.user'])->findOrFail($id);
        $petugas = \App\Models\User::where('peran', 'Petugas')->get();
        
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
            'petugas_id' => 'required_if:status,Terverifikasi|nullable|exists:users,id',
            'alasan_penolakan' => 'required_if:status,Ditolak|nullable|string',
        ]);

        $laporan = \App\Models\Laporan::findOrFail($id);
        
        if ($request->status === 'Terverifikasi') {
            $laporan->update([
                'status' => 'Ditugaskan',
                'petugas_id' => $request->petugas_id
            ]);

            \App\Models\RiwayatStatusLaporan::create([
                'laporan_id' => $laporan->id,
                'status' => 'Ditugaskan',
                'catatan' => 'Laporan diverifikasi dan ditugaskan ke petugas.',
                'diubah_oleh' => \Illuminate\Support\Facades\Auth::id()
            ]);

            \App\Models\Notifikasi::create([
                'user_id' => $request->petugas_id,
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
                'user_id' => $laporan->user_id,
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
