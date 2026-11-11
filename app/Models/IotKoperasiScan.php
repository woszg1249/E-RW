<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IotKoperasiScan extends Model
{
    protected $table = 'iot_koperasi_scans';
    public $timestamps = false;

    protected $fillable = [
        'device_id',
        'nik',
        'rfid_uid',
        'id_user',
        'status',
        'scanned_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
