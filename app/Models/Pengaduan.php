<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'pengaduan_warga';

    protected $fillable = [
        'id_user',
        'kategori',
        'judul',
        'isi_laporan',
        'lokasi',
        'foto',
        'status',
        'tanggapan_admin',
        'ditanggapi_oleh',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
