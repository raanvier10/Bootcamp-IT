<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'wilayah';

    protected $fillable = ['kode', 'nama'];

    public function users()
    {
        return $this->hasMany(User::class, 'wilayah_id');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'wilayah_id');
    }
}
