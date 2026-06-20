<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';

    protected $fillable = [
        'kode_laporan',
        'pengguna_id',
        'wilayah_id',
        'kategori_id',
        'judul',
        'deskripsi',
        'lintang',
        'bujur',
        'alamat',
        'prioritas',
        'status',
        'alasan_penolakan',
        'dilaporkan_pada',
    ];

    protected $casts = [
        'lintang' => 'decimal:7',
        'bujur' => 'decimal:7',
        'dilaporkan_pada' => 'datetime',
    ];

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function gambar()
    {
        return $this->hasMany(GambarLaporan::class, 'laporan_id');
    }

    public function gambarSebelum()
    {
        return $this->hasMany(GambarLaporan::class, 'laporan_id')->where('tipe_gambar', 'sebelum');
    }

    public function gambarSesudah()
    {
        return $this->hasMany(GambarLaporan::class, 'laporan_id')->where('tipe_gambar', 'sesudah');
    }

    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatusLaporan::class, 'laporan_id')->orderBy('dibuat_pada', 'desc');
    }

    public function penugasan()
    {
        return $this->hasOne(Penugasan::class, 'laporan_id')->latest();
    }

    public function semuaPenugasan()
    {
        return $this->hasMany(Penugasan::class, 'laporan_id');
    }

    public function ulasan()
    {
        return $this->hasOne(Ulasan::class, 'laporan_id');
    }

    /**
     * Generate kode laporan unik
     */
    public static function buatKode(): string
    {
        $prefix = 'TR';
        $tanggal = now()->format('Ymd');
        $acak = strtoupper(substr(md5(uniqid()), 0, 4));
        return "{$prefix}-{$tanggal}-{$acak}";
    }

    /**
     * Label status (sudah bahasa Indonesia dari database)
     */
    public function getLabelStatusAttribute(): string
    {
        return $this->status;
    }

    /**
     * CSS class badge status
     */
    public function getKelasBadgeStatusAttribute(): string
    {
        return match($this->status) {
            'Menunggu' => 'badge-pending',
            'Terverifikasi', 'Ditugaskan' => 'badge-verified',
            'Dalam Perjalanan', 'Sedang Dibersihkan' => 'badge-processing',
            'Selesai', 'Ditutup' => 'badge-completed',
            'Ditolak' => 'badge-rejected',
            'Menunggu Konfirmasi' => 'badge-pending',
            default => 'badge-pending',
        };
    }

    /**
     * Label prioritas (sudah bahasa Indonesia dari database)
     */
    public function getLabelPrioritasAttribute(): string
    {
        return $this->prioritas;
    }

    // ── Alias kompatibilitas lama (untuk blade yang masih pakai nama lama) ──
    public function getStatusLabelAttribute(): string { return $this->label_status; }
    public function getStatusBadgeClassAttribute(): string { return $this->kelas_badge_status; }
    public function getPriorityLabelAttribute(): string { return $this->label_prioritas; }
    public function getUserAttribute() { return $this->pengguna; }
    public function getDistrictAttribute() { return $this->wilayah; }
    public function getCategoryAttribute() { return $this->kategori; }
    public function getBeforeImagesAttribute() { return $this->gambarSebelum; }
    public function getAfterImagesAttribute() { return $this->gambarSesudah; }
    public function getStatusHistoriesAttribute() { return $this->riwayatStatus; }
    public function getAssignmentAttribute() { return $this->penugasan; }
    public function getFeedbackAttribute() { return $this->ulasan; }
    public function getReportCodeAttribute(): string { return $this->kode_laporan; }
    public function getLatitudeAttribute() { return $this->lintang; }
    public function getLongitudeAttribute() { return $this->bujur; }
    public function getAddressAttribute(): string { return $this->alamat; }
    public function getTitleAttribute(): string { return $this->judul; }
    public function getDescriptionAttribute(): string { return $this->deskripsi; }
    public function getReportedAtAttribute() { return $this->dilaporkan_pada; }
    public function getRejectionReasonAttribute() { return $this->alasan_penolakan; }
}
