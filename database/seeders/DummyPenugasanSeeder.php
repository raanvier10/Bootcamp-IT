<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyPenugasanSeeder extends Seeder
{
    public function run(): void
    {
        $petugasId = 3; // ID Petugas Karawang Barat
        $penggunaId = 2; // Budi Santoso
        $adminId = 1;

        // Laporan 1: Baru (Ditugaskan)
        $laporan1Id = DB::table('laporan')->insertGetId([
            'kode_laporan'    => 'TR-' . date('Ymd') . '-DUM1',
            'pengguna_id'     => $penggunaId,
            'wilayah_id'      => 1,
            'kategori_id'     => 1, // Plastik
            'judul'           => 'Tumpukan Sampah Plastik di Taman Kota',
            'deskripsi'       => 'Sisa acara malam minggu dibiarkan menumpuk, dominan botol dan gelas plastik.',
            'lintang'         => -6.3050000,
            'bujur'           => 107.2950000,
            'alamat'          => 'Taman I Love Karawang',
            'prioritas'       => 'Tinggi',
            'status'          => 'Ditugaskan',
            'dilaporkan_pada' => now()->subHours(2),
            'created_at'      => now()->subHours(2),
            'updated_at'      => now()->subHours(1),
        ]);

        DB::table('penugasan')->insert([
            'laporan_id'       => $laporan1Id,
            'petugas_id'       => $petugasId,
            'ditugaskan_oleh'  => $adminId,
            'ditugaskan_pada'  => now()->subHours(1),
            'created_at'       => now()->subHours(1),
            'updated_at'       => now()->subHours(1),
        ]);

        // Laporan 2: Sedang Dikerjakan (Dalam Perjalanan)
        $laporan2Id = DB::table('laporan')->insertGetId([
            'kode_laporan'    => 'TR-' . date('Ymd') . '-DUM2',
            'pengguna_id'     => $penggunaId,
            'wilayah_id'      => 1,
            'kategori_id'     => 7, // Sampah Besar
            'judul'           => 'Puing Bangunan dan Kayu',
            'deskripsi'       => 'Ada yang membuang sisa renovasi rumah di pinggir saluran air.',
            'lintang'         => -6.3150000,
            'bujur'           => 107.3000000,
            'alamat'          => 'Jl. Niaga, Saluran Air',
            'prioritas'       => 'Mendesak',
            'status'          => 'Dalam Perjalanan',
            'dilaporkan_pada' => now()->subDays(1),
            'created_at'      => now()->subDays(1),
            'updated_at'      => now()->subHours(5),
        ]);

        DB::table('penugasan')->insert([
            'laporan_id'       => $laporan2Id,
            'petugas_id'       => $petugasId,
            'ditugaskan_oleh'  => $adminId,
            'ditugaskan_pada'  => now()->subHours(5),
            'created_at'       => now()->subHours(5),
            'updated_at'       => now()->subHours(5),
        ]);

        // Laporan 3: Sedang Dikerjakan (Sedang Dibersihkan)
        $laporan3Id = DB::table('laporan')->insertGetId([
            'kode_laporan'    => 'TR-' . date('Ymd') . '-DUM3',
            'pengguna_id'     => $penggunaId,
            'wilayah_id'      => 1,
            'kategori_id'     => 2, // Organik
            'judul'           => 'Sampah Pasar Menggunung',
            'deskripsi'       => 'Daun dan sayur busuk menumpuk dan bau menyengat.',
            'lintang'         => -6.3120000,
            'bujur'           => 107.3020000,
            'alamat'          => 'Belakang Pasar Karawang',
            'prioritas'       => 'Sedang',
            'status'          => 'Sedang Dibersihkan',
            'dilaporkan_pada' => now()->subDays(2),
            'created_at'      => now()->subDays(2),
            'updated_at'      => now()->subHours(10),
        ]);

        DB::table('penugasan')->insert([
            'laporan_id'       => $laporan3Id,
            'petugas_id'       => $petugasId,
            'ditugaskan_oleh'  => $adminId,
            'ditugaskan_pada'  => now()->subDays(1),
            'created_at'       => now()->subDays(1),
            'updated_at'       => now()->subDays(1),
        ]);

        // Laporan 4: Selesai
        $laporan4Id = DB::table('laporan')->insertGetId([
            'kode_laporan'    => 'TR-' . date('Ymd') . '-DUM4',
            'pengguna_id'     => $penggunaId,
            'wilayah_id'      => 1,
            'kategori_id'     => 3, // Campuran
            'judul'           => 'Tong Sampah Terguling',
            'deskripsi'       => 'Tong sampah besar terguling dan isinya berserakan ke jalan raya.',
            'lintang'         => -6.3200000,
            'bujur'           => 107.3100000,
            'alamat'          => 'Jl. By Pass, Karawang',
            'prioritas'       => 'Rendah',
            'status'          => 'Selesai',
            'dilaporkan_pada' => now()->subDays(5),
            'created_at'      => now()->subDays(5),
            'updated_at'      => now()->subDays(3),
        ]);

        DB::table('penugasan')->insert([
            'laporan_id'       => $laporan4Id,
            'petugas_id'       => $petugasId,
            'ditugaskan_oleh'  => $adminId,
            'ditugaskan_pada'  => now()->subDays(4),
            'diselesaikan_pada'=> now()->subDays(3),
            'created_at'       => now()->subDays(4),
            'updated_at'       => now()->subDays(3),
        ]);

        // Laporan 5: Selesai
        $laporan5Id = DB::table('laporan')->insertGetId([
            'kode_laporan'    => 'TR-' . date('Ymd') . '-DUM5',
            'pengguna_id'     => $penggunaId,
            'wilayah_id'      => 1,
            'kategori_id'     => 5, // Rumah Tangga
            'judul'           => 'Pembuangan Liar di Tanah Kosong',
            'deskripsi'       => 'Warga sering buang sampah rumah tangga di tanah kosong ini.',
            'lintang'         => -6.3250000,
            'bujur'           => 107.3150000,
            'alamat'          => 'Komplek Perum Indah',
            'prioritas'       => 'Tinggi',
            'status'          => 'Selesai',
            'dilaporkan_pada' => now()->subDays(7),
            'created_at'      => now()->subDays(7),
            'updated_at'      => now()->subDays(5),
        ]);

        DB::table('penugasan')->insert([
            'laporan_id'       => $laporan5Id,
            'petugas_id'       => $petugasId,
            'ditugaskan_oleh'  => $adminId,
            'ditugaskan_pada'  => now()->subDays(6),
            'diselesaikan_pada'=> now()->subDays(5),
            'created_at'       => now()->subDays(6),
            'updated_at'       => now()->subDays(5),
        ]);

        // Tambah Riwayat Status (Opsional, agar terlihat realistis jika dicek pelapor)
        DB::table('riwayat_status_laporan')->insert([
            [
                'laporan_id'  => $laporan1Id,
                'status'      => 'Ditugaskan',
                'catatan'     => 'Tugas telah didelegasikan ke Petugas Lapangan.',
                'diubah_oleh' => $adminId,
                'dibuat_pada' => now()->subHours(1),
            ],
            [
                'laporan_id'  => $laporan2Id,
                'status'      => 'Dalam Perjalanan',
                'catatan'     => 'Petugas sedang menuju lokasi pelaporan.',
                'diubah_oleh' => $petugasId,
                'dibuat_pada' => now()->subHours(2),
            ],
            [
                'laporan_id'  => $laporan3Id,
                'status'      => 'Sedang Dibersihkan',
                'catatan'     => 'Petugas sedang membersihkan sampah.',
                'diubah_oleh' => $petugasId,
                'dibuat_pada' => now()->subHours(5),
            ]
        ]);
    }
}
