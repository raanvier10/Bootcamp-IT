<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use App\Models\Laporan;
use App\Models\PesanKontak;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    /**
     * FR-PU-01: Melihat Beranda
     */
    public function home()
    {
        $stats = [
            'total_reports' => Laporan::count(),
            'reports_completed' => Laporan::where('status', 'Ditutup')->count(),
            'reports_processing' => Laporan::whereIn('status', ['Ditugaskan', 'Dalam Perjalanan', 'Sedang Dibersihkan'])->count(),
        ];

        $latestArticles = Artikel::diterbitkan()
            ->latest('diterbitkan_pada')
            ->take(3)
            ->get();

        // Data laporan untuk preview peta
        $mapReports = Laporan::select('id', 'lintang', 'bujur', 'status', 'judul', 'kategori_id')
            ->with(['kategori:id,nama', 'gambarSebelum'])
            ->get();

        return view('guest.home', compact('stats', 'latestArticles', 'mapReports'));
    }

    /**
     * FR-PU-02: Melihat Peta Sebaran Titik Sampah
     */
    public function map()
    {
        $reports = Laporan::select('id', 'kode_laporan', 'judul', 'lintang', 'bujur', 'status', 'kategori_id', 'alamat', 'dilaporkan_pada')
            ->with(['kategori:id,nama', 'gambarSebelum'])
            ->get();

        return view('guest.map', compact('reports'));
    }

    /**
     * FR-PU-03: Melihat Artikel Edukasi Lingkungan
     */
    public function articles()
    {
        $articles = Artikel::diterbitkan()
            ->latest('diterbitkan_pada')
            ->paginate(9);

        return view('guest.articles', compact('articles'));
    }

    /**
     * FR-PU-03: Melihat Detail Artikel
     */
    public function articleDetail($slug)
    {
        $article = Artikel::diterbitkan()
            ->where('slug', $slug)
            ->with('penulis:id,nama')
            ->firstOrFail();

        $relatedArticles = Artikel::diterbitkan()
            ->where('id', '!=', $article->id)
            ->latest('diterbitkan_pada')
            ->take(3)
            ->get();

        return view('guest.article-detail', compact('article', 'relatedArticles'));
    }

    /**
     * FR-PU-04: Melihat Kontak Kami
     */
    public function contact()
    {
        return view('guest.contact');
    }

    /**
     * FR-PU-04: Submit form kontak
     */
    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string'],
        ]);

        PesanKontak::create([
            'nama' => $request->name,
            'email' => $request->email,
            'subjek' => $request->subject,
            'pesan' => $request->message,
        ]);

        return back()->with('success', 'Pesan Anda berhasil dikirim! Kami akan segera merespons.');
    }
}
