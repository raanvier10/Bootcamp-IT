<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penugasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan')->onDelete('cascade');
            $table->foreignId('petugas_id')->constrained('pengguna')->onDelete('cascade');
            $table->foreignId('ditugaskan_oleh')->constrained('pengguna')->onDelete('cascade');
            $table->timestamp('ditugaskan_pada')->useCurrent();
            $table->timestamp('diselesaikan_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan');
    }
};
