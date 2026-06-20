<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Artikel extends Model
{
    use HasFactory;

    protected $table = 'artikel';

    protected $fillable = [
        'penulis_id',
        'judul',
        'slug',
        'gambar_sampul',
        'isi',
        'sudah_diterbitkan',
        'diterbitkan_pada',
    ];

    protected $casts = [
        'sudah_diterbitkan' => 'boolean',
        'diterbitkan_pada' => 'datetime',
    ];

    public function penulis()
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }

    // Alias kompatibilitas
    public function author()
    {
        return $this->penulis();
    }

    /**
     * Scope hanya artikel yang sudah diterbitkan
     */
    public function scopeDiterbitkan($query)
    {
        return $query->where('sudah_diterbitkan', true)->whereNotNull('diterbitkan_pada');
    }

    // Alias lama
    public function scopePublished($query)
    {
        return $this->scopeDiterbitkan($query);
    }

    /**
     * Auto-generate slug dari judul
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function ($artikel) {
            if (empty($artikel->slug)) {
                $artikel->slug = Str::slug($artikel->judul);
            }
        });
    }

    /**
     * Kutipan isi artikel
     */
    public function getKutipanAttribute(): string
    {
        return Str::limit(strip_tags($this->isi), 120);
    }

    // Alias kompatibilitas
    public function getExcerptAttribute(): string { return $this->kutipan; }
    public function getTitleAttribute(): string { return $this->judul; }
    public function getContentAttribute(): string { return $this->isi; }
    public function getThumbnailAttribute(): ?string { return $this->gambar_sampul; }
    public function getIsPublishedAttribute(): bool { return $this->sudah_diterbitkan; }
    public function getPublishedAtAttribute() { return $this->diterbitkan_pada; }
    public function getAuthorIdAttribute(): int { return $this->penulis_id; }
}
