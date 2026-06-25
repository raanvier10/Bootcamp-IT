<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TrashReportSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Wilayah
        DB::table('wilayah')->insert([
            ['id' => 1, 'kode' => 'KRW-BRT', 'nama' => 'Karawang Barat',   'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'kode' => 'KRW-TMR', 'nama' => 'Karawang Timur',   'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'kode' => 'KRW-SLT', 'nama' => 'Karawang Selatan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'kode' => 'KRW-UTR', 'nama' => 'Karawang Utara',   'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'kode' => 'CLM',     'nama' => 'Cikampek',         'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Kategori
        DB::table('kategori')->insert([
            ['id' => 1, 'nama' => 'Plastik',             'deskripsi' => 'Sampah plastik seperti botol, kantong, dan kemasan',            'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama' => 'Organik',             'deskripsi' => 'Sampah organik seperti sisa makanan dan daun',                 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama' => 'Campuran',            'deskripsi' => 'Campuran berbagai jenis sampah',                              'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama' => 'Limbah Medis',        'deskripsi' => 'Limbah medis, jarum suntik, dan benda tajam',                 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nama' => 'Sampah Rumah Tangga', 'deskripsi' => 'Sampah dari aktivitas rumah tangga',                          'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nama' => 'Sampah Pasar',        'deskripsi' => 'Sampah dari pasar tradisional',                               'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'nama' => 'Sampah Besar',        'deskripsi' => 'Sampah berukuran besar seperti furnitur dan elektronik',      'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Admin
        DB::table('users')->insert([
            'id'                     => 1,
            'peran'                  => 'Admin',
            'name'                   => 'Admin TrashReport',
            'email'                  => 'admin@trashreport.id',
            'telepon'                => '081234567890',
            'password'               => Hash::make('password'),
            'email_verified_at'      => now(),
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        // 4. Demo Pengguna
        DB::table('users')->insert([
            'id'                     => 2,
            'peran'                  => 'Pelapor',
            'name'                   => 'Budi Santoso',
            'email'                  => 'budi@email.com',
            'telepon'                => '081298765432',
            'password'               => Hash::make('password'),
            'email_verified_at'      => now(),
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        // 5. Demo Petugas
        DB::table('users')->insert([
            'id'                     => 3,
            'peran'                  => 'Petugas',
            'wilayah_id'             => 1,
            'name'                   => 'Petugas Karawang Barat',
            'email'                  => 'petugas.krwbrt@trashreport.id',
            'telepon'                => '081311223344',
            'kode_pegawai'           => 'PTG-DEMO',
            'aktif'                  => true,
            'password'               => Hash::make('password'),
            'email_verified_at'      => now(),
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        // 6. Artikel edukasi
        DB::table('artikel')->insert([
            [
                'penulis_id'        => 1,
                'judul'             => 'Cara Mudah Memilah Sampah di Rumah',
                'slug'              => 'cara-mudah-memilah-sampah-di-rumah',
                'isi'               => '<p>Memilah sampah di rumah adalah langkah kecil yang memberikan dampak besar bagi lingkungan.</p><h2>1. Siapkan Tempat Sampah Terpisah</h2><p>Sediakan minimal 3 tempat sampah berbeda untuk sampah organik (sisa makanan, daun), anorganik (plastik, logam, kaca), dan sampah B3 (baterai, lampu neon).</p><h2>2. Kenali Jenis Sampah</h2><p>Sampah organik mudah terurai secara alami, sementara sampah anorganik membutuhkan proses daur ulang khusus.</p>',
                'sudah_diterbitkan' => true,
                'diterbitkan_pada'  => now()->subDays(10),
                'created_at'        => now()->subDays(10),
                'updated_at'        => now()->subDays(10),
            ],
            [
                'penulis_id'        => 1,
                'judul'             => 'Bahaya Sampah Liar bagi Kesehatan Masyarakat',
                'slug'              => 'bahaya-sampah-liar-bagi-kesehatan-masyarakat',
                'isi'               => '<p>Sampah liar yang dibiarkan menumpuk tanpa penanganan serius dapat menimbulkan berbagai masalah kesehatan serius.</p><h2>Penyakit yang Ditimbulkan</h2><p>Tumpukan sampah menjadi tempat berkembang biak nyamuk, tikus, dan lalat yang membawa penyakit seperti demam berdarah, leptospirosis, dan diare.</p><h2>Apa yang Bisa Kita Lakukan?</h2><p>Laporkan titik-titik sampah liar melalui TrashReport.</p>',
                'sudah_diterbitkan' => true,
                'diterbitkan_pada'  => now()->subDays(7),
                'created_at'        => now()->subDays(7),
                'updated_at'        => now()->subDays(7),
            ],
        ]);

        // 7. Dummy Laporan
        $kodeLaporan = 'TR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));
        DB::table('laporan')->insert([
            'id'               => 1,
            'kode_laporan'     => $kodeLaporan,
            'user_id'          => 2,
            'petugas_id'       => 3,
            'wilayah_id'       => 1,
            'kategori_id'      => 1,
            'judul'            => 'Tumpukan Sampah Plastik di Jalan Baru',
            'deskripsi'        => 'Banyak sampah plastik menumpuk di pinggir jalan dekat pasar.',
            'lintang'          => -6.3000000,
            'bujur'            => 107.3000000,
            'alamat'           => 'Jalan Baru Karawang Barat',
            'prioritas'        => 'Sedang',
            'status'           => 'Ditugaskan',
            'alasan_penolakan' => null,
            'dilaporkan_pada'  => now()->subDays(1),
            'created_at'       => now()->subDays(1),
            'updated_at'       => now()->subDays(1),
        ]);

        // 8. Riwayat Status Laporan (Log Penugasan)
        DB::table('riwayat_status_laporan')->insert([
            [
                'laporan_id'  => 1,
                'status'      => 'Menunggu',
                'catatan'     => 'Laporan baru dibuat',
                'diubah_oleh' => 2, // Pelapor
                'dibuat_pada' => now()->subDays(1),
            ],
            [
                'laporan_id'  => 1,
                'status'      => 'Ditugaskan',
                'catatan'     => 'Ditugaskan ke Petugas Karawang Barat',
                'diubah_oleh' => 1, // Admin
                'dibuat_pada' => now(),
            ]
        ]);
    }
}
