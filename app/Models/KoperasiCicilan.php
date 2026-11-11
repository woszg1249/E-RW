<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KoperasiCicilan extends Model
{
    protected $table = 'koperasi_cicilan';
    public $timestamps = false;

    protected $fillable = [
        'id_pinjaman',
        'jumlah',
        'tanggal',
        'catatan',
    ];

    public function pinjaman()
    {
        return $this->belongsTo(KoperasiPinjaman::class, 'id_pinjaman');
    }
}
