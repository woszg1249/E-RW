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

    /**
     * Urutkan hierarki keluarga sesuai standar Dukcapil:
     * 1. Kepala Keluarga
     * 2. Suami / Istri
     * 3. Anak
     * 4. Menantu / Cucu / Orangtua / Mertua / Famili Lain
     */
    public function scopeOrderFamilyHierarchy($query)
    {
        return $query->orderByRaw("
            CASE 
                WHEN LOWER(TRIM(COALESCE(status_keluarga, ''))) = 'kepala keluarga' THEN 1
                WHEN LOWER(TRIM(COALESCE(status_keluarga, ''))) = 'suami' THEN 2
                WHEN LOWER(TRIM(COALESCE(status_keluarga, ''))) = 'istri' THEN 3
                WHEN LOWER(TRIM(COALESCE(status_keluarga, ''))) = 'anak' THEN 4
                WHEN LOWER(TRIM(COALESCE(status_keluarga, ''))) = 'menantu' THEN 5
                WHEN LOWER(TRIM(COALESCE(status_keluarga, ''))) = 'cucu' THEN 6
                WHEN LOWER(TRIM(COALESCE(status_keluarga, ''))) = 'orangtua' THEN 7
                WHEN LOWER(TRIM(COALESCE(status_keluarga, ''))) = 'mertua' THEN 8
                WHEN LOWER(TRIM(COALESCE(status_keluarga, ''))) = 'famili lain' THEN 9
                ELSE 10
            END ASC
        ")
        ->orderByRaw("CASE WHEN tanggal_lahir IS NULL THEN 1 ELSE 0 END, tanggal_lahir ASC")
        ->orderBy('id', 'asc');
    }

    public static function getKetuaRw($rw = '19'): string
    {
        return 'FABIAN';
    }

    public static function getKetuaRt($rt = '06'): string
    {
        $rtClean = ltrim((string)$rt, '0');
        if ($rtClean === '7' || $rt === '07') {
            return 'BAMBANG';
        }
        return 'DIMAS';
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
