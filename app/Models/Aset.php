<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    protected $table = 'aset';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'deskripsi',
        'kategori',
        'kondisi',
        'lokasi',
        'jumlah',
        'status',
        'created_at',
    ];

    public function peminjaman()
    {
        return $this->hasMany(PeminjamanAset::class, 'id_aset');
    }
}
