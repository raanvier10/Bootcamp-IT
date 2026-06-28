<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStatusLaporan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_status_laporan';

    public $timestamps = false;

    protected $fillable = [
        'laporan_id',
        'status',
        'catatan',
        'diubah_oleh',
        'dibuat_pada',
    ];

    protected $casts = [
        'dibuat_pada' => 'datetime',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    // Alias kompatibilitas
    public function changedByUser()
    {
        return $this->user();
    }

    public function getNoteAttribute(): ?string { return $this->catatan; }
    public function getCreatedAtAttribute() { return $this->dibuat_pada; }
}
