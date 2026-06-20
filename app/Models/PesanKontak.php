<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesanKontak extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'pesan_kontak';

    protected $fillable = [
        'nama',
        'email',
        'subjek',
        'pesan',
    ];

    protected $casts = [
        'dibuat_pada' => 'datetime',
    ];

    // Alias kompatibilitas
    public function getNameAttribute(): string { return $this->nama; }
    public function getSubjectAttribute(): string { return $this->subjek; }
    public function getMessageAttribute(): string { return $this->pesan; }
    public function getCreatedAtAttribute() { return $this->dibuat_pada; }
}
