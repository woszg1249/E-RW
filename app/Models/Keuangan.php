<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keuangan extends Model
{
    protected $table = 'keuangan';
    public $timestamps = false;

    protected $fillable = [
        'nama_warga',
        'jenis',
        'keterangan',
        'jumlah',
        'tanggal',
    ];

    public function bukti()
    {
        return $this->hasMany(BuktiKas::class, 'id_keuangan');
    }
}
