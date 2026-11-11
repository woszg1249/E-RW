<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    protected $table = 'warga';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'alamat',
        'no_hp',
    ];
}
