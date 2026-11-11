<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeminjamanAset extends Model
{
    protected $table = 'peminjaman_aset';

    protected $fillable = [
        'id_aset',
        'id_user',
        'jumlah_pinjam',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'catatan',
    ];

    public function aset()
    {
        return $this->belongsTo(Aset::class, 'id_aset');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
