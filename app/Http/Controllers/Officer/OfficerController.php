<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Penugasan;

class OfficerController extends Controller
{
    public function dashboard()
    {
        $petugasId = Auth::id();

        // Ambil statistik tugas
        $tugasBaru = Penugasan::where('petugas_id', $petugasId)
            ->whereHas('laporan', function ($q) {
                $q->whereIn('status', ['Terverifikasi', 'Ditugaskan']);
            })->count();

        $sedangDikerjakan = Penugasan::where('petugas_id', $petugasId)
            ->whereHas('laporan', function ($q) {
                $q->whereIn('status', ['Dalam Perjalanan', 'Sedang Dibersihkan']);
            })->count();

        $tugasSelesai = Penugasan::where('petugas_id', $petugasId)
            ->whereHas('laporan', function ($q) {
                $q->whereIn('status', ['Selesai', 'Ditutup']);
            })->count();

        $tugasTerbaru = Penugasan::with(['laporan', 'laporan.wilayah', 'laporan.kategori'])
            ->where('petugas_id', $petugasId)
            ->latest('ditugaskan_pada')
            ->take(5)
            ->get();

        return view('officer.dashboard', compact(
            'tugasBaru',
            'sedangDikerjakan',
            'tugasSelesai',
            'tugasTerbaru'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('officer.profile', compact('user'));
    }

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
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pengguna->foto_profil);
            }
            $data['foto_profil'] = $request->file('avatar')->store('foto_profil', 'public');
        }

        $pengguna->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ]);

        $pengguna = Auth::user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $pengguna->kata_sandi)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $pengguna->update([
            'kata_sandi' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui!');
    }

    public function notifications()
    {
        $notifications = Auth::user()->notifikasi()->latest('dibuat_pada')->paginate(15);
        return view('officer.notifications', compact('notifications'));
    }

    public function readNotification($id)
    {
        $notif = Auth::user()->notifikasi()->findOrFail($id);
        
        $notif->update(['sudah_dibaca' => true]);

        // Coba cari kode laporan (misal: TR-20230912-A1B2) dari dalam judul atau pesan
        $textToSearch = $notif->judul . ' ' . $notif->pesan;
        preg_match('/TR-\d{8}-[A-Z0-9]+/', $textToSearch, $matches);
        
        if (!empty($matches)) {
            $laporan = \App\Models\Laporan::where('kode_laporan', $matches[0])->first();
            if ($laporan) {
                $tugas = \App\Models\Penugasan::where('laporan_id', $laporan->id)
                            ->where('petugas_id', Auth::id())
                            ->first();
                if ($tugas) {
                    return redirect()->route('officer.tasks.show', $tugas->id);
                }
            }
        }

        return redirect()->route('officer.tasks')->with('error', 'Tidak dapat membuka detail: Tugas atau laporan tidak ditemukan.');
    }
}
