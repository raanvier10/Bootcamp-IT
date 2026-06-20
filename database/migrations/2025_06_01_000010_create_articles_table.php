<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artikel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penulis_id')->constrained('pengguna')->onDelete('cascade');
            $table->string('judul', 200);
            $table->string('slug', 255)->unique();
            $table->string('gambar_sampul', 255)->nullable();
            $table->longText('isi');
            $table->boolean('sudah_diterbitkan')->default(false);
            $table->timestamp('diterbitkan_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artikel');
    }
};
