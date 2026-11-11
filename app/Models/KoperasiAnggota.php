<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KoperasiAnggota extends Model
{
    protected $table = 'koperasi_anggota';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'tanggal_gabung',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
