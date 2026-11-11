<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifDismissed extends Model
{
    protected $table = 'notif_dismissed';
    public $timestamps = false;

    protected $fillable = [
        'id_tagihan',
        'id_user',
        'created_at',
    ];
}
