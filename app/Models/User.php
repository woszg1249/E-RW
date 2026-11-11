<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'alamat',
        'kelamin',
        'no_hp',
        'foto',
        'role',
        'info_iuran',
        'nik',
        'rfid_uid',
        'no_kk',
        'rt',
        'rw',
        'status_keluarga',
        'pekerjaan',
        'status_perkawinan',
        'pendidikan',
        'agama',
        'tempat_lahir',
        'tanggal_lahir',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
        ];
    }

    public function getNameAttribute()
    {
        return $this->nama;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nama'] = $value;
    }

    public function isAdmin(): bool
    {
        return strtoupper($this->role ?? '') === 'ADMIN';
    }

    public function isWarga(): bool
    {
        return strtoupper($this->role ?? '') === 'WARGA';
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'id_user');
    }

    public function suratPengajuan()
    {
        return $this->hasMany(SuratPengajuan::class, 'id_user');
    }

    public function bankSampah()
    {
        return $this->hasMany(BankSampah::class, 'id_user');
    }

    public function peminjamanAset()
    {
        return $this->hasMany(PeminjamanAset::class, 'id_user');
    }

    public function koperasiAnggota()
    {
        return $this->hasOne(KoperasiAnggota::class, 'id_user');
    }

    public function koperasiSimpanan()
    {
        return $this->hasMany(KoperasiSimpanan::class, 'id_user');
    }

    public function koperasiPinjaman()
    {
        return $this->hasMany(KoperasiPinjaman::class, 'id_user');
    }
}
