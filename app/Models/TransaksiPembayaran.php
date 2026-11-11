<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPembayaran extends Model
{
    protected $table = 'transaksi_pembayaran';
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'id_user',
        'nama_warga',
        'jenis_kas',
        'keterangan',
        'jumlah',
        'id_tagihan',
        'metode',
        'status',
        'created_at',
        'paid_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'id_tagihan', 'id_tagihan');
    }
}
