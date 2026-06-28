<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GambarLaporan extends Model
{
    use HasFactory;

    protected $table = 'gambar_laporan';

    public $timestamps = false;

    protected $fillable = [
        'laporan_id',
        'tipe_gambar',
        'jalur_gambar',
        'lintang',
        'bujur',
        'dibuat_pada',
    ];

    protected $casts = [
        'lintang' => 'decimal:7',
        'bujur' => 'decimal:7',
        'dibuat_pada' => 'datetime',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }

    // Alias kompatibilitas
    public function getImagePathAttribute(): string { return $this->jalur_gambar; }
    public function getImageTypeAttribute(): string { return $this->tipe_gambar; }
    public function getLatitudeAttribute() { return $this->lintang; }
    public function getLongitudeAttribute() { return $this->bujur; }
    public function getCreatedAtAttribute() { return $this->dibuat_pada; }
}
