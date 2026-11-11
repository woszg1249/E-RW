<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    protected $table = 'umkm';

    protected $fillable = [
        'nama_usaha',
        'kategori',
        'deskripsi',
        'alamat',
        'jam_operasional',
        'whatsapp',
        'foto',
        'produk_layanan',
        'estimasi_jarak',
        'google_maps_link',
        'status',
    ];
}
