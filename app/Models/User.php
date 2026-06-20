<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Tabel menggunakan nama 'pengguna'
    protected $table = 'pengguna';

    // Kolom primary key untuk autentikasi (password)
    protected $authPasswordName = 'kata_sandi';

    protected $fillable = [
        'peran_id',
        'wilayah_id',
        'nama',
        'email',
        'telepon',
        'kata_sandi',
        'foto_profil',
        'kode_pegawai',
        'aktif',
    ];

    protected $hidden = [
        'kata_sandi',
        'remember_token',
    ];

    protected $casts = [
        'email_diverifikasi_pada' => 'datetime',
        'aktif' => 'boolean',
    ];

    // Override kolom timestamp email verified
    public function getEmailVerifiedAtAttribute()
    {
        return $this->email_diverifikasi_pada;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->email_diverifikasi_pada = $value;
    }

    // ── Relationships ──

    public function peran()
    {
        return $this->belongsTo(Peran::class, 'peran_id');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'pengguna_id');
    }

    public function penugasan()
    {
        return $this->hasMany(Penugasan::class, 'petugas_id');
    }

    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'pengguna_id');
    }

    public function artikel()
    {
        return $this->hasMany(Artikel::class, 'penulis_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'pengguna_id');
    }

    // ── Role Helpers ──

    public function isAdmin(): bool
    {
        return $this->peran && strtolower($this->peran->nama) === 'admin';
    }

    public function isPengguna(): bool
    {
        return $this->peran && strtolower($this->peran->nama) === 'pengguna';
    }

    public function isPetugas(): bool
    {
        return $this->peran && strtolower($this->peran->nama) === 'petugas';
    }

    // Alias lama untuk kompatibilitas
    public function isUser(): bool { return $this->isPengguna(); }
    public function isOfficer(): bool { return $this->isPetugas(); }

    /**
     * Get the user's photo URL or default avatar
     */
    public function getFotoProfilUrlAttribute(): string
    {
        if ($this->foto_profil) {
            return asset('storage/' . $this->foto_profil);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama) . '&background=16a34a&color=fff&size=128';
    }

    // Alias untuk kompatibilitas dengan kode lama yang pakai avatar_url
    public function getAvatarUrlAttribute(): string
    {
        return $this->foto_profil_url;
    }

    // Override getAuthPassword untuk pakai kolom kata_sandi
    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }
}
