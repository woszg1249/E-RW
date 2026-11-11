<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratPengajuan extends Model
{
    protected $table = 'surat_pengajuan';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nama',
        'jenis_surat',
        'nik',
        'ttl',
        'alamat',
        'tujuan',
        'nama_rt',
        'nama_rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'tanggal_permohonan',
        'detail_usaha',
        'keterangan',
        'status',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
