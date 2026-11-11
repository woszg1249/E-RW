<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KoperasiSimpanan extends Model
{
    protected $table = 'koperasi_simpanan';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'jenis',
        'jumlah',
        'tanggal',
        'catatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
