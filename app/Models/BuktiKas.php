<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuktiKas extends Model
{
    protected $table = 'bukti_kas';
    public $timestamps = false;

    protected $fillable = [
        'id_keuangan',
        'filename',
        'uploaded_at',
    ];

    public function keuangan()
    {
        return $this->belongsTo(Keuangan::class, 'id_keuangan');
    }
}
