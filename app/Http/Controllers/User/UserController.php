<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use App\Models\Kategori;
use App\Models\Wilayah;
use App\Models\Ulasan;
use App\Models\GambarLaporan;
use App\Models\RiwayatStatusLaporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * FR-US-01: Dashboard User
     */
    public function dashboard()
    {
        $pengguna = Auth::user();
        $laporan = $pengguna->laporan();

        $stats = [
            'total' => $laporan->count(),
            'pending' => (clone $laporan)->where('status', 'Menunggu')->count(),
            'processing' => (clone $laporan)->whereIn('status', ['Terverifikasi', 'Ditugaskan', 'Dalam Perjalanan', 'Sedang Dibersihkan'])->count(),
            'completed' => (clone $laporan)->whereIn('status', ['Ditutup', 'Selesai'])->count(),
            'rejected' => (clone $laporan)->where('status', 'Ditolak')->count(),
        ];

        $recentReports = $pengguna->laporan()
            ->with(['kategori', 'wilayah'])
            ->latest()
            ->take(5)
            ->get();

        $notifications = $pengguna->notifikasi()
            ->latest('dibuat_pada')
            ->take(5)
            ->get();

        return view('user.dashboard', compact('stats', 'recentReports', 'notifications'));
    }

    /**
     * FR-US-02: Form buat laporan via Eco-Cam
     */
    public function createReport()
    {
        $categories = Kategori::all();
        $districts = Wilayah::all();

        return view('user.create-report', compact('categories', 'districts'));
    }

    /**
     * FR-US-05: Kirim Laporan
     */
    public function storeReport(Request $request)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:kategori,id'],
            'district_id' => ['required', 'exists:wilayah,id'],
            'description' => ['required', 'string', 'min:20', 'max:500'],
            'latitude'    => ['required', 'numeric'],
            'longitude'   => ['required', 'numeric'],
            'address'     => ['required', 'string'],
            'photo'       => ['required', 'image', 'max:5120'],
        ]);

        $laporan = Laporan::create([
            'kode_laporan' => Laporan::buatKode(),
            'pengguna_id'  => Auth::id(),
            'wilayah_id'   => $request->district_id,
            'kategori_id'  => $request->category_id,
            'judul'        => $request->title,
            'deskripsi'    => $request->description,
            'lintang'      => $request->latitude,
            'bujur'        => $request->longitude,
            'alamat'       => $request->address,
            'status'       => 'Menunggu',
            'dilaporkan_pada' => now(),
        ]);

        // Simpan foto sebelum
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('laporan/sebelum', 'public');

            GambarLaporan::create([
                'laporan_id'  => $laporan->id,
                'tipe_gambar' => 'sebelum',
                'jalur_gambar' => $path,
                'lintang'     => $request->latitude,
                'bujur'       => $request->longitude,
            ]);
        }

        // Log riwayat status
        RiwayatStatusLaporan::create([
            'laporan_id'  => $laporan->id,
            'status'      => 'Menunggu',
            'catatan'     => 'Laporan dibuat oleh pelapor',
            'diubah_oleh' => Auth::id(),
        ]);

        return redirect()->route('user.report.detail', $laporan->id)
            ->with('success', 'Laporan berhasil dikirim! Menunggu verifikasi admin.');
    }

    /**
     * FR-US-06: Tracking Status Laporan / Detail
     */
    public function reportDetail($id)
    {
        $report = Laporan::where('pengguna_id', Auth::id())
            ->with(['kategori', 'wilayah', 'gambar', 'riwayatStatus.pengguna', 'penugasan.petugas', 'ulasan'])
            ->findOrFail($id);

        $statusSteps = [
            'Menunggu'           => 'Menunggu Verifikasi',
            'Terverifikasi'      => 'Terverifikasi',
            'Ditugaskan'         => 'Ditugaskan ke Petugas',
            'Dalam Perjalanan'   => 'Petugas Dalam Perjalanan',
            'Sedang Dibersihkan' => 'Sedang Dibersihkan',
            'Selesai'            => 'Selesai Diangkut',
            'Menunggu Konfirmasi'=> 'Menunggu Konfirmasi',
            'Ditutup'            => 'Ditutup',
        ];

        return view('user.report-detail', compact('report', 'statusSteps'));
    }

    /**
     * FR-US-08: Konfirmasi Laporan Selesai
     */
    public function confirmReport(Request $request, $id)
    {
        $report = Laporan::where('pengguna_id', Auth::id())
            ->where('status', 'Menunggu Konfirmasi')
            ->findOrFail($id);

        $report->update(['status' => 'Ditutup']);

        RiwayatStatusLaporan::create([
            'laporan_id'  => $report->id,
            'status'      => 'Ditutup',
            'catatan'     => 'Laporan dikonfirmasi selesai oleh pelapor',
            'diubah_oleh' => Auth::id(),
        ]);

        return redirect()->route('user.report.detail', $report->id)
            ->with('success', 'Laporan telah dikonfirmasi selesai. Terima kasih!');
    }

    /**
     * FR-US-09: Rating dan Feedback (Ulasan)
     */
    public function submitFeedback(Request $request, $id)
    {
        $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
        ]);

        $laporan = Laporan::where('pengguna_id', Auth::id())->findOrFail($id);

        Ulasan::create([
            'laporan_id'  => $laporan->id,
            'pengguna_id' => Auth::id(),
            'nilai'       => $request->rating,
            'komentar'    => $request->comment,
        ]);

        return redirect()->route('user.report.detail', $laporan->id)
            ->with('success', 'Terima kasih atas ulasan Anda!');
    }

    /**
     * FR-US-10: Riwayat Laporan
     */
    public function reports(Request $request)
    {
        $query = Auth::user()->laporan()->with(['kategori', 'wilayah']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('kategori_id', $request->category);
        }

        $reports = $query->latest()->paginate(10);
        $categories = Kategori::all();

        return view('user.reports', compact('reports', 'categories'));
    }

    /**
     * FR-US-11: Profil - tampilkan form
     */
    public function profile()
    {
        return view('user.profile', ['user' => Auth::user()]);
    }

    /**
     * FR-US-11: Profil - update
     */
    public function updateProfile(Request $request)
    {
        $pengguna = Auth::user();

        $request->validate([
            'name'   => ['required', 'string', 'max:100'],
            'phone'  => ['required', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = [
            'nama'    => $request->name,
            'telepon' => $request->phone,
        ];

        if ($request->hasFile('avatar')) {
            if ($pengguna->foto_profil) {
                Storage::disk('public')->delete($pengguna->foto_profil);
            }
            $data['foto_profil'] = $request->file('avatar')->store('foto_profil', 'public');
        }

        $pengguna->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * FR-US-11: Ubah Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ]);

        $pengguna = Auth::user();

        if (!Hash::check($request->current_password, $pengguna->kata_sandi)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $pengguna->update([
            'kata_sandi' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui!');
    }

    /**
     * FR-US-XX: Notifikasi
     */
    public function notifications()
    {
        $notifications = Auth::user()->notifikasi()->latest('dibuat_pada')->paginate(15);
        return view('user.notifications', compact('notifications'));
    }

    /**
     * FR-US-XX: Baca Notifikasi & Redirect
     */
    public function readNotification($id)
    {
        $notif = Auth::user()->notifikasi()->findOrFail($id);
        
        // Tandai sebagai sudah dibaca
        $notif->update(['sudah_dibaca' => true]);

        // Coba cari kode laporan (misal: TR-20230912-A1B2) dari dalam judul atau pesan
        $textToSearch = $notif->judul . ' ' . $notif->pesan;
        preg_match('/TR-\d{8}-[A-Z0-9]+/', $textToSearch, $matches);
        
        if (!empty($matches)) {
            $laporan = Laporan::where('kode_laporan', $matches[0])->first();
            if ($laporan) {
                return redirect()->route('user.report.detail', $laporan->id);
            }
        }

        // Kalau tidak ada kode spesifik atau data hilang, redirect ke halaman riwayat laporan umum dengan pesan
        return redirect()->route('user.reports')->with('error', 'Tidak dapat membuka detail: Laporan tidak ditemukan atau telah dihapus.');
    }
}
