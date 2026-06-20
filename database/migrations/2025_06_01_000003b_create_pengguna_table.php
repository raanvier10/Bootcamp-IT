<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peran_id')->constrained('peran')->onDelete('restrict');
            $table->foreignId('wilayah_id')->nullable()->constrained('wilayah')->onDelete('set null');
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->timestamp('email_diverifikasi_pada')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('kata_sandi', 255);
            $table->string('foto_profil', 255)->nullable();
            $table->string('kode_pegawai', 50)->nullable();
            $table->boolean('aktif')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
