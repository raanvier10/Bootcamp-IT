<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_laporan', 30)->unique();
            $table->foreignId('pengguna_id')->constrained('pengguna')->onDelete('cascade');
            $table->foreignId('wilayah_id')->constrained('wilayah')->onDelete('restrict');
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('restrict');
            $table->string('judul', 150);
            $table->text('deskripsi');
            $table->decimal('lintang', 10, 7);
            $table->decimal('bujur', 10, 7);
            $table->text('alamat');
            $table->enum('prioritas', ['Rendah', 'Sedang', 'Tinggi', 'Mendesak'])->default('Rendah');
            $table->enum('status', [
                'Menunggu',
                'Terverifikasi',
                'Ditolak',
                'Ditugaskan',
                'Dalam Perjalanan',
                'Sedang Dibersihkan',
                'Selesai',
                'Menunggu Konfirmasi',
                'Ditutup',
            ])->default('Menunggu');
            $table->text('alasan_penolakan')->nullable();
            $table->timestamp('dilaporkan_pada')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
