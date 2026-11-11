<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KoperasiPinjaman extends Model
{
    protected $table = 'koperasi_pinjaman';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'jumlah',
        'tenor',
        'bunga',
        'status',
        'tanggal_pengajuan',
        'keterangan',
        'metode_pengajuan',
        'scan_device_id',
        'nik_peminjam',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function cicilan()
    {
        return $this->hasMany(KoperasiCicilan::class, 'id_pinjaman');
    }

    public function getTotalPinjamanAttribute()
    {
        $bungaNominal = ($this->jumlah * ($this->bunga / 100));
        return $this->jumlah + $bungaNominal;
    }

    public function getTotalTerbayarAttribute()
    {
        return $this->cicilan()->sum('jumlah');
    }

    public function getSisaPinjamanAttribute()
    {
        return max(0, $this->total_pinjaman - $this->total_terbayar);
    }
}
