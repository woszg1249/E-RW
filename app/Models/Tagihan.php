<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan';
    protected $primaryKey = 'id_tagihan';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'bulan_tahun',
        'tanggal_tagihan',
        'jumlah',
        'status_bayar',
        'tgl_update',
        'tgl_buat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function transaksi()
    {
        return $this->hasMany(TransaksiPembayaran::class, 'id_tagihan', 'id_tagihan');
    }
}
