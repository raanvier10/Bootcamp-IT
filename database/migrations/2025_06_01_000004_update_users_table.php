<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration ini dikosongkan karena tabel pengguna sudah lengkap di create_users_table.php
return new class extends Migration
{
    public function up(): void
    {
        // Semua kolom sudah ada di 2014_10_12_000000_create_users_table.php
    }

    public function down(): void
    {
        // Tidak ada yang perlu di-rollback
    }
};
