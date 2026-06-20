<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penugasan extends Model
{
    use HasFactory;

    protected $table = 'penugasan';

    protected $fillable = [
        'laporan_id',
        'petugas_id',
        'ditugaskan_oleh',
        'ditugaskan_pada',
        'diselesaikan_pada',
    ];

    protected $casts = [
        'ditugaskan_pada' => 'datetime',
        'diselesaikan_pada' => 'datetime',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function ditugaskanOleh()
    {
        return $this->belongsTo(User::class, 'ditugaskan_oleh');
    }

    // Alias kompatibilitas
    public function officer() { return $this->petugas(); }
    public function assignedByUser() { return $this->ditugaskanOleh(); }
}
