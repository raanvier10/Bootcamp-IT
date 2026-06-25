<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'ulasan';

    protected $fillable = [
        'laporan_id',
        'user_id',
        'nilai',
        'komentar',
    ];

    protected $casts = [
        'nilai' => 'integer',
        'dibuat_pada' => 'datetime',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Alias kompatibilitas
    public function getRatingAttribute(): int { return $this->nilai; }
    public function getCommentAttribute(): ?string { return $this->komentar; }
    public function getCreatedAtAttribute() { return $this->dibuat_pada; }
}
