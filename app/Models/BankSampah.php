<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankSampah extends Model
{
    protected $table = 'bank_sampah';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nama_warga',
        'jenis_sampah',
        'berat_kg',
        'harga_per_kg',
        'total_harga',
        'status',
        'tanggal_pengumpulan',
        'tanggal_diproses',
        'catatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
