<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'peran',
        'wilayah_id',
        'telepon',
        'foto_profil',
        'kode_pegawai',
        'aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ── Relationships ──

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'user_id');
    }

    public function tugas()
    {
        return $this->hasMany(Laporan::class, 'petugas_id');
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'user_id');
    }

    public function artikel()
    {
        return $this->hasMany(Artikel::class, 'penulis_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }

    // ── Role Helpers ──

    public function isAdmin(): bool
    {
        return strtolower($this->peran) === 'admin';
    }

    public function isPengguna(): bool
    {
        return strtolower($this->peran) === 'pelapor';
    }

    public function isPetugas(): bool
    {
        return strtolower($this->peran) === 'petugas';
    }

    public function isUser(): bool { return $this->isPengguna(); }
    public function isOfficer(): bool { return $this->isPetugas(); }

    public function getFotoProfilUrlAttribute(): string
    {
        if ($this->foto_profil) {
            return asset('storage/' . $this->foto_profil);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=16a34a&color=fff&size=128';
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->foto_profil_url;
    }
}
