<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TrashReportSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Peran
        DB::table('peran')->insert([
            ['id' => 1, 'nama' => 'Admin',    'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama' => 'Pengguna', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama' => 'Petugas',  'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Wilayah
        DB::table('wilayah')->insert([
            ['id' => 1, 'kode' => 'KRW-BRT', 'nama' => 'Karawang Barat',   'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'kode' => 'KRW-TMR', 'nama' => 'Karawang Timur',   'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'kode' => 'KRW-SLT', 'nama' => 'Karawang Selatan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'kode' => 'KRW-UTR', 'nama' => 'Karawang Utara',   'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'kode' => 'CLM',     'nama' => 'Cikampek',         'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Kategori
        DB::table('kategori')->insert([
            ['id' => 1, 'nama' => 'Plastik',             'deskripsi' => 'Sampah plastik seperti botol, kantong, dan kemasan',            'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nama' => 'Organik',             'deskripsi' => 'Sampah organik seperti sisa makanan dan daun',                 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nama' => 'Campuran',            'deskripsi' => 'Campuran berbagai jenis sampah',                              'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'nama' => 'Limbah Medis',        'deskripsi' => 'Limbah medis, jarum suntik, dan benda tajam',                 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'nama' => 'Sampah Rumah Tangga', 'deskripsi' => 'Sampah dari aktivitas rumah tangga',                          'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'nama' => 'Sampah Pasar',        'deskripsi' => 'Sampah dari pasar tradisional',                               'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'nama' => 'Sampah Besar',        'deskripsi' => 'Sampah berukuran besar seperti furnitur dan elektronik',      'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. Admin
        DB::table('pengguna')->insert([
            'peran_id'               => 1,
            'nama'                   => 'Admin TrashReport',
            'email'                  => 'admin@trashreport.id',
            'telepon'                => '081234567890',
            'kata_sandi'             => Hash::make('password'),
            'aktif'                  => true,
            'email_diverifikasi_pada' => now(),
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        // 5. Demo Pengguna
        DB::table('pengguna')->insert([
            'peran_id'               => 2,
            'nama'                   => 'Budi Santoso',
            'email'                  => 'budi@email.com',
            'telepon'                => '081298765432',
            'kata_sandi'             => Hash::make('password'),
            'aktif'                  => true,
            'email_diverifikasi_pada' => now(),
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        // 6. Demo Petugas
        DB::table('pengguna')->insert([
            'peran_id'               => 3,
            'wilayah_id'             => 1,
            'nama'                   => 'Petugas Karawang Barat',
            'email'                  => 'petugas.krwbrt@trashreport.id',
            'telepon'                => '081311223344',
            'kata_sandi'             => Hash::make('password'),
            'kode_pegawai'           => 'PTG-001',
            'aktif'                  => true,
            'email_diverifikasi_pada' => now(),
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        // 7. Artikel edukasi
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
            [
                'penulis_id'        => 1,
                'judul'             => 'Mengenal Konsep Zero Waste untuk Kehidupan Sehari-hari',
                'slug'              => 'mengenal-konsep-zero-waste',
                'isi'               => '<p>Konsep zero waste atau nol sampah adalah filosofi yang mendorong kita untuk mengurangi produksi sampah hingga seminimal mungkin.</p><h2>Prinsip 5R</h2><p>Refuse (menolak), Reduce (mengurangi), Reuse (menggunakan kembali), Recycle (mendaur ulang), dan Rot (mengompos) adalah lima prinsip utama zero waste.</p>',
                'sudah_diterbitkan' => true,
                'diterbitkan_pada'  => now()->subDays(3),
                'created_at'        => now()->subDays(3),
                'updated_at'        => now()->subDays(3),
            ],
        ]);

        // Data laporan, penugasan, dan ulasan sengaja dikosongkan 
        // agar pengguna dapat mengisi data sendiri melalui aplikasi.
    }
}
