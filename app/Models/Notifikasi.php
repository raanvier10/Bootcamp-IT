<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';

    public $timestamps = false;

    protected $fillable = [
        'pengguna_id',
        'judul',
        'pesan',
        'tipe',
        'sudah_dibaca',
    ];

    protected $casts = [
        'sudah_dibaca' => 'boolean',
        'dibuat_pada' => 'datetime',
    ];

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function scopeBelumDibaca($query)
    {
        return $query->where('sudah_dibaca', false);
    }

    // Alias kompatibilitas
    public function scopeUnread($query) { return $this->scopeBelumDibaca($query); }
    public function getTitleAttribute(): string { return $this->judul; }
    public function getMessageAttribute(): string { return $this->pesan; }
    public function getIsReadAttribute(): bool { return $this->sudah_dibaca; }
    public function getCreatedAtAttribute() { return $this->dibuat_pada; }
}
