<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notifikasi;
use Carbon\Carbon;

class DummyNotifSeeder extends Seeder
{
    public function run()
    {
        // Delete old dummy notifications so they don't stack up infinitely
        Notifikasi::where('judul', 'like', '%Laporan%')
            ->orWhere('judul', 'like', '%Petugas%')
            ->orWhere('judul', 'like', '%Peringatan%')
            ->delete();

        // Get all users
        $users = User::all();

        foreach ($users as $user) {
            Notifikasi::create([
                'pengguna_id' => $user->id,
                'judul' => 'Laporan Berhasil Terkirim',
                'pesan' => 'Laporan tumpukan sampah kamu di wilayah Cikampek telah kami terima dan masuk ke antrean verifikasi.',
                'tipe' => 'info',
                'sudah_dibaca' => false,
                'dibuat_pada' => Carbon::now()->subHours(24)
            ]);

            // Ambil contoh laporan asli dari DB agar link notifikasi bisa diklik dan bekerja
            $laporanSatu = \App\Models\Laporan::first();
            $laporanDua = \App\Models\Laporan::skip(1)->first() ?? $laporanSatu;
            $kodeSatu = $laporanSatu ? $laporanSatu->kode_laporan : 'TR-20260619-4DC1';
            $kodeDua = $laporanDua ? $laporanDua->kode_laporan : 'TR-20260610-AABB';

            Notifikasi::create([
                'pengguna_id' => $user->id,
                'judul' => 'Petugas Dalam Perjalanan',
                'pesan' => "Petugas kami sedang menuju lokasi untuk membersihkan laporan sampah {$kodeSatu}.",
                'tipe' => 'warning',
                'sudah_dibaca' => true,
                'dibuat_pada' => Carbon::now()->subHours(2)
            ]);

            Notifikasi::create([
                'pengguna_id' => $user->id,
                'judul' => 'Laporan Selesai Dibersihkan!',
                'pesan' => 'Yeay! Lokasi sampah yang kamu laporkan sudah bersih. Yuk cek foto hasilnya dan jangan lupa beri rating untuk petugas!',
                'tipe' => 'success',
                'sudah_dibaca' => false,
                'dibuat_pada' => Carbon::now()->subMinutes(15)
            ]);
            
            Notifikasi::create([
                'pengguna_id' => $user->id,
                'judul' => 'Peringatan Sistem',
                'pesan' => "Laporan {$kodeDua} ditolak karena foto tidak menunjukkan tumpukan sampah yang valid.",
                'tipe' => 'error',
                'sudah_dibaca' => false,
                'dibuat_pada' => Carbon::now()->subMinutes(5)
            ]);

            echo "Berhasil menambahkan 4 notifikasi dummy untuk user: {$user->nama} (ID: {$user->id})\n";
        }
    }
}
