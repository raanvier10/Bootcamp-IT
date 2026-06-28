<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalLaporan = \App\Models\Laporan::count();
        $perluVerifikasi = \App\Models\Laporan::where('status', 'Menunggu')->count();
        $petugasAktif = \App\Models\User::where('peran', 'Petugas')->where('aktif', true)->count();
        $laporanSelesai = \App\Models\Laporan::where('status', 'Selesai')->count();

        $laporanTerbaru = \App\Models\Laporan::with(['user', 'wilayah'])
            ->where('status', 'Menunggu')
            ->latest('dilaporkan_pada')
            ->take(5)
            ->get();

        // FR-AD-12 Alarm Prediksi TPS Liar Baru
        $reportsLast30Days = \App\Models\Laporan::where('dilaporkan_pada', '>=', now()->subDays(30))->get();
        $tpsLiarAlarms = collect();
        $processedIds = [];

        foreach ($reportsLast30Days as $report) {
            if (in_array($report->id, $processedIds)) continue;

            $nearbyCount = 0;
            $nearbyReports = [];

            foreach ($reportsLast30Days as $otherReport) {
                $earthRadius = 6371000;
                $latFrom = deg2rad((float)$report->lintang);
                $lonFrom = deg2rad((float)$report->bujur);
                $latTo = deg2rad((float)$otherReport->lintang);
                $lonTo = deg2rad((float)$otherReport->bujur);

                $latDelta = $latTo - $latFrom;
                $lonDelta = $lonTo - $lonFrom;

                $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
                $distance = $angle * $earthRadius;

                if ($distance <= 50) {
                    $nearbyCount++;
                    $nearbyReports[] = $otherReport;
                }
            }

            // Jika > 3 kali dalam sebulan (berarti >= 3 laporan berdekatan)
            if ($nearbyCount >= 3) {
                $tpsLiarAlarms->push((object)[
                    'center_report' => $report,
                    'count' => $nearbyCount,
                    'reports' => $nearbyReports
                ]);
                
                // Mark all nearby as processed so we don't duplicate the alarm for the same area
                foreach ($nearbyReports as $nr) {
                    $processedIds[] = $nr->id;
                }
            }
        }

        return view('admin.dashboard', compact('totalLaporan', 'perluVerifikasi', 'petugasAktif', 'laporanSelesai', 'laporanTerbaru', 'tpsLiarAlarms'));
    }

    public function officers(Request $request)
    {
        $query = \App\Models\User::with(['wilayah', 'tugas.ulasan'])->where('peran', 'Petugas');
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_pegawai', 'like', '%' . $request->search . '%');
        }
        
        $officers = $query->paginate(15);
        $wilayahs = \App\Models\Wilayah::all();
            
        return view('admin.officers', compact('officers', 'wilayahs'));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'telepon' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->nama,
            'email' => $request->email,
            'telepon' => $request->telepon,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->letters()->numbers()->symbols()],
        ]);

        $user = auth()->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini salah.']);
        }

        $user->update(['password' => \Illuminate\Support\Facades\Hash::make($request->password)]);

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }

    public function storeOfficer(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', \Illuminate\Validation\Rules\Password::min(8)->letters()->numbers()->symbols()],
            'telepon' => 'nullable|string|max:20',
            'wilayah_id' => 'nullable|exists:wilayah,id',
            'aktif' => 'required|boolean',
        ]);

        $officer = \App\Models\User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'peran' => 'Petugas',
            'kode_pegawai' => 'PTG-' . strtoupper(substr(uniqid(), -5)),
            'telepon' => $request->telepon,
            'wilayah_id' => $request->wilayah_id,
            'aktif' => $request->aktif,
        ]);

        return redirect()->route('admin.officers')->with('success', 'Petugas baru berhasil ditambahkan.');
    }

    public function updateOfficer(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'nullable|string|max:20',
            'wilayah_id' => 'nullable|exists:wilayah,id',
            'aktif' => 'required|boolean',
        ]);

        $officer = \App\Models\User::where('peran', 'Petugas')->findOrFail($id);
        
        $officer->update([
            'name' => $request->nama,
            'telepon' => $request->telepon,
            'wilayah_id' => $request->wilayah_id,
            'aktif' => $request->aktif,
        ]);

        return redirect()->route('admin.officers')->with('success', 'Data petugas berhasil diperbarui.');
    }

    public function destroyOfficer($id)
    {
        $officer = \App\Models\User::where('peran', 'Petugas')->findOrFail($id);
        
        // Cek jika sudah pernah menangani laporan
        if (\App\Models\Laporan::where('petugas_id', $id)->exists()) {
            return back()->with('error', 'Petugas ini tidak dapat dihapus karena memiliki riwayat menangani laporan.');
        }

        $officer->delete();
        return back()->with('success', 'Data petugas berhasil dihapus.');
    }
}
