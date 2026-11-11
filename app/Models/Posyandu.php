<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    protected $table = 'posyandu';
    public $timestamps = false;

    protected $fillable = [
        'id_warga',
        'nama_bayi',
        'usia_bulan',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'status_kesehatan',
        'catatan',
        'tanggal_pemeriksaan',
        'pemeriksaan_oleh',
        'jenis_kelamin',
    ];

    public function warga()
    {
        return $this->belongsTo(User::class, 'id_warga');
    }
}
